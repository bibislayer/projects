$.fn.shiftClick = function () {
    var lastSelected; // Firefox error: LastSelected is undefined
    var checkBoxes = $(this);
    this.each(function () {
        $(this).click(function (ev) {
            if (ev.shiftKey) {
                var last = checkBoxes.index(lastSelected);
                var first = checkBoxes.index(this);
                var start = Math.min(first, last);
                var end = Math.max(first, last);
                var chk = lastSelected.checked;
                for (var i = start; i <= end; i++) {
                    checkBoxes[i].checked = chk;
                }
            } else {
                lastSelected = this;
            }
        })
    });
};

$(window).bind("hashchange", function(e) {
       var stats = History.getState("folder");
       $('li[data-id='+stats.data.folder+']').trigger('click');
});

$('#folder-selection').delegate("li.folder", 'click', function (event) {
	$('#filesManager').html('<p style="margin-left: 39%;top: 10px;width: 185px;">Chargement en cours</p>\
    						<img style="position: relative;left: 34%;top: -50px;" width="175" src="/bundles/facgeneral/images/loading.gif" alt="loading" title="loading" />\
    					');
    var view = '';
    var url = document.location.href.split('/');
    if(url[3] == 'app_dev.php'){
        view = url[4];
    }else{
        view = url[3];
    }
    //gestion de l'history
    History.pushState({folder: $(this).attr('data-id')}, "Dossier "+$(this).attr('data-name'), "#"+$(this).attr('data-name'));
    
    $('#remove_folder').attr('data-id', $(this).attr('data-id'));
    $('#folder-selection li').removeClass('active');
    var lvl = $(this).attr('class');
    var length = lvl.length;
    var level = parseInt(lvl.substring(length - 1, length)) + 1;
    var data_id = $(this).attr('data-id');
    var parent_id = $(this).attr('data-parent-id');
    $('li[data-id=' + data_id + ']').addClass('active');
    if($('#generatePrevu').hasClass('active')){
        generatePrevu();
    }else if($('#generatePrevu').hasClass('active')){
        generateList();
    }else if(view == 'files'){
        generateList();
    }else if(view == 'u'){
        generatePrevu();
    }
    if ($('li[data-id=' + data_id + '] i.fa').first().hasClass('fa-caret-down')) {
        $('li[data-id=' + data_id + '] i.fa').first().addClass('fa-caret-up');
        $('li[data-id=' + data_id + '] i.fa').first().removeClass('fa-caret-down');
    } else {
        $('li[data-id=' + data_id + '] i.fa').first().addClass('fa-caret-down');
        $('li[data-id=' + data_id + '] i.fa').first().removeClass('fa-caret-up');
    }
    showParent($(this));
    $('li[data-parent-id=' + parent_id + ']').show();
    $('#content' + parent_id).show();
    $('#content' + data_id).attr('aria-expanded', true);
    $('#content' + data_id).slideToggle('slow');
    var folder_name = $(this).attr('data-name');
    $("input[name=parent_id]").val(data_id);
    $("input[name=folder_id]").val(data_id);
    $('#display-selected-folder span').html(folder_name);
    if(url[3] == 'files'){
        $('#lbl_folder_name').html('Ajouter un dossier à ' + folder_name);
        $('.add-files .fileinput-button span:first').html('Ajouter un fichier à ' + folder_name);
        $('.name-file-conig').html(folder_name);
        $('button[data-conteneur=config_file]').attr('data-original-title', 'Configurer les droits d\'accés du dossier <strong class="name-file-conig">' + folder_name + '</strong>');
        $('button[data-conteneur=add_file]').attr('data-original-title', 'Ajouter un fichier à <strong class="name-file-conig">' + folder_name + '</strong>');
        $('button[data-conteneur=create_folder]').attr('data-original-title', 'Créer un dossier dans <strong class="name-file-conig">' + folder_name + '</strong>');
        $('#remove_folder').attr('data-original-title', 'Supprimer le dossier <strong class="name-file-conig">' + folder_name + '</strong>');
        $('button[data-conteneur=show_sharing]').attr('onclick', 'var win = window.open("/u/' + $(this).attr('data-user') + '", "_blank");win.focus();');
        $('button[data-conteneur=show_sharing]').attr('data-original-title', 'Afficher la vue de <strong class="name-file-conig">' + $(this).attr('data-user') + '</strong>');
    }
    event.stopPropagation();
    //$('li[data-parent-id="'+parent_id+'"].level'+level).toggle();
});

