var app = require('express')(),
        express = require('express'),
        cookie = require('express/node_modules/cookie'),
        path = require('path'),
        server = require('http').createServer(app),
        io = require('socket.io').listen(server),
        ent = require('ent'), // Permet de bloquer les caractères HTML (sécurité équivalente à htmlentities en PHP)
        fs = require('fs'),
        parseCookie = express.cookieParser;

// Gestion du routing
var index = require('./routes/index');
app.get('/', index.index);
var chat = require('./routes/chat');
app.get('/search', chat.chat);
var about = require('./routes/about');
app.get('/about', about.about);

app.configure(function () {
    // Allow parsing cookies from request headers
    this.use(express.cookieParser('quiabulanneaulac'));
    // Session management
    this.use(express.session({
        // Private crypting key
        "secret": "quiabulanneaulac",
        // Internal session data storage engine, this is the default engine embedded with connect.
        // Much more can be found as external modules (Redis, Mongo, Mysql, file...). look at "npm search connect session store"
        "store": new express.session.MemoryStore({reapInterval: 60000 * 10})
    }));
    this.use(express.static(path.join(__dirname, 'public')));
});

io.sockets.authorization(function (handshakeData, callback) {
    // Read cookies from handshake headers
    var cookies = cookie.parse(handshakeData.headers.cookies);
    // We're now able to retrieve session ID
    var sessionID = connect.utils.parseSignedCookie(cookies['connect.sid'], 'quiabulanneaulac');

    // No session? Refuse connection
    if (!sessionID) {
        callback('No session', false);
    } else {
        // Store session ID in handshake data, we'll use it later to associate
        // session with open sockets
        handshakeData.sessionID = sessionID;
        // On récupère la session utilisateur, et on en extrait son username
        app.sessionStore.get(sessionID, function (err, session) {
            if (!err && session && session.username) {
                // On stocke ce username dans les données de l'authentification, pour réutilisation directe plus tard
                handshakeData.username = session.username;
                // OK, on accepte la connexion
                callback(null, true);
            } else {
                // Session incomplète, ou non trouvée
                callback(err || 'User not authenticated', false);
            }
        });
    }
});

io.sockets.on('connection', function (socket, pseudo) {
// Dès qu'on nous donne un pseudo, on le stocke en variable de session et on informe les autres personnes
    socket.on('nouveau_client', function (pseudo) {
        pseudo = ent.encode(pseudo);
        app.sessionStore.username = pseudo;
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
server.listen(8080);