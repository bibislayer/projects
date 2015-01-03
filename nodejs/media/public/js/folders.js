$('#folder-selection').delegate("li", 'click', function (event) {

    $('#folder-selection li').removeClass('active');
    var lvl = $(this).attr('class');
    var length = lvl.length;
    var level = parseInt(lvl.substring(length - 1, length)) + 1;
    var data_id = $(this).attr('data-id');
    $('li[data-id=' + data_id + ']').addClass('active');
    if ($('li[data-id=' + data_id + '] i.fa').first().hasClass('fa-caret-down')) {
        $('li[data-id=' + data_id + '] i.fa').first().addClass('fa-caret-up');
        $('li[data-id=' + data_id + '] i.fa').first().removeClass('fa-caret-down');
    } else {
        $('li[data-id=' + data_id + '] i.fa').first().addClass('fa-caret-down');
        $('li[data-id=' + data_id + '] i.fa').first().removeClass('fa-caret-up');
    }
    $('#content' + data_id).html($('li[data-parent-id="' + data_id + '"].level' + level));
    $('#content' + data_id + ' li').show();
    $('#content' + data_id).attr('aria-expanded', true);
    $('#content' + data_id).slideToggle('slow');
    socket.emit('select_folder', data_id);
    var folder_name = $(this).attr('data-name');
    $('button[data-conteneur="create_folder"]').show();
    $('button[data-conteneur="config_file"]').show();
    $("input[name=parent_id]").val(data_id);
    $("input[name=folder_id]").val(data_id);
    $('#lbl_folder_name').html('Ajouter un dossier à ' + folder_name);
    $('.add-files .fileinput-button span:first').html('Ajouter un fichier à ' + folder_name);
    $('button[data-conteneur=config_file]').attr('data-original-title', 'Configurer les droits d\'accés du dossier <strong>' + folder_name + '</strong>');
    $('button[data-conteneur=add_file]').attr('data-original-title', 'Ajouter un fichier à <strong>' + folder_name + '</strong>');
    $('button[data-conteneur=create_folder]').attr('data-original-title', 'Créer un dossier dans <strong>' + folder_name + '</strong>');
    $('button[data-conteneur=show_sharing]').attr('onclick', 'var win = window.open("/u/' + $(this).attr('data-user') + '", "_blank");win.focus();');
    $('button[data-conteneur=show_sharing]').attr('data-original-title', 'Afficher la vue de <strong>' + $(this).attr('data-user') + '</strong>');
    event.stopPropagation();
    //$('li[data-parent-id="'+parent_id+'"].level'+level).toggle();
});

