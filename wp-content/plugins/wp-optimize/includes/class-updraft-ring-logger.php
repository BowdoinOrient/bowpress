<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_Ring_Logger')) return;

/**
 * Class Updraft_Ring_Logger
 */
class Updraft_Ring_Logger extends Updraft_Abstract_Logger {

	/**
	 * Updraft_Ring_Logger constructor
	 */
	public function __construct() {
	}

	/**
	 * Returns logger description
	 *
	 * @return string|void
	 */
	public function get_description() {
		return __('Store the most recent log entries in the WordPress database', 'wp-optimize');
	}

	/**
	 * Returns list of logger options.
	 *
	 * @return array
	 */
	public function get_options_list() {
		return array(
			'ring_logger_limit' => __('How many last records store?', 'wp-optimize')
		);
	}

	/**
	 * Emergency message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function emergency($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::EMERGENCY, $message, $context);
	}

	/**
	 * Alert message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function alert($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::ALERT, $message, $context);
	}

	/**
	 * Critical message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function critical($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::CRITICAL, $message, $context);
	}

	/**
	 * Error message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function error($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::ERROR, $message, $context);
	}

	/**
	 * Warning message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function warning($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::WARNING, $message, $context);
	}

	/**
	 * Notice message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function notice($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::NOTICE, $message, $context);
	}

	/**
	 * Info message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function info($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::INFO, $message, $context);
	}

	/**
	 * Debug message
	 *
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function debug($message, array $context = array()) {
		$this->log(Updraft_Log_Levels::DEBUG, $message, $context);
	}

	/**
	 * Log message with any level
	 *
	 * @param  string $level
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function log($level, $message, array $context = array()) {

		if (!$this->is_enabled()) return false;

		$message = date("Y-m-d H:i:s").' ['.Updraft_Log_Levels::to_text($level).'] : '.$this->interpolate($message, $context);
		$this->add_log($message);
	}

	/**
	 * Add message to log
	 *
	 * @param string $message Message to be added to log.
	 */
	public function add_log($message) {
		$log_option_name = $this->get_logger_option_name();
		$log_limit = $this->get_logger_limit();
		$log = $this->get_log();
		$log[] = $message;
		while (count($log) > 0 && count($log) > $log_limit) {
			array_shift($log);
		}
		update_option($log_option_name, $log);
	}

	/**
	 * Return logger option name value
	 *
	 * @return string
	 */
	public function get_logger_option_name() {
		return 'updraft_ring_log';
	}

	/**
	 * Return logger limit value
	 *
	 * @return string
	 */
	public function get_logger_limit() {
		return $this->get_option('ring_logger_limit', 20);
	}

	/**
	 * Set logger wordpress option name where log will stored
	 *
	 * @param string $option_name Name for logger option.
	 */
	public function set_logger_option_name($option_name) {
		$this->set_option('ring_logger_option_name', $option_name);
	}

	/**
	 * Return log content
	 *
	 * @return mixed|void
	 */
	public function get_log() {
		return get_option($this->get_logger_option_name(), array());
	}
}
