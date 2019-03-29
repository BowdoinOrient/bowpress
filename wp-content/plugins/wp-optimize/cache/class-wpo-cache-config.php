<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/**
 * Handles cache configuration and related I/O
 */

if (!class_exists('WPO_Cache_Config')) :

class WPO_Cache_Config {

	/**
	 * Defaults
	 *
	 * @var array
	 */
	public $defaults;

	/**
	 * Instance of this class
	 *
	 * @var mixed
	 */
	public static $instance;


	/**
	 * Set config defaults
	 */
	public function __construct() {
		$this->defaults = $this->get_defaults();
	}

	/**
	 * Get config from file or cache
	 *
	 * @return array
	 */
	public function get() {

		if (is_multisite()) {
			$config = get_site_option('wpo_cache_config', $this->get_defaults());
		} else {
			$config = get_option('wpo_cache_config', $this->get_defaults());
		}

		return wp_parse_args($config, $this->get_defaults());
	}


	/**
	 * Updates the given config object in file and DB
	 *
	 * @param  array $config - the cache configuration
	 * @return bool
	 */
	public function update($config) {
		$config = wp_parse_args($config, $this->get_defaults());

		if (is_multisite()) {
			update_site_option('wpo_cache_config', $config);
		} else {
			update_option('wpo_cache_config', $config);
		}

		return $this->write($config);
	}

	/**
	 * Deletes config files and options
	 *
	 * @return bool
	 */
	public function delete() {

		if (is_multisite()) {
			delete_site_option('wpo_cache_config');
		} else {
			delete_option('wpo_cache_config');
		}
		
		if (!WPO_Page_Cache::delete(WPO_CACHE_CONFIG_DIR)) {
			return false;
		}

		return true;
	}

	/**
	 * Writes config to file
	 *
	 * @param  array $config Configuration array.
	 * @return bool
	 */
	private function write($config) {

		$url = parse_url(site_url());

		if ($url['port']) {
			$config_file = WPO_CACHE_CONFIG_DIR.'/config-'.$url['host'].':'.$url['port'].'.php';
		} else {
			$config_file = WPO_CACHE_CONFIG_DIR.'/config-'.$url['host'].'.php';
		}

		$this->config = wp_parse_args($config, $this->get_defaults());

		if (!file_put_contents($config_file, json_encode($this->config))) {
			return false;
		}

		return true;
	}

	/**
	 * Return defaults
	 *
	 * @return array
	 */
	public function get_defaults() {

		$defaults = array(
			'enable_page_caching'			=> true,
			'enable_mobile_caching'			=> true,
			'enable_gzip_compression'		=> true,
			'page_cache_length'				=> 86400,
			'cache_exception_urls'			=> array(),
			'enable_url_exemption_regex'	=> false,
		);

		return apply_filters('wpo_cache_defaults', $defaults);
	}

	/**
	 * Return an instance of the current class, create one if it doesn't exist
	 *
	 * @since  1.0
	 * @return SC_Config
	 */
	public static function instance() {

		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
endif;