function FileConvertSize(aSize) {
    aSize = Math.abs(parseInt(aSize, 10));
    var def = [[1, 'octets'], [1024, 'ko'], [1024 * 1024, 'Mo'], [1024 * 1024 * 1024, 'Go'], [1024 * 1024 * 1024 * 1024, 'To']];
    for (var i = 0; i < def.length; i++) {
        if (aSize < def[i][0])
            return (aSize / def[i - 1][0]).toFixed(2) + ' ' + def[i - 1][1];
    }
}
socket.on('progress_bar', function (datas) {
    //console.log(datas);
    if (datas.file_id) {
        if ($('tr[data-id="' + datas.file_id + '"] td.name > div.' + datas.type).length > 0) {
            $('tr[data-id="' + datas.file_id + '"] td.name div.' + datas.type + ' .progress-bar').attr('aria-valuenow', datas.progress);
            $('tr[data-id="' + datas.file_id + '"] td.name div.' + datas.type + ' .progress-bar').css(
                    'width',
                    datas.progress / 2 + '%'
                    );
            $('tr[data-id="' + datas.file_id + '"] td.name div.' + datas.type + ' .progress-bar').html(datas.type + ' ' + datas.progress / 2 + '%');
        } else {
            $('tr[data-id="' + datas.file_id + '"] td.name').append('<div class="progress ' + datas.type + '">\
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="200">\
                </div></div>');
        }
        if (datas.progress / 2 == 100) {
            $('tr[data-id="' + datas.file_id + '"] td.name div.' + datas.type).remove();
        }
    }
});
socket.on('alert', function (datas) {
    $('#socket_alert').addClass('alert-' + datas.type);
    $('#socket_alert').html(datas.text);
    $('#socket_alert').slideToggle('slow');
    setTimeout(function () {
        $('#socket_alert').slideToggle('slow');
    }, 3000);
})
socket.on('selected_folder', function (datas) {
    $('#filesManager table tbody').html('');
    $('.inputs_emails').html('');
    var params = document.URL.split('/');
    var cls = "";
    if (params[3] == 'u') {
        if (datas.files.child) {
            $('#content-file').html('');
            $.each(datas.files.child, function (k, file) {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                var ext = file.name.substring(length - 3, length);
                if (ext == 'jpg' || ext == 'png' || ext == 'gif') {
                    $('#content-file').append('<div class="col-lg-3 col-md-3 col-xs-6" style="margin-bottom:10px">\
                        <a class="thumbnail fancybox-thumbs" rel="fancybox-thumb" href="/get_file/' + file._id + '/' + ext + '" >\
                            <img class="img-responsive" src="/get_file/' + file._id + '/' + ext + '" alt="' + file.name + '">\
                        </a></div>');
                } else if (ext == 'avi') {
                    var id = "rand" + Math.floor((Math.random() * 10000) + 1);
                    $('#content-file').append('<div class="col-lg-3 col-md-3" style="margin-bottom:10px">\
                        <center><a class="thumbnail">\
                        <video data-setup=\'{"techOrder": ["html5", "flash", "other supported tech"]}\' preload="auto" id="' + id + '" class="video-js vjs-default-skin" controls width="360" height="250">\
                        <source src="/get_file/' + file._id + '/mp4" type="video/mp4" />\
                        <source src="/get_file/' + file._id + '/webm" type="video/webm" />\
                        <source src="/get_file/' + file._id + '/ogg" type="video/ogg" /></video></a></center></div>');
                    videojs(id, {}, function () {
                        vidPlayer = this;
                        vidPlayer.load();
                    });
                }
            });
        }
    } else {
        if (datas.shared == true) {
            $('button[data-conteneur="show_sharing"]').show();
            $('button[data-conteneur="create_folder"]').hide();
            $('button[data-conteneur="config_file"]').hide();
            if (cls == '') {
                $('#filesManager table tr th').first().hide();
                $('#filesManager table tr th').last().hide();
            }
            $('#filesManager table tr').each(function () {
                $(this).find('td').first().not('.owner').html('');
                $(this).find('td').last().not('.owner').html('');
            });
        } else {
            $('button[data-conteneur="show_sharing"]').hide();
            $('button[data-conteneur="create_folder"]').show();
            $('button[data-conteneur="config_file"]').show();
        }
        //affichage des donnees
        if (datas.files.child) {
            files = datas.files.child;
            generateList(datas.files.child, datas.user);
        }
        //console.log(folder);
        //affichage des permissions
        if (datas.files.allowedUsers) {
            $.each(datas.files.allowedUsers, function (k, user) {
                $('.inputs_emails').append('<div class="form-group input-group"> <input class="alwM' + $('.inputs_emails div').length + ' form-control" type="text" name="emails" value="' + user.email + '" /> <span class="input-group-btn">\
                                    <button id="del-email" data-remove="alwM' + $('.inputs_emails div').length + '" class="btn btn-default" type="button">\
                                        <span class="glyphicon glyphicon-minus"></span>\
                                    </button>\
                                    <button class="send-email btn btn-default" type="button">\
                                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>\
                                    </button>\
                                </span></div>');
            })
        }
    }
});
function generatePrevu(files, user) {
    $('#filesManager').html('<div class="row"><div class="col-lg-12">');
    $.each(files, function (k, file) {
        var length = file.name.length;
        var noExt = file.name.substring(0, length - 4);
        var ext = file.name.substring(length - 3, length);
        if (ext == 'jpg' || ext == 'png' || ext == 'gif') {
            $('#filesManager div.row div.col-lg-12').append('<div class="col-lg-3 col-md-3 col-xs-6" style="margin: 5px 0px;">\
                        <a class="thumbnail" href="/get_file/' + file._id + '/' + ext + '" >\
                            <img class="img-responsive" src="/get_file/' + file._id + '/' + ext + '" alt="' + file.name + '">\
                        </a></div>');
        } else if (ext == 'avi') {
            var id = "rand" + Math.floor((Math.random() * 10000) + 1);
            $('#filesManager div.row div.col-lg-12').append('<div class="col-lg-5 col-md-5" style="margin: 5px 0px;">\
                        <center class="thumbnail">\
                        <video data-setup=\'{"techOrder": ["html5", "flash", "other supported tech"]}\' preload="auto" id="' + id + '" class="video-js vjs-default-skin" controls width="360" height="250">\
                        <source src="/get_file/' + file._id + '/mp4" type="video/mp4" />\
                        <source src="/get_file/' + file._id + '/webm" type="video/webm" />\
                        <source src="/get_file/' + file._id + '/ogg" type="video/ogg" /></video></center></div>');
            videojs(id, {}, function () {
                vidPlayer = this;
                vidPlayer.load();
            });
        }
    });
    $('#filesManager').append('</div></div>');
}
function generateList(files, user) {
    var cls = "";
    console.log(files);
    $('#filesManager').html('<table class="table table-hover"><thead>\
        <tr><th style="text-align:center;"><input id="check_all_file_checkbox" type="checkbox"/></th>\
            <th>Prévu</th><th>Name</th><th>Type</th><th>Poid</th><th>Actions</th></tr></thead>\
            <tbody>');
    $.each(files, function (k, file) {
        if (file.type != "Directory") {
            if (file.user == user._id) {
                cls = "owner";
            }
            var length = file.name.length;
            var noExt = file.name.substring(0, length - 4);
            var ext = file.name.substring(length - 3, length);
            $('#filesManager table tbody').append('<tr data-id="' + file._id + '" class="level-' + file.level + '">\
              <td style="text-align:center;" class="' + cls + '"><input id="' + file._id + '" class="file_checkbox" type="checkbox"/></td>\
              <td style="padding-left:17px;"><span onMouseOver="showPrevu(this)" data-conteneur="prevu_file" data-type="' + file.type + '" data-name="' + file.name + '" data-id="' + file._id + '" class="prevu glyphicon glyphicon-zoom-in" aria-hidden="true"></span></td>\
              <td>' + file.name + '</td>\
              <td class="type">' + file.type + '</td>\
              <td class="size"></td>\
              <td class="' + cls + '">\
                <a href="/get_file/' + file._id + '/' + ext + '" onMouseOver="tooltip(this)" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                <span class="glyphicon glyphicon-download" aria-hidden="true"></span></a>\
                <button onMouseOver="tooltip(this)" title="Supprimer" data-id="' + file._id + '" data-remove="true" id="remove" type="button" class="btn btn-default" data-original-title="Supprimer">\
                <span class="glyphicon glyphicon-remove-circle"></span>\
                </button></td>\
            </tr>');
            $('tr[data-id="' + file._id + '"] td.size').html(FileConvertSize(file.size));

        }
    });
    $('#filesManager').append('</tbody></table>');
}

