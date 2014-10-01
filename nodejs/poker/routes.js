var passport = require('passport'),
        User = require('./models/user'),
        Chat = require('./models/chat'),
        Poker = require('./models/poker'),
        mongoose = require('mongoose'),
        flash = require('connect-flash');
module.exports = function (app) {

    app.get('/', ensureAuthenticated, function (req, res) {
        Chat.find(function (err, chats, count) {
            res.render('index', {
                title: 'accueil',
                h1: 'Dashboard <small>Statistics Overview</small>',
                breadcrumb: 'Index',
                user: req.user,
                chats: chats
            });
        });
    });
    app.get('/poker', ensureAuthenticated, function (req, res) {
        req.session.redirect_to = '/poker';
        var content = '';
        Poker.find(function (err, tables, count) {
            if (tables.length > 1)
                content = tables.length + ' tables actives';
            else
                content = tables.length + ' table active';
            res.render('table_selection', {
                title: 'poker',
                h1: 'Poker <small>Choose your table (' + content + ')</small>',
                breadcrumb: 'Poker >> Table selection',
                user: req.user,
                tables: tables
            });
        });

    });
    app.get('/poker/table/:num', ensureAuthenticated, function (req, res) {
        req.session.redirect_to = '/poker/table/' + req.params.num;
        Poker.findOne({table: req.params.num}, function (err, poker) {
            if (poker) {
                Chat.find(function (err, chats, count) {
                    res.render('poker', {
                        title: 'poker',
                        h1: 'Poker <small>Game and chat</small>',
                        breadcrumb: 'Poker >> Table nº' + poker.table,
                        user: req.user,
                        chats: chats,
                        req: req,
                        poker: poker
                    });
                });
            } else {
                console.log('no data for this company');
            }
        });
    });
    app.get('/account', ensureAuthenticated, function (req, res) {
        res.render('account.ejs', {title: 'account', user: req.user});
    });
    app.get('/register', function (req, res) {
        res.render('register', {title: "register", user: req.user, breadcrumb: 'Register', message: req.flash('error')});
    });
    app.post('/register', function (req, res) {
        User.register(new User({username: req.body.username}), req.body.password, function (err, account) {
            if (err) {
                return res.render('register', {title: "register", user: req.user, message: err});
            }
            passport.authenticate('local', {failureRedirect: '/login', failureFlash: true})(req, res, function () {
                res.redirect('/');
            });
        });
    });
    app.get('/login', function (req, res) {
        res.render('login', {title: 'login',
            breadcrumb: 'Login',
            user: req.user,
            message: req.flash('error')});
    });
    app.post('/login', passport.authenticate('local', {failureRedirect: '/login', failureFlash: true}),
            function (req, res) {
                console.log(req.session.redirect_to);
                var redirect_to = req.session.redirect_to ? req.session.redirect_to : '/';
                delete req.session.redirect_to;
                //is authenticated ?
                res.redirect(redirect_to);
                //res.redirect('/');
            });
    app.get('/logout', function (req, res) {
        req.logout();
        res.redirect('/');
    });
    app.get('/ping', function (req, res) {
        res.send("pong!", 200);
    });
    function ensureAuthenticated(req, res, next) {
        console.log(req.session.redirect_to);
        if (req.isAuthenticated()) {
            return next();
        }
        res.redirect('/login');
    }
};


