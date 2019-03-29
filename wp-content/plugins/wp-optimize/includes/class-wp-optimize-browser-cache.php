<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

require_once 'class-wp-optimize-htaccess.php';

/**
 * Class WP_Optimize_Browser_Cache
 */
class WP_Optimize_Browser_Cache {

	private $_htaccess = null;

	private $_options = null;

	private $_wp_optimize = null;

	/**
	 * Browser cache section in htaccess will wrapped with this comment
	 *
	 * @var string
	 */
	private $_htaccess_section_comment = 'WP-Optimize Browser Cache';

	/**
	 * WP_Optimize_Browser_Cache constructor.
	 */
	public function __construct() {
		$this->_wp_optimize = WP_Optimize();

		$this->_htaccess = new WP_Optimize_Htaccess();

		$this->_options = $this->_wp_optimize->get_options();
	}

	/***
	 * Check headers for Cache-Control and Etag. And if they are exist return true.
	 *
	 * @return bool|WP_Error
	 */
	public function is_cache_enabled() {
		$style = get_template_directory_uri() . '/style.css';

		$response = wp_remote_get($style, array('timeout' => 15));

		if (is_a($response, 'WP_Error')) return $response;

		$headers = wp_remote_retrieve_headers($response);

		if (is_a($headers, 'Requests_Utility_CaseInsensitiveDictionary')) {
			$headers = $headers->getAll();
		}

		if (false === array_key_exists('etag', $headers) && array_key_exists('cache-control', $headers)) {
			return true;
		}

		if ($this->is_browser_cache_section_exists() && !$this->_wp_optimize->is_apache_module_loaded(array('mod_expires', 'mod_headers'))) {
			return new WP_Error('Browser cache', __('We successfully updated your .htaccess file. But it seems one of Apache modules - mod_expires or mod_headers is not active.', 'wp-optimize'));
		}

		return false;
	}

	/**
	 * Enable browser cache - add settings into .htaccess.
	 *
	 * @param string $expiry_time
	 */
	public function enable($expiry_time = '1 month') {
		$this->_htaccess->update_commented_section($this->prepare_browser_cache_section($expiry_time), $this->_htaccess_section_comment);
		$this->_htaccess->write_file();
	}

	/**
	 * Disable cache - remove settings from .htaccess added in enable() function.
	 */
	public function disable() {
		$this->_htaccess->remove_commented_section($this->_htaccess_section_comment);
		$this->_htaccess->write_file();
	}

	/**
	 * Check if section with browser cache settings already exists.
	 */
	public function is_browser_cache_section_exists() {
		return $this->_htaccess->is_commented_section_exists($this->_htaccess_section_comment);
	}

	/**
	 * Handle for enable_browser_cache command used in WP_Optimize_Commands.
	 *
	 * @param array $params - ['browser_cache_expire' => '1 month 15 days 2 hours' || '' - for disable cache]
	 * @return array
	 */
	public function enable_browser_cache_command_handler($params) {
		$expire_days = isset($params['browser_cache_expire_days']) ? $params['browser_cache_expire_days'] : '';
		$expire_hours = isset($params['browser_cache_expire_hours']) ? $params['browser_cache_expire_hours'] : '';

		$current_expire_days = $this->_options->get_option('browser_cache_expire_days', '');
		$current_expire_hours = $this->_options->get_option('browser_cache_expire_hours', '');

		$section_updated = false;

		$expiry_time = $this->prepare_interval($expire_days, $expire_hours);

		$enable = ('' == $expiry_time) ? false : true;

		/**
		 * If we don't need to do anything in .htaccess then return message.
		 */
		if ($enable == $this->_htaccess->is_commented_section_exists() && $expire_days == $current_expire_days && $expire_hours == $current_expire_hours) {
			$message = __('Browser static caching settings already exists in the .htaccess file', 'wp-optimize');

			return array(
				'success' => true,
				'enabled' => $enable,
				'message' => $message,
			);
		}

		if ($this->_htaccess->is_writable()) {
			// update commented section

			if ($enable) {
				$this->enable($expiry_time);
			} else {
				$this->disable();
			}

			// read updated file.
			$this->_htaccess->read_file();
			// check if section added or removed successfully.
			$section_exists = $this->_htaccess->is_commented_section_exists();
			// set correct $section-updated flag.
			$section_updated = $enable === $section_exists;
		}

		if ($section_updated) {
			$enabled = $this->is_cache_enabled();

			// save $expire value to options.
			$this->_options->update_option('browser_cache_expire_days', $expire_days);
			$this->_options->update_option('browser_cache_expire_hours', $expire_hours);

			if (is_wp_error($enabled)) {
				return array(
					'success' => true,
					'enabled' => $enabled,
					'error_message' => $enabled->get_error_message(),
				);
			} else {
				return array(
					'success' => true,
					'enabled' => $enabled,
					'message' => __('We successfully updated your .htaccess file.', 'wp-optimize'),
				);
			}
		} else {
			$cache_section = $this->prepare_browser_cache_section($expiry_time);

			if ($enable) {
				$message = sprintf(__('We can\'t update your %s file. Please try to add following lines manually:', 'wp-optimize'), $this->_htaccess->get_filename());
				$output = htmlentities($this->_htaccess->get_section_begin_comment() . PHP_EOL .
						  join(PHP_EOL, $this->_htaccess->get_flat_array($cache_section)).
						  PHP_EOL . $this->_htaccess->get_section_end_comment());
			} else {
				$message = sprintf(__('We can\'t update your %s file. Please try to remove following lines manually:', 'wp-optimize'), $this->_htaccess->get_filename());
				$output = htmlentities($this->_htaccess->get_section_begin_comment() . PHP_EOL .
					' ... ... ... '.
					PHP_EOL . $this->_htaccess->get_section_end_comment());
			}

			return array(
				'success' => false,
				'enabled' => $this->is_cache_enabled(),
				'error_message' => $message,
				'output' => $output,
			);
		}
	}

