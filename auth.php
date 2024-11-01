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
function insertTempUser($email,$username,$pass,$usercode){
  global $wpdb;
  $temp_table = $wpdb->prefix.'spost_temp_users';

	$tempuser = $wpdb->insert( $temp_table, array(
    'user_email' =>$email,
    'user_login' => $username,
    'user_password'=>$pass,
    'user_code' => $usercode,
    // 'rating_status'=>'valid',
    'date_recorded'=>date('Y-m-d H:i:s')
  ));
	return $wpdb->insert_id;
}
function tempEmailExists($email){
  global $wpdb;
  $table  = $wpdb->prefix.'spost_temp_users';
  $userTemp = $wpdb->get_row("SELECT * FROM $table WHERE user_email = '".$email."'");

  if($userTemp){
    return true;
      }else {
    return false;
  }
}
function obliterateTempUser($email,$usercode){
  global $wpdb;
  $temp_table = $wpdb->prefix.'spost_temp_users';
  $deleted = $wpdb->delete( $temp_table, array( 'user_email' => $email));
  if($deleted){
    return true;
  }else{
    return false;
  }
}

function confirmFullUser($owner,$userid){
  global $wpdb;
  $temp_table = $wpdb->prefix.'spost_temp_users';
  $users_table = $wpdb->prefix.'users';

  // $deleted = $wpdb->delete( $temp_table, array( 'user_email' => $email));
  $userTemp = $wpdb->get_row($wpdb->prepare("SELECT * FROM $temp_table WHERE ID='".$userid."'"));
  $userEmail = $userTemp->user_email;
  $userLogin = $userTemp->user_login;
  $userPass = $userTemp->user_pass;


  if($userEmail == $owner){
    $default_role = get_option('default_role');
    $user_id = $wpdb->insert(
   $users_table,
   array(
     'user_login' => $userLogin,
      'user_pass' => $userPass,
      'user_email' => $userEmail,
     'user_registered' => date('Y-m-d H:i:s'),
   ));

    $updated = wp_update_user( array( 'ID' => $user_id, 'role' => ( $default_role!=""?$default_role : "subscriber") ) );
    if($updated){
      $final = array(
        "message"  => __("Account confirmed successfully. You can use it now.","spost"),
        "msg_type" => "success"
      );
    }else{
      $final = array(
        "message"  => __("There were errors confirming your account.","spost"),
        "msg_type" => "error"
      );
    }
  }else{
    $final = array(
      "message"  => __("The information provided is not valid.","spost"),
      "msg_type" => "error"
    );
  }
  return $final;
}
function spostAuth_fx(){
  $form     = $_POST['theForm'];
  $authtype = $_POST['auth_type'];
  $retunTo  = get_permalink($_POST['return_to']);

  parse_str($form, $vars);
  $identifier = $vars['user_email'];
  $pass  = $vars['user_password'];
  /***************************************
              AUTH is LOGIN
  ****************************************/
  if($authtype=="login"){
    $remember  = $vars['user_remember'];

    if ( is_email($identifier) ) { //If it's an email
      if (email_exists( $identifier )):
        $user =  get_user_by( 'email', $identifier );
        $username = $user->user_login;
        // $authenticated1 = wp_authenticate($username,$pass);
        $creds = array(
            'user_login'    => $username,
            'user_password' => $pass,
            'remember'      => false
        );

        $newUser = wp_signon( $creds, false );

        if ( is_wp_error( $newUser ) ) {
          $authenticated = $newUser->get_error_message();
        }else{
          $authenticated = array(
            "message"   => __("Logged in successfully", "spost"),
            "msg_type"  => "success"
          );
        }
      else:
        $authenticated = array(
          "message"   =>__("That email does not exist in our records", "spost"),
          "msg_type"  => "error"
        );
      endif;
    }else{              //IF IT'S a username
      $creds = array(
          'user_login'    => $identifier,
          'user_password' => $pass,
          'remember'      => false
      );

      $newUser = wp_signon( $creds, false );

      if ( is_wp_error( $newUser ) ) {
        $authenticated = $newUser->get_error_message();
      }else{
        $authenticated = array(
          "message"   => __("Logged in successfully", "spost"),
          "msg_type"  => "success"
        );
      }
    }
  }
  /****************************************
              AUTH is REGISTER
  *****************************************/
  if($authtype=="register"){
    if(email_exists($identifier)){
      $authenticated = array(
        "message"   => __("That email exists in our records. Please log in instead.","spost"),
        "msg_type"  => "error"
      );
    }else{
      if ( !is_email($identifier) ) {
        $authenticated = array(
          "message"   => __("That email is not well structured. Please check it","spost"),
          "msg_type"  => "error"
        );
      }else{
        $username = strtolower(wp_generate_password( 10, false ));
        $password = wp_generate_password( 10, false );
        $usercode = wp_generate_password( 10, false );
        $userdata = array(
            'user_login'  =>  $username,
            'user_pass'    =>  $password,
            'user_email'   =>  $identifier  // When creating an user, `user_pass` is expected.
        );

        // $user_id = wp_insert_user( $userdata ) ;
        //
        // //On success
        // if ( ! is_wp_error( $user_id ) ) {
        //     //echo "User created : ". $user_id;
        //     $authenticated = array(
        //       "message"   => __("Account created successfully","spost"),
        //       "msg_type"  => "success"
        //     );
        // }
        if(tempEmailExists($identifier)){
          $authenticated = array(
                "message"   => __("That email has been registered already","spost"),
                "msg_type"  => "error"
              );
        }else{
          $pass_  = wp_hash_password($pass);
          $newUser = insertTempUser($identifier,$username,$pass_,$usercode);
          if($newUser){
            // $retunTo

            add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
            $headers[] = 'From: Staff at '.get_bloginfo("name").' <'.get_option("spost_admin_from_email")!=""? get_option("spost_admin_from_email"):get_option("admin_email").'>';
            // $headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
            // $headers[] = 'Cc: iluvwp@wordpress.org';
            $msgContent  = '';
            $msgContent .= __('Hello!','spost');
            $msgContent .= '<p>'.__("An account has been created using this email address.","spost").'';
            $msgContent .= '<p>'.__("To confirm this account, please click on the link below or copy and paste it in your browser","spost");
            $msgContent .= '<br><br><p>
                            <div style="text-align:center;background-color: #67af7d; padding:1rem 5px; color: #fff;  max-width:100%">
                              <a href="'.$retunTo.'?owner='.base64_encode($identifier.'__'.$newUser).'" style="color:#fff; background-color:#105e2e; padding:0.5rem;">'.__("Comfirm account","spot").'</a>
                            </div>
                            </p>
                            <p>
                            '.__("You can also copy and paste this link in your browser's address bar","spost").': <br>
                            <span style="font-size: 1.6rem;">'.$retunTo.'?owner='.base64_encode($identifier.'__'.$newUser).'</span>
                            </p><br><br>';
            $msgContent .= '<div style="text-align:center; font-size:1.6rem;background-color: #eaa8a8; padding:1rem 5px; color: #fff;  max-width:100%">
              '.__("If you did not create this account, please CANCEL this by clicking on the button below", "spost").'
              <span style="background-color:#000; padding:1rem 3rem; width:15rem; display:block; margin:1rem auto; text-align:center; font-size:1.2rem;">
                <a href="'.$retunTo.'?obli='.base64_encode("yes").'&owner='.base64_encode($newUser.'__'.$identifier).'" style="color:#fff;">'.__("Cancel this request","spot").'</a>
              </span>
            </div>';

            $msgContent .= '<br><br><p style="font-size:1.6rem">Staff at '.get_bloginfo("name").'</p>';

            wp_mail($identifier, __('Confirm email','spost'),$msgContent, $headers);

            // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
            remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

            $authenticated = array(
                  "message"   => __("Account created successfully. Please check your email for confirmation","spost").' (<strong>'.$identifier.'</strong>)',
                  "msg_type"  => "success"
                );
          }else{
            $authenticated = array(
                  "message"   => __("Error creating account","spost"),
                  "msg_type"  => "error"
                );
          }
        }
      }
    }
  }
  wp_send_json($authenticated);

  die();
}
add_action( 'wp_ajax_spostAuth','spostAuth_fx');
add_action( 'wp_ajax_nopriv_spostAuth','spostAuth_fx');
