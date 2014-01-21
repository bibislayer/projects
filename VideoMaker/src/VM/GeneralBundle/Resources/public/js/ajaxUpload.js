/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



// function to make the page dark with some loader while ajax call..
function grayOut(vis, loader_div, options)
{
    var optionsoptions = options || {};
    var zindex = options.zindex || 500;
    var opacity = options.opacity || 70;
    var opaque = (opacity / 500);
    var bgcolor = options.bgcolor || '#000000';
    var dark = document.getElementById(loader_div);
    if (!dark)
    {
        // The dark layer doesn't exist, it's never been created.  So we'll    
        // create it here and apply some basic styles.     
        var tbody = document.getElementsByTagName("body")[0];
        var tnode = document.createElement('div');
        tnode.style.position = 'absolute';
        tnode.style.top = '0px';
        tnode.style.left = '0px';
        tnode.style.overflow = 'hidden';
        tnode.style.display = 'none';
        tnode.id = loader_div;
        tbody.appendChild(tnode);
        dark = document.getElementById(loader_div);
    }

    if (vis)
    {
        var pageWidth = '100%';
        var pageHeight = '100%';
        dark.style.opacity = opaque;
        dark.style.MozOpacity = opaque;
        dark.style.filter = 'alpha(opacity=' + opacity + ')';
        dark.style.zIndex = zindex;
        dark.style.backgroundColor = bgcolor;
        dark.style.width = pageWidth;
        dark.style.height = pageHeight;
        dark.style.display = 'block';
        dark.style.position = 'absolute';
    }
    else
    {
        dark.style.display = 'none';
    }
}

// for jquery file upload..
jQuery.extend({
    createUploadIframe: function(id, uri)
    {
        //create frame
        var frameId = 'jUploadFrame' + id;
        var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
        if (window.ActiveXObject)
        {
            if (typeof uri == 'boolean') {
                iframeHtml += ' src="' + 'javascript:false' + '"';

            }
            else if (typeof uri == 'string') {
                iframeHtml += ' src="' + uri + '"';

            }
        }
        iframeHtml += ' />';
        jQuery(iframeHtml).appendTo(document.body);

        return jQuery('#' + frameId).get(0);
    },
    createUploadForm: function(id, fileElementId, data)
    {
        
        //create form	
        var formId = 'jUploadForm' + id;
        var fileId = 'jUploadFile' + id;
        var form = jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
        if (data)
        {
            for (var i in data)
            {
                jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
            }
        }
        var oldElement = jQuery('#' + fileElementId);
        var newElement = jQuery(oldElement).clone();
        jQuery(oldElement).attr('id', fileId);
        jQuery(oldElement).before(newElement);
        jQuery(oldElement).appendTo(form);



        //set attributes
        jQuery(form).css('position', 'absolute');
        jQuery(form).css('top', '-1200px');
        jQuery(form).css('left', '-1200px');
        jQuery(form).appendTo('body');
        return form;
    },
    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout		
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var id = new Date().getTime()
        var form = jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data) == 'undefined' ? false : s.data));
        var io = jQuery.createUploadIframe(id, s.secureuri);
        var frameId = 'jUploadFrame' + id;
        var formId = 'jUploadForm' + id;
        // Watch for a new set of requests
        if (s.global && !jQuery.active++)
        {
            jQuery.event.trigger("ajaxStart");
        }
        var requestDone = false;
        // Create the request object
        var xml = {}
        if (s.global)
            jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
        {
            var io = document.getElementById(frameId);
            try
            {
                if (io.contentWindow)
                {
                    xml.responseText = io.contentWindow.document.body ? io.contentWindow.document.body.innerHTML : null;
                    xml.responseXML = io.contentWindow.document.XMLDocument ? io.contentWindow.document.XMLDocument : io.contentWindow.document;

                } else if (io.contentDocument)
                {
                    xml.responseText = io.contentDocument.document.body ? io.contentDocument.document.body.innerHTML : null;
                    xml.responseXML = io.contentDocument.document.XMLDocument ? io.contentDocument.document.XMLDocument : io.contentDocument.document;
                }
            } catch (e)
            {
                //jQuery.handleError(s, xml, null, e);
            }
            if (xml || isTimeout == "timeout")
            {
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if (status != "error")
                    {
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData(xml, s.dataType);
                        // If a local callback was specified, fire it and pass it the data
                        if (s.success)
                            s.success(data, status);

                        // Fire the global callback
                        if (s.global)
                            jQuery.event.trigger("ajaxSuccess", [xml, s]);
                    } else
                    {
                        //jQuery.handleError(s, xml, status);
                    }
                } catch (e)
                {
                    status = "error";
                    //jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if (s.global)
                    jQuery.event.trigger("ajaxComplete", [xml, s]);

                // Handle the global AJAX counter
                if (s.global && !--jQuery.active)
                    jQuery.event.trigger("ajaxStop");

                // Process result
                if (s.complete)
                    s.complete(xml, status);

                jQuery(io).unbind()

                setTimeout(function()
                {
                    try
                    {
                        jQuery(io).remove();
                        jQuery(form).remove();

                    } catch (e)
                    {
                        //jQuery.handleError(s, xml, null, e);
                    }

                }, 100)

                xml = null

            }
        }
        // Timeout checker
        if (s.timeout > 0)
        {
            setTimeout(function() {
                // Check to see if the request is still happening
                if (!requestDone)
                    uploadCallback("timeout");
            }, s.timeout);
        }
        try
        {

            var form = jQuery('#' + formId);
            jQuery(form).attr('action', s.url);
            jQuery(form).attr('method', 'POST');
            jQuery(form).attr('target', frameId);
            if (form.encoding)
            {
                jQuery(form).attr('encoding', 'multipart/form-data');
            }
            else
            {
                jQuery(form).attr('enctype', 'multipart/form-data');
            }
            jQuery(form).submit();

        } catch (e)
        {
            //jQuery.handleError(s, xml, null, e);
        }

        jQuery('#' + frameId).load(uploadCallback);
        return {abort: function() {
            }};

    },
    uploadHttpData: function(r, type) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if (type == "script")
            jQuery.globalEval(data);
        // Get the JavaScript object, if JSON is used.
        if (type == "json")
            eval("data = " + data);
        // evaluate scripts within html
        if (type == "html")
            jQuery("<div>").html(data).evalScripts();

        return data;
    }
});


