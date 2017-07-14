<?php
/*
Plugin Name: Just Variables for Wordpress
Plugin URI: http://justcoded.com/blog/just-wordpress-theme-variables-plugin/
Description: This plugin add custom page with theme text variables to use inside the templates.
Tags: theme, variables, template, text data
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
Version: 1.2.2
*/

define('JV_ROOT', dirname(__FILE__));
define('JV_TEXTDOMAIN', 'just-wp-variables');

if(!function_exists('pa')){
function pa($mixed, $stop = false) {
	$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
	$print = array($key => $mixed); echo( '<pre>'.htmlentities(print_r($print,1)).'</pre>' );
	if($stop == 1) exit();
}
}

require_once( JV_ROOT . '/just-variables.admin.php' );
require_once( JV_ROOT . '/just-variables.theme.php' );

/**
 *	plugin init
 */
add_action('plugins_loaded', 'jv_init');
function jv_init(){
	if( !is_admin() ) return;
	
	/**
	 *	load translations
	 */
	load_plugin_textdomain( JV_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	// add admin page
	add_action( 'admin_menu', 'jv_admin_menu' );
}

/*
 	functions for templates to print variables
 */

/**
 *	get actual value for variable
 *	@param string $var variable name
 *	@return string  return variable value or NULL
 */
function jv_get_variable_value( $var ){
	$values = get_option('jv_values');
	if( !empty($values[$var]) ){
		return $values[$var];
	}
	
	return NULL;
}


/**
 *	get actual value for variable
 *	@param string $var   variable name
 *	@param bool   $echo  print variable by default.
 *	@return string  return variable value or NULL
 */
function just_variable( $var, $echo = true ){
	$value = jv_get_variable_value( $var );
	if( !is_null($value) && $echo ){
		echo $value;
	}
	else{
		return $value;
	}
}

/**
 *	register custom shortcode to print variables in the content
 *	@param array $atts attributes array submitted to shortcode
 *	@return string  return parsed shortcode
 */
function just_variable_shortcode( $atts ){
	if( empty($atts['code']) ){
		return '';
	}
	else{
		return just_variable( $atts['code'], false );
	}
}
add_shortcode('justvar', 'just_variable_shortcode');



?>