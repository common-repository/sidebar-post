<?php
/*
Plugin Name: Sidebar Posting
Plugin URI: http://www.codiiv.com
Description: This plugin places a posting form in the sidebar
Version: 4.0
Author: Janvier M @ CODIIV
Author URI: http://www.codiiv.com
Text Domain: spost

*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function spost_loaded() {
	do_action( 'spost_loaded' );
}
add_action( 'plugins_loaded', 'spost_loaded', 20 );
global $shortname;
$shortname = "spost_";
define('SPOST_VERSION','4.0');
define('SPOST_DATABASE_VERSION','4.0');
define('SPOST_BUILD','06112017');
define('SOFTWARE_NAME',__('Sidebar Post','spost'));
define( 'SPOST_PLUGIN_DIR', WP_PLUGIN_DIR . '/sidebar-post' );
define( 'SPOST_PLUGIN_URL', WP_PLUGIN_URL . '/sidebar-post' );

define( 'NEXUS_FRAMEWK_DIR', SPOST_PLUGIN_DIR . '/nexusframework' );
define( 'NEXUS_FRAMEWK_URL', SPOST_PLUGIN_URL . '/nexusframework' );
define( 'SPOST_UNINSTALL_CODE','AKDJEMSL');
$site_home = get_option('home');
include("htmls.php");
include("auth.php");
// require_once("tables.php");
include(NEXUS_FRAMEWK_DIR.'/nexus.php');
function spost_core_get_table_prefix() {
	global $wpdb;

	return apply_filters( 'spost_core_get_table_prefix', $wpdb->base_prefix );
}
function spost_loader_activate() {

	global $wpdb;
	require( dirname( __FILE__ ) . '/tables.php' );
	spost_core_install_matrix();
	do_action( 'spost_loader_activate' );
}
register_activation_hook( 'sidebar-post/sidebar_post.php', 'spost_loader_activate' );

function update_db_options(){
	update_option("spost_db_version",SPOST_VERSION);
	update_option("spost_db_build",SPOST_BUILD);
}add_action("spost_loader_activate", "update_db_options");

function spost_theme_setup(){
    //load_theme_textdomain('spost', SPOST_PLUGIN_URL . '/languages');
	load_plugin_textdomain( 'spost', false, basename( dirname(__FILE__) ).'/languages/');
}add_action( 'plugins_loaded', 'spost_theme_setup' );

function curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}add_action('wp_head', 'curPageName');
function spost_dynamic_stylesScripts(){ ?>
	<style>
    #spostLogin{ background-color: <?php if(get_option('spost_login_button_background')==''){ echo '#3b8dbd';}else{ echo get_option('spost_login_button_background');}?>;
	color: <?php if(get_option('spost_login_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_login_button_text_color');}?>}
	#spostRegister{
	background-color: <?php if(get_option('spost_register_button_background')==''){ echo '#000';}else{ echo get_option('spost_register_button_background');}?>;
		color: <?php if(get_option('spost_register_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_register_button_text_color');}?>
		}
	#or{background-color: <?php if(get_option('spost_or_button_background')==''){ echo '#000';}else{ echo get_option('spost_or_button_background');}?>;
		color: <?php if(get_option('spost_or_button_text_color')==''){ echo '#FFF';}else{ echo get_option('spost_or_button_text_color');}?>}
    </style>
		<script type="text/javascript">
			var spostUrl = "<?php echo SPOST_PLUGIN_URL; ?>";
		</script>
<?php }add_action('wp_head','spost_dynamic_stylesScripts');
 function spost_script_enqueue_js_css() {
	// wp_register_script( 'spost-validate', SPOST_PLUGIN_URL . '/js/validate.js', array('jquery'),SPOST_VERSION);
	wp_enqueue_script( 'spost', SPOST_PLUGIN_URL . '/js/spost.js', array('jquery'),SPOST_VERSION);
	wp_enqueue_script( 'spost-validate' );
  /* $handle = 'jquery-ui-core';
   $list = 'registered';
     if (wp_script_is( $handle, $list )) {
       wp_enqueue_script( 'jquery-ui-core' );
     } else {
		wp_register_script( 'jquery-ui-core', get_bloginfo('home') . '/wp-includes/js/jquery/ui/jquery.ui.core.min.js');
       wp_enqueue_script( 'jquery-ui-core' );
     }*/

		//  wp_enqueue_script( 'spost' );
		wp_enqueue_style( 'spost',  SPOST_PLUGIN_URL . '/css/spost.css',  array(), '', 'screen' );
	 	wp_enqueue_style( 'spost-fontawesome',  SPOST_PLUGIN_URL . '/css/font-awesome.min.css',  array('spost'), '', 'screen' );
    //  wp_enqueue_style( 'spost' );

	}add_action('wp_enqueue_scripts', 'spost_script_enqueue_js_css');

