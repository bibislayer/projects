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

var io = require('socket.io').listen(server);
io.sockets.on('connection', function (socket, pseudo) {
    // Dès qu'on nous donne un pseudo, on le stocke en variable de session et on informe les autres personnes
    socket.on('nouveau_client', function (pseudo) {
        pseudo = ent.encode(pseudo);
        socket.set('pseudo', pseudo);
        socket.broadcast.emit('nouveau_client', pseudo);
    });
    var uId;
    socket.on('new_poker_user', function (pseudo, table, place) {
        var used = 0;
        pseudo = ent.encode(pseudo);
        socket.set('place', place);
        //console.log(user);
        Poker.findOne({table: table}, function (err, poker) {
            if (poker) {
                for (var i = 0; i < poker.user.length; i++) {
                    if (pseudo == poker.user[i].pseudo) {
                        used = 1;
                        uId = poker.user[i]._id;
                    }
                }
                if (!used) {
                    poker.user.push(new PokerUser({username: pseudo, place: place, money: 100}));
                    poker.save();
                    socket.broadcast.emit('poker_alert', {message: "New player connected", class: 'alert alert-dismissable alert-success'});
                    socket.broadcast.emit('new_poker_user', {username: pseudo, place: place, money: 100});
                } else {
                    socket.emit('poker_alert', {message: "Have existing place on this table", class: 'alert alert-dismissable alert-warning'});
                }
            } else {
                console.log('no data for this company');
            }
        });
    });
    socket.on('del_poker_user', function (pseudo, table, place) {
        console.log(pseudo + ' user delete');
        Poker.findOne({table: table}, function (err, poker) {
            if (poker) {
                for (var i = 0; i < poker.user.length; i++) {
                    if (pseudo == poker.user[i].username) {
                        console.log(poker.user[i]);
                        poker.user.splice(i,1);
                        console.log(poker);
                    }
                }
                Poker.update({table: table}, {$set: {user: poker.user}}, function (err, poker) {
                    if (err)
                        return handleError(err);
                    socket.broadcast.emit('del_poker_user', {username: pseudo, place: place});
                    socket.broadcast.emit('poker_alert', {message: "Player: "+pseudo+" disconnected", class: 'alert alert-dismissable alert-warning'});
                    
                });
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