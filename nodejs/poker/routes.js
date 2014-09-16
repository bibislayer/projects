var passport = require('passport'),
        User = require('./models/user'),
        mongoose = require('mongoose'),
        flash = require('connect-flash');

module.exports = function (app) {

    app.get('/', ensureAuthenticated, function (req, res) {
        res.render('index', {title: 'accueil', user: req.user});
    });

    app.get('/account', ensureAuthenticated, function (req, res) {
        res.render('account.ejs', {title: 'account', user: req.user});
    });

    app.get('/register', function (req, res) {
        User.find({}, function (err, teams) {
        if (err) {
            console.log(err);
        } else {
            mongoose.connection.close();
            console.log(teams, teams.length);
        }
    });
        res.render('register', {title: "register", user: req.user, message: req.flash('error')});
    });

    app.post('/register', function (req, res) {
        User.register(new User({username: req.body.username}), req.body.password, function (err, account) {
            if (err) {
                return res.render('register', {title: "register", user: req.user, message: req.flash('error')});
            }

            passport.authenticate('local', {failureRedirect: '/login', failureFlash: true})(req, res, function () {
                res.redirect('/');
            });
        });
    });

    app.get('/login', function (req, res) {
        res.render('login', {title: 'Login', user: req.user, message: req.flash('error')});
    });

    app.post('/login', passport.authenticate('local', {failureRedirect: '/login', failureFlash: true}), 
    function (req, res) {
        res.redirect('/');
    });

    app.get('/logout', function (req, res) {
        req.logout();
        res.redirect('/');
    });

    app.get('/ping', function (req, res) {
        res.send("pong!", 200);
    });
    function ensureAuthenticated(req, res, next) {
        if (req.isAuthenticated()) {
            return next();
        }
        res.redirect('/login')
    }
};


