
function slide(cls){
    $('.hidden_'+cls).slideToggle('medium');
}
function show(cls){
    $('.hidden_'+cls).show('medium');
}
function fade(cls){
    $('.hidden_'+cls).fadeIn('medium');
}

//Function adding items into list
function addListItems(data,params){
    
    var list_div = 'auto_'+params['type']+'_list';
    var auto_id =  data ? data.id :  params['auto_id'];
    
    var exist = 0;
   
    $('#'+list_div + ' input[name^="'+params['type']+'[]"]').each(function(){        
        var value = $(this).val();       
        if(value == auto_id)
           exist = 1;
    });
     
    if(exist==0){
        if(auto_id){
            var new_div = '<div class="dynamic" id="'+ params['type'] +'_div_'+auto_id+'"><b>'+params['count']+'</b>&nbsp;&nbsp;'+params['text']+'&nbsp;&nbsp;<a href="javascript:deleteListItem('+auto_id+',\''+ params['type'] +'\');">supprimer</a></div>';
            new_div += '<input type="hidden" name="'+params['type']+'[]" id="'+ params['type'] +'_'+auto_id+'" value="'+auto_id+'">';
        }else{
            alert('Please select form autocomplete list');
        }
    }else{
        alert('Already Exists');
    }
    
    //add goal into goal list
    $('#'+list_div).append(new_div); 
    
}


function deleteList(iid,type){
    $('#'+type+'_div_'+iid).remove();
}
//to genrate input list 
function generateInputList(that){
    
    var input_text_id = $(that).attr('data-text-input-id');
    var input_hidd_id = $(that).attr('data-hidd-input-id');
    var auto_id = '';
   
    
    //getting data from attributes
    var list_div = 'auto_'+$(that).attr('data-type')+'_list';    
    var data_url = $(that).attr('data-add-url');
    var limit = $(that).attr('data-limit');
    var input_value = $("#"+input_text_id).val(); 
    var count = $("#"+list_div +" div").length;


    //if no goal entered
    if(input_value==''){
        alert("Please fill input value.");
    }else{
        if(count < limit){
            var params = new Array();
             //For getting id of autocomplete item
             
            if($('#'+input_hidd_id).length==1){
               auto_id = $('#'+input_hidd_id).val();
            }
            
            params['id'] = input_text_id; 
            params['auto_id'] = auto_id;
            params['type'] = $(that).attr('data-type');
            params['count'] = count+1;
            params['text']=$("#"+input_text_id).val(); ;
            
            //if data not selected from autocomplete
            if(auto_id){
                 addListItems('', params);              
            }else{              
                 params['url'] = data_url;
                 params['data'] = 'name='+input_value;
                 params['dataType'] = 'json';
                 params['action'] = addListItems;
                 
                 //callled ajax function from ajax.js
                 ajax('' , params);
            }
            
            $('#'+input_text_id).val(""); 
         
        }else{
            alert("You can not add more than "+limit+" values");
        }
    }
}


//function to delete items from particular list
function deleteListItem(id, type){
    
    var input_id = $("[data-type="+type+"]").attr('data-input-id');
    var list_div = input_id+'_list';
    var delconfirm =confirm('Are you sure?');
    
    if(delconfirm){
        $('#'+type+'_div_'+id).remove();
        $('#'+type+'_'+id).remove();
        var i=1; 
        $('#'+list_div+ '.dynamic b').each(function(){
            $(this).html(i);
            i++;
        });
    }
}
 
//bof code for Adding additional annexes and diplome options   

//function for validating zip code
function validate_zip_code(type){
        var strValidChars = '0123456789';
        var zipcode_val = $('#'+type+'_zip_code_new').val();
        var flag = 1;
        
        //zipcode should be only 5 digit long
        if(zipcode_val.length < 5 || zipcode_val.length > 5){
            flag=0;
        }
        //if not numeric
        for(var i=0;i<zipcode_val.length;i++){
            strChar = zipcode_val.charAt(i);
                if (strValidChars.indexOf(strChar) == -1){
                   flag=0;
                }
        }
       
        return flag;
        
}

