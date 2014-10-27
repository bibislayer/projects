var passport = require('passport'),
        User = require('./models/user'),
        Files = require('./models/files'),
        utils = require('./utils');

module.exports = function (app) {
    app.get('/', function (req, res) {
        res.render('index', {
                title: 'Accueil',
                h1: 'Dashboard <small>Statistics Overview</small>',
                breadcrumb: 'Accueil',
                user: req.user
        });
    });
    app.get('/u/:username', function (req, res) {
        User.findOne({username: req.params.username}, function (err, user) {
            if(user){
                Files.find({user: user._id}, function (err, files) {
                    res.render('show', {
                            title: 'Images et photos de '+req.params.username,
                            h1: 'Images et photos de <small>'+req.params.username+'</small>',
                            files: files,
                            user: req.user
                    });
                });
            }
        });
    });
    app.get("/get_img/:id", function(req, res) {
        Files.findOne({_id: req.params.id}, function (err, file) {
            if(file){
                res.download(__dirname+file.path+file.name);
            }
        });    
    });
    app.get('/files', ensureAuthenticated, function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            res.render('files', {
                title: 'Tous vos fichiers',
                user: req.user,
                files: files
            });
        });
    });

    app.post('/saveConfig', ensureAuthenticated, function (req, res, next) {
        var arrFiles = req.body.id_files;
        var access = req.body.access;
        var emails = req.body.emails;
        for(var i=0;i<arrFiles.length;i++){
            Files.findOne({_id: arrFiles[i]}, function (err, file) {
                file.permissions = {access: access, users: emails}
                file.save();
            });
        }
        res.json({ success: "success" });
    });

    app.get('/filesContainerType/:type', ensureAuthenticated, function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            if(req.params.type == 'icones' || req.params.type == 'list')
            res.render('files_'+req.params.type, {
                user: req.user,
                files: files
            });
        });
    });
    
    app.get('/video', ensureAuthenticated,function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            res.render('video', {
                title: 'Toutes vos videos',
                h1: 'Vos videos',
                breadcrumb: '<a href="/">Accueil</a> > Video',
                user: req.user,
                files: files
            });
        });
    });

    app.get('/convert', ensureAuthenticated, function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            console.log(files);
            res.render('converter', {
                title: 'converter',
                h1: 'Convertisser tous vos fichier <small>Beta</small>',
                breadcrumb: 'Convertisseur',
                user: req.user,
                files: files
            });
        });
    });

    app.get('/account', ensureAuthenticated, function (req, res) {
        res.render('account.ejs', {title: 'account', user: req.user, message: ""});
    });
    app.get('/register', function (req, res) {
        res.render('register', {title: "register", user: req.user, breadcrumb: 'Register', message: req.flash('error')});
    });
    app.post('/edit-profile', ensureAuthenticated, function (req, res) {
        var email = req.body.email;
        var username = req.body.username;
        var message = "";
        User.findOne({_id: req.user._id}, function (err, user) {
            if(user){
                if(email)
                    user.email = email;
                if(username){
                    User.findOne({username: username}, function (err, exist) {
                        if(!exist)
                            user.username = username;
                        else
                            message = "username already exist";
                        user.save();
                    })   
                }
            }
        });
        res.render('account.ejs', {
            title: 'account', 
            user: req.user,
            message: message
        });
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
            function (req, res, next) {
                // issue a remember me cookie if the option was checked
                if (!req.body.remember_me) {
                    return next();
                }

                var token = utils.randomString(64);
                Token.save(token, {userId: req.user.id}, function (err) {
                    if (err) {
                        return done(err);
                    }
                    res.cookie('remember_me', token, {path: '/', httpOnly: true, maxAge: 604800000}); // 7 days
                    return next();
                });
            },
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
        console.log(req.session.redirect_to);
        if (req.isAuthenticated()) {
            return next();
        }
        res.redirect('/login');
    }
};


