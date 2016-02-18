var express = require('express');
var socket_io = require("socket.io");
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var cookie = require('cookie');
var bodyParser = require('body-parser');

// Connection db
var mongo = require('mongodb');
var monk = require('monk');
var db = monk('localhost:27017/conqueror');

var routes = require('./routes/index');
var users = require('./routes/users');

var app = express(),
    session = require('express-session'),
    sessionStore = new session.MemoryStore();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser('quabulaneaulac'));
app.use(session({
    name: 'sid',
    store: sessionStore,
    secret: 'quabulaneaulac',
    saveUninitialized: true,
    resave: true,
    cookie: {
        path: '/',
        httpOnly: true,
        secure: false,
        maxAge: null
    }
}));
app.use(express.static(path.join(__dirname, 'public')));
// Make our db accessible to our router
app.use(function(req, res, next) {
	req.db = db;
	next();
});

app.use('/', routes);
app.use('/users', users);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});


module.exports = app;
