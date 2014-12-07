var passport = require('passport'),
        User = require('./models/user'),
        Files = require('./models/files'),
        utils = require('./utils'),
        formidable = require('formidable'),
        fs = require('fs'),
        mkdirp = require('mkdirp'),
        ffmpeg = require('fluent-ffmpeg'),
        spawn = require('child_process').spawn;
        // configure upload middleware
module.exports = function (app) {
    // Setup the ready route, and emit talk event.
    app.io.route('remove_file', function (req) {
        if(req.session.user){
            User.findOne({_id: req.session.user}, function (err, user) {
                if(user){
                    for(var i=0;i<req.data.length;i++){
                        Files.findOneAndRemove({_id: req.data[i]}, function (err, fld) {
                            Files.findOne({_id: fld.parent_id}, function (err, fd) {
                                fd.child.pull(fld._id);
                                fd.save();
                            })
                            req.io.emit('file_removed', fld._id);
                            if(fld.type == "Directory"){
                                deleteFolderRecursive(__dirname+fld.path+fld.name);
                            }else{
                                fs.unlinkSync(__dirname+fld.path+fld.name);
                            }
                        });
                    }
                }
            });
        }
    });

    app.io.route('move_file', function (req) {
        if(req.session.user){
            console.log(req.data);
            for(var i=0;i<req.data.files.length;i++){
                Files.findOne({_id: req.data.files[i]}, function (err, fld) {
                    console.log('move file');
                    if(fld){
                        Files.findOne({_id: fld.parent_id}, function (err, fd) {
                            fd.child.pull(fld._id);
                            fd.save();
                        })
                        console.log('child pull');
                        Files.findOne({_id: req.data.folder}, function (err, fold) {
                            if(fold){
                                console.log(__dirname+fld.path+fld.name+' '+__dirname+fold.path+'/'+fld.name);
                                level = (typeof fold.level == 'undefined') ? 0 : fold.level; 
                                fs.renameSync(__dirname+fld.path+fld.name, __dirname+fold.path+'/'+fld.name);
                                fld.parent_id = fold._id;
                                fld.root_id = fold.root_id;
                                fld.level = level+1;
                                fld.path = fold.path;
                                fld.save();
                                fold.child.push(fld);
                                fold.save(); 
                                console.log('file moved');
                            }
                        });
                    }
                });
            }
            Files.find({user: req.session.user}, function (err, user_files) {
                req.io.emit('file_saved', user_files);
            });
        }
    });

    var connections = {}

    app.io.route('delAlwMail', function (req) {
        if(req.session.user){
            Files.findOne({_id: req.data.folder_id}, function (err, file) {
                if(file){
                    emails = file.allowedEmails;
                    emails.unset(req.data.email);
                    file.allowedEmails = emails;
                    file.save();
                }
            });
        }
    });
    app.io.route('progress_bar', function (req) {
        console.log(req.session.progress_bar);
        connections[req.session.user].emit('progress_bar', req.session.progress_bar);
        //req.io.respond({'progress_bar': req.session.progress_bar});
        //req.io.broadcast('progress_bar', req.session.progress_bar);
    });
    app.io.route('new_user', function (req) {
        console.log('new user');
        User.findOne({_id: req.data}, function (err, user) {
            if(user){
                connections[req.data] = req.io;
                req.session.user = req.data;
                req.session.folder_path = '/uploads';
                req.session.save();
                req.io.emit('user_id', req.data);
                req.io.emit('user_logged', req.data);
            }
        });
    });
    app.io.route('select_folder', function (req) {
        var folder_path = "";
        //user logged in
        if(req.session.user){
            User.findOne({_id: req.session.user}, function (err, user) {
                user.selected_folder = req.data;
                user.save();
            });
            Files
                .findOne({_id: req.data})
                .populate('child')
                .exec(function(err, file) { 
                    if(file){
                        req.io.emit('folder_path', file.path);
                        req.session.folder_path = file.path;
                        req.session.save();
                        req.io.emit('selected_folder', file); 
                        req.io.emit('folder_id', req.data);
                    } 
                });
        }
    });
    app.io.route('new_folder', function (req) {
        //user logged in
        console.log(req.data);
        if(req.session.user){
            if(req.data.parent_id){
                Files.findOne({_id: req.data.parent_id}, function (err, file) {
                    if(file){
                        mkdirp(__dirname + req.session.folder_path+'/'+req.data.folder_name, function(err) {
                            // path was created unless there was error
                            //console.log(err);
                            var files = new Files({
                                user: req.session.user,
                                parent_id: file._id,
                                level: file.level+1,
                                root_id: file.root_id,
                                name: req.data.folder_name,
                                type: 'Directory',
                                size: 0,
                                time: 0,
                                path: req.session.folder_path+'/'+req.data.folder_name+'/',
                                permissions: {access: 0, users: ""},
                            });
                            file.child.push(files);
                            file.save();
                            files.save(function (err, file) {
                                if (err)
                                    return console.error(err);
                                Files.find({user: req.session.user}, function (err, user_files) {
                                    req.io.emit('file_saved', user_files, files._id);
                                });
                            });
                        });
                    }
                });
            }else{
                mkdirp(__dirname + req.session.folder_path+'/'+req.data.folder_name, function(err) {
                    // path was created unless there was error
                    //console.log(err);
                    var files = new Files({
                        user: req.session.user,
                        level: 1,
                        name: req.data.folder_name,
                        type: 'Directory',
                        size: 0,
                        time: 0,
                        path: req.session.folder_path+'/'+req.data.folder_name+'/',
                        permissions: {access: 0, users: ""},
                    });
                    files.root_id = files._id;
                    files.save(function (err, file) {
                        if (err)
                            return console.error(err);
                        Files.find({user: req.session.user}, function (err, user_files) {
                            req.io.emit('file_saved', user_files, files._id);
                        });
                    });
                });
            }
        }
    });
    function convert(id, type, req) {
        Files.findOne({_id: id}, function (err, file) {
            if (file) {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                //var exec = require('child_process').exec;
                //var cmd = "";
                if(type == 'ogv' || type == 'webm'){
                    var ffmpeg = spawn('ffmpeg', [
                        '-i', ''+__dirname + file.path + file.name+'',
                        '-acodec', 'libvorbis', // PCM 16bits, little-endian
                        '-ab', '96k',
                        '-ar', '44100', // Sampling rate
                        '-ac', 2, // Stereo
                        '-b', '345k',
                        '-s', '640x360', '-y',
                        __dirname + file.path + noExt + '.' + type // Output on stdout
                      ]);
                }else if(type == 'mp4'){
                    var ffmpeg = spawn('ffmpeg', [
                        '-i', ''+__dirname + file.path + file.name+'',
                        '-c:v', 'libx264',
                        '-crf', '19',
                        '-preset', 'slow',
                        '-c:a', 'aac',
                        '-strict', 'experimental',
                        '-b:a', '192k',
                        '-ac', '2', '-y',
                        __dirname + file.path + noExt + '.' + type // Output on stdout
                      ]);
                }else{
                    cmd = "pwd";
                }
                var active = false;
                ffmpeg.stderr.setEncoding('utf8');

                var duration = 0, time = 0, progress = 0, transfert = 0;
                var retour = {};
                ffmpeg.stderr.on('data', function (data) {
                  if (/^execvp\(\)/.test(data)) {
                    console.log('Failed to start child process.');
                  }else{
                    //console.log(data);
                    var matches = data.match(/Duration: (.*?), start:/);
                    if(typeof matches == 'object' && matches){
                        var rawDuration = matches[1];
                        // convert rawDuration from 00:00:00.00 to seconds.
                        var ar = rawDuration.split(":").reverse();
                        duration = parseFloat(ar[0]);
                        if (ar[1]) duration += parseInt(ar[1]) * 60;
                        if (ar[2]) duration += parseInt(ar[2]) * 60 * 60;
                    }
                    var matches = data.match(/time=(.*?) bitrate/g);
                    if(typeof matches == 'object' && matches){
                        var rawTime = matches.pop();
                        // needed if there is more than one match
                        rawTime = rawTime.replace('time=','').replace(' bitrate','');
                        // convert rawTime from 00:00:00.00 to seconds.
                        ar = rawTime.split(":").reverse();
                        time = parseFloat(ar[0]);
                        if (ar[1]) time += parseInt(ar[1]) * 60;
                        if (ar[2]) time += parseInt(ar[2]) * 60 * 60;
                        //calculate the progress
                    }
                    if(duration){
                        progress = Math.round((time/duration) * 100);
                        var retour = {file_id: id, status: 200, duration: duration,
                                    current: time, progress: progress, type : type};
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

    app.get('/', function (req, res) {
        res.render('index', {
                title: 'Accueil',
                h1: 'Dashboard <small>Statistics Overview</small>',
                breadcrumb: 'Accueil',
                user: req.user
        });
    });

    app.post('/uploads', function (req, res, next) {
        console.log('file on saving');
         /* Chunked upload sessions will have the content-range header */
        if(req.headers['content-range']) {
            /* the format of content range header is 'Content-Range: start-end/total' */
            var match = req.headers['content-range'].match(/(\d+)-(\d+)\/(\d+)/);
            if(!match || !match[1] || !match[2] || !match[3]) {
                /* malformed content-range header */
                res.send('Bad Request', 400);
                return;
            }

            var start = parseInt(match[1]);
            var end = parseInt(match[2]);
            var total = parseInt(match[3]);

            /* 
             * The filename and the file size is used for the hash since filenames are not always 
             * unique for our customers 
             */

            var hash = crypto.createHash('sha1').update(filename + total).digest('hex');
            var target_file = "app/uploads/" + hash + path.extname(filename);

            /* The individual chunks are concatenated using a stream */  
            var stream = streams[hash];
            if(!stream) {
                stream = fs.createWriteStream(target_file, {flags: 'a+'});
                streams[hash] = stream;
            }

            var size = 0;
            if(fs.existsSync(target_file)) {
                size = fs.statSync(target_file).size;
            }

            /* 
             * basic sanity checks for content range
             */
            if((end + 1) == size) {
                /* duplicate chunk */
                res.send('Created', 201);
                return;
            }

            if(start != size) {
                /* missing chunk */
                res.send('Bad Request', 400);
                return;
            }

            /* if everything looks good then read this chunk and append it to the target */
            fs.readFile(req.headers['x-file'], function(error, data) {
                if(error) {
                    res.send('Internal Server Error', 500);
                    return;
                }

                stream.write(data);
                fs.unlink(req.headers['x-file']);

                if(start + data.length >= total) {
                    /* all chunks have been received */
                    stream.on('finish', function() {
                        process_upload(target_file);
                    });
                    stream.end();
                } else {
                    /* this chunk has been processed successfully */
                    res.send("Created", 201);
                }
            });
        } else {
            var form = new formidable.IncomingForm();
            //Formidable uploads to operating systems tmp dir by default
            form.uploadDir = "./uploads";       //set upload directory
            form.keepExtensions = true;     //keep file extension
            console.log(req);
            form.parse(req, function(err, fields, files) {
                //save bdd
                var ext,saveId;
                console.log(files);
                if(typeof files == 'object' && files.length>0){
                    var file = files['files[]'];
                    Files.findOne({name: file.name}, function (err, exist) {
                        if (!exist) {
                            Files.findOne({_id: req.user.selected_folder}, function (err, fl) {
                                if(fl){
                                    var type = "";
                                    var length = file.name.length;
                                    var noExt = file.name.substring(0, length - 4);
                                    ext = file.name.substring(length - 3, length);
                                    if(ext == 'png' || ext == 'jpg' || ext == 'gif'){
                                        type = 'Image';
                                    }else if(ext == 'flv' || ext == 'avi' || ext == 'mkv'){
                                        type = 'Vidéo';
                                    }else if(ext == 'zip'){
                                        type = 'zip';
                                    }
                                    var files = new Files({
                                        user: req.user._id,
                                        parent_id: fl._id,
                                        level: fl.level+1,
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
                                        fs.rename(__dirname+'/'+file.path, __dirname+fl.path+file.name, function(err) {
                                            if (err)
                                                throw err;
                                            console.log('renamed complete');
                                            Files.find({user: req.session.user}, function (err, user_files) {
                                                console.log(req.user.selected_folder);
                                                connections[req.session.user].emit('file_saved', {user_files: user_files, folder_id:req.user.selected_folder});
                                            });
                                            if(type == 'Vidéo'){
                                                convert(files._id, 'ogv', req);
                                                convert(files._id, 'webm', req);
                                                convert(files._id, 'mp4', req);
                                            }
                                        });
                                    });  
                                }
                            });
                            console.log('File saved.');
                        } else {
                            console.log('File exist');
                        }
                    });
                }
            });
        }
    }
    /*
       
        res.end();*/
    });
    app.get('/u/:username', function (req, res) {
        User.findOne({username: req.params.username}, function (err, user) {
            if(user){
                Files
                .find({user: user._id})
                .populate('child')
                .exec(function(err, files) { 
                    res.render('show', {
                        title: 'Images et photos de '+req.params.username,
                        h1: 'Images et photos de <small>'+req.params.username+'</small>',
                        user: req.user,
                        files: files
                    });  
                });
            }
        });
    });
    app.get("/get_file/:id/:type", function(req, res) {
        Files.findOne({_id: req.params.id}, function (err, file) {
            if(file){
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                res.download(__dirname+file.path+noExt+'.'+req.params.type);
            }
        });    
    });
    var sort_by = function(field, reverse, primer){
       var key = primer ? 
           function(x) {return primer(x[field])} : 
           function(x) {return x[field]};

       reverse = [-1, 1][+!!reverse];

       return function (a, b) {
           return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
         } 
    }
    app.get('/files', ensureAuthenticated, function (req, res) {
        var folder_path = "";
         Files
            .find({user: req.user._id})
            .populate('child')
            .exec(function(err, files) { 
                res.render('files', {
                    title: 'Tous vos fichiers',
                    user: req.user,
                    files: files.sort(sort_by('level', true, parseInt))
                });  
            });
    });

    app.post('/saveConfig', ensureAuthenticated, function (req, res, next) {
        var folder_id = req.body.folder_id;
        var access = req.body.access;
        var email = req.body.prevEmail;
        var emails = new Array();
        Files.findOne({_id: folder_id}, function (err, file) {
            if(file.allowedEmails){
                emails = file.allowedEmails;
                emails.push(email); 
            }else{
                emails.push(email); 
            }
            file.access = access;
            file.allowedEmails = emails;
            file.save();
            //console.log(file);
        });

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
        //console.log(req.session.redirect_to);
        if (req.isAuthenticated()) {
            return next();
        }
        res.redirect('/login');
    }
};


