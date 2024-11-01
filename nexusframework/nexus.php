<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

wp_enqueue_style( 'spost-admin-general-style', NEXUS_FRAMEWK_URL . '/css/general.css', false, SPOST_VERSION );

function spost_admin_actions() {

  $one = add_menu_page(__('Sidebar Post','spost'),__('Sidebar Post','spost'),'switch_themes', "spost", "spost_admin_fx", SPOST_PLUGIN_URL.'/images/post-it.svg', '5.4');
	$mainsettings = add_submenu_page('spost', __('Advanced Settings','spost'), __('Advanced Settings','spost'), 'switch_themes', 'spost-main-settings', 'spost_other_admin_options' );
	$uninstallspost = add_submenu_page('spost', __('Uninstall','spost'), __('Uninstall','spost'), 'switch_themes', 'spost-uninstall', 'spost_uninstall_fx' );
	$loginattemps = add_submenu_page('spost', __('Documentation','spost'), __('Documentation','spost'), 'switch_themes', 'spost-documentation', 'spost_documentation_fx' );

add_action( 'admin_print_styles-' . $one, 'spost_plugin_enqueue_scripts' );
add_action( 'admin_print_styles-' . $mainsettings, 'spost_plugin_enqueue_scripts' );
add_action( 'admin_print_styles-' . $loginattemps, 'spost_plugin_enqueue_scripts' );
add_action( 'admin_print_styles-' . $uninstallspost, 'spost_plugin_enqueue_scripts' );

add_action( 'admin_head-'. $one, 'spost_header_admin' );
add_action( 'admin_head-'. $mainsettings, 'spost_header_admin' );
add_action( 'admin_head-'. $loginattemps, 'spost_header_admin' );
add_action( 'admin_head-'. $uninstallspost, 'spost_header_admin' );

}add_action('admin_menu', 'spost_admin_actions');

