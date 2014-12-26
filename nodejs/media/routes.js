var passport = require('passport'),
        User = require('./models/user'),
        Files = require('./models/files'),
        utils = require('./utils'),
        formidable = require('formidable'),
        fs = require('fs'),
        path = require('path'),
        mkdirp = require('mkdirp'),
        ffmpeg = require('fluent-ffmpeg'),
        spawn = require('child_process').spawn,
        templatesDir = path.join(__dirname, 'templates'),
        emailTemplates = require('email-templates'),
        nodemailer = require('nodemailer'),
        crypto = require('crypto'),
        getMac = require('getmac');
// configure upload middleware
module.exports = function (app) {
    // Setup the ready route, and emit talk event.
    app.io.route('remove_file', function (req) {
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                if (user) {
                    for (var i = 0; i < req.data.length; i++) {
                        Files.findOneAndRemove({_id: req.data[i]}, function (err, fld) {
                            Files.findOne({_id: fld.parent_id}, function (err, fd) {
                                fd.child.pull(fld._id);
                                fd.save();
                            })
                            req.io.emit('file_removed', fld._id);
                            if (fld.type == "Directory") {
                                deleteFolderRecursive(__dirname + fld.path + fld.name);
                            } else {
                                fs.unlinkSync(__dirname + fld.path + fld.name);
                            }
                        });
                    }
                }
            });
        }
    });

    app.io.route('move_file', function (req) {
        if (req.session.user) {
            console.log(req.data);
            for (var i = 0; i < req.data.files.length; i++) {
                Files.findOne({_id: req.data.files[i]}, function (err, fld) {
                    console.log('move file');
                    if (fld) {
                        Files.findOne({_id: fld.parent_id}, function (err, fd) {
                            fd.child.pull(fld._id);
                            fd.save();
                        })
                        console.log('child pull');
                        Files.findOne({_id: req.data.folder}, function (err, fold) {
                            if (fold) {
                                console.log(__dirname + fld.path + fld.name + ' ' + __dirname + fold.path + '/' + fld.name);
                                level = (typeof fold.level == 'undefined') ? 0 : fold.level;
                                fs.renameSync(__dirname + fld.path + fld.name, __dirname + fold.path + '/' + fld.name);
                                fld.parent_id = fold._id;
                                fld.root_id = fold.root_id;
                                fld.level = level + 1;
                                fld.path = fold.path;
                                fld.save();
                                fold.child.push(fld);
                                fold.save();
                                Files.find({user: req.session.user._id}, function (err, user_files) {
                                    Files.find({sharedUser: req.session.user._id}, function (err, shared_files) {
                                        req.io.emit('file_saved', {shared_files: shared_files, user_files: user_files, folder_id: fld._id});
                                    });
                                });
                                console.log('file moved');
                            }
                        });
                    }
                });
            }
        }
    });

    var connections = {}

    app.io.route('delAlwMail', function (req) {
        if (req.session.user) {
            /*Files.update({_id: req.data.folder_id}, {$unset: {allowedEmails: req.data.email}}, function (err, file) {
             });*/
            var allowedUsers = [];
            Files.findOne({_id: req.data.folder_id})
                    .populate('allowedUsers')
                    .exec(function (err, file) {
                        if (file) {
                            console.log(file.allowedUsers);
                            for (var i = 0; i < file.allowedUsers.length; i++) {
                                if (file.allowedUsers[i].email != req.data.email) {
                                    console.log(file.allowedUsers[i].email + " " + req.data.email);
                                    allowedUsers.push(file.allowedUsers[i]);
                                }
                            }
                            console.log(allowedUsers);
                            file.allowedUsers = allowedUsers;
                            file.save();
                        }
                    });
        }
    });
    app.io.route('progress_bar', function (req) {
        console.log(req.session.progress_bar);
        connections[req.session.user._id].emit('progress_bar', req.session.progress_bar);
        //req.io.respond({'progress_bar': req.session.progress_bar});
        //req.io.broadcast('progress_bar', req.session.progress_bar);
    });
    app.io.route('send_invitation', function (req) {
        var email = req.data;
        User.findOne({email: email}, function (err, user) {
            // Prepare nodemailer transport object
            var transport = nodemailer.createTransport({
                service: "Gmail",
                auth: {
                    user: "adthev@gmail.com",
                    pass: "@nicktalope78@"
                }
            });
            if (!user) {
                var current_date = (new Date()).valueOf().toString();
                var random = Math.random().toString();
                var hash = crypto.createHash('sha1').update(current_date + random).digest('hex');
                console.log(hash);
                var invitedUser = new User();
                invitedUser.email = email;
                invitedUser.hash = hash;
                invitedUser.save();
                emailTemplates(templatesDir, function (err, template) {
                    // Render a single email with one template
                    var locals = {
                        link: 'https://files.dev-monkey.org/register/' + invitedUser.email + '/' + invitedUser.hash,
                        linkPartage: 'https://files.dev-monkey.org/u/' + req.session.user.username
                    };
                    console.log(locals);
                    template('invitation-email', locals, function (err, html, text) {
                        if (err) {
                            console.log(err);
                        } else {
                            transport.sendMail({
                                from: req.session.user.username + ' sur files.dev-monkey.org <bot-no-reponse@dev-monkey.org>',
                                to: invitedUser.email,
                                subject: 'Invitation pour files.dev-monkey.org',
                                html: html,
                                text: text
                            }, function (err, info) {
                                if (err) {
                                    console.log(err);
                                } else {
                                    req.io.emit('user_invited', email);
                                    console.log(info.response);
                                }
                            });
                        }
                    });
                });
            } else {
                emailTemplates(templatesDir, function (err, template) {
                    // Render a single email with one template
                    var locals = {
                        linkPartage: 'https://files.dev-monkey.org/u/' + req.session.user.username
                    };
                    template('share-email', locals, function (err, html, text) {
                        if (err) {
                            console.log(err);
                        } else {
                            transport.sendMail({
                                from: req.session.user.username + ' sur files.dev-monkey.org <bot-no-reponse@dev-monkey.org>',
                                to: user.email,
                                subject: 'Partage sur files.dev-monkey.org',
                                html: html,
                                text: text
                            }, function (err, info) {
                                if (err) {
                                    console.log(err);
                                } else {
                                    req.io.emit('user_invited', email);
                                    console.log(info.response);
                                }
                            });
                        }
                    });
                });
            }
        });
    });
    app.io.route('new_user', function (req) {
        console.log('new user');
        User.findOne({_id: req.data}, function (err, user) {
            if (user) {
                connections[req.data] = req.io;
                req.session.user = user;
                req.session.folder_path = '/uploads';
                req.session.save();
                req.io.emit('user_id', req.data);
                req.io.emit('user_logged', req.data);
            }
        });
    });
    app.io.route('select_folder', function (req) {
        var folder_path = "";
        var shared = true;
        //user logged in
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                user.selected_folder = req.data;
                user.save();
            });
            Files.findOne({user: req.session.user._id, _id: req.data}, function (err, exist) {
                if (exist) {
                    shared = false;
                }
            });
            Files.findOne({_id: req.data})
                    .populate('child')
                    .populate('allowedUsers')
                    .exec(function (err, file) {
                        if (file) {
                            req.io.emit('folder_path', file.path);
                            req.session.folder_path = file.path;
                            req.session.save();
                            req.io.emit('selected_folder', {user: req.session.user, files: file, shared: shared});
                            req.io.emit('folder_id', req.data);
                        }
                    });
        }
    });
    app.io.route('new_folder', function (req) {
        //user logged in
        if (req.session.user) {
            Files.findOne({name: req.data.folder_name}, function (err, exist) {
                if(!exist){
                    if (req.data.parent_id) {
                        Files.findOne({_id: req.data.parent_id}, function (err, file) {
                            if (file) {
                                mkdirp(__dirname + req.session.folder_path + '/' + req.data.folder_name, function (err) {
                                    // path was created unless there was error
                                    //console.log(err);
                                    var files = new Files({
                                        user: req.session.user._id,
                                        parent_id: file._id,
                                        level: file.level + 1,
                                        root_id: file.root_id,
                                        name: req.data.folder_name,
                                        type: 'Directory',
                                        size: 0,
                                        time: 0,
                                        path: req.session.folder_path + '/' + req.data.folder_name + '/',
                                        permissions: [],
                                        allowedUsers: []
                                    });
                                    file.child.push(files);
                                    file.save();
                                    files.save(function (err, file) {
                                        if (err)
                                            return console.error(err);
                                        Files.find({user: req.session.user._id}, function (err, user_files) {
                                            Files.find({sharedUser: req.session.user._id}, function (err, shared_files) {
                                                req.io.emit('file_saved', {shared_files: shared_files, user_files: user_files, folder_id: file._id});
                                            });
                                        });
                                    });
                                });
                            }
                        });
                    } else {
                        mkdirp(__dirname + req.session.folder_path + '/' + req.data.folder_name, function (err) {
                            // path was created unless there was error
                            //console.log(err);
                            var files = new Files({
                                user: req.session.user._id,
                                level: 1,
                                name: req.data.folder_name,
                                type: 'Directory',
                                size: 0,
                                time: 0,
                                path: req.session.folder_path + '/' + req.data.folder_name + '/',
                                permissions: [],
                                allowedUsers: []
                            });
                            files.root_id = files._id;
                            files.save(function (err, file) {
                                if (err)
                                    return console.error(err);
                                Files.find({user: req.session.user._id}, function (err, user_files) {
                                    Files.find({sharedUser: req.session.user._id}, function (err, shared_files) {
                                        req.io.emit('file_saved', {shared_files: shared_files, user_files: user_files, folder_id: file._id});
                                    });
                                });
                            });
                        });
                    }
                }else{
                    req.io.emit('alert', {type: 'warning', text: 'Ce dossier existe déjà.'});
                }
            });
    });
    function convert(id, type, req) {
        Files.findOne({_id: id}, function (err, file) {
            if (file) {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                //var exec = require('child_process').exec;
                //var cmd = "";
                if (type == 'ogv' || type == 'webm') {
                    var ffmpeg = spawn('ffmpeg', [
                        '-i', '' + __dirname + file.path + file.name + '',
                        '-acodec', 'libvorbis', // PCM 16bits, little-endian
                        '-ac', 2, // Stereo
                        '-b', '345k', '-y',
                        __dirname + file.path + noExt + '.' + type // Output on stdout
                    ]);
                } else if (type == 'mp4') {
                    var ffmpeg = spawn('ffmpeg', [
                        '-i', '' + __dirname + file.path + file.name + '',
                        '-vcodec', 'libx264',
                        '-crf', '19',
                        '-preset', 'slow',
                        '-acodec', 'aac',
                        '-strict', 'experimental',
                        '-b', '345k',
                        '-ac', 2, '-y',
                        __dirname + file.path + noExt + '.' + type // Output on stdout
                    ]);
                } else {
                    cmd = "pwd";
                }
                var active = false;
                ffmpeg.stderr.setEncoding('utf8');

                var duration = 0, time = 0, progress = 0, transfert = 0;
                var retour = {};
                ffmpeg.stderr.on('data', function (data) {
                    if (/^execvp\(\)/.test(data)) {
                        console.log('Failed to start child process.');
                    } else {
                        //console.log(data);
                        var matches = data.match(/Duration: (.*?), start:/);
                        if (typeof matches == 'object' && matches) {
                            var rawDuration = matches[1];
                            // convert rawDuration from 00:00:00.00 to seconds.
                            var ar = rawDuration.split(":").reverse();
                            duration = parseFloat(ar[0]);
                            if (ar[1])
                                duration += parseInt(ar[1]) * 60;
                            if (ar[2])
                                duration += parseInt(ar[2]) * 60 * 60;
                        }
                        var matches = data.match(/time=(.*?) bitrate/g);
                        if (typeof matches == 'object' && matches) {
                            var rawTime = matches.pop();
                            // needed if there is more than one match
                            rawTime = rawTime.replace('time=', '').replace(' bitrate', '');
                            // convert rawTime from 00:00:00.00 to seconds.
                            ar = rawTime.split(":").reverse();
                            time = parseFloat(ar[0]);
                            if (ar[1])
                                time += parseInt(ar[1]) * 60;
                            if (ar[2])
                                time += parseInt(ar[2]) * 60 * 60;
                            //calculate the progress
                        }
                        if (duration) {
                            progress = Math.round((time / duration) * 100);
                            var retour = {file_id: id, status: 200, duration: duration,
                                current: time, progress: progress, type: type};
                            req.session.progress_bar = retour;
                            req.session.save();
                            req.io.route('progress_bar');
                        }
                    }
                });
                ffmpeg.stdout.on('data', function (data) {
                    console.log('stdout: ' + data);
                });
                ffmpeg.on('error', function (code) {
                    console.log('Error exited with code ' + code);
                });
                ffmpeg.on('exit', function (code) {
                    console.log('child process exited with code ' + code);
                });

                file.convert.push(type);
                file.save();
            }

        });
    }

    app.get('/', ensureAuthenticated, function (req, res) {
        res.render('index', {
            title: 'Accueil',
            h1: 'Tableau de bord <small>Statistiques</small>',
            user: req.user,
            message: {type: 'warning', text: req.flash('error')}
        });
    });

    app.post('/uploads', ensureAuthenticated, function (req, res, next) {
        console.log('file on saving');

        var form = new formidable.IncomingForm();
        //Formidable uploads to operating systems tmp dir by default
        form.uploadDir = "./uploads";       //set upload directory
        form.keepExtensions = true;     //keep file extension

        form.parse(req, function (err, fields, files) {
            //save bdd
            var ext, saveId;
            console.log('parsed');
            if (typeof files == 'object') {
                var file = files['files[]'];
                if (typeof file == 'object') {
                    Files.findOne({name: file.name}, function (err, exist) {
                        if (!exist) {
                            Files.findOne({_id: req.user.selected_folder}, function (err, fl) {
                                if (fl) {
                                    var type = "";
                                    var length = file.name.length;
                                    var noExt = file.name.substring(0, length - 4);
                                    ext = file.name.substring(length - 3, length);
                                    if (ext == 'png' || ext == 'jpg' || ext == 'gif') {
                                        type = 'Image';
                                    } else if (ext == 'flv' || ext == 'avi' || ext == 'mkv') {
                                        type = 'Vidéo';
                                    } else if (ext == 'zip') {
                                        type = 'zip';
                                    }
                                    var files = new Files({
                                        user: req.user._id,
                                        parent_id: fl._id,
                                        level: fl.level + 1,
                                        root_id: fl.root_id,
                                        name: file.name,
                                        type: type,
                                        size: file.size,
                                        time: 0,
                                        path: fl.path,
                                        allowedEmails: []
                                    });
                                    files.save();
                                    fl.child.push(files);
                                    fl.save(function (err, fil) {
                                        if (err)
                                            return console.error(err);
                                        fs.rename(__dirname + '/' + file.path, __dirname + fl.path + file.name, function (err) {
                                            if (err)
                                                throw err;
                                            Files.find({user: req.session.user._id}, function (err, user_files) {
                                                Files.find({sharedUser: req.session.user._id}, function (err, shared_files) {
                                                    connections[req.session.user._id].emit('file_saved', {shared_files: shared_files, user_files: user_files, folder_id: req.user.selected_folder});
                                                });
                                            });
                                            if (type == 'Vidéo') {
                                                convert(files._id, 'ogv', req);
                                                convert(files._id, 'webm', req);
                                                convert(files._id, 'mp4', req);
                                            }
                                            res.end();
                                        });
                                    });
                                }
                            });
                        } else {
                            req.io.emit('alert', {type: 'warning', text: 'Ce fichier existe déjà.'});
                        }
                    });
                }
            }
        });
    });
    app.get('/u/:username', ensureAuthenticated, function (req, res) {
        User.findOne({username: req.params.username}, function (err, user) {
            if (user) {
                Files.find({user: user._id})
                        .populate('child')
                        .populate('allowedUsers')
                        .exec(function (err, files) {
                            res.render('show', {
                                title: 'Images et photos de ' + req.params.username,
                                h1: 'Images et photos de <small>' + req.params.username + '</small>',
                                user: req.user,
                                files: files
                            });
                        });
            }
        });
    });
    app.get('/admin', ensureAdminAuthenticated, function (req, res) {
        var user = req.user;
        User.find({}, function (err, users) {
            if (users) {
                res.render('admin', {
                    title: 'Admin',
                    h1: 'Gestion utilisateurs',
                    user: req.user,
                    users: users,
                    message: {type: 'warning', text: req.flash('error')}
                });
            }
        });
    });
    
    app.get("/get_file/:id/:type", ensureAuthenticated, function (req, res) {
        Files.findOne({_id: req.params.id}, function (err, file) {
            if (file) {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                res.download(__dirname + file.path + noExt + '.' + req.params.type);
            }
        });
    });
    var sort_by = function (field, reverse, primer) {
        var key = primer ?
                function (x) {
                    return primer(x[field])
                } :
                function (x) {
                    return x[field]
                };

        reverse = [-1, 1][+!!reverse];

        return function (a, b) {
            return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
        }
    }
    app.get('/files', ensureAuthenticated, function (req, res) {
        var folder_path = "";
        Files.find({user: req.user._id})
                .populate('child')
                .exec(function (err, files) {
                    Files.find({allowedUsers: {"$in": [req.user._id]}}, function (err, sharedFiles) {
                        console.log(sharedFiles);
                        res.render('files', {
                            title: 'Tous vos fichiers',
                            user: req.user,
                            files: files.sort(sort_by('level', true, parseInt)),
                            sharedFiles: sharedFiles,
                            message: {type: 'warning', text: req.flash('error')}
                        });
                    });
                });
    });

    app.post('/saveConfig', ensureAuthenticated, function (req, res, next) {
        var folder_id = req.body.folder_id;
        var access = req.body.access;
        var email = req.body.prevEmail;
        Files.findOne({_id: folder_id}, function (err, file) {
            User.findOne({email: email}, function (err, user) {
                if (user) {
                    var allowedUsers = new Array();
                    if (file.allowedUsers) {
                        allowedUsers = file.allowedUsers;
                        allowedUsers.push(user);
                    } else {
                        allowedUsers.push(user);
                    }
                    console.log(allowedUsers);
                    file.access = access;
                    file.allowedEmails = allowedUsers;
                    file.save();
                }
            });
        });
        res.json({success: "success"});
    });

    app.get('/filesContainerType/:type', ensureAuthenticated, function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            if (req.params.type == 'icones' || req.params.type == 'list')
                res.render('files_' + req.params.type, {
                    user: req.user,
                    files: files
                });
        });
    });

    app.get('/video', ensureAuthenticated, function (req, res) {
        Files.find({user: req.user._id}, function (err, files) {
            res.render('video', {
                title: 'Toutes vos videos',
                h1: 'Vos videos',
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

    app.get('/profile', ensureAuthenticated, function (req, res) {
        res.render('profile.ejs', {title: 'Profile', user: req.user, message: ""});
    });
    app.get('/register/:email/:hash', function (req, res) {
        User.findOne({hash: req.params.hash}, function (err, user) {
            if (user) {
                user.email = req.params.email;
                res.render('register', {title: "register", user: user,
                    breadcrumb: 'Register', message: {type: 'warning', text: req.flash('error')}
                });
            } else {
                res.redirect('/');
            }
        });
    });
    app.post('/edit-profile', ensureAuthenticated, function (req, res) {
        var username = req.body.username;
        var message = "";
        User.findOne({_id: req.user._id}, function (err, user) {
            if (user) {
                if (username) {
                    User.findOne({username: username}, function (err, exist) {
                        if (!exist)
                            user.username = username;
                        else
                            message = "username already exist";
                        user.save();
                    })
                }
            }
        });
        res.render('profile.ejs', {
            title: 'profile',
            user: req.user,
            message: {type: 'warning', text: message}
        });
    });
    app.post('/register', function (req, res) {
        User.findOne({hash: req.body.hash}, function (err, user) {
            user.username = req.body.username;
            getMac.getMac(function (err, macAddress) {
                if (err)
                    throw err;
                if (user.addressMac.length < 3)
                    user.addressMac.push(macAddress);
            });
            User.register(user, req.body.password, function (err, account) {
                if (err) {
                    return res.render('register', {title: "register", user: user, message: {type: 'warning', text: err}});
                }
                mkdirp(__dirname + req.body.username, function (err) {
                    // path was created unless there was error
                    //console.log(err);
                    var files = new Files({
                        user: user._id,
                        level: 1,
                        name: req.body.username,
                        type: 'Directory',
                        size: 0,
                        time: 0,
                        path: req.body.username + '/',
                        permissions: {access: 0, users: ""},
                    });
                    files.root_id = files._id;
                    files.save(function (err, file) {
                        if (err)
                            return console.error(err);
                        Files.find({user: account._id}, function (err, user_files) {
                            passport.authenticate('local', {failureRedirect: '/login', failureFlash: true})(req, res, function () {
                                res.redirect('/files');
                            });
                        });
                    });
                });
            });
        });
    });
    app.get('/login', function (req, res) {
        res.render('login', {title: 'login',
            breadcrumb: 'Login',
            user: req.user,
            message: {type: 'warning', text: req.flash('error')}});
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
                res.redirect('/files');
            });


    app.get('/logout', function (req, res) {
        req.logout();
        res.redirect('/');
    });
    app.get('/ping', function (req, res) {
        res.send("pong!", 200);
    });
    function ensureAuthenticated(req, res, next) {
        if (req.isAuthenticated()){
            return next();
        }
        res.redirect('/login');
    }
    function ensureAdminAuthenticated(req, res, next) {
        if (req.isAuthenticated() && req.user.role == 'admin'){
            return next();
        }
        res.redirect('/login');
    }
    function ensureSecondFactor(req, res, next) {
        if (req.session.secondFactor == 'totp') {
            return next();
        }
        res.redirect('/login-otp')
    }

};