function generateMenu(files, sharedFiles) {
    $('#folderSelect').html('<option value="default">Selectionner un dossier</option>');
    $('#folder-selection').html('<li class="title"><span>&nbsp;<i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;&nbsp;Mes dossiers</span></li>');
    for (var i = 0; i < files.length; i++) {
        var parent = '';
        var style = '';
        if (files[i].type == "Directory") {
            if (files[i].parent_id) {
                parent = 'data-parent-id=' + files[i].parent_id + '';
                style = 'style=display:none';
            }
            var esc = '';
            for (var j = 0; j < files[i].level; j++) {
                esc += '&nbsp;';
            }
            $('#folder-selection').append('<li ' + parent + ' ' + style + ' data-name="' + files[i].name + '" data-id="' + files[i]._id + '" class="level' + files[i].level + '">\
                    <a href="javascript:;" data-toggle="collapse" data-target="content' + files[i]._id + '"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;' + files[i].name + ' <i class="fa fa-fw fa-caret-down"></i></a>\
                    <ul id="content' + files[i]._id + '" class="collapse">\
                    </ul>\
                </li>');
            $('#folderSelect').append('<option value="' + files[i]._id + '">' + files[i].name + '</option>');
        }
    }
    $('#folder-selection').append('<li class="divider"><span></span></li>');
    $('#folder-selection').append('<li class="title"><span>&nbsp;<i class="glyphicon glyphicon-share"></i>&nbsp;&nbsp;&nbsp;&nbsp;Mes partages</span></li>');
    for (var i = 0; i < sharedFiles.length; i++) {
        if (sharedFiles[i].user) {
            $('#folder-selection').append('<li data-user="' + sharedFiles[i].user.username + '" data-name="' + sharedFiles[i].name + '" data-id="' + sharedFiles[i]._id + '" class="shared level' + sharedFiles[i].level + '">\
                    <a href="javascript:;" data-toggle="collapse" data-target="content' + sharedFiles[i]._id + '"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;' + sharedFiles[i].name + ' <i class="fa fa-fw fa-caret-down"></i></a>\
                    <ul id="content' + sharedFiles[i]._id + '" class="collapse">\
                    </ul>\
                </li>');
        }
    }
}

