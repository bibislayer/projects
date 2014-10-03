var express = require('express'),
        app = express(),
        server = require('http').createServer(app),
        ent = require('ent'),
        passport = require('passport'),
        flash = require('connect-flash'),
        LocalStrategy = require('passport-local').Strategy,
        mongoose = require('mongoose'),
        RememberMeStrategy = require('passport-remember-me').Strategy;

// Configure passport
var User = require('./models/user');
var Chat = require('./models/chat');
var Poker = require('./models/poker');
var PokerUser = require('./models/poker_user');

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
mongoose.connect('mongodb://localhost/poker');

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
io.sockets.on('connection', function (socket, pseudo) {
    // Dès qu'on nous donne un pseudo, on le stocke en variable de session et on informe les autres personnes
    socket.on('nouveau_client', function (pseudo) {
        pseudo = ent.encode(pseudo);
        socket.set('pseudo', pseudo);
        socket.broadcast.emit('nouveau_client', pseudo);
    });
    socket.on('init_poker', function (table) {
        Poker.findOne({table: table}, function (err, poker) {
            if (poker) {
                idTable = poker.table;
                socket.emit('initialised', {poker: poker});
            } else {
                console.log('no data for this company');
            }
        });
    });
    //creation de table
    socket.on('create_table', function (nbTable) {
        var poker = new Poker({
            user: {},
            table: nbTable,
            round: 0,
            money: 0,
            place: 0,
            nbUsers: 0
        });
        poker.save(function (err, poker) {
            if (err)
                return console.error(err);
        });
    });
    if (!running) {
        setInterval(function () {
            launchRound();
        }, 10000);
    }

    function launchRound() {
        //initialise la partie
        console.log('launching game');
        //compteur general d'un round par table
        Poker.findOne({table: idTable}, function (err, poker) {
            if (poker) {
                if (poker.user.length == poker.nbUsers) {
                    var used = false;
                    //verifi la place et passe a la suivante
                    for (var i = 0; i < poker.user.length; i++) {
                        if (!used && poker.user[i] && poker.user[i].place == poker.place) {
                            if (!used && poker.user.hasOwnProperty(i + 1) && poker.user[i + 1].place) {
                                console.log(poker.place);
                                poker.place = poker.user[i + 1].place;
                                used = true;
                            } else if (!used) {
                                poker.place = poker.user[0].place;
                                used = true;
                            }
                        }
                    }
                    if (!used && poker.user[0]) {
                        poker.place = poker.user[0].place;
                    }
                    poker.save();
                    socket.set('poker', poker);
                }
                io.sockets.emit('next_poker_user', {poker: poker, user: user[poker.place]});
            }
        });
        running = true;
    }

    socket.on('new_poker_user', function (pseudo, table, place) {
        var used = 0;
        pseudo = ent.encode(pseudo);
        socket.set('place', place);
        Poker.findOne({table: table}, function (err, poker) {
            if (poker) {
                for (var i = 0; i < poker.user.length; i++) {
                    if (pseudo == poker.user[i].pseudo) {
                        used = 1;
                    }
                }
                if (!used) {
                    var pokerUser = new PokerUser({username: pseudo, place: parseInt(place), money: 100, moneyUsed: 0});
                    user[parseInt(place)] = pokerUser;
                    poker.user.push(pokerUser);
                    poker.user.sort(keysrt('place'));
                    poker.nbUsers = poker.nbUsers + 1;
                    poker.save();
                    socket.set('user', pokerUser);
                    socket.set('poker', poker);
                    socket.broadcast.emit('poker_alert', {message: "New player connected", class: 'alert alert-dismissable alert-success'});
                    socket.broadcast.emit('new_poker_user', {username: pseudo, place: parseInt(place), money: 100});
                } else {
                    socket.emit('poker_alert', {message: "Have existing place on this table", class: 'alert alert-dismissable alert-warning'});
                }
            } else {
                console.log('no data for this company');
            }
        });
        //console.log(user);
        Poker.findOne({table: table}, function (err, poker) {

        });
    });
    socket.on('poker_user_mise', function (mise, table, pseudo) {
        Poker.findOne({table: table}, function (err, poker) {
            socket.get('user', function (error, user) {
                if (poker && user) {
                    poker.money += mise;
                    user.moneyUsed += mise;
                    user.money -= mise;
                    user.save();
                    poker.save();
                    running = false;
                    launchRound();
                }
            });
        });
    });
    socket.on('del_poker_user', function (pseudo, table) {
        console.log(pseudo + ' user delete');
        Poker.findOne({table: table}, function (err, poker) {
            if (poker) {
                for (var i = 0; i < poker.user.length; i++) {
                    if (pseudo == poker.user[i].username) {
                        var place = poker.user[i].place;
                        console.log(poker.user[i]);
                        poker.user.splice(i, 1);
                        console.log(poker);
                    }
                }
                Poker.update({table: table}, {$set: {user: poker.user}}, function (err, poker) {
                    if (err)
                        return handleError(err);
                    socket.broadcast.emit('del_poker_user', {username: pseudo, place: place});
                    socket.broadcast.emit('poker_alert', {message: "Player: " + pseudo + " disconnected", class: 'alert alert-dismissable alert-warning'});

                });
                if (poker.nbUsers > 0) {
                    poker.nbUsers = poker.nbUsers - 1;
                }
                poker.save();
                socket.set('poker', poker);
            } else {
                console.log('no data for this company');
            }
        });
    });
// Dès qu'on reçoit un message, on récupère le pseudo de son auteur et on le transmet aux autres personnes
    socket.on('message', function (message) {
        socket.get('pseudo', function (error, pseudo) {
            message = ent.encode(message);
            var chat = new Chat({
                message: message,
                pseudo: pseudo,
                hasCreditCookie: true
            });
            chat.save(function (err, chat) {
                if (err)
                    return console.error(err);
            });
            socket.broadcast.emit('message', {pseudo: pseudo, message: message});
        });
    });
});
require('./routes')(app);
server.listen(8080, function () {
    console.log('Express server listening on port 8080');
});
/*app.listen(8080, function () {
 console.log('Express server listening on port 8080');
 });*/