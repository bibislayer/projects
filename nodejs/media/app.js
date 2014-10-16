var express = require('express'),
        app = express(),
        server = require('http').createServer(app),
        ent = require('ent'),
        passport = require('passport'),
        flash = require('connect-flash'),
        LocalStrategy = require('passport-local').Strategy,
        mongoose = require('mongoose'),
        RememberMeStrategy = require('passport-remember-me').Strategy,
        ffmpeg = require('fluent-ffmpeg'),
        dl = require('delivery'),
        fs = require('fs');

// Configure passport
var User = require('./models/user');
var Files = require('./models/files');

passport.use(new LocalStrategy(User.authenticate()));

/* Fake, in-memory database of remember me tokens */

var tokens = {}

function consumeRememberMeToken(token, fn) {
    var uid = tokens[token];
    // invalidate the single-use token
    delete tokens[token];
    return fn(null, uid);
}

function saveRememberMeToken(token, uid, fn) {
    tokens[token] = uid;
    return fn();
}

passport.use(new RememberMeStrategy(
        function (token, done) {
            Token.consume(token, function (err, user) {
                if (err) {
                    return done(err);
                }
                if (!user) {
                    return done(null, false);
                }
                return done(null, user);
            });
        },
        function (user, done) {
            var token = utils.randomString(64);
            Token.save(token, {userId: user.id}, function (err) {
                if (err) {
                    return done(err);
                }
                return done(null, token);
            });
        }
));

passport.serializeUser(User.serializeUser());
passport.deserializeUser(User.deserializeUser());

passport.use(new RememberMeStrategy(
        function (token, done) {
            consumeRememberMeToken(token, function (err, uid) {
                if (err) {
                    return done(err);
                }
                if (!uid) {
                    return done(null, false);
                }

                findById(uid, function (err, user) {
                    if (err) {
                        return done(err);
                    }
                    if (!user) {
                        return done(null, false);
                    }
                    return done(null, user);
                });
            });
        },
        issueToken
        ));

function issueToken(user, done) {
    var token = utils.randomString(64);
    saveRememberMeToken(token, user.id, function (err) {
        if (err) {
            return done(err);
        }
        return done(null, token);
    });
}

// Connect mongoose
mongoose.connect('mongodb://localhost/media');

// configure Express
app.configure(function () {
    app.set('views', __dirname + '/views');
    app.set('view engine', 'ejs');
    app.engine('ejs', require('ejs-locals'));
    app.use(express.logger());
    app.use(express.cookieParser());
    app.use(express.bodyParser());
    app.use(express.methodOverride());
    app.use(express.session({secret: 'quiabulanneaulac'}));
    app.use(flash());
    // Initialize Passport!  Also use passport.session() middleware, to support
    // persistent login sessions (recommended).
    app.use(passport.initialize());
    app.use(passport.session());
    app.use(passport.authenticate('remember-me'));
    app.use(app.router);
    app.use(express.static(__dirname + '/public'));
});

var timer, idTable;
var user = {};
var io = require('socket.io').listen(server);
var running = false;
function keysrt(key, desc) {
    return function (a, b) {
        return desc ? ~~(a[key] < b[key]) : ~~(a[key] > b[key]);
    }
}
//file upload
io.sockets.on('connection', function (socket) {
    //upload file
    var delivery = dl.listen(socket);
    delivery.on('receive.success', function (file) {
        fs.writeFile(__dirname + '/uploads/' + file.name, file.buffer, function (err) {
            if (err) {
                console.log('File could not be saved.');
            } else {
                socket.get('pseudo', function (error, pseudo) {
                    User.findOne({username: pseudo}, function (err, user) {
                        if (user) {
                            //make sure you set the correct path to your video file
                            console.log(file);
                            var type = "";
                            var length = file.name.length;
                            var noExt = file.name.substring(0, length - 4);
                            var ext = file.name.substring(length - 3, length);
                            if(ext == 'png' || ext == 'jpg' || ext == 'gif'){
                                type = 'image';
                            }
                            var path = __dirname + '/uploads/' + file.name;
                            Files.findOne({name: file.name}, function (err, exist) {
                                if (!exist) {
                                    var files = new Files({
                                        user: user._id,
                                        name: file.name,
                                        type: type,
                                        size: file.size,
                                        time: 0,
                                        path: path
                                    });
                                    files.save(function (err, file) {
                                        if (err)
                                            return console.error(err);
                                    });
                                    console.log('File saved.');
                                } else {
                                    console.log('File exist');
                                    console.log(exist);
                                }
                            });
                        }
                    });
                });
            }
        });
    });

    function convert(fileName, type) {
        var length = fileName.length;
        var noExt = fileName.substring(0, length - 4);
        Files.findOne({name: noExt}, function (err, file) {
            if (file) {
                var proc = new ffmpeg({source: __dirname + '/uploads/' + fileName, nolog: true})
                //Set the path to where FFmpeg is installed
                proc.setFfmpegPath("/Applications/ffmpeg")
                proc.withSize('100%')
                        .withFps(24)
                        .toFormat(type)
                        .on('end', function () {
                            console.log('file has been converted successfully');
                        })
                        .on('error', function (err) {
                            console.log('an error happened: ' + err.message);
                        })
                        .saveToFile(__dirname + '/uploads/' + noExt + '.' + type);
                file.type.push(type);
                file.save();
            }

        });
    }

    socket.on('convert', function (fileName, type) {
        convert(fileName, type);
    });
    // Dès qu'on nous donne un pseudo, on le stocke en variable de session et on informe les autres personnes
    socket.on('nouveau_client', function (pseudo) {
        pseudo = ent.encode(pseudo);
        socket.set('pseudo', pseudo);
        socket.broadcast.emit('nouveau_client', pseudo);
    });
});
require('./routes')(app);
server.listen(8080, function () {
    console.log('Express server listening on port 8080');
});