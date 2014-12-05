var express = require('express.io'),
        app = express().http().io(),
        passport = require('passport'),
        flash = require('connect-flash'),
        LocalStrategy = require('passport-local').Strategy,
        mongoose = require('mongoose'),
        RememberMeStrategy = require('passport-remember-me').Strategy,
        ffmpeg = require('fluent-ffmpeg'),
        dl = require('delivery'),
        fs = require('fs'),
        logfile = fs.createWriteStream('./logfile.log', {flags: 'a'}),
        fstream = require('fstream'),
        unzip = require('unzip'),
        bodyParser = require('body-parser'),
        winston = require('winston');

    // Configure passport
var User = require('./models/user');
var Files = require('./models/files');

var logger = new (winston.Logger)({
    transports: [
      new (winston.transports.Console)(),
      new (winston.transports.File)({ filename: './debug.log' })
    ]
  });

passport.use(new LocalStrategy(User.authenticate()));

/* Fake, in-memory database of remember me tokens */

var tokens = {}

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
}

// Connect mongoose
mongoose.connect('mongodb://localhost/media');
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
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}


require('./routes')(app);
app.listen(8080, function(){
   console.log("Express server listening on port 8080");
});