// ajax attachment file upload on step-4
function ajaxUploadAttachment(params)
{
   
  /*  $(this).ajaxStart(function() {
        $("#fileuploading").show();
    }).ajaxComplete(function() {
        $("#fileuploading").remove();
    });

  */
    var old_file = $('#'+params['file_place']).html().trim();
    
    $.ajaxFileUpload
            ({
                url: params['url'],
                secureuri: false,
                fileElementId: params['id'],
                dataType: 'json',
                data: {'old_file':old_file , 'type': params['type'],'elem_id':params['elem_id']},
                success: function(data, status)
                {
                    if (data.error != null) { 
                        $("#"+params['file_place']).html(data.error);                       
                    }
                    else if (data.result != null) {                        
                        $("#"+params['file_place']).html(data.result.replace('media_type_',''));
                        $("#"+params['media_file']).val(data.result.replace('media_type_',''));
                    }
                    else {
                        if ($("#"+params['file_place']).html() != '') {
                            $("#"+params['file_place']).html('');
                            $("#media_file").val('');
                        }
                    }
                },
                error: function(data, status, e)
                {
                    alert(e);
                }
            });
            
    return false;
}

function uploadFile(that){
    var params = new Array();
    
    params['file_place'] = $(that).attr('data-file_place');
    params['id'] = $(that).attr('id');
    params['url'] = $(that).attr('data-url');
    params['type'] = $(that).attr('data-type');
    params['media_file'] = $(that).attr('data-media_id');    
    var data_id = $(that).attr('data-id').split('_'); 
    
    if(data_id[1]){
         params['elem_id'] = data_id[1];
    }else{
        params['elem_id'] = '';
    }
    $(that).bind("change", function() {
        if ($.trim($(that).val()) != '') {
            $('#'+ params['file_place']).val($(this).val());
            ajaxUploadAttachment(params);
        }
    });   
} 