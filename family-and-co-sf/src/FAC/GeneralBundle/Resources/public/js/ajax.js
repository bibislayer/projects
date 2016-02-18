var base_url = "http://dev.family-and-co.com/app_dev.php";
$('body').delegate('form[data-type="ajax"]', 'submit', function (e) {
  //$('form').on('submit', function(e) {
    e.preventDefault(); 
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: $(this).serialize(), 
        success: function(datas) { 
            //hidden form
            $('[data-conteneur="create_folder"]').trigger('click');
            //adding one item
            var parent = '';
            if (datas.file.parent_id)
                parent = 'data-parent-id=' + datas.file.parent_id + '';
            $('#content' + datas.file.parent_id).append('<li ' + parent + ' data-type="ajax" data-method="POST" data-url="'+base_url+'/selected-folder" data-name="' + datas.file.name + '" data-id="' + datas.file.id + '" class="level' + datas.file.level + ' folder">\
                <a href="javascript:;" data-toggle="collapse" data-target="content' + datas.file.id + '"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;' + datas.file.name + ' <i class="fa fa-fw fa-caret-down"></i></a>\
                <ul id="content' + datas.file.id + '" class="collapse">\
                </ul>\
            </li>');
            $('#content' + datas.file.parent_id).show();
            generate('success', 'Dossier créé');
            //$.notify("Do not press this button", "info");
            //$.notify("Warning: Self-destruct in 3.. 2..", "warn");
        },
        error: function(e){
            $.notify("Une erreur est survenu pendant la création du dossier", "error");
        }
    });
  });
$('body').delegate('[data-type="ajax"]:not(form)', 'click', function (e) {
    var method = 'POST';
    var url = $(this).attr('data-url');
    if(url == base_url+'/change-status')
    {
        var status = ($('#havePass').is(':checked')) ? 2 : 0;
        var datas = {status : status};
    }
    else
    {
         var datas = {id : $(this).attr('data-id')};
    }
    
    $.ajax({
      method: method,
      url: url,
      data: datas
    })
    .done(function( datas ) {
        if(url == base_url+'/change-status')
        {
            if ($('#havePass').is(':checked')) {
                $('#havePass').prop("checked", true);
                $('#passwordInfo #folderReelLink').html(base_url+'/u/'+datas.username+'/'+datas.name);
                $('#passwordInfo #folderLink').html('<a href="'+base_url+'/u/'+datas.username+'/'+datas.name+'" title="partage">Lien</a>');
                $('#passwordInfo #folderPass').html(datas.password);
                $('#passwordInfo').show();
            } else {
                $(this).prop("checked", true);
                $('#passwordInfo').hide();
            }
            generate('success', 'status modifié');
        }
        else if(url == base_url+'/remove-folder'){
            if($('li[data-id='+datas.id+']').prev('li').attr('class'))
                $('li[data-id='+datas.id+']').prev('li').trigger('click');
            else
                $('li[data-id='+datas.id+']').parent('ul').parent('li').trigger('click');
            $('li[data-id='+datas.id+']').remove();
            generate('success', 'Dossier supprimé');
        }
        else
        {
            $('tr[data-id="' + file.id + '"] td.size').html(bytesToSize(file.size));
        }
    });
});
//removed one or many files
$(".panel").delegate("#remove", 'click', function () {
    //console.log('removed');
    var files = [];
    if ($(this).attr('data-remove')) {
        files.push($(this).attr('data-id'));
    } else {
        //removed one or many files
        $(".file_checkbox:checked").each(function () {
            files.push($(this).attr('id'));
        });
    }
    //console.log(files);
    if (confirm("Voulez-vous supprimer " + files.length + " fichiers ?")) {
        var data = {id: files};
        //data = JSON.stringify(data);
        $.ajax({
          method: 'POST',
          url: base_url+'/remove-files',
          data: data
        })
        .done(function( datas ) {
            for(var i=0;i < datas.id.length;i++){
                $('tr[data-id='+datas.id[i]+']').remove();
            }
            if(datas.id.length > 1)
                var msg = 'Tous vos fichiers ont bien été supprimés';
            else
                 var msg = 'Votre fichier a bien été supprimé';
            generate('success', msg);
        });
    }
});

$(".my-panel").delegate("#del-email", 'click', function () {
        var input = $(this).attr('data-remove');
        //alert($('#'+input).val());
        socket.emit('delAlwMail', {email: $('.' + input).first().val(), folder_id: $('input[name=folder_id]').val()});

        $('.' + input).parent('div').remove();
        $(this).remove();
});
$(".my-panel").delegate(".add-email", 'click', function () {
    var datastring = $("#formConfig").serialize();
    $.ajax({
        type: "POST",
        url: base_url+"/invite-user",
        data: datastring,
        dataType: "json",
        success: function (data) {
            $('#contentConfig').modal('hide');
            generate('success', 'Utilisateur invité');
        },
        error: function () {
            generate('error', 'error handing here');
        }
    });
    var email = $('input[name=email]').val();
    $('input[name=prevEmail]').val('');
    addInputEmail(email);
});

var folder;
function onChangeFolder(val) {
    console.log(val);
    folder = val;
}
function changePath() {
    var files = Array();
    $(".file_checkbox:checked").each(function () {
        files.push($(this).attr('id'));
    });
    var data = {id: files, folder: folder};
    $.ajax({
          method: 'POST',
          url: base_url+'/move-files',
          data: data
        })
        .done(function( datas ) {
            for(var i=0;i < datas.id.length;i++){
                $('tr[data-id='+datas.id[i]+']').remove();
            }
        });
    $('button[data-conteneur="move_file"]').trigger('click');
}

function activateCheckbox() {
     $("#filesManager").delegate("#check_all_file_checkbox", 'click', function () {
        $('.file_checkbox').each(function () {
            if ($('#check_all_file_checkbox').is(':checked')) {
                $(this).prop("checked", true);
                $('.files-actions').show();
            } else {
                $(this).prop("checked", false);
                $('.files-actions').hide();
            }
        })
    })
    $("#filesManager table tbody").delegate("tr", 'click', function () {
        var selector = $(this).find('.file_checkbox');
        var selected = $("input.file_checkbox:checked");
        if (selector.is(':checked')) {
            $('.files-actions').show();
        } else if (selected.length < 1) {
            $('.files-actions').hide();
        }
    });
}

function bytesToSize(bytes){
    var kilobyte = 1000;
    var megabyte = kilobyte * 1000;
    var gigabyte = megabyte * 1000;
    var terabyte = gigabyte * 1000;
    var petabyte = terabyte * 1000;
    var b;
    if(bytes < kilobyte)
        b = Math.round(bytes) + ' B';
    else if(bytes < megabyte)
        b = Math.round((bytes / kilobyte)) + ' KB';
    else if(bytes < gigabyte)
        b = Math.round((bytes / megabyte)) + ' MB';
    else if(bytes < terabyte)
        b = Math.round((bytes / gigabyte)) + ' GB';
    else if(bytes < petabyte)
        b = Math.round((bytes / terabyte)) + ' TB';
    else
        b = Math.round((bytes / petabyte)) + ' PB';
    return b;
}
