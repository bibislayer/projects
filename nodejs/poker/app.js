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

passport.use(new LocalStrategy(User.authenticate()));

passport.serializeUser(User.serializeUser());
passport.deserializeUser(User.deserializeUser());

// Connect mongoose
mongoose.connect('mongodb://localhost/poker');

mongoose.connection.on("open", function () {
    console.log("mongodb is connected");
});
User.find({}, function (err, teams) {
    if (err) {
        console.log(err);
    } else {
        mongoose.connection.close();
        console.log(teams, teams.length);
    }
});
// configure Express
app.configure(function () {
    app.set('views', __dirname + '/views');
    app.set('view engine', 'ejs');
    //app.engine('ejs', require('ejs'));
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
// Dès qu'on reçoit un message, on récupère le pseudo de son auteur et on le transmet aux autres personnes
    socket.on('message', function (message) {
        socket.get('pseudo', function (error, pseudo) {
            message = ent.encode(message);
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