function onChangeFolder(val) {
    console.log(val);
    folder = val;
}
function changePath() {
    var files = Array();
    $(".file_checkbox:checked").each(function () {
        files.push($(this).attr('id'));
    });
    console.log('files ' + files);
    socket.emit('move_file', {files: files, folder: folder});
}

function tooltip(that) {
    /*$(that).tooltip({
     placement: 'top',
     html : true
     });*/
}

function showPrevu(that) {
    var type = $(that).attr("data-type");
    var length = $(that).attr("data-name").length;
    var noExt = $(that).attr("data-name").substring(0, length - 4);
    var ext = $(that).attr("data-name").substring(length - 3, length);
    $('#' + $(that).attr('data-conteneur') + ' .popover-title').html('<span class="text-info"><strong>' + $(that).attr("data-name") + '</strong></span>&nbsp;<button type="button" id="close" class="close" onclick="$(\'#' + $(that).attr('data-conteneur') + ' .popover\').hide();">&times;</button>');
    if (type == 'Image' || type == 'image') {
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center><img height="125px" src="/get_file/' + $(that).attr('data-id') + '/' + ext + '" alt="' + noExt + '" title="' + noExt + '" /></center>');
    } else if (type == 'Vidéo') {
        var id = "rand" + Math.floor((Math.random() * 10000) + 1);
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center><video data-setup=\'{"techOrder": ["html5", "flash", "other supported tech"]}\' preload="auto" id="' + id + '" class="video-js vjs-default-skin" controls\
               width="360" height="240">\
               <source src="/get_file/' + $(that).attr('data-id') + '/mp4" type="video/mp4" />\
               <source src="/get_file/' + $(that).attr('data-id') + '/ogv" type="video/ogv" />\
               <source src="/get_file/' + $(that).attr('data-id') + '/webm" type="video/webm" />\
              </video></center>');
        videojs(id, {}, function () {
            vidPlayer = this;
            vidPlayer.load();
        });
        $('#' + $(that).attr('data-conteneur') + ' .popover').draggable();
    } else {
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center>Format not found</center>');
    }
    var offset = $(that).offset();
    //show the menu directly over the placeholder
    $('#' + $(that).attr('data-conteneur') + ' .popover').css({
        top: offset.top - document.body.clientHeight / 5 + "px",
        left: (offset.left + document.body.clientWidth / 4) + "px"
    }).show();
    $(that).trigger('click');
}