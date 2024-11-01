/*
	spost.js

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	Copyright: (c) 2014 JANVIER Manishimwe http://www.janvierdesigns.com
*/

jQuery(document).ready(function($) {
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
	var AJAX_URL = $("#AjaxUrl").val();
	var user_email = $("#spost_poster_email").val();
	var spost_title = $("#spost_title").val();
	var spost_content = $("#spost_content").val();
	var spost_poster_name = $("#spost_poster_name").val();

	/************************************* AJAX AND FORM STUFF ******************************/
	$("#SidebarPost").on( "submit", function( event ) {
	event.preventDefault();

        $('.loading-messages').removeClass('sphidden').html('<img src="'+spostUrl+'/images/loading.gif" />');
        // alert($("#SidebarPost").serialize());
    	  var pdata = {
    		 action: "spostPublish",
    		 curret_user: $("#current_user_id").val(),
         theForm: $("#SidebarPost").serialize(),
    	  }
    	  $.post(AJAX_URL, pdata, function( data ) {
          var msgType = data.msg_type;
          var msg = data.message;
          $('.loading-messages').addClass(msgType).html('<img src="'+spostUrl+'/images/'+msgType+'.png" /><div class="reaction-content">'+msg+'</div>');
    		    // $("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
          $('#SidebarPost input').prop('disabled', true);
    	  });

	});
	/************************************* /AJAX AND FORM STUFF ******************************/

	/************************************* LOGIN/REGISTER SLIDING ******************************/
  $('.auth-tabs ul li').on("click", function(event){
    event.preventDefault();
    if(!$(this).hasClass('chosen')){
      $('.auth-tabs ul li').removeClass('chosen');
      $(this).addClass('chosen');
      var corresponding = $(this).data('corresponding');
      $('.auth-entity').addClass('sposthidden');
      $('.'+corresponding+'-box').removeClass('sposthidden');
    }
  });
  $('.spost-authenticate').on("submit",function(event){
    event.preventDefault();
    var formAction = $(this).data("action");
    var formSerialized = $(this).serialize();
    var returnTo   = $('#returnTo').val();



    /********************  LOGGING IN *************************/
    if(formAction=="login"){
      var authData = {
        action    : "spostAuth",
        theForm   : formSerialized,
        auth_type : "login",
        return_to : returnTo,
      }
      $('button[name="signin-btn"] i').remove();
      $('button[name="signin-btn"]').prepend('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

      $.post(AJAX_URL, authData, function( response ) {
        var msgType = response.msg_type;
        var msg = response.message;
        $('.loading-messages').addClass(msgType).html('<img src="'+spostUrl+'/images/'+msgType+'.png" /><div class="reaction-content">'+msg+'</div>').removeClass('sposthidden');
          // $("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
        $('#SidebarPost input').prop('disabled', true);
        $('button[name="signin-btn"] i').remove();
        $('button[name="signin-btn"]').prepend('<i class="fa fa-lock"></i>');
        location.reload();

      });
    }
    /********************  REGISTER IN *************************/
    if(formAction=="register"){
      var authData = {
        action  : "spostAuth",
        theForm : formSerialized,
        auth_type: "register",
        return_to : returnTo,
      }
      $('button[name="register-btn"] i').remove();
      $('button[name="register-btn"]').prepend('<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>');

      $.post(AJAX_URL, authData, function( response ) {
        // alert(JSON.stringify(response));
        var msgType = response.msg_type;
        var msg = response.message;
        $('.loading-messages').addClass(msgType).html('<img src="'+spostUrl+'/images/'+msgType+'.png" /><div class="reaction-content">'+msg+'</div>').removeClass('sposthidden');
          // $("#spostMessage").registerhtml(data).show("slow").delay(5000).hide("slow");
          if(msgType=="error"){
            $('#SidebarPost input').prop('disabled', true);
          }else{
            // location.reload();
          }
          $('button[name="register-btn"] i').remove();
          $('button[name="register-btn"]').prepend('<i class="fa fa-lock"></i>');
      });
    }
  });
  $(document).on("click", ".loading-messages.error img",function(e){
    $(this).closest('.loading-messages').addClass('sposthidden');
  });
	/************************************* /LOGIN/REGISTER SLIDING  ****************************/
});
