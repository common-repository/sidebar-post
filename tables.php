<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function spost_core_set_charset() {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/* Wpdating component DB schema */
	if ( !empty($wpdb->charset) )
		return "DEFAULT CHARACTER SET $wpdb->charset";

	return '';
}
function spost_core_install_matrix() {
	global $wpdb,$installed_ver;
$installed_ver = get_option( "spost_version" );

if( $installed_ver != SPOST_DATABASE_VERSION ) {
	$charset_collate = spost_core_set_charset();
	$spost_prefix = spost_core_get_table_prefix();
	$sql[] = "CREATE TABLE {$spost_prefix}spost_temp_users (
							ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							-- matrix_id bigint(20) NOT NULL,
							user_email varchar(70) NOT NULL,
							user_login varchar(70) NOT NULL,
							user_password varchar(70) NOT NULL,
							user_code varchar(70) NOT NULL,
							date_recorded datetime NOT NULL,
							KEY ID (ID)
		       ) {$charset_collate};";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	update_option( 'spost_version', SPOST_DATABASE_VERSION );
	update_option( 'spost_build', SPOST_BUILD );
	}

}
