<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/**
 * Holds utility functions used by file based cache
 */

/**
 * Cache output before it goes to the browser
 *
 * @param  string $buffer Page HTML.
 * @param  int    $flags  OB flags to be passed through.
 * @return string
 */
function wpo_cache($buffer, $flags) {
	global $post;

	if (strlen($buffer) < 255) {
		return $buffer;
	}

	// Don't cache search, 404, or password protected.
	if (is_404() || is_search() || !empty($post->post_password)) {
		return $buffer;
	}

	// No root cache folder, exit here
	if (!file_exists(WPO_CACHE_DIR)) {
		// Can not cache!
		return $buffer;
	}

	// Try creating a folder for cached files, if it was flushed recently
	if (!file_exists(WPO_CACHE_FILES_DIR)) {
		if (!mkdir(WPO_CACHE_FILES_DIR)) {
			// Can not cache!
			return $buffer;
		}
	}

	$buffer = apply_filters('wpo_pre_cache_buffer', $buffer);

	$url_path = wpo_get_url_path();

	$dirs = explode('/', $url_path);

	$path = WPO_CACHE_FILES_DIR;

	foreach ($dirs as $dir) {
		if (!empty($dir)) {
			$path .= '/' . $dir;

			if (!file_exists($path)) {
				if (!mkdir($path)) {
					// Can not cache!
					return $buffer;
				}
			}
		}
	}

	$modified_time = time(); // Make sure modified time is consistent.

	// Prevent mixed content when there's an http request but the site URL uses https.
	$home_url = get_home_url();
	
	if (!is_ssl() && 'https' === strtolower(parse_url($home_url, PHP_URL_SCHEME))) {
		$https_home_url = $home_url;
		$http_home_url  = str_ireplace('https://', 'http://', $https_home_url);
		$buffer		 = str_replace(esc_url($http_home_url), esc_url($https_home_url), $buffer);
	}

	if (preg_match('#</html>#i', $buffer)) {
		if (!empty($GLOBALS['wpo_cache_config']['enable_mobile_caching']) && wpo_is_mobile()) {
			$buffer .= "\n<!-- Cached by WP Optimize for mobile devices - Last modified: " . gmdate('D, d M Y H:i:s', $modified_time) . " GMT -->\n";
		} else {
			$buffer .= "\n<!-- Cached by WP Optimize - Last modified: " . gmdate('D, d M Y H:i:s', $modified_time) . " GMT -->\n";
		}
	}

	if (!empty($GLOBALS['wpo_cache_config']['enable_gzip_compression']) && function_exists('gzencode')) {
		if (!empty($GLOBALS['wpo_cache_config']['enable_mobile_caching']) && wpo_is_mobile()) {
			file_put_contents($path . '/mobile.index.gzip.html', gzencode($buffer, 3));
			touch($path . '/mobile.index.gzip.html', $modified_time);
		} else {
			file_put_contents($path . '/index.gzip.html', gzencode($buffer, 3));
			touch($path . '/index.gzip.html', $modified_time);
		}
	} else {
		if (!empty($GLOBALS['wpo_cache_config']['enable_mobile_caching']) && wpo_is_mobile()) {
			file_put_contents($path . '/mobile.index.html', $buffer);
			touch($path . '/mobile.index.html', $modified_time);
		} else {
			file_put_contents($path . '/index.html', $buffer);
			touch($path . '/index.html', $modified_time);
		}
	}

	header('Cache-Control: no-cache'); // Check back every time to see if re-download is necessary.
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modified_time) . ' GMT');

	if (function_exists('ob_gzhandler') && !empty($GLOBALS['wpo_cache_config']['enable_gzip_compression'])) {
		return ob_gzhandler($buffer, $flags);
	} else {
		return $buffer;
	}
}

/**
 * Serves the cache and exits
 */