function hideSideBar(){
    $('#page-wrapper').animate({
            paddingLeft: 65
        });
        $('.side-nav').animate({
            marginLeft: -50,
            left: 50,
            width: 50
        });
        $('#folder-selection li:not(.no-remove)').hide();
        $('#folder-selection li a#collapse-sidebar').removeClass('glyphicon-chevron-left');
        $('#folder-selection li a#collapse-sidebar').addClass('glyphicon-chevron-right');
        $('#folder-selection li a#collapse-sidebar').css({
            left: 0,
            top: 51,
            position: 'absolute'
        });
}
function showSideBar(){
    $('#page-wrapper').animate({
        paddingLeft: 210
    });
    $('.side-nav').animate({
        marginLeft: -200,
        left: 200,
        width: 200
    });
    $('#folder-selection li:not(.no-remove)').show();
    $('#folder-selection li a#collapse-sidebar').addClass('glyphicon-chevron-left');
    $('#folder-selection li a#collapse-sidebar').removeClass('glyphicon-chevron-right');
    $('#folder-selection li a#collapse-sidebar').css({
        left: 100,
        top: 0,
        position: 'relative'
    });
}

$('#folder-selection li a#collapse-sidebar').click(function (event) {
    hideSideBar();
});

function showButton(){
    $('button[data-conteneur="create_folder"]').show();
    $('button[data-conteneur="config_file"]').show();
    $('button#remove_folder').show();
    $('button#btnUpload').show();
}

function showParent(elem){
    elem.parent('ul').show();
    elem.parent('ul').attr('aria-expanded', 'true');
    //console.log(elem.parent('ul').parent('li').attr('class'));
    if(elem.parent('ul').parent('li.folder').attr('class') && elem.parent('ul').parent('li.folder').attr('class') != 'undefined')
        showParent(elem.parent('ul').parent('li.folder'));
}

function addInputEmail(email){
    $('.inputs_emails').append('<div class="form-group input-group"> <input class="alwM' + $('.inputs_emails div').length + ' form-control" type="text" name="emails" value="' + email + '" /> <span class="input-group-btn">\
        <button data-tooltip="tooltip" title="Supprimer le partage pour ce mail" id="del-email" data-remove="alwM' + $('.inputs_emails div').length + '" class="btn btn-default" type="button">\
        <span class="glyphicon glyphicon-minus"></span>\
        </button>\
        <button data-tooltip="tooltip" title="Prévenir par email" class="send-email btn btn-default" type="button">\
        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button></span></div>');
}

function generateFolderConfig(datas){
    $('.inputs_emails').html('');
    if(datas.folder.allowed_users){
        for(var i=0;i<datas.folder.allowed_users.length;i++){
            addInputEmail(datas.folder.allowed_users[i].email);
        }
    }
    if(datas.folder.status && datas.folder.status == 2){
        $('#havePass').attr("checked", "checked");
        $('#havePass').prop("checked", true);
        $('#passwordInfo #folderReelLink').html(datas.folder.link);
        $('#passwordInfo #folderLink').html('<a target="_blank" href="'+datas.folder.link+'">Partage</a>');
        $('#passwordInfo #folderPass').html(datas.folder.password);
        $('#passwordInfo').show();
    }else{
        $('#havePass').prop("checked", false);
        $('#passwordInfo').hide();
    }
}