function spost_plugin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'spost-admin-style', NEXUS_FRAMEWK_URL . '/css/spost-admin.css', false, SPOST_VERSION );
	wp_enqueue_script( 'spost-admin-js', NEXUS_FRAMEWK_URL . '/js/spost-admin.js', false, SPOST_VERSION );
	wp_enqueue_script( 'spost-colorPicker-js',NEXUS_FRAMEWK_URL . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
	  // first check that $hook_suffix is appropriate for your admin page
  wp_enqueue_style( 'wp-color-picker' );
}
function spost_admin_fx(){  ?>
<div id="spostContainer">
<form id="spostForm1" enctype="multipart/form-data" method="post" action="">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Main Settings','spost'); ?></h1>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_allowed_level">
    	<div class="inputTitle"><?php _e('Allow Posting','spost'); ?>?</div>
        <div class="inputContent">
        <select id="spost_allowed_level" name="spost_allowed_level">
        	<option value="everybody" <?php if(get_option('spost_allowed_level')=='everybody'){echo 'selected="selected"';} ?>><?php _e('Everybody','spost'); ?></option>
            <option value="loggedin" <?php if(get_option('spost_allowed_level')=='loggedin'){echo 'selected="selected"';} ?>><?php _e('Only logged in','spost'); ?></option>
            <option value="none" <?php if(get_option('spost_allowed_level')=='none'){echo 'selected="selected"';} ?>><?php _e('Do NOT allow','spost'); ?></option>
        </select></div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_require_email">
    	<div class="inputTitle"><?php _e('Require Email to post','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_require_email')=='1' || get_option('spost_require_email')==''){echo 'yes';}else{echo 'no';} ?>" data-toggleyesno="spost_require_email"><?php if(get_option('spost_require_email')=='1' || get_option('spost_require_email')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_require_email" id="spost_require_email" value="<?php echo get_option('spost_require_email'); ?>" />
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_posting_posttype">
    	<div class="inputTitle"><?php _e('Target Post type','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_posttype" id="spost_posting_posttype">
        <?php

			$post_types = get_post_types( '', 'names' );

			foreach ( $post_types as $post_type ) {
			if(get_option('spost_posting_posttype')==$post_type){
			   echo '<option value="' . $post_type . '" selected="selected">' . $post_type . '</option>';
				}else{
			   echo '<option value="' . $post_type . '">' . $post_type . '</option>';
				}
			}

		 ?>
         </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_posting_taxonomy">
    	<div class="inputTitle"><?php _e('Target Taxonomy','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_taxonomy" id="spost_posting_taxonomy">
			<?php
            $taxonomies = get_taxonomies();
            foreach ( $taxonomies as $taxonomy ) {
			if(get_option('spost_posting_taxonomy')==$taxonomy){
			   echo '<option value="' . $post_type . '" selected="selected">' . $taxonomy . '</option>';
				}else{
			   echo '<option value="' . $taxonomy . '">' . $taxonomy . '</option>';
				}
            }
            ?>
        </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_status">
    	<div class="inputTitle"><?php _e('Default Post Status','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_posting_status" id="spost_posting_status">
			<option value="draft" <?php if(get_option('spost_posting_status')=='draft'){echo 'selected="selected"';} ?>><?php _e('Draft','spost'); ?></option>
            <option value="publish" <?php if(get_option('spost_posting_status')=='publish'){echo 'selected="selected"';} ?>><?php _e('Publish','spost'); ?></option>

        </select>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_default_user">
    	<div class="inputTitle"><?php _e('Default Post user','spost'); ?></div>
        <div class="inputContent">
        <?php if(get_option('spost_posting_default_user')==''){
			$user = get_user_by('email', get_option('admin_email') );
			$selected_user = $user->ID;
			}else{
			$selected_user = get_option('spost_posting_default_user');
			}

		wp_dropdown_users(array('name' => 'spost_posting_default_user',
									  'show'=> 'display_name',
									  'orderby'                 => 'display_name',
									  'id'                      => 'spost_posting_default_user',
									  'selected'                => $selected_user,)); ?>
        </div>
        <div class="spostLegend"><?php _e('Posts from unregistred users will be assigned to this user','spost'); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>

<div class="spostRow" data-spostid="spost_posting_taxonomy">
    <input type="submit" name="save_settings" id="save_settings" style="margin-left:auto; margin-right:auto; border: none; background:#333; color: #FFF; border-radius: 15px; padding:5px 25px; cursor: pointer;" value="<?php _e('Save Settings','spost'); ?>"/>	 <div id="spostMessages"></div>
</div><div class="clearall"></div>
</form>

</div><div class="clearall"></div>
<?php }
function spost_header_admin(){?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	/************************************* AJAX AND FORM STUFF ******************************/
function toggle_yesno(){
	var yesText = '<?php _e('Yes','spost'); ?>';
	var noText = '<?php _e('No','spost'); ?>';
	$('.inputContent div').on("click", function(event){
		var theElement = $(this).closest( ".spostRow" ).attr( "data-spostid" );
		var yesno = $(this).attr("class");
		if(yesno=='yes'){
			$(this).removeClass('yes');
			$(this).addClass('no');
			$('#'+theElement).val('0');
			$(this).html(noText);

		}else if(yesno=='no'){
			$(this).removeClass('no');
			$(this).addClass('yes');
			$('#'+theElement).val('1');
			$(this).html(yesText);
		}

	})
}
toggle_yesno();

	$('#save_settings').on("click",function(event){
		event.preventDefault();

	  console.log( $(  "#spostForm1" ).serialize() ); //serialize form on client side
	  var pdata = {
		 action: "spostSaveSettings",
		 postdata: $("#spostForm1").serialize(),
	  }
	  $.post(ajaxurl , pdata, function( response ) {
		//$("#spostMessage").html(data).show("slow").delay(5000).hide("slow");
		//$("#SidebarPost").hide();
		 var obj = JSON.parse(response);
			if(obj.updatestatus=='success'){
				$('#spostMessages').fadeIn().delay(5000).hide("slow");;
				$('#spostMessages').html(obj.updatecontext);
				$('#spostMessages').addClass('success');
			}else{
				$('#spostMessages').fadeIn().delay(5000).hide("slow");;
				$('#spostMessages').html(obj.updatecontext);
				$('#spostMessages').addClass('failure');
			}
	  });

	})
 $('#spost_custom_login_page option').attr('disabled','disabled');
 $('#spost_custom_login_page').attr('disabled','disabled');
  $('#uninstallit1').on("click",function(event){

	 event.preventDefault();
	 $('#uninstallit').removeAttr('disabled');
	 $('#uninstallit').addClass('finalRed');
	 $(this).attr('disabled','disabled');


 });

	/************************************* /AJAX AND FORM STUFF ******************************/

});
</script>
<?php }
