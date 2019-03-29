<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/**
 * File based page cache drop in
 */
require_once(dirname(__FILE__) . '/file-based-page-cache-functions.php');

// Don't cache robots.txt or htacesss.
if (false !== strpos($_SERVER['REQUEST_URI'], 'robots.txt') || false !== strpos($_SERVER['REQUEST_URI'], '.htaccess')) {
	return;
}

// Don't cache non-GET requests.
if (!isset($_SERVER['REQUEST_METHOD']) || 'GET' !== $_SERVER['REQUEST_METHOD']) return;

$file_extension = $_SERVER['REQUEST_URI'];
$file_extension = preg_replace('#^(.*?)\?.*$#', '$1', $file_extension);
$file_extension = trim(preg_replace('#^.*\.(.*)$#', '$1', $file_extension));

// Don't cache disallowed extensions. Prevents wp-cron.php, xmlrpc.php, etc.
if (!preg_match('#index\.php$#i', $_SERVER['REQUEST_URI']) && in_array($file_extension, array( 'php', 'xml', 'xsl' ))) {
	return;
}

// Don't cache if logged in.
if (!empty($_COOKIE)) {
	$wp_cookies = array( 'wordpressuser_', 'wordpresspass_', 'wordpress_sec_', 'wordpress_logged_in_' );

	foreach ($_COOKIE as $key => $value) {
		foreach ($wp_cookies as $cookie) {
			if (strpos($key, $cookie) !== false) {
				// Logged in!
				return;
			}
		}
	}

	if (!empty($_COOKIE['wpo_commented_posts'])) {
		foreach ($_COOKIE['wpo_commented_posts'] as $path) {
			if (rtrim($path, '/') === rtrim($_SERVER['REQUEST_URI'], '/')) {
				// User commented on this post.
				return;
			}
		}
	}
}

// Deal with optional cache exceptions.
if (!empty($GLOBALS['wpo_cache_config']['cache_exception_urls'])) {
	$exceptions = preg_split('#(\n|\r)#', $GLOBALS['wpo_cache_config']['cache_exception_urls']);
	$regex = !empty($GLOBALS['wpo_cache_config']['enable_url_exemption_regex']);

	foreach ($exceptions as $exception) {
		if (wpo_url_exception_match($exception, $regex)) {
			// Exception match.
			return;
		}
	}
}

wpo_serve_cache();

ob_start('wpo_cache');
