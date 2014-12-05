$('#folders ul').delegate("li span.brace", 'click', function(){
    var lvl = $(this).parent('li:first').attr('class');
    var length = lvl.length;
    var level = parseInt(lvl.substring(length - 1, length)) + 1;
    var parent_id = $(this).parent('li:first').attr('data-id');
    $('li[data-id="'+parent_id+'"]').after($('li[data-parent-id="'+parent_id+'"].level'+level));
    $('li[data-parent-id="'+parent_id+'"].level'+level).toggle();
    if($(this).attr('class') == 'brace closed'){
        $(this).removeClass('closed');
        $(this).addClass('opened');
        //$('#folders .level-'+level).hide();
    }else{
        $(this).removeClass('opened');
        $(this).addClass('closed');
    }
});

$('#folders ul').delegate("li span.folder", 'click', function(){
    var data_id = $(this).attr('data-id');
    var folder_name = $(this).attr('data-name');
    socket.emit('select_folder', data_id);
    $("input[name=parent_id]").val(data_id);
    $("input[name=folder_id]").val(data_id);
    $('#lbl_folder_name').html('Ajouter un dossier à '+folder_name);
    $('.add-files .fileinput-button span:first').html('Ajouter un fichier à '+folder_name);
    $('button[data-conteneur=config_file]').attr('data-original-title', 'Configurer les droits d\'accés du dossier <strong>'+ folder_name +'</strong>');
    $('button[data-conteneur=add_file]').attr('data-original-title', 'Ajouter un fichier à <strong>'+ folder_name +'</strong>');
    $('button[data-conteneur=create_folder]').attr('data-original-title', 'Créer un dossier dans <strong>'+ folder_name+'</strong>'); 
    $('span.folder').each(function(i, d){
        $(d).removeClass('active');
    })
    $(this).addClass('active');  
});
function FileConvertSize(aSize){
    aSize = Math.abs(parseInt(aSize, 10));
    var def = [[1, 'octets'], [1024, 'ko'], [1024*1024, 'Mo'], [1024*1024*1024, 'Go'], [1024*1024*1024*1024, 'To']];
    for(var i=0; i<def.length; i++){
        if(aSize<def[i][0]) return (aSize/def[i-1][0]).toFixed(2)+' '+def[i-1][1];
    }
}
socket.on('progress_bar', function (datas) {
    //console.log(datas);
    if(datas.file_id){
        if($('tr[data-id="'+datas.file_id+'"] td.name > div.'+datas.type).length >0){
            $('tr[data-id="'+datas.file_id+'"] td.name div.'+datas.type+' .progress-bar').attr('aria-valuenow', datas.progress);
            $('tr[data-id="'+datas.file_id+'"] td.name div.'+datas.type+' .progress-bar').css(
                'width',
                datas.progress/2 + '%'
            );
            $('tr[data-id="'+datas.file_id+'"] td.name div.'+datas.type+' .progress-bar').html(datas.type+' '+datas.progress/2+'%');
        }else{
            $('tr[data-id="'+datas.file_id+'"] td.name').append('<div class="progress '+datas.type+'">\
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="200">\
                </div></div>');
        }
        if(datas.progress/2 == 100){
            $('tr[data-id="'+datas.file_id+'"] td.name div.'+datas.type).remove();
        }
    }
});
socket.on('selected_folder', function (files) {
	$('#filesManager table tbody').html('');
    $('#inputs_emails').html('');
    var params = document.URL.split('/');
    if(params[3] == 'u'){
        if(files.child){
            $('#content-file').html('');
            $.each( files.child, function( k, file ) {
                var length = file.name.length;
                var noExt = file.name.substring(0, length - 4);
                var ext = file.name.substring(length - 3, length);
                if(ext == 'jpg' || ext == 'png' || ext == 'gif'){
                    $('#content-file').append('<div class="col-lg-3 col-md-3 col-xs-6" style="margin-bottom:10px">\
                        <a class="thumbnail fancybox-thumbs" rel="fancybox-thumb" href="/get_file/'+file._id+'/'+ext+'" >\
                            <img style="height:250px;" class="img-responsive" src="/get_file/'+file._id+'/'+ext+'" alt="'+file.name+'">\
                        </a></div>');
                }else if(ext == 'avi'){ 
                    var id = "rand" + Math.floor((Math.random() * 10000) + 1);
                    $('#content-file').append('<div class="col-lg-3 col-md-3" style="margin-bottom:10px">\
                        <center><a class="thumbnail">\
                        <video data-setup=\'{"techOrder": ["html5", "flash", "other supported tech"]}\' preload="auto" id="'+id+'" class="video-js vjs-default-skin" controls width="360" height="250">\
                        <source src="/get_file/'+file._id+'/mp4" type="video/mp4" />\
                        <source src="/get_file/'+file._id+'/webm" type="video/webm" />\
                        <source src="/get_file/'+file._id+'/ogg" type="video/ogg" /></video></a></center></div>');
                        videojs(id, {}, function() {
                            vidPlayer = this;
                            vidPlayer.load();
                        });
                }
            });
        }
    }else{
        //affichage des donnees
        console.log(files);
        if(files.child){
            $.each( files.child, function( k, file ) {
                if(file.type != "Directory"){
                     $('#filesManager table tbody').append('<tr data-id="'+file._id+'" class="level-'+file.level+'">\
                      <td style="text-align:center;" class="col-md-1"><input id="'+file._id+'" class="file_checkbox" type="checkbox"/></td>\
                      <td style="padding-left:17px;" class="col-md-1"><span onMouseOver="showPrevu(this)" data-conteneur="prevu_file" data-type="'+file.type+'" data-name="'+file.name+'" data-id="'+file._id+'" class="prevu glyphicon glyphicon-zoom-in" aria-hidden="true"></span></td>\
                      <td class="name col-md-7">'+file.name+'</td>\
                      <td class="col-md-1 type">'+file.type+'</td>\
                      <td class="col-md-1 size"></td>\
                      <td class="col-md-1"><button data-tooltip="tooltip" title="" data-id="'+file._id+'" data-remove="true" id="remove" type="button" class="btn btn-default" data-original-title="Supprimer la sélection">\
                        <span class="glyphicon glyphicon-remove-circle"></span>\
                        </button></td>\
                    </tr>');
                     $('tr[data-id="'+file._id+'"] td.size').html(FileConvertSize(file.size));
                }
            });
        }
        //console.log(folder);
        //affichage des permissions
        if(files.allowedEmails){
            $.each( files.allowedEmails, function( key, email ) {
                 $('#inputs_emails').append('<div class="form-group input-group"> <input id="alwM'+key+'" class="form-control" type="text" name="emails" value="'+email+'" /> <span class="input-group-btn">\
                                    <button id="del-email" data-remove="alwM'+key+'" class="btn btn-default" type="button">\
                                        <span class="glyphicon glyphicon-minus"></span>\
                                    </button>\
                                </span></div>');
            })
        }
    }
});