var myWin = new Array();
function btnUpload(folder_id){
	if(!myWin[folder_id])
		myWin[folder_id] = window.open('http://dev.family-and-co.com/app_dev.php/upload/'+folder_id, 'CNN_WindowName'+folder_id, strWindowFeatures);
	else
		myWin[folder_id].focus();
}
function generateList() {
	var folder_id = $('#folder-selection li.active').attr('data-id');
    var datas = {id : $('#folder-selection li.active').attr('data-id'), display: 'liste'};
    $.ajax({
      method: 'POST',
      url: $('#folder-selection li.active').attr('data-url'),
      data: datas
    })
    .done(function( datas ) {
        var files = datas.files;
        var user_id = datas.user_id;
        var pagesUpload = datas.pagesUpload;
        var cls = "";
        var url = document.location.href.split('/');
        var base_url = url[0]+'/'+url[1]+'/'+url[2];
        generateFolderConfig(datas);
        
        //gestion des pages d'uploads
        console.log(pagesUpload);
    	if($.inArray(folder_id, pagesUpload) > 0){
    		$('#btnUpload').attr('onclick', "window.open('javascript:void(0)', 'CNN_WindowName"+folder_id+"', strWindowFeatures);");
    	}else{
    		$('#btnUpload').attr('onclick', "btnUpload("+folder_id+")");
    	}
    	showButton();
        //console.log(files);
        $('#generatePrevu').removeClass('active');
        $('#generateList').addClass('active');
        $('#filesManager').html('<table class="table table-hover"><thead>\
            <tr><th style="text-align:center;"><input id="check_all_file_checkbox" type="checkbox"/></th>\
                <th>Nom</th><th>Type</th><th>Poid</th><th>Actions</th></tr></thead>\
                <tbody>');
        $.each(files, function (k, file) {
            if (file.type != "Directory") {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                var ext = file.name.substring(length - 3, length);
                if (file.user == user_id) {
                    cls = "owner";
                } else {
                    cls = "no-owner";
                }
                if(file.type != 'avi' || file.type != 'mkv' ||
                   file.type != 'AVI' || file.type != 'MKV'){
                    //$.preloadImages('/get_file/' + file._id + '/' + ext);
                }
                $('#filesManager table tbody').append('<tr data-id="' + file.id + '" class="level-' + file.level + '">\
                  <td style="text-align:center;" class="' + cls + '"><input id="' + file.id + '" class="file_checkbox" type="checkbox"/></td>\
                  <td class="name"><span>' + file.name + '</span></td>\
                  <td class="type">' + file.type + '</td>\
                  <td class="size"></td>\
                  <td class="' + cls + '">\
                    <button data-tooltip="tooltip" title="Prévisualiser" onClick="showPrevu(this)" data-conteneur="prevu_file" data-type="' + file.type + '" data-name="' + file.name + '" data-id="' + file.id + '" type="button" class="btn btn-default">\
                    <span class="prevu glyphicon glyphicon-zoom-in" aria-hidden="true"></span>\
                    </button>\
                    <a href="'+base_url+'/get_file/' + file.id + '/' + ext + '" data-tooltip="tooltip" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                    <span class="glyphicon glyphicon-download" aria-hidden="true"></span></a>\
                    <button data-tooltip="tooltip" data-placement="bottom" data-original-title="<strong>Supprimer</strong>" title="Supprimer" data-id="' + file.id + '" data-remove="true" id="remove" type="button" class="btn btn-default" data-original-title="Supprimer">\
                    <span class="glyphicon glyphicon-remove-circle"></span>\
                    </button></td>\
                </tr>');
                $('tr[data-id="' + file.id + '"] td.size').html(bytesToSize(file.size));
            }
        });
        $('#filesManager').append('</tbody></table>');
        if(datas.success == 'show'){
            $('button[data-remove="true"]').remove();
        } 
        activateCheckbox();
        $('#filesManager table').fixedHeaderTable({ height: $(window).height()/1.22, altClass: 'odd' });
        $('.file_checkbox').shiftClick();
    });
}

