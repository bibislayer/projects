
//This function is for sending ajax request using conditions
function ajax(that, params,base_url) {
    var url, fancy, obj, data, dataType;
    
    dataType = '';
    
    if (that) {
        event.preventDefault();
        url = $(that).attr('data-url');
        if ($(that).attr('data-fancy'))
            params['action'] = fctFancy;
        obj = $('#' + $(that).attr('data-replace'));
        history.pushState({}, '', $(that).attr("href"));
    } else {
        url = base_url + params['url'];
        obj = params['obj'];
        if(params['data'] != undefined)
             data = params['data'];
         
        if(params['dataType'] != undefined){
            dataType = params['dataType'] ;
        } 
        
        fancy = false;
    }
    //For autocomplete part
    if (params && params['autocomplete']) {
        params['action'] = fctAutocomplete;
        data = {
            limit: params['autocomplete']['limit'],
            term: params['autocomplete']['request'].term
        };
        dataType = 'jsonp';
    }


    if (!params || params && !params['autocomplete'])
        $(obj).addClass('opacity');

    $.ajax({
        type: "POST",
        url: url,
        dataType: dataType,
        data: data,
        success: function(data) {
            $(obj).removeClass('opacity');
            if (params && params['action']) {
                params['action'](data, params);
            } else {
                obj.html(data);
            }
        },
        error: function(data) {
            return 0;
        }
    });
    return false;
}