	/**
	 * Use $days an $hours values to build correct time interval as a string like '2 days 3 hours' or empty string if date is empty.
	 *
	 * @param int $days
	 * @param int $hours
	 * @return string
	 */
	private function prepare_interval($days, $hours) {

		$days = floor($days);
		$hours = floor($hours);

		if (0 == $days && 0 == $hours) {
			return '';
		}

		$parts = array();

		// if hours value more than one day then fix it.
		$days += floor($hours / 24);
		$hours = $hours % 24;

		$years = floor($days / 365);
		$days = $days % 365;
		$months = floor($days / 30);
		$days = $days % 30;

		if ($years > 0) {
			$parts[] = $years . ($years > 1 ? ' years' : ' year');
		}

		if ($months > 0) {
			$parts[] = $months . ($months > 1 ? ' months' : ' month');
		}

		if ($days > 0) {
			$parts[] = $days . ($days > 1 ? ' days' : ' day');
		}

		if ($hours > 0) {
			$parts[] = $hours . ($hours > 1 ? ' hours' : ' hour');
		}

		return join(' ', $parts);
	}

	/**
	 * Build browser cache section array.
	 *
	 * @param string $expire - value like - 1 day 12 hours 15 minutes
	 * @return array
	 */
	public function prepare_browser_cache_section($expire) {
		return array(
			array(
				'<IfModule mod_expires.c>',
				'ExpiresActive On',
				'ExpiresByType text/css "access '.$expire.'"',
				'ExpiresByType text/html "access '.$expire.'"',
				'ExpiresByType image/gif "access '.$expire.'"',
				'ExpiresByType image/png "access '.$expire.'"',
				'ExpiresByType image/jpg "access '.$expire.'"',
				'ExpiresByType image/jpeg "access '.$expire.'"',
				'ExpiresByType image/webp "access '.$expire.'"',
				'ExpiresByType image/x-icon "access '.$expire.'"',
				'ExpiresByType application/pdf "access '.$expire.'"',
				'ExpiresByType application/javascript "access '.$expire.'"',
				'ExpiresByType text/x-javascript "access '.$expire.'"',
				'ExpiresByType application/x-shockwave-flash "access '.$expire.'"',
				'ExpiresDefault "access '.$expire.'"',
				'</IfModule>',
			),
			'',
			array(
				'<IfModule mod_headers.c>',
				array(
					'<filesMatch "\.(ico|jpe?g|png|gif|webp|swf)$">',
					'Header set Cache-Control "public"',
					'</filesMatch>',
				),
				array(
					'<filesMatch "\.(css)$">',
					'Header set Cache-Control "public"',
					'</filesMatch>',
				),
				array(
					'<filesMatch "\.(js)$">',
					'Header set Cache-Control "private"',
					'</filesMatch>',
				),
				array(
					'<filesMatch "\.(x?html?|php)$">',
					'Header set Cache-Control "private, must-revalidate"',
					'</filesMatch>',
				),
				'</IfModule>',
			),
			'',
			'#Disable ETag',
			'FileETag None',
		);
	}
}
