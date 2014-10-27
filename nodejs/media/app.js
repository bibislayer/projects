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
        fs = require('fs'),
        fstream = require('fstream'),
        unzip = require('unzip'),
        mkdirp = require('mkdirp');

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
io.set('transports', ['xhr-polling']);
var running = false;
function keysrt(key, desc) {
    return function (a, b) {
        return desc ? ~~(a[key] < b[key]) : ~~(a[key] > b[key]);
    }
}
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

//file upload
io.sockets.on('connection', function (socket) {
    //upload file
    var delivery = dl.listen(socket);
    var folder_path = "";
    var user_files = Array();
    delivery.on('receive.success', function (file) {
        //if selected folder
        socket.get('folder_path', function (error, fold_path) {
            if(fold_path){
                folder_path = fold_path+'/';
            }
        });
        console.log('folder path '+folder_path);
        //console.log('folder path'path);
        fs.writeFile(__dirname + '/uploads/' + folder_path+file.name, file.buffer, function (err) {
            if (err) {
                console.log('File could not be saved.');
            } else {
                socket.get('user_id', function (error, user_id) {
                    User.findOne({_id: user_id}, function (err, user) {
                        if (user) {
                            //make sure you set the correct path to your video file
                            //console.log(file);
                            var type = "";
                            var length = file.name.length;
                            var noExt = file.name.substring(0, length - 4);
                            var ext = file.name.substring(length - 3, length);
                            if(ext == 'png' || ext == 'jpg' || ext == 'gif'){
                                type = 'image';
                            }else if(ext == 'zip'){
                                type = 'zip';
                            }
                            var path = __dirname + '/uploads/' + folder_path+file.name;
                            if(type == 'zip'){
                                var root_id,parent_id,level = 0;
                                var first = true;
                                fs.createReadStream(path)
                                .pipe(unzip.Parse())
                                .on('entry', function (entry) {
                                    var invalidArray = ['.DS_Store', '__MACOSX/'];
                                    var fileName = entry.path;
                                    var type = entry.type; // 'Directory' or 'File'
                                    var size = entry.size;
                                    if(fileName.search('.DS_Store') < 0 && fileName.search('__MACOSX/') < 0){
                                        var arrayName = fileName.split('/');
                                        var newName = arrayName[arrayName.length-1];
                                        if(type == 'Directory'){
                                            newName = arrayName[arrayName.length-2]
                                        }
                                        Files.findOne({name: newName}, function (err, exist) {
                                            if (!exist) {
                                                console.log('l : '+level);
                                                var files = new Files({
                                                    user: user_id,
                                                    level: level,
                                                    root_id: root_id,
                                                    parent_id: parent_id,
                                                    name: newName,
                                                    type: type,
                                                    size: size,
                                                    time: 0,
                                                    permissions: {access: 0, users: ""},
                                                    path: '/uploads/'+ folder_path
                                                });
                                                files.save(function (err, file) {
                                                    if (err)
                                                        return console.error(err);
                                                });
                                                Files.find({user: user_id}, function (err, user_files) {
                                                    socket.emit('file_saved', user_files);
                                                });
                                                if(first){
                                                    root_id = files._id;
                                                }
                                                first = false;
                                                if(type == "Directory"){
                                                    parent_id = files._id;
                                                    if(!first){
                                                        level++;
                                                        folder_path += newName+'/';
                                                    }
                                                }
                                                console.log('File saved.');
                                                if(type == 'Directory'){
                                                    mkdirp(__dirname + '/uploads/'+ folder_path, function(err) {
                                                    });
                                                }else{
                                                    var stream = fs.createWriteStream(__dirname + '/uploads/' + folder_path+newName);
                                                    entry.pipe(stream);
                                                }   
                                            } else {
                                                entry.autodrain();
                                                console.log('File exist');
                                                console.log(exist);
                                            }
                                        });
                                    } else {
                                      entry.autodrain();
                                    }
                                })
                                //.pipe(fstream.Writer(__dirname + '/uploads/' + folder_path))
                            }else{
                                Files.findOne({name: file.name}, function (err, exist) {
                                    if (!exist) {
                                        socket.get('folder_id', function (error, folder_id) {
                                            if(folder_id){
                                                Files.findOne({_id: folder_id}, function (err, fl) {
                                                    var files = new Files({
                                                        user: user._id,
                                                        parent_id: folder_id,
                                                        level: fl.level+1,
                                                        root_id: fl.root_id,
                                                        name: file.name,
                                                        type: type,
                                                        size: fl.size,
                                                        time: 0,
                                                        path: '/uploads/'+ folder_path,
                                                        permissions: {access: 0, users: ""}
                                                    });
                                                    files.save(function (err, file) {
                                                        if (err)
                                                            return console.error(err);
                                                    });
                                                    Files.find({user: user_id}, function (err, user_files) {
                                                        socket.emit('file_saved', user_files);
                                                    });
                                                });
                                            }
                                        });
                                        console.log('File saved.');
                                    } else {
                                        console.log('File exist');
                                    }
                                });
                            } 
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

    var deleteFolderRecursive = function(path) {
          if( fs.existsSync(path) ) {
            fs.readdirSync(path).forEach(function(file,index){
              var curPath = path + "/" + file;
              if(fs.lstatSync(curPath).isDirectory()) { // recurse
                deleteFolderRecursive(curPath);
              } else { // delete file
                fs.unlinkSync(curPath);
              }
            });
            fs.rmdirSync(path);
          }
        };

    socket.on('convert', function (fileName, type) {
        convert(fileName, type);
    });
    // Dès qu'on nous donne un pseudo, on le stocke en variable de session et on informe les autres personnes
    socket.on('new_user', function (user_id) {
        User.findOne({_id: user_id}, function (err, user) {
            if(user){
                socket.set('user_id', user_id);
                socket.emit('user_logged', user_id);
            }
        });
    });

    socket.on('remove_file', function (files, user_id) {
        User.findOne({_id: user_id}, function (err, user) {
            if(user){
                for(var i=0;i<files.length;i++){
                    Files.findOneAndRemove({_id: files[i]}, function (err, fld) {
                        socket.emit('file_removed', fld._id);
                        if(fld.type == "Directory"){
                            deleteFolderRecursive(fld.path+fld.name);
                        }else{
                            fs.unlinkSync(fld.path+fld.name);
                        }
                    });
                }
            }
        });
    });

    socket.on('move_file', function (files, folder_id) {
        socket.get('user_id', function (error, user_id) {
            if(user_id){
                console.log('user_id '+user_id);
                for(var i=0;i<files.length;i++){
                    Files.findOne({_id: files[i]}, function (err, fld) {
                        if(fld){
                            console.log('file : '+fld.name);
                            Files.findOne({_id: folder_id}, function (err, fold) {
                                if(fold){
                                    console.log('folder bd  : '+fold.name+' '+fold.level);
                                    level = (typeof fold.level == 'undefined') ? 0 : fold.level; 
                                    console.log(__dirname+fld.path+fld.name);
                                    console.log(__dirname+fold.path+fold.name+'/'+fld.name);
                                    fs.renameSync(__dirname+fld.path+fld.name, __dirname+fold.path+fold.name+'/'+fld.name);
                                    fld.parent_id = fold._id;
                                    fld.root_id = fold.root_id;
                                    fld.level = level+1;
                                    fld.path = fold.path+fold.name+'/';
                                    fld.save();
                                    Files.find({user: user_id}, function (err, user_files) {
                                        socket.emit('file_saved', user_files);
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });
    });

    socket.on('select_folder', function (folder_id) {
        var folder_path = "";
        //user logged in
        socket.get('user_id', function (error, user_id) {
            if(user_id){
                Files.findOne({_id: folder_id}, function (err, fld) {
                    if(fld){
                        //file have parent (in directory)
                        if(fld.parent_id){
                            Files.findOne({_id: fld.parent_id}, function (err, parent) {
                                if(parent){
                                    folder_path = parent.name+'/'+fld.name;
                                    Files.findOne({_id: parent.parent_id}, function (err, sub_parent) {
                                        //file have sub parent (in sub directory)
                                        if(sub_parent){
                                            folder_path = sub_parent.name+'/'+parent.name+'/'+fld.name;
                                        }
                                        Files.find({parent_id: folder_id}, function (err, files) {
                                            if(files){
                                                socket.set('folder_path', folder_path);
                                                socket.emit('selected_folder', files);
                                            }
                                        });
                                     });
                                }
                            });
                        }else{
                            folder_path += fld.name;
                            Files.find({parent_id: folder_id}, function (err, files) {
                                if(files){
                                    socket.set('folder_path', folder_path);
                                    socket.emit('selected_folder', files);
                                }
                            });
                        }
                        console.log('path : '+folder_path);
                        //folder_path = folder_path.replace(/\s+/g, '');  
                    }
                    socket.set('folder_id', folder_id);   
                });
            }
        });
    });
    
    function compare(a,b) {
      if (a.level < b.level)
         return -1;
      if (a.level > b.level)
        return 1;
      return 0;
    }

    socket.on('new_folder', function (folder_name, parent_id) {
        //user logged in
        socket.get('user_id', function (error, user_id) {
            if(user_id){
                Files.findOne({_id: parent_id}, function (err, file) {
                    if(file){
                        socket.get('folder_path', function (error, folder_path) {
                            if(folder_path){
                                mkdirp(__dirname + '/uploads/'+ folder_path+'/'+folder_name, function(err) {
                                    // path was created unless there was error
                                    //console.log(err);
                                    var files = new Files({
                                        user: user_id,
                                        parent_id: file._id,
                                        level: file.level+1,
                                        root_id: file.root_id,
                                        name: folder_name,
                                        type: 'Directory',
                                        size: 0,
                                        time: 0,
                                        path: '/uploads/'+ folder_path,
                                        permissions: {access: 0, users: ""},
                                    });
                                    files.save(function (err, file) {
                                        if (err)
                                            return console.error(err);
                                        Files.find({user: user_id}, function (err, user_files) {
                                            user_files = user_files.sort(compare);
                                            socket.emit('file_saved', user_files);
                                        });
                                    });
                                });
                            }
                        });
                    }
                });
            }
        });
    });
});
require('./routes')(app);
server.listen(8080, function () {
    console.log('Express server listening on port 8080');
});