//Function for showing and hiding diplome options and annexes div
function showHideOptionDiv(that , type){
    var area = $(that).attr('data-type');
    var option_div = $(that).attr('data-option-div-id');
    var list_type = $(that).attr('data-list-type');
    
    var show_by_checkbox = $(that).attr('data-show_div_id');
    
    //if click event performed on checkbox then show hide a div by id
    if(show_by_checkbox!=''){
        var check_that = $(that).is(':checked');
        if(check_that){
            $('#'+show_by_checkbox).show();
        }else{
            $('#'+show_by_checkbox).hide();
        }
    }
    
    //this is for annexes and diplome option
    if(option_div != ''){  
            if(type === 'show'){
                $('#'+option_div).slideDown();
                $('#'+option_div+' .add_option').show();
                $('#'+option_div+' #modifier_'+list_type).val('');
                $('#'+option_div+' .modifier').hide();
            }else{
                 $('#'+option_div).slideUp(); 
            }
    }
    
    if(list_type != '' ){
        //if adding diplome options
        if(list_type == 'option'){
             $('#'+type+'_option_name').val('');
             $('#'+type+'_option_programme').val('');
        }else{
            //else annexes

             //Reset values of all fields of annexes
            $('#'+option_div+' input[id^="'+area+'_"]').each(function(){
               $(this).val('');
            });
        }   
    }
}

$('#total_annex').val($("#annexes_list div").length);

//adding or modifying list of diploma option and annex
function insert_update_option(that){
    
    var option_list = $(that).attr('data-option-list');    
    var option_div = $(that).attr('data-option-div-id');   
    var type = $(that).attr('data-type');
    var button_type = $(that).attr('data-button');
    var list_type = $(that).attr('data-list-type');
   
    //if adding new annex
    if( button_type === 'add'){
        if(type=='Training'){
          var option_count =  $('#option_count').val();
        }else{
          var option_count =  $('#'+type+'_count').val();
        }
        option_count++;
        
    }else{
        var data_option = $(that).attr('data-option');
        var option_count = $('#'+data_option).val();  
    }
    
    if(list_type==='annex'){
         if($('#'+type+'_address_new').val()==''){
           alert('Please add address');
           return false;
        }
        else if($('#'+type+'_zip_code_new').val()==''){
           alert('Please add postal code');
           return false;
        }else if($('#'+type+'_std_place_city_id_new').val()=='' && $('#'+type+'_auto_std_place_city_id_new').val()==''){
           alert('Please add city');
           return false;
        }else if($('#'+type+'_std_place_city_id_new').val()!='' && $('#'+type+'_auto_std_place_city_id_new').val()==''){
           alert('Invalid city');
           return false;
        
	}else{
              //if not valid postal code  
              if(!validate_zip_code(type)){
                  alert('Postal code should have only 5 digits eg. 24543');
                  return false;
              } 
              
                
            //getting values corresponding to all fields
             var address = $('#'+type+'_address_new').val();
             var address_complement = $('#'+type+'_address_complement_new').val();
             var zip_code = $('#'+type+'_zip_code_new').val();
             var city = $('#'+type+'_auto_std_place_city_id_new').val();
             var city_name = $('#'+type+'_std_place_city_id_new').val();
             var accessvility = $('#'+type+'_accessibility_new').val();
             
             
             
             if( button_type === 'add'){
                //adding div corresponding each field 
                var new_div = '<div class="dynamic" id="annex_div_'+option_count+'"><span>'+address+'</span>&nbsp;&nbsp;<a href="javascript:modifyOption('+option_count+',\''+option_div+'\',\''+type+'\',\''+list_type+'\');">Modifier</a>&nbsp;&nbsp;<a href="javascript:deleteOption('+option_count+',\''+list_type+'\');">supprimer</a></div>';
                new_div += '<input type="hidden" name="places_extra[address]['+option_count+']" id="addresses_'+option_count+'" value="'+address+'">';
                new_div += '<input type="hidden" name="places_extra[complement]['+option_count+']" id="acomplements_'+option_count+'" value="'+address_complement+'">';
                new_div += '<input type="hidden" name="places_extra[zip_code]['+option_count+']" id="zipcodes_'+option_count+'" value="'+zip_code+'">';
                new_div += '<input type="hidden" name="places_extra[ville]['+option_count+']" id="villess_'+option_count+'" value="'+city+'">';
                new_div += '<input type="hidden" name="places_extra[ville_name]['+option_count+']" id="ville_name_'+option_count+'" value="'+city_name+'" >';
                new_div += '<input type="hidden" name="places_extra[accessibility]['+option_count+']" id="accesses_'+option_count+'" value="'+accessvility+'">';
               
                //adding into list
                $('#'+option_list).append(new_div);            
                $('#annex_count').val(option_count);
                $('#total_annex').val($("#"+option_list+" div").length);
                
            }else{
                //Adding new values
                $('#annex_div_'+option_count+' span').html(address);
                $('#addresses_'+option_count).val(address);
                $('#acomplements_'+option_count).val(address_complement);
                $('#zipcodes_'+option_count).val(zip_code);
                $('#villess_'+option_count).val(city);
                $('#ville_name_'+option_count).val(city_name);
                $('#accesses_'+option_count).val(accessvility);
               
             }
             
              //Reset values of all fields of annexes
                $('#'+option_div+' input[id^="'+type+'_"]').each(function(){
                   $(this).val('');
                });

                $('#'+option_div).slideToggle();
         }         
    }else{
            if($('#'+type+'_option_name').val()===''){
                       alert('Please add option name');
                       return false;
            }else{

                //getting fields values
                 var option_name = $('#'+type+'_option_name').val();
                 var programme = $('#'+type+'_option_programee').val();

                //if add button pressed
                 if( button_type === 'add'){
                        var new_div = '<div class="dynamic" id="option_div_'+option_count+'"><span>'+option_name+'</span>&nbsp;&nbsp;<a href="javascript:modifyOption('+option_count+',\''+option_div+'\',\''+type+'\',\''+list_type+'\');">Modifier</a>&nbsp;&nbsp;<a href="javascript:deleteOption('+option_count+',\''+list_type+'\');">supprimer</a></div>';
                        new_div += '<input type="hidden" name="option['+option_count+']" id="option_'+option_count+'" value="'+option_name+'">';
                        new_div += '<input type="hidden" name="programee['+option_count+']" id="programee_'+option_count+'" value="'+programme+'">';
                        new_div += '<input id="option_programee'+option_count+'" type="hidden" value="'+option_count+'" name="option_programee[]">';
                     
                        $('#'+option_list).append(new_div);

                 }else{
                 //if modify button pressed  
                        $('#option_div_'+option_count+' span').html(option_name);
                        $('#option_'+option_count).val(option_name);
                        $('#programee_'+option_count).val(programme);

                 }       

                    $('#'+type+'_option_name').val('');
                    $('#'+type+'_option_programee').val('');
                    
                    $('#option_count').val(option_count);
                    $('#total_option').val($("#"+option_list+" div").length);
                
                    $('#'+option_div).slideToggle();
             }
    }            
                
}

