/** Login form validation **/
$('#login_form').submit(function(){
   var x = 0;
   $('.valid_alert').slideUp();
   $('.form-control').removeClass('red_border');
   if($('#username').val() == ''){
   		x = 1;
   		$('#username').addClass('red_border');
   		$('#username_req').slideDown();
   }
   if($('#password').val() == ''){
   	    x = 1;
   	    $('#password').addClass('red_border');
   	    $('#password_req').slideDown();
   }
   if(x != 0){
   	return false;
   }
});

/** main menu form validation **/
$('#main_menu').submit(function(){
   var x = 0;
   $('input[name=title]').removeClass('red_border');
   if($('input[name=title]').val() == ''){
     $('input[name=title]').addClass('red_border');
     x = 1;
   }
   if($('input[name=ordering]').val() == ''){
     $('input[name=ordering]').addClass('red_border');
     x = 1;
   }
     

   if(x > 0){
    return false;
   }
});

/** main menus show & hide **/
$('#hide_menu').click(function(){
   $('.main_menu').hide(); 
   $('.meta_div').show();
});
$('#show_menu').click(function(){
   $('.main_menu').show();
   $('.meta_div').hide(); 
});

$(document).on('click', '#all', function(){
    if($(this).is(":checked"))
    {
      $( ":checkbox" ).prop('checked', true);
    }else
    {
     $( ":checkbox" ).prop('checked', false);
    }  
});
$('#select_all').click(function(){
    if($(this).attr('st') == 'sel'){
        $('.check_delete').each(function( index ) {       // add all to add image form
          $('<input>').attr({
           type: 'hidden',
           name: 'img_sel[]',
           id: $(this).val(),
           value: $(this).val()
           }).appendTo($('#sel_imgs')); 
        });
            
        
        $('.check_delete').prop('checked', true);
        $(this).html('الغاء تحديد كل الصور');
        $(this).attr('st','dsel');
    }else{
         $('.check_delete').each(function( index ) {              // remove all
             $('#'+$(this).val()).remove();
         });  
        $('.check_delete').prop('checked', false);
        $(this).html('تحديد كل الصور');
        $(this).attr('st','sel');
    }  
    return false;
});

/***** gallery **/
$('.check_delete').change(function(){
    var x = $(this).val();
    if($(this).is(":checked")){
      $('<input>').attr({
      type: 'hidden',
      name: 'img_sel[]',
      id: x,
      value: x
      }).appendTo($('#sel_imgs'));  
    }else{
        
      $('#'+x).remove();
    }
    
});

/** $($('#sel_imgs')).submit(function(){
    window.close(); 
}); **/

$('input[type=checkbox][name=ratio]').change(function(){
    if($(this).is(":checked"))
    {
       $('.img_width').val('');
       $('.img_width').prop( "disabled", true );
    }else{
        $('.img_width').prop( "disabled", false );
    } 
});

$('input[type=checkbox][name=thumb]').change(function(){
   $('.thumb_hide').slideToggle(); 
});

$('#catid_sel').change(function(){
    $('input[name=sel_catid]').val($(this).val());
    $('input[name=sel_subcatid]').val($('input[name=sub_catid]').val());
    $('#sel_catid').submit();
});

$('#subcatid_sel').change(function(){
    $('input[name=sel_catid]').val($('#catid_sel').val());
    $('input[name=sel_subcatid]').val($(this).val());
    $('#sel_catid').submit();
});


$('#delete_form').submit(function(){

    var x = 0;
   $('.check_delete').each(function( index ) {              
    if($(this).is(":checked")){
      x = 1;
    }
    
 }); 
   if(x < 1){
     alert('يجب عليك اختيار الصور المراد حذفها');
     return false;
    }
});

  $(document).ready(function() {     
   
     
    });

/*********** menus active **/
$('.menu_item').click(function(){
    $('.menu_item').removeClass('active');
    $(this).addClass('active');
});
$('.cat_menu').click(function(){
    
    $('.cat_menu').removeClass('active_cat');
    $(this).addClass('active_cat');
});

function get_cats(){
  $('<input>').attr({
           type: 'hidden',
           name: 'sel_cat',
           value: $('#catid_sel').val()
           }).appendTo($('#delete_form')); 
    $('<input>').attr({
           type: 'hidden',
           name: 'sel_subcat',
           value: $('#subcatid_sel').val()
           }).appendTo($('#delete_form'));  
    
}
$('#del_all').click(function(){
   $('<input>').attr({
           type: 'hidden',
           name: 'sel_cat',
           value: $('#catid_sel').val()
           }).appendTo($('#delete_form')); 
    $('<input>').attr({
           type: 'hidden',
           name: 'sel_subcat',
           value: $('#subcatid_sel').val()
           }).appendTo($('#delete_form'));
     
});


$('#add_port_tab').click(function(){
    $('#main_menu').append('<input type="hidden" name="add_tab" value="'+$('.port_tab').length+'">');
    $('#main_menu').submit();
});


$('.validate-form').submit(function(e){
    hasError = false;
    $('input[mandatory]').removeClass('mandatory');
    $('textarea[mandatory]').removeClass('mandatory');
    $('input[mandatory],textarea[mandatory]',$(this)).each(function( index ){
        if($(this).val() == ''){
            hasError = true;
            $(this).addClass('mandatory');
        }
    });
    $('input[email]',$(this)).each(function( index ){
        if(!validateEmail($(this).val())){
            hasError = true;
            $(this).addClass('mandatory').attr('title','not email');
        }
    });

    if(hasError){
        e.preventDefault();
    }
});

function validateEmail(email)
{
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(email);
}