<?php

/**
 * Class WP_Optimize_Gzip_Compression
 */
class WP_Optimize_Gzip_Compression {

	/**
	 * WP_Optimize_Htaccess instance.
	 *
	 * @var WP_Optimize_Htaccess
	 */
	private $_htaccess = null;

	/**
	 * WP_Optimize instance.
	 *
	 * @var WP_Optimize
	 */
	private $_wp_optimize = null;

	/**
	 * Gzip section in htaccess will wrapped with this comment
	 *
	 * @var string
	 */
	private $_htaccess_section_comment = 'WP-Optimize Gzip compression';

	/**
	 * WP_Optimize_Gzip_Compression constructor.
	 */
	public function __construct() {
		$this->_wp_optimize = WP_Optimize();
		$this->_htaccess = $this->_wp_optimize->get_htaccess();
	}

	/**
	 * Make http request to theme style.css and check returned headers for gzip encoding option.
	 *
	 * @return bool|WP_Error
	 */
	public function check_headers_for_gzip() {
		// get url to theme style.css file.
		$url = get_template_directory_uri() . '/style.css';
		// trying to load style.css.
		$response = wp_remote_get($url, array('timeout' => 10));

		if (is_wp_error($response)) return $response;

		// get returned headers.
		$headers = wp_remote_retrieve_headers($response);

		/**
		 * Since 4.6.0 wp_remote_retrieve_headers() returns Requests_Utility_CaseInsensitiveDictionary instance.
		 * Therefore we use getAll() function to get array with headers as array and keep compatibility with
		 * previous WordPress versions.
		 */
		if (is_a($headers, 'Requests_Utility_CaseInsensitiveDictionary')) {
			$headers = $headers->getAll();
		}

		// check if there exists Content-encoding header with gzip value.
		if (array_key_exists('content-encoding', $headers) && preg_match('/gzip/i', $headers['content-encoding'])) {
			return true;
		}

		return false;
	}

	/**
	 * Make request to checkgzipcompression.com api and check if gzip option enabled.
	 *
	 * @return bool|WP_Error
	 */
	public function check_api_for_gzip() {
		$url = get_template_directory_uri() . '/style.css';

		$api_url = 'https://checkgzipcompression.com/js/checkgzip.json?url=' . urlencode($url);

		$result = wp_remote_get($api_url, array('timeout' => 10));

		if (is_wp_error($result)) return $result;

		if (!isset($result['body'])) return new WP_Error('Gzip', __("We can't definitely determine Gzip status as API doesn't return correct answer.", 'wp-optimize'));

		$body = json_decode($result['body']);

		if (isset($body->error) && $body->error)  return new WP_Error('Gzip', __("We can't definitely determine Gzip status as API doesn't return correct answer.", 'wp-optimize'));

		if ($body->result->gzipenabled && !$body->error) {
			return true;
		}

		return false;
	}

	/**
	 * Check if Gzip compression is enabled.
	 *
	 * @return bool|WP_Error
	 */
	public function is_gzip_compression_enabled() {
		// trying to get info about gzip in headers.
		$is_gzip_compression_enabled = $this->check_headers_for_gzip();

		// if we got error then trying to get info from api otherwise get result from check_headers_for_gzip().
		$is_gzip_compression_enabled = is_wp_error($is_gzip_compression_enabled) ? $this->check_api_for_gzip() : $is_gzip_compression_enabled;

		// we can't determine then return WP_Error.
		if (is_wp_error($is_gzip_compression_enabled)) return $is_gzip_compression_enabled;

		// if Gzip is not enabled but we have added settings and Apache modules nt loaded then return error.
		if (false == $is_gzip_compression_enabled && $this->is_gzip_compression_section_exists() && false === $this->_wp_optimize->is_apache_module_loaded(array('mod_filter', 'mod_deflate'))) {
			return new WP_Error('Gzip', __('We successfully added Gzip compression settings into .htaccess file. But it seems one of Apache modules - mod_filter or mod_deflate is not active.', 'wp-optimize'));
		}

		return $is_gzip_compression_enabled;
	}

	/**
	 * Check if section with Gzip options already exists in htaccess file.
	 *
	 * @return bool
	 */
	public function is_gzip_compression_section_exists() {
		return $this->_htaccess->is_commented_section_exists($this->_htaccess_section_comment);
	}

	/**
	 * Enable Gzip compression - add settings into .htaccess.
	 */
	public function enable() {
		$this->_htaccess->update_commented_section($this->prepare_gzip_section(), $this->_htaccess_section_comment);
		$this->_htaccess->write_file();
	}

	/**
	 * Disable Gzip compression - remove settings from .htaccess.
	 */
	public function disable() {
		$this->_htaccess->remove_commented_section($this->_htaccess_section_comment);
		$this->_htaccess->write_file();
	}

