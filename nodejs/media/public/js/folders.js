$(function () {
    'use strict';
    var socket = io.connect('http://localhost:8080');
    
    $('#folders ul').delegate("li span.brace", 'click', function(){
        var lvl = $(this).parent('li:first').attr('class');
        var length = lvl.length;
        var level = parseInt(lvl.substring(length - 1, length)) + 1;
        if($(this).attr('class') == 'brace opened'){
            $(this).removeClass('opened');
            $(this).addClass('closed');
            $('#folders .level-'+level).hide();

        }else{
            $(this).removeClass('closed');
            $(this).addClass('opened');
            $('#folders .level-'+level).show();

        }  
    });
    $('#folders ul').delegate("li span.folder", 'click', function(){
        var data_id = $(this).attr('data-id');
        var folder_name = $(this).attr('data-name');
        socket.emit('select_folder', data_id);
        $("input[name=parent_id]").val(data_id);
        $('#lbl_folder_name').html('Ajouter un dossier à '+folder_name);
        $('.add-files .fileinput-button span:first').html('Ajouter un fichier à '+folder_name);
        $('span.folder').each(function(i, d){
            $(d).removeClass('active');
        })
        $(this).addClass('active');
    });
    socket.on('selected_folder', function (files) {
    	$('#filesManager table tbody').html('');
        $.each( files, function( key, file ) {
            if(file.type != 'Directory'){
                $('#filesManager table tbody').append('<tr class="level-'+file.level+'">\
                  <td><input id="'+file._id+'" class="file_checkbox" type="checkbox"/></td>\
                  <td>'+file.name+'</td>\
                  <td>'+file.type+'</td>\
                  <td>? Mo</td>\
                </tr>');
            }
		});
    });
});