function generatePrevu() {
    var datas = {id : $('#folder-selection li.active').attr('data-id'), display: 'prevu'};
    $.ajax({
      method: 'POST',
      url: $('#folder-selection li.active').attr('data-url'),
      data: datas
    })
    .done(function( datas ) {
        var files = datas.files;
        var user_id = datas.user_id;
        var url = document.location.href.split('/');
        var base_url = url[0]+'/'+url[1]+'/'+url[2];
        var nbFile = 0;
        var folders = [];
        generateFolderConfig(datas) 
        showButton();
        $('#filesManager').html('<div class="row"><div class="col-lg-12">');
        $('#generatePrevu').addClass('active');
        $('#generateList').removeClass('active');
        $.each(files, function (k, file) {
            var length = file.name.length;
            var noExt = file.name.substring(0, length - 4);
            var ext = file.name.substring(length - 3, length);
            if(file.type != 'Directory'){
                nbFile++;
                if (file.type != 'video/quicktime' && file.type != 'video/x-msvideo' && file.type != 'video/x-matroska' && file.type != 'video/mp4') {
                    $('#filesManager div.row div.col-lg-12').append('\
                        <div class="col-lg-4 col-md-4 imgfancycontent" style="margin: 5px 0px;">\
                                <a class="thumbnail fancy" data-fancybox-group="gallery" data-url="'+base_url+'/get_file/' + file.id + '/' + ext + '" href="'+file.full+'" >\
                                    <img class="img-responsive" src="/bundles/facgeneral/images/loading.gif" data-original="'+file.thumb+'">\
                                </a>\
                                <div class="overlay-action">\
                                    <a target="_blank" href="'+base_url+'/get_file/' + file.id + '/' + ext + '" data-tooltip="tooltip" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                                        <span class="glyphicon glyphicon-download" aria-hidden="true"></span>\
                                    </a>\
                                </div>\
                        </div>');
                } else if (file.type == 'video/quicktime' || file.type == 'video/x-msvideo'){
                    $('#filesManager div.row div.col-lg-12').append('\
                         <div class="col-lg-4 col-md-4" style="margin: 5px 0px;">\
                            <center class="thumbnail">\
                                <h4>'+file.name+'</h4>\
                                <br/>\
                                Format non supporté\
                                <br/>\
                                <a href="'+base_url+'/get_file/' + file.id + '/' + ext + '">Télécharger</a>\
                            </center>\
                            <div class="overlay-action">\
                                <a target="_blank" href="'+base_url+'/get_file/' + file.id + '/' + ext + '" data-tooltip="tooltip" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                                    <span class="glyphicon glyphicon-download" aria-hidden="true"></span>\
                                </a>\
                            </div>\
                        </div>');
                }else {
                    var id = "rand" + Math.floor((Math.random() * 10000) + 1);
                    $('#filesManager div.row div.col-lg-12').append('\
                        <div class="col-lg-4 col-md-4" style="margin: 5px 0px;">\
                            <center class="thumbnail fancy" data-url="'+base_url+'/get_file/' + file.id + '/' + ext + '" vid-id="' + id + '">\
                                <h4>'+file.name+'</h4>\
                                <video data-setup=\'{"techOrder": ["flash", "html5", "other supported tech"]}\' preload="none" id="' + id + '" class="video-js vjs-default-skin" controls width="215" height="250">\
                                    <source src="'+base_url+'/get_file/' + file.id + '/mp4" type="video/mp4" />\
                                </video>\
                            </center>\
                            <div class="overlay-action">\
                                <a target="_blank" href="'+base_url+'/get_file/' + file.id + '/' + ext + '" data-tooltip="tooltip" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                                    <span class="glyphicon glyphicon-download" aria-hidden="true"></span>\
                                </a>\
                            </div>\
                        </div>');
                    /*if(file.type != 'video/mp4'){
                        videojs(id, {}, function () {
                            vidPlayer = this;
                        });
                    }*/
                }
            }else{
                folders.push(file);
            }
        });
        if(nbFile == 0){
            $('#filesManager').html('\
                <div class="panel panel-default" style="margin-top: 15px;">\
                    <div class="panel-body">\
                        <div class="text-center">\
                            Ce dossier ne contient pas de fichiers :(\
                        </div>\
                    </div>\
                </div>\
                <div class="panel panel-default">\
                    <div class="panel-body">\
                        <div class="text-center">\
                            Accédez aux autres dossiers :)\
                                <div id="folder-list"></div>\
                        </div>\
                    </div>\
                </div>\
            ');
            $.each(folders, function (k, folder) {
                $('#folder-list').append('\
                    <li onclick=\'$( "li[data-id='+folder.id+']" ).trigger( "click" );\'>\
                        <i class="glyphicon glyphicon-folder-open"></i>\
                            &nbsp;&nbsp;'+folder.name+'\
                        <i class="fa fa-fw fa-caret-down"></i>\
                </li>');
            });
        }
        $('#filesManager').append('</div></div>'); 
        $("img").lazyload({
            effect: "fadeIn"
        });
    });
}

