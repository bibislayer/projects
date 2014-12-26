var express = require('express.io'),
        app = express().http().io(),
        passport = require('passport'),
        utils = require('./utils'),
        base32 = require('thirty-two'),
        flash = require('connect-flash'),
        LocalStrategy = require('passport-local').Strategy,
        TotpStrategy = require('passport-totp').Strategy,
        mongoose = require('mongoose'),
        RememberMeStrategy = require('passport-remember-me').Strategy,
        fs = require('fs'),
        logfile = fs.createWriteStream('./logfile.log', {flags: 'a'}),
        fstream = require('fstream'),
        unzip = require('unzip'),
        bodyParser = require('body-parser'),
        winston = require('winston');

// Configure passport
var User = require('./models/user');
var Files = require('./models/files');

var keys = {};
function findKeyForUserId(id, fn) {
    return fn(null, keys[id]);
}

function saveKeyForUserId(id, key, fn) {
    keys[id] = key;
    return fn(null);
}


var logger = new (winston.Logger)({
    transports: [
        new (winston.transports.Console)(),
        new (winston.transports.File)({filename: './debug.log'})
    ]
});

passport.use(new LocalStrategy(User.authenticate()));
passport.use(new TotpStrategy(
        function (user, done) {
            // setup function, supply key and period to done callback
            findKeyForUserId(user.id, function (err, obj) {
                if (err) {
                    return done(err);
                }
                return done(null, obj.key, obj.period);
            });
        }
));

/* Fake, in-memory database of remember me tokens */

var tokens = {}

var deleteFolderRecursive = function (path) {
    if (fs.existsSync(path)) {
        fs.readdirSync(path).forEach(function (file, index) {
            var curPath = path + "/" + file;
            if (fs.lstatSync(curPath).isDirectory()) { // recurse
                deleteFolderRecursive(curPath);
            } else { // delete file
                fs.unlinkSync(curPath);
            }
        });
        fs.rmdirSync(path);
    }
};

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

/*passport.use(new RememberMeStrategy(
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
 ));*/

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
}var options = {
  user: 'bibislayer',
  pass: '@nicktalope78@'
}
// Connect mongoose
mongoose.connect('mongodb://localhost/media', options);
// configure Express
app.configure(function () {
    app.set('views', __dirname + '/views');
    app.set('view engine', 'ejs');
    app.engine('ejs', require('ejs-locals'));
    app.use(express.logger({stream: logfile}));
    app.use(express.cookieParser());
    app.use(bodyParser.urlencoded({
        extended: true
    }));
    app.use(bodyParser.json());
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
var io = app.io;
var running = false;
function keysrt(key, desc) {
    return function (a, b) {
        return desc ? ~~(a[key] < b[key]) : ~~(a[key] > b[key]);
    }
}
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}
function ensureAuthenticated(req, res, next) {
    if (req.isAuthenticated()) {
        return next();
    }
    res.redirect('/login');
}
app.get('/setup', ensureAuthenticated, function (req, res, next) {
    findKeyForUserId(req.user.id, function (err, obj) {
        if (err) {
            return next(err);
        }
        if (obj) {
            // two-factor auth has already been setup
            var encodedKey = base32.encode(obj.key);

            // generate QR code for scanning into Google Authenticator
            // reference: https://code.google.com/p/google-authenticator/wiki/KeyUriFormat
            var otpUrl = 'otpauth://totp/' + req.user.email
                    + '?secret=' + encodedKey + '&period=' + (obj.period || 30);
            var qrImage = 'https://chart.googleapis.com/chart?chs=166x166&chld=L|0&cht=qr&chl=' + encodeURIComponent(otpUrl);

            res.render('setup', {title: 'setup', user: req.user, key: encodedKey, qrImage: qrImage, message: req.flash('error')});
        } else {
            // new two-factor setup.  generate and save a secret key
            var key = utils.randomString(10);
            var encodedKey = base32.encode(key);

            // generate QR code for scanning into Google Authenticator
            // reference: https://code.google.com/p/google-authenticator/wiki/KeyUriFormat
            var otpUrl = 'otpauth://totp/' + req.user.email
                    + '?secret=' + encodedKey + '&period=30';
            var qrImage = 'https://chart.googleapis.com/chart?chs=166x166&chld=L|0&cht=qr&chl=' + encodeURIComponent(otpUrl);
            saveKeyForUserId(req.user.id, {key: key, period: 30}, function (err) {
                if (err) {
                    return next(err);
                }
                res.render('setup', {title: 'setup', user: req.user, key: encodedKey, qrImage: qrImage, message: req.flash('error')});
            });
            console.log(keys);
        }
    });
});
app.get('/login-otp', ensureAuthenticated,
        function (req, res, next) {
            // If user hasn't set up two-factor auth, redirect
            findKeyForUserId(req.user.id, function (err, obj) {
                if (err) {
                    return next(err);
                }
                if (!obj) {
                    return res.redirect('/setup');
                }
                return next();
            });
        },
        function (req, res) {
            res.render('login-otp', {title: 'login', user: req.user, message: req.flash('error')});
        });
app.post('/login-otp',
        passport.authenticate('totp', {failureRedirect: '/login-otp', failureFlash: true}),
        function (req, res) {
            req.session.secondFactor = 'totp';
            res.redirect('/');
        });

require('./routes')(app);
app.listen(8080, function () {
    console.log("Express server listening on port 8080");
});