function postingForm($thecase){
/*********************************************************************************************************************/
/**************************************** IF POSTING HAS BEEN DISABLED ***********************************************/
/*********************************************************************************************************************/
$htmls = new Htmls();

if($thecase=='none'){

	echo '<div id="spostError">'.__('Posting is disabled by the site admin','spost').'</div>';

}
/*********************************************************************************************************************/
/**************************************** else IF POSTING is for LOGGED IN USERS ONLY  *******************************/
/*********************************************************************************************************************/
elseif($thecase=='loggedin'){?>
<div id="wp_sidebarpost_main">
<div id="loginRegisterHidden"></div>
<div id="spostMessage"></div>
    <?php if(is_user_logged_in()): ?>

	<form action="" method="post"  id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />
	<p>
    <?php $current_user = wp_get_current_user(); ?>
	    <label><strong><?php _e('Your E-mail','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" type="text" style="width: 90%; background: #E5E5E5" value="<?php echo $current_user->user_email; ?>" readonly>
	    </label>
    </p>
	<p>
	    <label><strong><?php _e('Your Post Title','spost'); ?></strong><br>
	    <input name="spost_title" id="spost_title" type="text" class="white" style="width: 90%; " placeholder="<?php _e('Enter post title','spost'); ?>" required>
	    </label>
    </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" required style="width: 90%;"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomyX = get_option('spost_posting_taxonomy');
			if($taxonomyX=='category' || $taxonomyX==''){
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy=category');
			}else{
$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}

				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="<?php _e('Send for review','spost'); ?>" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>
	</form>
    <?php else: ?>
<!-- <div id="loginButtons">
<div class="mustLog">
<button name="spostLogin" id="spostLogin" onclick="location.href='<?php echo get_bloginfo('home'); ?>/wp-login.php'"><?php _e('Login','spost'); ?></button>
</div><div class="clearall"></div>
<div class="separatorHorizontal">
<div id="or"><?php _e('Or','spost'); ?></div>
</div><div class="clearall"></div>
<div class="mustLogRegister">
<button name="spostRegister" id="spostRegister" onclick="location.href='<?php echo get_bloginfo('home'); ?>/wp-login.php?action=register'"><?php _e('Register','spost'); ?></button>
</div><div class="clearall"></div>
</div> -->
		<?php $htmls::loginRegisterForm(); ?>
    <?php endif; ?>
</div>
<?php
}
/*********************************************************************************************************************/
/**************************************** IF POSTING IS ALLOWED FOR ALL***********************************************/
/*********************************************************************************************************************/
elseif($thecase=='everybody'){ ?>
<div id="wp_sidebarpost_main">
<div id="loginRegisterHidden"></div>
<div id="spostMessage"></div>
    <?php if(is_user_logged_in()): ?>

	<form action="" method="post"  id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />
	<p>
    <?php $current_user = wp_get_current_user(); ?>
	    <label><strong><?php _e('Your E-mail','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" type="text" style="width: 90%; background: #E5E5E5" value="<?php echo $current_user->user_email; ?>" readonly>
	    </label>
    </p>
	<p>
	    <label><strong><?php _e('Your Post Title','spost'); ?></strong><br>
	    <input name="spost_title" id="spost_title" type="text" class="white" style="width: 90%; " placeholder="<?php _e('Enter post title','spost'); ?>" required>
	    </label>
    </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" required style="width: 90%;"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomyX = get_option('spost_posting_taxonomy');
			if($taxonomyX=='category' || $taxonomyX==''){
				$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy=category');
							}else{
				$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}

				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="<?php _e('Send for review','spost'); ?>" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>
	</form>
    <?php else: ?>
	<form action="" method="post"  id="SidebarPost">
    <input type="hidden" name="submit-post" value="yes" />
    <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />



     <p>
	    <label><strong><?php _e('Your Name','spost'); ?></strong><br>
	    <input name="spost_poster_name" id="spost_poster_name" required type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
     </p>
	 <p>
	    <label><strong><?php _e('Your email','spost'); ?></strong><br>
	    <input name="spost_poster_email" id="spost_poster_email" required type="text" style="width: 90%; background:#EFEFEF" value="">
	    </label>
     </p>
	 <p>
	    <label><strong><?php _e('Your Post Title','spost'); ?></strong><br>
	    <input name="spost_title" id="spost_title" type="text" style="width: 90%; background:#EFEFEF" placeholder="<?php _e('Enter post title','spost'); ?>" required>
	    </label>
     </p>
	  <p>
	    <label> <strong><?php _e('Content of the post','spost'); ?> </strong><br>
	    <textarea id="spost_content" name="spost_content" required rows="10" style="width: 90%; background: #EFEFEF"></textarea>
		</label>
	    <br>
		<label><strong><?php _e('Category','spost'); ?></strong>
			<?php
			$taxonomyX = get_option('spost_posting_taxonomy');
			if($taxonomyX=='category' || $taxonomyX==''){
				$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy=category');
							}else{
				$select = wp_dropdown_categories('show_option_none='.__('Select a category','spost').'&show_count=1&hide_empty=0&orderby=name&echo=0&name=spost_cat&id=spost_category&taxonomy='.get_option('spost_posting_taxonomy'));
			}

				$select = preg_replace("#<select([^>]*)>#", "<select$1 >", $select);
				echo $select;
			?>
		</label><p>
		<input type="hidden" name="owner" value="<?php echo $user_id;?>"  /></p>
	    <input type="submit" name="send_post"  id="send_post" value="<?php _e('Send for review','spost'); ?>" style="width: 150px; height: 40px; background:#EFEFEF" />
        </p>
	</form>
    <div id="spostMessage"></div>
    <?php endif; ?>
</div>
<?php
}

 }
function sidebar_post_widget() {
	$allowed_level = get_option('spost_allowed_level');

	if($allowed_level=='everybody' || $allowed_level=='' ){
		postingForm('everybody');
	}
	elseif($allowed_level=='loggedin' || $allowed_level=='' ){
		 postingForm('loggedin');
	}
	elseif($allowed_level=='none' || $allowed_level=='' ){
		postingForm('none');
	}
}
class wp_sidebar_post_plugin extends WP_Widget {

	// constructor
	function wp_sidebar_post_plugin() {
		/* ... */
		parent::WP_Widget(false, $name = __('Sidebar Post Widget', 'spost') );
	}

	// widget form creation
	function form($instance) {

	// Check values
	if( $instance) {
		 $title = esc_attr($instance['title']);
		 //$text = esc_attr($instance['text']);
		 $textarea = esc_textarea($instance['textarea']);
	} else {
		 $title = '';
		 //$text = '';
		 $textarea = '';
	}
	?>

	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'spost'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<!--<p>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'spost'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
	</p>-->

	<p>
	<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Posting Instructions:', 'spost'); ?></label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Posting Instructions:', 'spost'); ?></label>
	<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
	</p>
	<?php
	}

	// update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  $instance['text'] = strip_tags($new_instance['text']);
		  $instance['textarea'] = $new_instance['textarea'];
		 return $instance;
	}

	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $text = $instance['text'];
	   $textarea = $instance['textarea'];
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text spost_box">';

	   // Check if title is set
	   if ( $title ) {
		  echo $before_title . $title . $after_title;
	   }

	   // Check if text is set
	   if( $text ) {
		  echo '<p class="spost_text">'.$text.'</p>';
	   }
	   // Check if textarea is set
	   if( $textarea ) {
		 echo '<p class="spost_textarea">'.$textarea.'</p>';
	   }
	   sidebar_post_widget();
		 echo '<div class="loading-messages sphidden"></div>';
	   echo '</div>';
	   echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_sidebar_post_plugin");'));

/*add_action('admin_menu', 'sidebar_posting_menu');

function sidebar_posting_menu() {
	add_options_page('SidebarPosting', 'Sidebar Post', 'manage_options', 'sidebar-posting', 'sidebar_post_fx');
}*/

function sidebar_post_fx(){

}
function spost_other_admin_options(){?>
<div id="spostContainer">
<form id="spostForm1" enctype="multipart/form-data" method="post" action="">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Avanced Settings','spost'); ?></h1>
        <div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_admin_email">
    	<div class="inputTitle"><?php _e('Admin Notification email','spost'); ?>?</div>
        <div class="inputContent">
        <input type="text" name="spost_admin_email" id="spost_admin_email" value="<?php if(get_option('spost_admin_email')==''){echo get_option('admin_email');}else{ echo get_option('spost_admin_email');} ?>" /></div>
        <div class="spostLegend"><?php _e('The email address to be notified when posts are created','spost'); ?></div><div class="clearall"></div>
  </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_admin_from_email">
			<div class="inputTitle"><?php _e('From email','spost'); ?>?</div>
				<div class="inputContent">
				<input type="text" name="spost_admin_from_email" id="spost_admin_from_email" value="<?php if(get_option('spost_admin_from_email')==''){echo get_option('admin_email');}else{ echo get_option('spost_admin_from_email');} ?>" /></div>
				<div class="spostLegend"><?php _e('The email is used as the "from" email. Usually something like "noreply@yourdomain.com"','spost'); ?></div><div class="clearall"></div>
	</div><div class="clearall"></div>

	<div class="spostRow" data-spostid="spost_send_poster_email">
    	<div class="inputTitle"><?php _e('Send an email to the poster','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_send_poster_email')=='1' || get_option('spost_send_poster_email')==''){echo 'yes';}else{echo 'no';} ?>" data-toggleyesno="spost_send_poster_email"><?php if(get_option('spost_send_poster_email')=='1' || get_option('spost_send_poster_email')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_send_poster_email" id="spost_send_poster_email" value="<?php echo get_option('spost_send_poster_email'); ?>" />
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow premium" data-spostid="spost_custom_login_page">
    	<div class="inputTitle"><?php _e('Advanced Login/Register','spost'); ?></div>
		<div class="inputContent">
        <?php wp_dropdown_pages(array(
				'name'                  => 'spost_custom_login_page',
				'show_option_none'      => '- - - '.__('Select One','spost').' - - - ', // string
				'selected'              => get_option('spost_custom_login_page'),
				'depth'                 => 0,
				'id'                    => 'spost_custom_login_page', // string

		)); ?>
        </div>
        <div class="spostLegend"></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow premium" data-spostid="spost_interval_between_posts">
    	<div class="inputTitle"><?php _e('Interval between Posts','spost'); ?></div>
        <div class="inputContent">
        <select name="spost_interval_between_posts" id="spost_interval_between_posts" disabled="disabled">
			<?php
            $i=0;
			$intervals = array('1'=>'5','2'=>'10','3'=>'15','4'=>'20','5'=>'25','6'=>'30','7'=>'35','8'=>'40','9'=>'45','10'=>'50','11'=>'55');
            foreach ( $intervals as $key=>$value ) {
			if(get_option('spost_interval_between_posts')==$value){
			   echo '<option value="' . $value . '" selected="selected">' . $value . '</option>';
				}else{
			   echo '<option value="' . $value . '">' . $value . '</option>';
				}
			$i++;
            }
            ?>
        </select>
        </div>
        <div class="spostLegend"><?php _e('The amount of time between a user is allowed to post again, in minutes','spost'); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_log_user_ip">
    	<div class="inputTitle"><?php _e('Log user IP','spost'); ?></div>
        <div class="inputContent">
        <div class="<?php if(get_option('spost_log_user_ip')=='1' || get_option('spost_log_user_ip')==''){echo 'yes';}else{echo 'no';} ?>" data-toggleyesno="spost_log_user_ip"><?php if(get_option('spost_log_user_ip')=='1' || get_option('spost_log_user_ip')==''){_e('Yes','spost');}else{_e('No','spost');} ?></div>
        <input type="hidden" name="spost_log_user_ip" id="spost_log_user_ip" value="<?php echo get_option('spost_log_user_ip'); ?>" />
        </div>
        <div class="spostLegend"><?PHP _e('Whether or not to log the user&apos;s IP ddress',''); ?></div><div class="clearall"></div>
    </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_login_button_background">
            <div class="inputTitle"><?php _e('Login Button Background','spost'); ?></div>
            <div class="inputContent">
			<input type="text" value="<?php echo get_option('spost_login_button_background'); ?>" name="spost_login_button_background" class="spost_login_button_background" data-default-color="<?php echo get_option('spost_login_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Login button','spost'); ?></div><div class="clearall">		    </div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_login_button_text_color">
            <div class="inputTitle"><?php _e('Login Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_login_button_text_color'); ?>" name="spost_login_button_text_color" class="spost_login_button_text_color" data-default-color="<?php echo get_option('spost_login_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Login button','spost'); ?></div><div class="clearall"></div>
     </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_register_button_background">
            <div class="inputTitle"><?php _e('Register Button Background','spost'); ?></div>
            <div class="inputContent">
			<input type="text" value="<?php echo get_option('spost_register_button_background'); ?>" name="spost_register_button_background" class="spost_register_button_background" data-default-color="<?php echo get_option('spost_register_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Register button','spost'); ?></div>
            <div class="clearall"></div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_register_button_text_color">
            <div class="inputTitle"><?php _e('Register Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_register_button_text_color'); ?>" name="spost_register_button_text_color" class="spost_register_button_text_color" data-default-color="<?php echo get_option('spost_register_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Register button','spost'); ?></div>
            <div class="clearall"></div>
     </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_or_button_background">
            <div class="inputTitle"><?php _e('OR separator Button Background','spost'); ?></div>
            <div class="inputContent">
			<input type="text" value="<?php echo get_option('spost_or_button_background'); ?>" name="spost_or_button_background" class="spost_or_button_background" data-default-color="<?php echo get_option('spost_or_button_background'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The background color of the Or button','spost'); ?></div>
            <div class="clearall"></div>
        </div><div class="clearall"></div>
    <div class="spostRow" data-spostid="spost_or_button_text_color">
            <div class="inputTitle"><?php _e('Or Button Text Color','spost'); ?></div>
            <div class="inputContent">
            <input type="text" value="<?php echo get_option('spost_or_button_text_color'); ?>" name="spost_or_button_text_color" class="spost_or_button_text_color" data-default-color="<?php echo get_option('spost_or_button_text_color'); ?>" />
            </div>
            <div class="spostLegend"><?PHP _e('The text color of the Or button','spost'); ?></div>
            <div class="clearall"></div>
     </div><div class="clearall"></div>
<div class="spostRow" data-spostid="spost_posting_taxonomy">
    <input type="submit" name="save_settings" id="save_settings" style="margin-left:auto; margin-right:auto; border: none; background:#333; color: #FFF; border-radius: 15px; padding:5px 25px; cursor: pointer;" value="<?php _e('Save Settings','spost'); ?>"/>	 <div id="spostMessages"></div>
</div><div class="clearall"></div>
</form>

</div><div class="clearall"></div><?php }
function spost_documentation_fx(){?>
<div id="spostContainer">

	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Documentation','spost'); ?></h1>
    </div><div class="clearall"></div>
	<div class="spostRow" data-spostid="spost_allowed_level">
    	<p><h2><?php _e('Introduction','spost'); ?></h2></p>
    	<p><?php _e('We are thrilled that you installed our Plugin.','spost'); ?></p>
        <p><?php _e('We hope is meets your needs and boosts our website or blog to another user engagement level','spost'); ?></p>
        <p><?php _e('As you will see, this documentation is just in its infancy, but we will continue to improve upon it as we go.','spost'); ?></p>
        <p><?php _e('You can also visit the plugin page if you have any question at ','spost'); ?><a href="http://www.codiiv.com/forums/forum/plugins/sidebar-post/">http://www.codiiv.com/forums/forum/plugins/sidebar-post/</a></p>
        <p><h2><?php _e('Usage','spost'); ?></h2></p>
        <p><?php _e('We made this plugin as simple to use as it could be, by making it into a widget, and also providing a shortcode you can use to insert in pages and posts on your website. ','spost'); ?></p>
        <p><?php _e('Simply go to the widget area, and add the <b>`Sidebar Post Widget`</b> whereyou want it to appear','spost'); ?></p>
        <p><?php echo __('Alternatively, you can insert the <b>[sidebarpost]</b> shortcode in a page or post','spost'); ?>
    </div><div class="clearall"></div>

</div>
<?php }
function spost_uninstall_fx(){
?>
<div id="spostContainer">
	<div class="spostRow" id="theHeader">
    	<h1><?php _e('Sidebar Post - Uninstall','spost'); ?></h1>
        <div class="clearall"></div>
    </div><div class="clearall"></div>
	<div class="spostRow">
    	<div class="warningRed">
        <?php _e('You are about to uninstall SideBar Post. This action is irreversible, and you will need to reactivate the plugin','spost'); ?>
        <p>
        <form id="uninstallspost" name="uninstallspost" enctype="multipart/form-data" method="post">
        <input type="hidden" name="finalUninstallSpost" value="<?PHP echo SPOST_UNINSTALL_CODE; ?>" />

        <button name="uninstallit1" id="uninstallit1"><?php _e('You really want to uninstall?','spost'); ?></button><button type="submit" name="uninstallit" id="uninstallit"  disabled="disabled"><?php _e('Finish the Uninstall','spost'); ?></button>
        </form>
        </div>

        <div class="clearall"></div>
    </div><div class="clearall"></div>
</div>
<?php
}
function create_spost_ajax(){

	if($_POST['current_user_id']=='0'){
	$owner = get_option('spost_posting_default_user');
	}else{
	$owner = $_POST['current_user_id'];
	}
	$theform = $_POST['theForm'];
	parse_str($theform, $sOutPost);

	$cat_= $sOutPost['spost_cat'];
	$email_ = $sOutPost['spost_poster_email'];
	$name_	= $sOutPost['spost_poster_name'];

	$postingCat = get_option('spost_posting_taxonomy');
  if(is_user_logged_in()){
		$sPost['post_title'] = $sOutPost['spost_title'];
		$sPost['post_content'] = $sOutPost['spost_content'];
		$sPost['post_status'] = (($owner > 0) ? get_option('spost_posting_status'):"draft"); //FORCE draft for anonymous users
		$sPost['post_author'] = $owner;
		$sPost['post_type'] = get_option('spost_posting_posttype');
			// Insert the post into the database
				$successful_post = wp_insert_post($sPost);
				if($postingCat=="category" || $postingCat =="post_tag"){
					$insertedTerms = wp_set_post_terms( $successful_post, array($cat_), 'category' );
				}else{
					$insertedTerms = wp_set_object_terms( $successful_post, array($cat_), 'category' );
				}

			}else{ //****************************** WHEN USER IS NOT LOGGED IN **************

				//spost_posting_default_user
				$sPostDefaultUser = get_option('spost_posting_default_user'); //Check if we plugin has saved a default user for posts
				if($sPostDefaultUser!=""){
					$userid= $sPostDefaultUser;
				}else{
					$defaultUserPass = wp_generate_password( 10, true, true );
					$identifier = wp_generate_password( 10, false );  //Generate string with numbers and letters only
					$tld = wp_generate_password( 10, false );	//Generate string with numbers and letters only
					$userEmail = $identifier."@".$tld.".io"; // Create a random email (doesn't exist necessarily)
					$userdata = array(
							'user_login'  =>  'login_name',
							'user_email'  =>  $userEmail,
							'user_pass'   =>	$defaultUserPass,
							'display_name'=>  __("Sidebar Poster"),
							'nickname'		=>	__("Sidebar Poster"),
					);
					$userid = wp_insert_user($userdata) ;
					update_option('spost_posting_default_user',$userid);
				}
				$sPost['post_title'] = $sOutPost['spost_title'];
			  $sPost['post_content'] = $sOutPost['spost_content'];
			  $sPost['post_status'] = (($userid > 0) ? get_option('spost_posting_status'):"draft"); //FORCE draft for anonymous users
			  $sPost['post_author'] = $userid;
			  $sPost['post_type'] = get_option('spost_posting_posttype');

				$successful_post = wp_insert_post($sPost);
				// $emailExists = get_post_meta();
				$postMeta = add_post_meta($successful_post,"poster_name",$name_);
				$meta = add_user_meta($userid,'stranger_posting_email',$email_);

				if($postingCat=="category" || $postingCat =="post_tag"){
					$insertedTerms = wp_set_post_terms( $successful_post, array($cat_), 'category' );
				}else{
					$insertedTerms = wp_set_object_terms( $successful_post, array($cat_), 'category' );
				}
			}
			if($successful_post){
					$reaction = __('Thank you for submitting your post. It will be reviewed and once approved, published','spost');
					$reactionType = "success";

					$spost_admin_email = get_option('spost_admin_email');

					if($spost_admin_email==''){
						$admin_email =  get_option('admin_email');
					}else{
						$admin_email = $spost_admin_email;
					}
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					wp_mail($admin_email, __('New Post created on website','spost'), __('<p>A New post created.','spost').__('<p>Please moderate...','spost').'<p><a href="'.get_option('home').'/wp-admin/post.php?post='.$successful_post.'&action=edit">'.get_option('home').'/wp-admin/post.php?post='.$successful_post.'&action=edit</a>');

					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
		}else{
			$reaction = __('There was an error publishing the post. Please make sure all information is entered correctly','spost');
			$reactionType = "error";
		}
			wp_send_json(array("message"=>$reaction, "msg_type"=>$reactionType));
	die();

}
add_action('wp_ajax_spostPublish', 'create_spost_ajax');
add_action('wp_ajax_nopriv_spostPublish', 'create_spost_ajax');//for users that are not logged in.
//[foobar]
function spostShortened( $atts ){
	return sidebar_post_widget();
}
add_shortcode( 'sidebarpost', 'spostShortened' );
function save_spost_settings(){
$myData = $_POST['postdata']; //The Data we get  from  jQuery (querystring)
$responsible = get_current_user_id(); // We get the user who is submitting the form NOT from the array
$arr = array();
parse_str( $myData, $arr); //We parse it into an associative array
if(current_user_can('administrator','update_plugins')){
	foreach ($arr as $key => $value){
				update_option($key,$value);
			}
$settingsStuff = array('updatestatus'=>'success','updatecontext'=>__('Setting Saved successfully','spost'));
}else{
$settingsStuff = array('updatestatus'=>'error','updatecontext'=>__('There was an error','spost'));
}
	echo json_encode($settingsStuff);

	die();
}
add_action('wp_ajax_spostSaveSettings', 'save_spost_settings');
function uninstall_spost_last(){
	if($_POST['finalUninstallSpost']!=SPOST_UNINSTALL_CODE){

	}else{

$spostOptions = array(
				'spost_db_version',
				'spost_db_build',
				'spost_allowed_level',
				'spost_require_email',
				'spost_posting_posttype',
				'spost_posting_taxonomy',
				'spost_posting_status',
				'spost_posting_default_user',
				'spost_admin_email',
				'spost_send_poster_email',
				'spost_custom_login_page',
				'spost_interval_between_posts',
				'spost_log_user_ip',
				'spost_login_button_background',
				'spost_login_button_text_color',
				'spost_register_button_background',
				'spost_register_button_text_color',
				'spost_or_button_background',
				'spost_or_button_text_color',
				);
	foreach($spostOptions as $spostOption){
	 //$wpdb->delete( $wpdb -> prefix.'options', array( 'option_name' => $spostOption ) );
	 delete_option($spostOption);
	}

	deactivate_plugins( plugin_basename( __FILE__ ) );
	//delete_spost_options();
	wp_redirect( admin_url().'plugins.php?#sidebar-posting' ); exit;
		}

}add_action('admin_init','uninstall_spost_last');
?>
