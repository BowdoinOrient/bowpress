<?php
if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed');

/*
This is a small glue class, which makes available all the commands in WP_Optimize_Commands, and translates the response from WP_Optimize_Commands (which is either data to return, or a WP_Error) into the format used by UpdraftCentral.
*/

class UpdraftCentral_WP_Optimize_Commands extends UpdraftCentral_Commands {

	private $commands;

	public function __construct() {
	
		if (!class_exists('WP_Optimize_Commands')) require_once(WPO_PLUGIN_MAIN_PATH.'includes/class-commands.php');
		$this->commands = new WP_Optimize_Commands();
		
	}

	public function __call($name, $arguments) {
	
		$result = call_user_func_array(array($this->commands, $name), $arguments);
		
		if (is_wp_error($result)) {
		
			return $this->_generic_error_response($result->get_error_code(), $result->get_error_data());
		
		} else {
		
			return $this->_response($result);
		
		}
		
	}

}
