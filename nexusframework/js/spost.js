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
	$("#send_post").on( "click", function( event ) {
	event.preventDefault();
	  console.log( $(  "#SidebarPost" ).serialize() ); //serialize form on client side
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

	/************************************* /LOGIN/REGISTER SLIDING  ****************************/

});