	/**
	 * Handler for Gzip compression enable command, called from WP_Optimize_Commands.
	 *
	 * @param array $params - ['enable' => true|false]
	 * @return array
	 */
	public function enable_gzip_command_handler($params) {
		$section_updated = false;

		$enable = (isset($params['enable']) && $params['enable']) ? true : false;

		if ($this->_htaccess->is_writable()) {

			// update commented section
			if ($enable) {
				$this->enable();
			} else {
				$this->disable();
			}

			// read updated file.
			$this->_htaccess->read_file();
			// check if section added or removed successfully.
			$section_exists = $this->_htaccess->is_commented_section_exists($this->_htaccess_section_comment);
			// set correct $section-updated flag.
			$section_updated = $enable === $section_exists;
		}

		$is_gzip_compression_enabled = $this->is_gzip_compression_enabled();

		if ($section_updated) {
			return array(
				'success' => true,
				'enabled' => is_wp_error($is_gzip_compression_enabled) ? false : $is_gzip_compression_enabled,
				// if we can't determine gzip status then return error message.
				'message' => is_wp_error($is_gzip_compression_enabled) ? $is_gzip_compression_enabled->get_error_message() : '',
			);
		} else {
			$gzip_section = $this->prepare_gzip_section();

			if ($is_gzip_compression_enabled) {
				$message = sprintf(__('We can\'t update your %s file. Please try to remove following lines manually:', 'wp-optimize'), $this->_htaccess->get_filename());
			} else {
				$message = sprintf(__('We can\'t update your %s file. Please try to add following lines manually:', 'wp-optimize'), $this->_htaccess->get_filename());
			}

			return array(
				'success' => false,
				'enabled' => is_wp_error($is_gzip_compression_enabled) ? false : $is_gzip_compression_enabled,
				'message' => $message,
				'output' =>
					htmlentities($this->_htaccess->get_section_begin_comment($this->_htaccess_section_comment).PHP_EOL.
					join(PHP_EOL, $this->_htaccess->get_flat_array($gzip_section)).
					PHP_EOL.$this->_htaccess->get_section_end_comment($this->_htaccess_section_comment)),
			);
		}
	}

	/**
	 * Prepare array with options to switch on gzip in htaccess.
	 *
	 * @return array
	 */
	private function prepare_gzip_section() {
		return array(
			array(
				'<IfModule mod_filter.c>',
				array(
					'<IfModule mod_deflate.c>',
					'# Compress HTML, CSS, JavaScript, Text, XML and fonts',
					'AddType application/vnd.ms-fontobject .eot',
					'AddType font/ttf .ttf',
					'AddType font/otf .otf',
					'AddType font/x-woff .woff',
					'AddType image/svg+xml .svg',
					'',
					'AddOutputFilterByType DEFLATE application/javascript',
					'AddOutputFilterByType DEFLATE application/rss+xml',
					'AddOutputFilterByType DEFLATE application/vnd.ms-fontobject',
					'AddOutputFilterByType DEFLATE application/x-font',
					'AddOutputFilterByType DEFLATE application/x-font-opentype',
					'AddOutputFilterByType DEFLATE application/x-font-otf',
					'AddOutputFilterByType DEFLATE application/x-font-truetype',
					'AddOutputFilterByType DEFLATE application/x-font-ttf',
					'AddOutputFilterByType DEFLATE application/x-font-woff',
					'AddOutputFilterByType DEFLATE application/x-javascript',
					'AddOutputFilterByType DEFLATE application/xhtml+xml',
					'AddOutputFilterByType DEFLATE application/xml',
					'AddOutputFilterByType DEFLATE font/opentype',
					'AddOutputFilterByType DEFLATE font/otf',
					'AddOutputFilterByType DEFLATE font/ttf',
					'AddOutputFilterByType DEFLATE font/woff',
					'AddOutputFilterByType DEFLATE image/svg+xml',
					'AddOutputFilterByType DEFLATE image/x-icon',
					'AddOutputFilterByType DEFLATE text/css',
					'AddOutputFilterByType DEFLATE text/html',
					'AddOutputFilterByType DEFLATE text/javascript',
					'AddOutputFilterByType DEFLATE text/plain',
					'AddOutputFilterByType DEFLATE text/xml',
					'',
					'# Remove browser bugs (only needed for really old browsers)',
					'BrowserMatch ^Mozilla/4 gzip-only-text/html',
					'BrowserMatch ^Mozilla/4\.0[678] no-gzip',
					'BrowserMatch \bMSIE !no-gzip !gzip-only-text/html',
					'Header append Vary User-Agent',
					'</IfModule>',
				),
				'</IfModule>',
			),
		);
	}
}
