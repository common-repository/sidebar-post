<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
 // Exit if accessed directly
 if ( !defined( 'ABSPATH' ) ) exit;

 /**
  *
  */
 class Htmls
 {
   public function loginRegisterForm(){
     global $wpdb;
    //  $table  = $wpdb->prefix.'spost_temp_users';
    //  $userTemp = $wpdb->get_row( "SELECT * FROM $table WHERE user_email = 'yayayahooo@gmail.com'");
    //
    // $to = 'jmanishimwe@hotmail.com';
    // $subject = 'ESSAIE local';
    // $body = 'The email body content';
    // $headers = array('Content-Type: text/html; charset=UTF-8');
    //
    // wp_mail( $to, $subject, $body, $headers );
    // echo base64_decode($_GET['key']);
    // echo base64_decode($_GET['key']);
    if(base64_decode($_GET['obli'])=="yes"){
      $owner    = base64_decode($_GET['owner']);
      $pieces   = explode("__", $owner);
      $usercode = $pieces[0];
      $email    = $pieces[1];
      if(obliterateTempUser($email,$usercode)){
        echo __("Request Cancelled successfully", "spost");
      }else{
        echo __("There were errors cancelling the request. Please check the email well.", "spost");
      }
    }else{
      $owner = base64_decode($_GET['owner']);
      $pieces   = explode("__", $owner);

      // print_r(confirmFullUser($pieces[0],$pieces[1]));
    }
     ?>
     <div class="clearall"></div>
     	<div class="spostlogin-tabs">
     		<div class="auth-tabs">
     			<ul>
     				<li class="chosen" data-corresponding="login"><?php _e("Sign in", "spost"); ?></li>
     				<li data-corresponding="register"><?php _e("Register", "spost"); ?></li>
     			</ul>
     			<div class="auth-container">
            <div class="loading-messages sposthidden"><i class="fa fa-close"></i></div>
            <input type="hidden" name="submit-post" value="yes" />
            <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo get_current_user_id(); ?>" />
            <input type="hidden" name="AjaxUrl" id="AjaxUrl" value="<?php bloginfo('home') ?>/wp-admin/admin-ajax.php" />
            <?php
            $queried_object = get_queried_object();

            if ( $queried_object ) {
                $post_id = $queried_object->ID;
                echo '<input type="hidden" name="returnTo" id="returnTo" value="'.$post_id.'" />';
            }
             ?>
            <form class="spost-authenticate" data-action="login" action="" method="post" enctype="multipart/form-data">
              <div class="auth-entity login-box">
       					<fieldset>
       						<label for="email_container"><?php _e("Email address Or Username","spost"); ?></label>
       						<div class="inputcontainer">
       							<i class="fa fa-envelope" aria-hidden="true"></i><input type="text" required name="user_email" class="user_email" value="">
       						</div>
       					</fieldset>
       					<fieldset>
       						<label for="password_container"><?php _e("Password","spost"); ?></label>
       						<div class="inputcontainer">
       							<i class="fa fa-key" aria-hidden="true"></i><input type="password" required name="user_password" class="user_password" value="">
       						</div>
       					</fieldset>
       					<fieldset>
       						<button type="submit" name="signin-btn"><i class="fa fa-lock" aria-hidden="true"></i><?php _e("Sign in","spost"); ?></button>
       					</fieldset>
       				</div>
            </form>
            <form class="spost-authenticate" data-action="register" action="" method="post" enctype="multipart/form-data">
              <div class="auth-entity sposthidden register-box">
                <fieldset>
       						<label for="email_container"><?php _e("Email address","spost"); ?></label>
       						<div class="inputcontainer">
       							<i class="fa fa-envelope" aria-hidden="true"></i><input type="email" required name="user_email" class="user_email" value="">
       						</div>
       					</fieldset>
       					<fieldset>
       						<label for="password_container"><?php _e("Password","spost"); ?></label>
       						<div class="inputcontainer">
       							<i class="fa fa-key" aria-hidden="true"></i><input type="password" required name="user_password" class="user_password" value="">
       						</div>
       					</fieldset>
       					<fieldset>
       						<button type="submit" name="register-btn"><i class="fa fa-lock" aria-hidden="true"></i><?php _e("Sign up","spost"); ?></button>
       					</fieldset>
       				</div>
            </form>
     			</div>
     		</div>
     	</div>
     <div class="clearall"></div>
   <?php }

 }
