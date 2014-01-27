
//If allow media type yes then add form show otherwise hide
$('.input-block .media_allow_element input[type="radio"]').bind('click',function(){
    if($(this).val()==1)
        $('#media_type_area').show();
    else
        $('#media_type_area').hide(); 
});


//If allow media type yes then add form show otherwise hide
$('.input-block .media_allow_element input[type="radio"]').each(function(){
    if ($(this).is(':checked')) {
        if($(this).val()==1){
            $('#media_type_area').show();
        }else{
            $('#media_type_area').hide(); 
        }
   }
}); 

//onchange media element
$('.input-block .media_element').change(function(){
    var media = $(this).val();
    //Media is embedded
    if(media == 'embed'){
        $('#media_type_browse').hide();
        $('#media_type_embed').show();
    }else if(media == 'video' || media == 'image'){
        $('.input-block input[type="file"]').attr('data-type',media);
        $('#media_type_embed').hide();
        $('#media_type_browse').show(); 
        $('#attachment_file').html('');
        $('#media_file').val('');
    }else{
        $('.input-block input[type="file"]').attr('data-type',media);
        $('#media_type_embed').hide();
        $('#media_type_browse').hide();    
    }
});

//If selected media 
if($('.input-block .media_element option:selected').val()!=''){
    var media = $('.input-block .media_element option:selected').val();
     //Media is embedded
    if(media == 'embed'){
        $('#media_type_browse').hide();
        $('#media_type_embed').show();
    }else if(media == 'video' || media == 'image'){
        $('.input-block input[type="file"]').attr('data-type',media);
        $('#media_type_embed').hide();
        $('#media_type_browse').show();                
    }else{
        $('.input-block input[type="file"]').attr('data-type',media);
        $('#media_type_embed').hide();
        $('#media_type_browse').hide();
    }
}
