var passport = require('passport'),
        User = require('./models/user'),
        Files = require('./models/files'),
        Bug = require('./models/bug'),
        Sondage = require('./models/sondage'),
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
        dns = require("dns");
// configure upload middleware
module.exports = function (app) {
    // Setup the ready route, and emit talk event.
    app.io.route('remove_file', function (req) {
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                if (user) {
                    for (var i = 0; i < req.data.length; i++) {
                        Files.findOneAndRemove({_id: req.data[i]}, function (err, fld) {
                            if(fld){
                                Files.findOne({_id: fld.parent_id}, function (err, fd) {
                                    console.log(fld._id);
                                    fd.child.pull(fld._id);
                                    fd.save(function (err, file){
                                        if(err)
                                            console.log(err);
                                    });
                                });
                                req.io.emit('file_removed', fld._id);
                                if (fld.type == "Directory")
                                    deleteFolderRecursive(__dirname + fld.path + fld.name);
                                else
                                    fs.unlinkSync(__dirname + fld.path + fld.name);
                            }
                        });
                    }
                }
            });
        }
    });

    app.io.route('remove_folder', function (req) {
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                if (user) {
                    Files.findOne({_id: req.session.user.selected_folder}, function (err, fld) {
                        if(fld){
                            if(fld.parent_id){
                                Files.findOne({_id: fld.parent_id}, function (err, parent) {
                                    parent.child.pull(fld._id);
                                    parent.save();
                                });
                                fld.remove();
                            }else{
                                req.io.emit('alert', {type: 'warning', text: "Vous ne pouvez pas supprimer ce dossier."});
                                return 0;
                            }
                            Files.find({user: req.session.user._id})
                            .populate('child')
                            .exec(function (err, files) {
                                Files.find({allowedUsers: {"$in": [req.session.user._id]}})
                                .populate('user')
                                .exec(function (err, sharedFiles) {
                                    req.io.emit('alert', {type: 'success', text: "Dossier supprimé."});
                                    req.io.emit('folder_saved', {
                                        shared_files: sharedFiles.sort(sort_by('level', true, parseInt)),
                                        user_files: files.sort(sort_by('level', true, parseInt)),
                                        folder_id: fld.parent_id
                                    });
                                });
                            });
                            if (fld.type == "Directory")
                                deleteFolderRecursive(__dirname + fld.path);
                            else
                                fs.unlinkSync(__dirname + fld.path);   
                        }
                    });
                }
            });
        }
    });

    app.io.route('check_domaine', function (req) {
        if (req.session.user) {
            var ext = [".org", ".net", ".com", ".fr", ".media"];
            for (var i = 0; i < ext.length; i++) {
                checkAvailable(req.data + ext[i], req);
            }
        }
    });
    app.io.route('save_domaine', function (req) {
        if (req.session.user) {
            var sondage = new Sondage({
                user: req.user,
                text: req.data,
                type: 'Nom du site'
            });
            sondage.save(function (err, data) {
                req.io.emit('domaine_saved', data);
            });
        }
    });

    var deleteFolderRecursive = function (path) {
        if (fs.existsSync(path)) {
            fs.readdirSync(path).forEach(function (file, index) {
                var curPath = path + "/" + file;
                if (fs.lstatSync(curPath).isDirectory()) { // recurse
                    deleteFolderRecursive(curPath);
                } else { // delete file
                    fs.unlinkSync(curPath);
                }
            });
            fs.rmdirSync(path);
        }
    };

    function checkAvailable(name, req) {
        dns.resolve4(name, function (err, addresses) {
            if (err) {
                req.io.emit('domaine_checked', name);
            }
        });
    }
    function makeid(){
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( var i=0; i < 8; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    app.io.route('report_bug', function (req) {
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                if (user) {
                    var bug = new Bug({
                        category: req.data.category,
                        comment: req.data.comment,
                        status: 2,
                        user: user._id
                    });
                    bug.save(function (err, rep) {
                        if (err) {
                            req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                        } else {
                            req.io.emit('alert', {type: 'success', text: 'Bug reporté'});
                        }
                    });
                }
            });
        }
    });

    app.io.route('move_file', function (req) {
        if (req.session.user) {
            for (var i = 0; i < req.data.files.length; i++) {
                Files.findOne({_id: req.data.files[i]}, function (err, fld) {
                    console.log('move file');
                    if (fld) {
                        Files.findOne({_id: fld.parent_id}, function (err, fd) {
                            if(fd){
                                console.log(fd.name+" moved");
                                fd.child.pull(fld._id);
                                fd.save(function(err, fd){
                                    if(err)
                                        console.log(err);
                                });
                            }
                        });
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
                                    Files.find({allowedUser: {"$in": [req.session.user._id]}}, function (err, shared_files) {
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
                                    allowedUsers.push(file.allowedUsers[i]);
                                }
                            }
                            file.allowedUsers = allowedUsers;
                            file.save();
                            req.io.emit('alert', {type: 'success', text: 'Permission retirée'});
                        } else {
                            req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite"});
                        }
                    });
        }
    });
    app.io.route('progress_bar', function (req) {
        connections[req.session.user._id].emit('progress_bar', req.session.progress_bar);
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

            emailTemplates(templatesDir, function (err, template) {
                // Render a single email with one template
                var locals = {
                    linkPartage: 'https://files.dev-monkey.org/u/' + req.session.user.username
                };
                template('share-email', locals, function (err, html, text) {
                    if (err) {
                        req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                    } else {
                        transport.sendMail({
                            from: req.session.user.username + ' sur files.dev-monkey.org <bot-no-reponse@dev-monkey.org>',
                            to: email,
                            subject: 'Partage sur files.dev-monkey.org',
                            html: html,
                            text: text
                        }, function (err, info) {
                            if (err) {
                                req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                            } else {
                                req.io.emit('user_invited', email);
                                req.io.emit('alert', {type: 'success', text: "Permission ajouté pour " + email});
                            }
                        });
                    }
                });
            });
        });
    });
    app.io.route('new_user', function (req) {
        User.findOne({_id: req.data}, function (err, user) {
            if (user) {
                connections[req.data] = req.io;
                req.session.user = user;
                req.session.folder_path = '/uploads/'+user.username;
                req.session.save();
                req.io.emit('user_id', req.data);
                req.io.emit('user_logged', user.selected_folder);
            }
        });
    });
    app.io.route('change_status', function (req) {
        if (req.session.user) {
            var status = 2;
            if (req.data.status == "success") {
                status = 0;
            }
            if (req.data.status == "warning") {
                status = 1;
            }
            if (req.data.status == "danger") {
                status = 2;
            }
            Bug.findOne({_id: req.data.id}, function (err, bug) {
                if (err) {
                    req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                } else {
                    var oldStatus = bug.status;
                    bug.status = status;
                    bug.save(function (err, bugS) {
                        req.io.broadcast('status_changed', {id: bugS._id, status: bugS.status, oldStatus: oldStatus});
                    });
                }
            });
        }
    });
    app.io.route('file_change_status', function (req) {
        if (req.session.user) {
            console.log(req.session.user);
            Files.findOne({_id: req.session.user.selected_folder}, function (err, file) {
                if (err) {
                    req.io.emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                } else {
                    if(file){
                        password = makeid();
                        file.password = password;
                        file.access = req.data;
                        file.save(function (err, new_file) {
                            req.io.emit('file_status_changed', {password: password, name: file.name});
                            req.io.emit('alert', {type: 'success', text: "Status changé"});
                        });
                    }
                }
            });
        }
    });

    app.io.route('select_folder', function (req) {
        var folder_path = "";
        var shared = true;
        //user logged in
        if (req.session.user) {
            User.findOne({_id: req.session.user._id}, function (err, user) {
                if(user){
                    user.selected_folder = req.data;
                    user.save();
                }
            });
            Files.findOne({user: req.session.user._id, _id: req.data}, function (err, exist) {
                if (exist) {
                    shared = false;
                }
            });
            Files.findOne({_id: req.data}, null, {sort: {name: -1}})
                    .populate('child')
                    .populate('allowedUsers')
                    .exec(function (err, file) {
                        if (file) {
                            req.io.emit('folder_path', file.path);
                            req.session.folder_path = file.path;
                            req.session.user.selected_folder = req.data;
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
                if (!exist) {
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
                                        path: req.session.folder_path + req.data.folder_name + '/',
                                        permissions: [],
                                        allowedUsers: file.allowedUsers
                                    });
                                    file.child.push(files);
                                    file.save();
                                    files.save(function (err, file) {
                                        if (err)
                                            return console.error(err);
                                        Files.find({user: req.session.user._id})
                                        .populate('child')
                                        .exec(function (err, files) {
                                            Files.find({allowedUsers: {"$in": [req.session.user._id]}})
                                            .populate('user')
                                            .exec(function (err, sharedFiles) {
                                                req.io.emit('alert', {type: 'success', text: "Dossier créé."});
                                                req.io.emit('folder_saved', {
                                                    shared_files: sharedFiles.sort(sort_by('level', true, parseInt)),
                                                    user_files: files.sort(sort_by('level', true, parseInt)), 
                                                    folder_id: file._id
                                                });
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
                } else {
                    req.io.emit('alert', {type: 'warning', text: 'Ce dossier existe déjà.'});
                }
            });
        }
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
        Bug.find({user: req.user._id}, function (err, bugs) {
            Sondage.find({}, function (err, sondages) {
                //console.log(bugs);
                res.render('index', {
                    title: 'Accueil',
                    template: 'noMenu',
                    h1: 'Tableau de bord',
                    user: req.user,
                    bugs: bugs,
                    sondages: sondages,
                    message: {type: 'warning', text: req.flash('error')}
                });
            });
        });
    });

    app.post('/uploads', ensureAuthenticated, function (req, res, next) {
        //console.log('file on saving');

        var form = new formidable.IncomingForm();
        //Formidable uploads to operating systems tmp dir by default
        form.uploadDir = "./uploads";       //set upload directory
        form.keepExtensions = true;     //keep file extension

        form.parse(req, function (err, fields, files) {
            //save bdd
            var ext, saveId;
            //console.log('parsed');
            if (typeof files == 'object') {
                var file = files['files[]'];
                if (typeof file == 'object') {
                    Files.findOne({user: req.user, name: file.name}, function (err, exist) {
                        if (!exist) {
                            Files.findOne({_id: req.user.selected_folder}, function (err, fl) {
                                if (fl) {
                                    var type = "";
                                    var length = file.name.length;
                                    var noExt = file.name.substring(0, length - 4);
                                    ext = file.name.substring(length - 3, length);
                                    if (ext == 'png' || ext == 'jpg' || ext == 'gif' ||
                                        ext == 'PNG' || ext == 'JPG' || ext == 'GIF') {
                                        type = 'Image';
                                    } else if (ext == 'flv' || ext == 'avi' || ext == 'mkv' ||
                                               ext == 'FLV' || ext == 'AVI' || ext == 'MKV') {
                                        type = 'Vidéo';
                                    } else if (ext == 'zip' || ext == 'ZIP') {
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
                            connections[req.session.user._id].emit('alert', {type: 'warning', text: 'Ce fichier existe déjà.'});
                        }
                    });
                }
            }
        });
    });
    app.get('/84aa3f388254173628cfae8092a866b2aeb45dc0d50d56150badf73d3c4a330b.txt', function (req, res) {
        return '659b760e55742261369f7951f93365babf6ebd10d9faa69bada0bb2e18959239';
    });
    app.get('/u/:username', ensureAuthenticated, function (req, res) {
        User.findOne({username: req.params.username}, function (err, user) {
            if (user) {
                Files.find({user: user._id})
                        .populate('child')
                        .populate('allowedUsers')
                        .populate('user')
                        .exec(function (err, files) {
                            res.render('show', {
                                title: 'Images et photos de ' + req.params.username,
                                page: 'show',
                                user: req.user,
                                username: req.params.username,
                                files: files.sort(sort_by('level', true, parseInt))
                            });
                        });
            }
        });
    });
    app.get('/u/:username/:folder', ensureFakeAuthenticated, function (req, res) {
        User.findOne({username: req.params.username}, function (err, user) {
            if (user) {
                Files.find({user: user._id, name: req.params.folder})
                .populate('child')
                .exec(function (err, file) {
                    Files.find({user: user._id, parent_id: file.parent_id})
                    .exec(function (err, files) {
                        var filesMerged = file.concat(files);
                        res.render('show', {
                            title: 'Images et photos de ' + req.params.username,
                            page: 'show',
                            user: req.user,
                            files: filesMerged.sort(sort_by('level', true, parseInt)),
                            alowed: 1
                        });
                    });
                });
            }
        });
    });
    app.get('/admin', ensureAdminAuthenticated, function (req, res) {
        var user = req.user;
        User.find({}, function (err, users) {
            Bug.find({})
            .populate('user')
            .exec(function (err, bugs) {
                res.render('admin', {
                    title: 'Admin',
                    template: 'noMenu',
                    h1: 'Gestion utilisateurs',
                    user: req.user,
                    users: users,
                    bugs: bugs,
                    message: {type: 'warning', text: req.flash('error')}
                });
            });
        });
    });

    app.get("/get_file/:id/:type", ensureFileAuthenticated, function (req, res) {
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
        var folder_path = "", sharedMerged, user_shared_id=0;
        Files.find({user: req.user._id})
                .populate('child')
                .exec(function (err, files) {
                    Files.find({allowedUsers: {"$in": [req.user._id]}})
                        .populate('user')
                        .exec(function (err, sharedFiles) {
                            res.render('files', {
                                title: 'Tous vos fichiers',
                                page: 'files',
                                user: req.user,
                                files: files.sort(sort_by('level', true, parseInt)),
                                sharedFiles: sharedFiles.sort(sort_by('level', true, parseInt)),
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
            if (file) {
                User.findOne({email: email}, function (err, user) {
                    if (!user) {
                        var transport = nodemailer.createTransport({
                            service: "Gmail",
                            auth: {
                                user: "adthev@gmail.com",
                                pass: "@nicktalope78@"
                            }
                        });
                        var current_date = (new Date()).valueOf().toString();
                        var random = Math.random().toString();
                        var hash = crypto.createHash('sha1').update(current_date + random).digest('hex');
                        console.log(hash);
                        var user = new User();
                        user.email = email;
                        user.hash = hash;
                        user.save();
                        emailTemplates(templatesDir, function (err, template) {
                            // Render a single email with one template
                            var locals = {
                                link: 'https://files.dev-monkey.org/register/' + user.email + '/' + user.hash,
                                linkPartage: 'https://files.dev-monkey.org/u/' + req.session.user.username
                            };
                            template('invitation-email', locals, function (err, html, text) {
                                if (err) {
                                   connections[req.session.user._id].emit('alert', {type: 'warning', text: "Une erreur c'est produite ( " + err + " )"});
                                } else {
                                    transport.sendMail({
                                        from: req.session.user.username + ' sur files.dev-monkey.org <bot-no-reponse@dev-monkey.org>',
                                        to: user.email,
                                        subject: 'Invitation pour files.dev-monkey.org',
                                        html: html,
                                        text: text
                                    }, function (err, info) {
                                        if (err) {
                                            console.log(err);
                                        } else {
                                            connections[req.session.user._id].emit('user_invited', email);
                                            connections[req.session.user._id].emit('alert', {type: 'success', text: "Permission ajoutée à "+email+", cette utilisateur n'est pas encore membre, nous lui avons envoyé un mail."});
                                        }
                                    });
                                }
                            });
                        });
                    }else{
                        connections[req.session.user._id].emit('alert', {type: 'success', text: "Permission ajoutée à "+email});
                    }
                    var allowedUsers = new Array();
                    if (file.allowedUsers) {
                        allowedUsers = file.allowedUsers;
                        allowedUsers.push(user);
                    } else {
                        allowedUsers.push(user);
                    }
                    file.access = access;
                    file.allowedEmails = allowedUsers;
                    file.save();
                });
            }
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
        res.render('profile.ejs', {
            title: 'Profile',
            template: 'noMenu',
            user: req.user,
            message: {type: 'warning', text: req.flash('error')}
        });
    });
    app.get('/register/:email/:hash', function (req, res) {
        User.findOne({hash: req.params.hash}, function (err, user) {
            if (user) {
                user.email = req.params.email;
                res.render('register', {template: 'noMenu', title: "register", user: user,
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
            User.findOne({username: user.username}, function (err, existUser) {
                if(existUser)
                    return res.render('register', {title: "register", template: 'noMenu', user: user, message: {type: 'warning', text: 'Pseudonyme déjà utilisé'}});
            });
            User.register(user, req.body.password, function (err, account) {
                if (err) {
                    return res.render('register', {title: "register", template: 'noMenu', user: user, message: {type: 'warning', text: err}});
                }
                if (!fs.exists(__dirname + '/uploads/' + req.body.username)) {
                    mkdirp(__dirname + '/uploads/' + req.body.username, function (err) {
                        // path was created unless there was error
                        //console.log(err);
                        var files = new Files({
                            user: user._id,
                            level: 1,
                            name: req.body.username,
                            type: 'Directory',
                            size: 0,
                            time: 0,
                            path: '/uploads/' + req.body.username + '/',
                            permissions: {access: 0, users: ""},
                        });
                        files.root_id = files._id;
                        files.save(function (err, file) {
                            if (err)
                                return console.error(err);
                            Files.find({user: account._id}, function (err, user_files) {
                                passport.authenticate('local', {failureRedirect: '/login', failureFlash: true})(req, res, function () {
                                    if(req.session && req.session.url){
                                        res.redirect(req.session.url);
                                    }else{
                                        res.redirect('/');
                                    }
                                });
                            });
                        });
                    });
                }
            });
        });
    });
    app.get('/login', function (req, res) {
        res.render('login', {title: 'login',
            template: 'noMenu',
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
                User.findOne({_id: req.user.id}, function (err, user) {
                    user.lastLogged = new Date();
                    user.save();
                });
                if(req.session && req.session.url){
                    res.redirect(req.session.url);
                }else{
                    res.redirect('/');
                }
            });

    app.get('/u/:username/:folder/login', function (req, res) {
        res.render('fake-login', {title: 'login',
            template: 'noMenu',
            breadcrumb: 'Mot de passe',
            user: req.user,
            message: {type: 'warning', text: req.flash('error')}});
    });
    app.post('/u/:username/:folder/login', function (req, res, next) {
        // issue a remember me cookie if the option was checked
        req.session.isFakeLogged = 1;
        User.findOne({username: req.params.username}, function (err, user) {
            if (user) {
                Files.findOne({name: req.params.folder}, function (err, file) {
                    if(file){
                        if(req.body.password == file.password){
                            req.session.user = {name:'temp'};
                            req.session.access = file.access;
                        }
                    }
                    res.redirect('/u/'+req.params.username+'/'+req.params.folder);
                });
            }
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
        req.session.url = req.url;
        if (req.isAuthenticated()) {
            return next();
        }
        res.redirect('/login');
    }
    function ensureFileAuthenticated(req, res, next) { 
        req.session.url = req.url;
        if (req.isAuthenticated() || req.session.access == 2 && req.session.isFakeLogged) {
            return next();
        }
        res.redirect('/login');
    }
    function ensureFakeAuthenticated(req, res, next) {
        if(req.session.access == 2 && req.session.isFakeLogged || req.isAuthenticated() && req.session.access == 2){
            return next();
        }
        res.redirect(req.url+'/login');
    }
    function ensureAdminAuthenticated(req, res, next) {
        if (req.isAuthenticated() && req.user.role == 'admin') {
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


