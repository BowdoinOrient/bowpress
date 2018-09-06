<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_Email_Logger')) return;

/**
 * Class Updraft_Email_Logger
 */
class Updraft_Email_Logger extends Updraft_Abstract_Logger {

	protected $allow_multiple = true;

	/**
	 * Updraft_Email_Logger constructor
	 */
	public function __construct() {
	}

	/**
	 * Returns logger description
	 *
	 * @return string
	 */
	public function get_description() {
		return __('Log events to email', 'wp-optimize');
	}

	/**
	 * Returns list of logger options.
	 *
	 * @return array
	 */
	public function get_options_list() {
		return array(
			'emails' => array(
				__('Enter email for logs here', 'wp-optimize'),
				'email', // validator
			)
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
	 * @param  mixed  $level
	 * @param  string $message
	 * @param  array  $context
	 * @return null|void
	 */
	public function log($level, $message, array $context = array()) {

		if (!$this->is_enabled()) return false;

		$log = WP_Optimize()->get_options()->get_option('updraft_mail_logger_log', array());

		$message = '['.Updraft_Log_Levels::to_text($level).'] : '.$this->interpolate($message, $context);

		$log[] = $message;
		WP_Optimize()->get_options()->update_option('updraft_mail_logger_log', $log);
	}

	/**
	 * Add recipient email
	 *
	 * @param string $email
	 */
	public function add_email($email) {
		$emails = $this->get_option('emails', array());
		$emails[] = $email;
		$this->set_option('emails', $emails);
	}

	/**
	 * Return list of recipients email
	 *
	 * @return null
	 */
	public function get_emails() {
		return $this->get_option('emails', get_option('admin_email'));
	}

	/**
	 * Email and clear log
	 */
	public function flush_log() {
		$log = $this->get_log();
		if (empty($log)) return;

		WP_Optimize()->get_options()->update_option('updraft_mail_logger_log', array());

		if (!$this->is_enabled()) return;

		$email_addresses = $this->get_emails();
		$subject = $this->get_option('updraft_mail_logger_subject', 'Updraft Email Log');

		$log = join("\n", $log);

		wp_mail($email_addresses, $subject, $log);
	}

	/**
	 * Return log messages
	 *
	 * @return mixed|void
	 */
	public function get_log() {
		return WP_Optimize()->get_options()->get_option('updraft_mail_logger_log', array());
	}
}