function wpo_serve_cache() {
	$file_name = 'index.html';

	if (function_exists('gzencode') && !empty($GLOBALS['wpo_cache_config']['enable_gzip_compression'])) {
		$file_name = 'index.gzip.html';
	}

	if (!empty($GLOBALS['wpo_cache_config']['enable_mobile_caching']) && wpo_is_mobile()) {
		$file_name = 'mobile.' . $file_name;
	}

	$path = WPO_CACHE_FILES_DIR . '/' . rtrim(wpo_get_url_path(), '/') . '/' . $file_name;


	$modified_time = file_exists($path) ? (int) filemtime($path) : time();

	header('Cache-Control: no-cache'); // Check back in an hour.

	if (!empty($modified_time) && !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $modified_time) {
		if (function_exists('gzencode') && !empty($GLOBALS['wpo_cache_config']['enable_gzip_compression'])) {
			header('Content-Encoding: gzip');
		}

		header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
		exit;
	}

	if (file_exists($path) && is_readable($path)) {
		if (function_exists('gzencode') && !empty($GLOBALS['wpo_cache_config']['enable_gzip_compression'])) {
			header('Content-Encoding: gzip');
		}

		readfile($path);

		exit;
	}
}

/**
 * Clears the cache
 */
function wpo_cache_flush() {

	$this->wpo_delete_files(WPO_CACHE_FILES_DIR);

	if (function_exists('wp_cache_flush')) {
		wp_cache_flush();
	}
}

/**
 * Get URL path for caching
 *
 * @since  1.0
 * @return string
 */
function wpo_get_url_path() {

	$host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

	return rtrim($host, '/') . $_SERVER['REQUEST_URI'];
}


/**
 * Return true of exception url matches current url
 *
 * @param  string $exception Exceptions to check URL against.
 * @param  bool   $regex	 Whether to check with regex or not.
 * @return bool   true if matched, false otherwise
 */
function wpo_url_exception_match($exception, $regex = false ) {
	if (preg_match('#^[\s]*$#', $exception)) return false;

	$exception = trim($exception);

	if (!preg_match('#^/#', $exception)) {

		$url = rtrim('http' . (isset($_SERVER['HTTPS']) ? 's' : '' ) . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", '/');

		if ($regex) {
			if (preg_match('#^' . $exception . '$#', $url)) {
				// Exception match!
				return true;
			}
		} elseif (preg_match('#\*$#', $exception)) {
			$filtered_exception = str_replace('*', '', $exception);

			if (preg_match('#^' . $filtered_exception . '#', $url)) {
				// Exception match!
				return true;
			}
		} else {
			$exception = rtrim($exception, '/');

			if (strtolower($exception) === strtolower($url)) {
				// Exception match!
				return true;
			}
		}
	} else {
		$path = $_SERVER['REQUEST_URI'];

		if ($regex) {
			if (preg_match('#^' . $exception . '$#', $path)) {
				// Exception match!
				return true;
			}
		} elseif (preg_match('#\*$#', $exception)) {
			$filtered_exception = preg_replace('#/?\*#', '', $exception);

			if (preg_match('#^' . $filtered_exception . '#i', $path)) {
				// Exception match!
				return true;
			}
		} else {
			if ('/' !== $path) {
				$path = rtrim($path, '/');
			}

			if ('/' !== $exception) {
				$exception = rtrim($exception, '/');
			}

			if (strtolower($exception) === strtolower($path)) {
				// Exception match!
				return true;
			}
		}
	}

	return false;
}

/**
 * Checks if its a mobile device
 *
 * @see https://developer.wordpress.org/reference/functions/wp_is_mobile/
 */
function wpo_is_mobile() {
	if (empty($_SERVER['HTTP_USER_AGENT'])) {
		$is_mobile = false;
	// many mobile devices (all iPhone, iPad, etc.)
	} elseif (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Android')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini')
		|| false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi')
	) {
		$is_mobile = true;
	} else {
		$is_mobile = false;
	}

	return $is_mobile;
}

/**
 * Delete function that deals with directories recursively
 *
 * @param string $src path of the folder
 */
function wpo_delete_files($src) {
	if (!file_exists($src)) return;

	if (is_file($src)) unlink($src);

	$dir = opendir($src);
	$file = readdir($dir);

	while (false !== $file) {
		if ('.' != $file && '..' != $file) {
			if (is_dir($src . '/' . $file)) {
				wpo_delete_files($src . '/' . $file);
			} else {
				unlink($src . '/' . $file);
			}
		}
	}

	closedir($dir);
	rmdir($src);
}
