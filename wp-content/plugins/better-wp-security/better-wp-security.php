<?php

/*
 * Plugin Name: iThemes Security
 * Plugin URI: https://ithemes.com/security
 * Description: Take the guesswork out of WordPress security. iThemes Security offers 30+ ways to lock down WordPress in an easy-to-use WordPress security plugin.
 * Author: iThemes
 * Author URI: https://ithemes.com
 * Version: 6.2.1
 * Text Domain: better-wp-security
 * Network: True
 * License: GPLv2
 */


$locale = apply_filters( 'plugin_locale', get_locale(), 'better-wp-security' );
load_textdomain( 'better-wp-security', WP_LANG_DIR . "/plugins/better-wp-security/better-wp-security-$locale.mo" );
load_plugin_textdomain( 'better-wp-security' );

if ( isset( $itsec_dir ) || class_exists( 'ITSEC_Core' ) ) {
	include( dirname( __FILE__ ) . '/core/show-multiple-version-notice.php' );
	return;
}


$itsec_dir = dirname( __FILE__ );

if ( is_admin() ) {
	require( "$itsec_dir/lib/icon-fonts/load.php" );
}

require( "$itsec_dir/core/class-itsec-core.php" );
$itsec_core = ITSEC_Core::get_instance();
$itsec_core->init( __FILE__, esc_html__( 'iThemes Security', 'better-wp-security' ) );