function showPrevu(that) {
    var type = $(that).attr("data-type");
    var length = $(that).attr("data-name").length;
    var noExt = $(that).attr("data-name").substring(0, length - 4);
    var ext = $(that).attr("data-name").substring(length - 3, length);
    var url = document.location.href.split('/');
    if(url[3] == 'files')
        var base_url = url[0]+'/'+url[1]+'/'+url[2];
    else
        var base_url = url[0]+'/'+url[1]+'/'+url[2]+'/'+url[3];
    $('#' + $(that).attr('data-conteneur') + ' .popover-title').html('<span class="text-info"><strong>' + $(that).attr("data-name") + '</strong></span>&nbsp;<button type="button" id="close" class="close" onclick="$(\'#' + $(that).attr('data-conteneur') + ' .popover\').hide();">&times;</button>');
    if (type == 'image/png' || type == 'image/jpg' || type == 'image/jpeg' || type == 'text/plain') {
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center><img height="125px" src="'+base_url+'/get_file/' + $(that).attr('data-id') + '/' + ext + '" alt="' + noExt + '" title="' + noExt + '" /></center>');
    } else if (type == 'video/x-msvideo') {
        var id = "rand" + Math.floor((Math.random() * 10000) + 1);
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center><video data-setup=\'{"techOrder": ["html5", "flash", "other supported tech"]}\' preload="auto" id="' + id + '" class="video-js vjs-default-skin" controls\
               width="360" height="240">\
               <source src="rtmp:/'+url[1]+'/'+url[2]+'" type="video/mp4" />\
               <source src="'+base_url+'/get_file/' + $(that).attr('data-id') + '/ogv" type="video/ogv" />\
               <source src="'+base_url+'/get_file/' + $(that).attr('data-id') + '/webm" type="video/webm" />\
              </video></center>');
        videojs(id, {}, function () {
            vidPlayer = this;
            vidPlayer.load();
        });
        $('#' + $(that).attr('data-conteneur') + ' .popover').draggable();
    } else {
        $('#' + $(that).attr('data-conteneur') + ' .popover-content').html('<center>Format not found</center>');
    }
    //show the menu directly over the placeholder
    //console.log($('#prevu_file .popover').width());
    var left = (screen.width/2)-($('#prevu_file .popover').width()/2);
    var top = (screen.height/2)-($('#prevu_file .popover').height()+40/2);
    $('#' + $(that).attr('data-conteneur') + ' .popover').css({
        top: top + "px",
        left: left + "px"
    }).show();
    //$(that).trigger('click');
}