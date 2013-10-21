var base_url = "http://localhost/Forma-Search2/web/app_dev.php/";

window.addEventListener('popstate', function(event) {
    if (event.state) {
        location.reload();
    }
}, false);

//This function is for sending ajax request using conditions
function ajax(that, params) {
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


//function for autocomplete which returns response 
var fctAutocomplete = function(data, params) {
    params['autocomplete']['response']($.map(data.success, function(i, item) {
        return {
            label: i.name,
            value: i.name,
            id: i.id
        }
    }));
}

//function for fancy box
var fctFancy = function(data) {
    $('.fancybox').fancybox({
        'titleShow': false,
        'autoscale': true,
        'width': '600',
        'height': '700',
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'content': data
    });
}


//function for genrate autocomplete
function generateAutocomplete(that) {

    var params = new Array();
    var url = $(that).attr('data-url');
    var typed = $(that).attr('data-typed');
    if(typed != undefined ){    
        if($("#"+typed.length>0)){
             url  += '?typed='+ $('#'+typed).val(); 
        }  
    }
    var obj = $(that);
    
    //seting params 
    params['url'] = url;
    params['obj'] = obj;
    var limit;
    
    params['autocomplete'] = new Array();
    if (!$(that).attr('data-limit'))
        limit = 5;
    else
        limit = $(that).attr('data-limit');
    
    params['autocomplete']['limit'] = limit;
    if ($(obj).next().attr('class') === "dynamic")
        $(obj).next().attr('value', '');
    $(obj).autocomplete({
        source: function(request, response) {
            params['autocomplete']['request'] = request;
            params['autocomplete']['response'] = response;
            ajax('', params);
        },
        select: function(event, ui) {
            if (ui.item.id) {
                $('.ui-helper-hidden-accessible').css('display', 'none');
                var name = $(obj).attr('name');
                $(obj).attr('name', '');
               
                if ($(obj).next().attr('class') != 'dynamic')
                    $(obj).after('<input class="dynamic" type="hidden" id= "'+name+'" name="' + name + '" value="' + ui.item.id + '"/>');
                else
                    $(obj).next().attr('value', ui.item.id);
            }
        }
    });

    String.prototype.replaceAt = function(index, chr) {
        return this.substr(0, index) + "<span style='font-weight:bold;'>" + chr + "</span>";
    }

    $.ui.autocomplete.prototype._renderItem = function(ul, item) {
        this.term = this.term.toLowerCase();
        var resultStr = item.label.toLowerCase();
        var t = "";
        while (resultStr.indexOf(this.term) != -1) {
            var index = resultStr.indexOf(this.term);
            t = t + item.label.replaceAt(index, item.label.slice(index, index + this.term.length));
            resultStr = resultStr.substr(index + this.term.length);
            item.label = item.label.substr(index + this.term.length);
        }
        return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + t + item.label + "</a>")
                .appendTo(ul);

    };
}
//function for genrate autocomplete
function generateAutocompleteCommon(that,type) {

    var params = new Array();
    var url = 'list/autocomplete/'+type;    
    var obj = $(that);    
    //seting params 
    params['url'] = url;
    params['obj'] = obj;
    var limit=10;
    
    params['autocomplete'] = new Array();      
    params['autocomplete']['limit'] = limit;
    if ($(obj).next().attr('class') === "dynamic")
        $(obj).next().attr('value', '');
    $(obj).autocomplete({
        source: function(request, response) {
            params['autocomplete']['request'] = request;
            params['autocomplete']['response'] = response;
            ajax('', params);
        },
        select: function(event, ui) {
            if (ui.item.id) {
                $('.ui-helper-hidden-accessible').css('display', 'none');
                var name = $(obj).attr('name');
                $(obj).attr('name', '');
               
                if ($(obj).next().attr('class') != 'dynamic')
                    $(obj).after('<input class="dynamic" type="hidden" id= "'+name+'" name="' + name + '" value="' + ui.item.id + '"/>');
                else
                    $(obj).next().attr('value', ui.item.id);
            }
        }
    });

    String.prototype.replaceAt = function(index, chr) {
        return this.substr(0, index) + "<span style='font-weight:bold;'>" + chr + "</span>";
    }

    $.ui.autocomplete.prototype._renderItem = function(ul, item) {
        this.term = this.term.toLowerCase();
        var resultStr = item.label.toLowerCase();
        var t = "";
        while (resultStr.indexOf(this.term) != -1) {
            var index = resultStr.indexOf(this.term);
            t = t + item.label.replaceAt(index, item.label.slice(index, index + this.term.length));
            resultStr = resultStr.substr(index + this.term.length);
            item.label = item.label.substr(index + this.term.length);
        }
        return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + t + item.label + "</a>")
                .appendTo(ul);

    };
}
