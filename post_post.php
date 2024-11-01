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
 
define('WP_USE_THEMES', false);

/** Loads the WordPress Environment and Template */
require('../../../wp-blog-header.php');
?>

<?php
require_once('../../../wp-load.php' );
$wp->init();
?>

<?php if ( is_user_logged_in() ){?>
<?php $cat_= $_POST['cat'];
	  $owner = $_POST['curret_user'];
	  $title = $_POST['title'];
	  $content = $_POST['content'];

	   // Create post object
  $my_post = array();
  $my_post['post_title'] = $title;
  $my_post['post_content'] = $content;
  $my_post['post_status'] = 'draft';
  $my_post['post_author'] = $owner;
  $my_post['post_type'] = 'post';
  $my_post['post_category'] = array($cat_);

// Insert the post into the database
	$successful_post = wp_insert_post($my_post);

    // add a custom field
    //add_post_meta($successful_post, "character_id", $character_id );
	//add_post_meta($successful_post, "post_outside_author", $post_author );
	//function email_blog_admin_new_post($post_title)  {
    //$admin_email = 'janvier@janvierdesigns.com';
    //mail($admin_email, "New Submission::".$post_title."", $post_content);
    //return $post_ID;
	//}
  if ($successful_post==''){echo "Your post may be empty<br>"." or you are trying to enter through the wrong channel";}
  else{echo '<script type="text/javascript">
window.location.href="'.get_permalink(2).'";
</script>'; //email_blog_admin_new_post($post_title) >> COMING IN NEW VERSION;
  }
?>

<?php }else{ ?>
<a href="<?php echo get_option('home'); ?>/wp-login.php"><img src="<?php echo get_option('home'); ?>/wp-content/plugins/sidebar-posting/images/not_logged_in.gif" /></a>

<?php } ?>
