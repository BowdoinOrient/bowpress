<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/**
 * Page caching rules and exceptions
 */

if (!class_exists('WPO_Cache_Config')) require_once('class-wpo-cache-config.php');

if (!class_exists('WPO_Cache_Rules')) :

class WPO_Cache_Rules {

	/**
	 * Cache config object
	 *
	 * @var mixed
	 */
	public $config;

	/**
	 * Instance of this class
	 *
	 * @var mixed
	 */
	public static $instance;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->config = WPO_Cache_Config::instance()->get();
		$this->setup_hooks();
	}

	/**
	 * Setup hooks/filters
	 */
	public function setup_hooks() {
		add_action('pre_post_update', array($this, 'purge_post_on_update'), 10, 1);
		add_action('save_post', array($this, 'purge_post_on_update'), 10, 1);
		add_action('wp_trash_post', array($this, 'purge_post_on_update'), 10, 1);
		add_action('wp_set_comment_status', array($this, 'purge_post_on_comment_status_change'), 10);
		add_action('set_comment_cookies', array($this, 'set_comment_cookie_exceptions'), 10);
	}

	/**
	 * When user posts a comment, set a cookie so we don't show them page cache
	 *
	 * @param WP_Comment $comment Comment to check.
	 */
	public function set_comment_cookie_exceptions($comment) {
	
		if (empty($this->config['enable_page_caching'])) return;
		
		$path = $this->get_post_path($comment->comment_post_ID);

		$this->purge_from_cache($path);
	}
		
	/**
	 * Purge files for a particular path from the cache
	 *
	 * @param String $path - the path
	 */
	public function purge_from_cache($path) {
		WPO_Page_Cache::delete(untrailingslashit($path) . '/index.html');
		WPO_Page_Cache::delete(untrailingslashit($path) . '/index.gzip.html');

		if (!empty($this->config['enable_mobile_caching'])) {
			WPO_Page_Cache::delete(untrailingslashit($path) . '/mobile.index.html');
			WPO_Page_Cache::delete(untrailingslashit($path) . '/mobile.index.gzip.html');
		}
	}
		

	/**
	 * Get the cache path for a given post
	 *
	 * @param Integer $post_id - WP post ID
	 *
	 * @return String
	 */
	private function get_post_path($post_id) {
		return WPO_CACHE_DIR . preg_replace('#^https?://#i', '', get_permalink($post_id));
	}
	
	/**
	 * Every time a comment's status changes, purge it's parent posts cache
	 *
	 * @param Integer $comment_id Comment ID.
	 */
	public function purge_post_on_comment_status_change($comment_id) {

		if (empty($this->config['enable_page_caching'])) return;
		
		$comment = get_comment($comment_id);

		$path = $this->get_post_path($comment->comment_post_ID);

		$this->purge_from_cache($path);
		
	}

	/**
	 * Automatically purge all file based page cache on post changes
	 * We want the whole cache purged here as different parts
	 * of the site could potentially change on post updates
	 *
	 * @param Integer $post_id WordPress post id
	 */
	public function purge_post_on_update($post_id) {
		$post_type = get_post_type($post_id);

		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || 'revision' === $post_type) {
			return;
		} elseif (!current_user_can('edit_post', $post_id) && (!defined('DOING_CRON') || !DOING_CRON)) {
			return;
		}

		if (!empty($this->config['enable_page_caching'])) {
			wpo_cache_flush();
		}
	}

	/**
	 * Returns an instance of the current class, creates one if it doesn't exist
	 *
	 * @return WPO_Cache_Rules
	 */
	public static function instance() {
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

endif;