//Modify diploma option and annex
function modifyOption(id, odiv, type, list_type){
    if(list_type === 'annex'){    
        //Showing annexes fields with filled values
        $('#'+odiv).slideDown();

        $('#'+type+'_address_new').val($('#addresses_'+id).val());
        $('#'+type+'_address_complement_new').val($('#acomplements_'+id).val());
        $('#'+type+'_zip_code_new').val($('#zipcodes_'+id).val());
        $('#'+type+'_std_place_city_id_new').val($('#ville_name_'+id).val());
        $('#'+type+'_auto_std_place_city_id_new').val($('#villess_'+id).val());
        $('#'+type+'_accessibility_new').val($('#accesses_'+id).val());
       
    }else{
        $('#'+odiv).slideDown();
        $('#'+type+'_option_name').val($('#option_'+id).val());
        $('#'+type+'_option_programee').val($('#programee_'+id).val());
       
    }
    
     $('#'+odiv+' .add_option').hide();
     $('#'+odiv+' #modifier_'+list_type).val(id);
     $('#'+odiv+' .modifier').show();
}


//delete option from form
function deleteOption(id,list_type){
   //For deleting annexes list 
   if(list_type === 'annex'){
        if($('#annexx_'+id).length>0){
               if($('#dannex').val()==''){
                    $('#dannex').val($('#annexx_'+id).val());
               }else{
                    $('#dannex').val($('#dannex').val()+','+$('#annexx_'+id).val());
               }
               $('#annexx_'+id).remove();
        }

        //removing all div id for deleted annex
        $('#annex_div_'+id).remove();
        $('#addresses_'+id).remove();
        $('#acomplements_'+id).remove();
        $('#zipcodes_'+id).remove();    
        $('#villess_'+id).remove();
        $('#ville_name_'+id).remove();
        $('#accesses_'+id).remove();
   }else{ 
       
        //for deleting siploma options
        $('#option_div_'+id).remove();
        $('#option_'+id).remove();
        $('#programee_'+id).remove();
        $('#option_programee_'+id).remove();
   }
}

//end of code 

//code for if any checkbox is already check then display a div if need to show
  $(document).ready(function(){
            $('input[type="checkbox"]').each(function(){
                  if($(this).attr('data-show_div_id')){
                      var check_that = $(this).is(':checked');
                      if(check_that){
                        $('#'+$(this).attr('data-show_div_id')).show();
                      }else{
                        $('#'+$(this).attr('data-show_div_id')).hide();
                      }
                  }            
           }); 
});

//this function do empty the fields given by coma seperated
function emptyFields(that){
    var source_id = $(that).attr('id');
    //list of fields to be emptied should given coma seperated
    var empty_to_fields = ($('#'+source_id).attr('empty_fields')).split(',');
    
    //setting all value blank
    for(var i=0 ; i< empty_to_fields.length ; i++){
        $('#'+empty_to_fields[i]).val('');
    }
}

