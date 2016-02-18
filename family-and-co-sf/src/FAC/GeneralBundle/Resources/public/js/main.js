/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */
$(function () {
    'use strict';
    var nbItem = 0;
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 1000, 10);
            $(data.context[0]).find('.start').remove();
            $(data.context[0]).find('.ui-progressbar-value.ui-widget-header.ui-corner-left').addClass('progress-bar progress-bar-striped active');
            $(data.context[0]).find('.ui-progressbar-value.ui-widget-header.ui-corner-left').removeClass('ui-progressbar-value ui-widget-header ui-corner-left');
            $(data.context[0]).find('.progress .progress-bar').css('display','block');
            $(data.context[0]).find('.progress .progress-bar').css(
                'width',
                progress/10 + '%'
            );
            $(data.context[0]).find('.progress .progress-bar').html('<span style="vertical-align:middle;">'+progress/10 + '%</span>');

            if(progress >= 1000){
                nbItem--;
                $('#nbRest').html(nbItem);
                $(data.context[0]).remove();
            }
            if(nbItem <= 0){
                $('.infoText').hide();
                $('.fileupload-progress').hide();
                $('#loadAllFiles').show();
                if(parseInt($('#nbMax').html()) > 1)
                    var msg = 'Tous vos fichiers ont bien été transférés';
                else
                    var msg = 'Votre fichier a bien été transféré';
                generate('success', msg);
            } 
        },
        done: function(e, datas){
            datas = datas.result;
            var url = document.location.href.split('/');
            if(url[3] == 'files')
                var base_url = url[0]+'/'+url[1]+'/'+url[2];
            else
                var base_url = url[0]+'/'+url[1]+'/'+url[2]+'/'+url[3];
            var length = datas.file.name.length;
            var ext = datas.file.name.substring(length - 3, length);
            $('#filesManager table tbody').append('<tr data-id="' + datas.file.id + '"">\
                  <td style="text-align:center;" class="owner"><input id="' + datas.file.id + '" class="file_checkbox" type="checkbox"/></td>\
                  <td class="name"><span>' + datas.file.name + '</span></td>\
                  <td class="type">' + datas.file.type + '</td>\
                  <td class="size"></td>\
                  <td class="owner">\
                    <button data-tooltip="tooltip" title="Prévisualiser" onClick="showPrevu(this)" data-conteneur="prevu_file" data-type="' + datas.file.type + '" data-name="' + datas.file.name + '" data-id="' + datas.file.id + '" type="button" class="btn btn-default">\
                    <span class="prevu glyphicon glyphicon-zoom-in" aria-hidden="true"></span>\
                    </button>\
                    <a href="'+base_url+'/get_file/' + datas.file.id + '/' + ext + '" data-tooltip="tooltip" title="Télécharger" type="button" class="btn btn-default" data-title="Télécharger">\
                    <span class="glyphicon glyphicon-download" aria-hidden="true"></span></a>\
                    <button data-tooltip="tooltip" data-placement="bottom" data-original-title="<strong>Supprimer</strong>" title="Supprimer" data-id="' + datas.file.id + '" data-remove="true" id="remove" type="button" class="btn btn-default" data-original-title="Supprimer">\
                    <span class="glyphicon glyphicon-remove-circle"></span>\
                    </button></td>\
                </tr>');
        }
    });
    $('#fileupload')
        .bind('fileuploadadd', function (e, data) {
            nbItem++;
            $('#nbRest').html(nbItem);
            $('#nbMax').html(nbItem);
        })
        .bind('fileuploadstart', function (e) {
            $('.infoText').show();
            $('.fileupload-progress.fade').removeClass('fade');
            $('.fileupload-progress')
            .find('.ui-progressbar-value.ui-widget-header.ui-corner-left')
            .removeClass('ui-progressbar-value ui-widget-header ui-corner-left')
            .addClass('progress-bar progress-bar-striped active');
            $('.fileupload-progress').show();
        });
    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    $('#fileupload').fileupload('option', {
        url: 'http://dev.family-and-co.com/app_dev.php/uploads',
        limitMultiFileUploads: 1,
        sequentialUploads: true,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        maxFileSize: 10000000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|avi|mkv)$/i,
        autoUpload:true
    });
    // Upload server status check for browsers with CORS support:
    if ($.support.cors) {
        $.ajax({
            url: '',
            type: 'HEAD'
        }).fail(function () {
            $('<div class="alert alert-danger"/>')
                .text('Upload server currently unavailable - ' +
                        new Date())
                .appendTo('#fileupload');
        });
    }
});