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
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $(data.context[0]).find('.start').remove();
            $(data.context[0]).find('.ui-progressbar-value.ui-widget-header.ui-corner-left').addClass('progress-bar progress-bar-striped active');
            $(data.context[0]).find('.ui-progressbar-value.ui-widget-header.ui-corner-left').removeClass('ui-progressbar-value ui-widget-header ui-corner-left');
            $(data.context[0]).find('.progress .progress-bar').css('display','block');
            $(data.context[0]).find('.progress .progress-bar').css(
                'width',
                progress + '%'
            );
            $(data.context[0]).find('.progress .progress-bar').html('<span style="vertical-align:middle;">'+progress + '%</span>');

            if(progress >= 100){
                nbItem--;
                $('#nbRest').html(nbItem);
                $(data.context[0]).remove();
            }
            if(nbItem <= 0){
                $('.infoText').hide();
                $('#loadAllFiles').show();
                $('#folder-selection li.active').trigger('click');
                $('#folder-selection li.active').trigger('click');
            } 
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
        url: '/uploads',
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        maxFileSize: 10000000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|avi|mkv)$/i
    });
    // Upload server status check for browsers with CORS support:
    if ($.support.cors) {
        $.ajax({
            url: '/',
            type: 'HEAD'
        }).fail(function () {
            $('<div class="alert alert-danger"/>')
                .text('Upload server currently unavailable - ' +
                        new Date())
                .appendTo('#fileupload');
        });
    }
    // Load existing files:
    /*$('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });*/
});