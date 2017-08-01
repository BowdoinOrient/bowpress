<?php

/*
 * Plugin Name: Bowdoin Orient — Documentation
 * Plugin URI: http://bowdoinorient.com/wordpress
 * Description: Adds an admin panel for documentation.
 * Author: James Little
 * Author URI: http://jameslittle.me
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) or die;

add_action( 'admin_menu', 'documentation_menu' );

function documentation_menu() {
	add_menu_page('Orient Wordpress Documentation', 'Orient Docs', 'manage_options', 'orient-docs', 'my_plugin_options');
	// add_submenu_page( 'orient-docs', 'Page title', 'Sub-menu title', 'manage_options', 'my-submenu-handle', 'my_plugin_options');
}

function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo "Docs";
}
