var express = require('express'),
        app = express(),
        server = require('http').createServer(app),
        ent = require('ent'),
        passport = require('passport'),
        flash = require('connect-flash'),
        LocalStrategy = require('passport-local').Strategy,
        mongoose = require('mongoose');

// Configure passport
var User = require('./models/user');
var Chat = require('./models/chat');
var Poker = require('./models/poker');
var PokerUser = require('./models/poker_user');

passport.use(new LocalStrategy(User.authenticate()));

passport.serializeUser(User.serializeUser());
passport.deserializeUser(User.deserializeUser());

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
    app.use(app.router);
    app.use(express.static(__dirname + '/public'));
});
var idTable;
var io = require('socket.io').listen(server);
var running = false;
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
                socket.set('poker', poker);
                socket.emit('initialised', poker);
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
    if(!running){
        //verifi le positionnement
        setInterval(function () {
            Poker.findOne({table: idTable}, function (err, poker) {
                console.log(poker);
                var place;
                if (poker) {
                    socket.get('user', function (error, user) {
                        place = poker.place;
                        console.log('nb user ' + poker.user.length + ' ' + poker.nbUsers);
                        if (poker.user.length == poker.nbUsers) {
                            var used = false;
                            for (var i = 0; i < poker.user.length; i++) {
                                if (place == poker.user[i].place) {
                                    poker.place = poker.user[0].place;
                                }
                                if (poker.user.hasOwnProperty(i + 1) && poker.user[i + 1].place) {
                                    if(!used){
                                        poker.place = poker.user[i + 1].place;
                                        console.log('next place ' + poker.place);
                                        //used = true;
                                    }
                                }
                                console.log('set poker '+poker.place);
                                poker.save();
                                socket.set('poker', poker);
                            }
                        }
                        console.log('emit next user')
                        io.sockets.emit('next_poker_user', {poker: poker});
                    });
                }
            });
        }, 5000);
        running = true;
    }
    
    socket.on('next_poker_user', function (params) {
        socket.get('poker', function (error, poker) {
            if (poker) {
                socket.get('user', function (error, user) {
                    console.log("It has been one second!");
                    for (var i = 0; i < poker.user.length; i++) {
                        console.log(poker.place + ' ' + poker.user[i].place);
                        //cherche le prochain joueur si introuvable retourne au 1er joueur
                        if (poker.user.hasOwnProperty(i + 1) && poker.user[i + 1].place) {
                            poker.place = poker.user[i + 1].place;
                            console.log('next place ' + poker.place);
                        } else {
                            if (poker.user[0].place) {
                                poker.place = poker.user[i].place;
                                console.log('same place ' + poker.place);
                            }
                        }
                    }
                    poker.save();
                    socket.set('poker', poker);
                    //io.sockets.emit('next_poker_user', {poker: poker, user: user});
                    console.log(poker.user);
                });
            } else {
                console.log('no session');
            }
        });
    });
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
                    poker.user.push(pokerUser);
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
                if(poker.nbUsers > 0){
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