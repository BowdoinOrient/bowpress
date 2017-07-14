<?php

if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed');

if (!class_exists('Updraft_Notices_1_0')) require_once(WPO_PLUGIN_MAIN_PATH.'/includes/updraft-notices.php');

class WP_Optimize_Notices extends Updraft_Notices_1_0 {

	protected static $_instance = null;

	private $initialized = false;

	protected $self_affiliate_id = 216;

	protected $notices_content = array();

	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function populate_notices_content() {
		
		$parent_notice_content = parent::populate_notices_content();

		$child_notice_content = array(
			'updraftplus' => array(
				'prefix' => '',
				'title' => __('Make sure you backup as well as optimize your database', 'wp-optimize'), 
				'text' => __("UpdraftPlus is the world's most trusted backup plugin from the owners of WP-Optimize", 'wp-optimize'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://wordpress.org/plugins/updraftplus/',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_page_notice_until',
				'supported_positions' => $this->dashboard_top_or_report,
				'validity_function' => 'is_updraftplus_installed',
			),
			'updraftcentral' => array(
				'prefix' => '',
				'title' => __('Introducing UpdraftCentral - from the team behind WP-Optimize', 'wp-optimize'), 
				'text' => __('UpdraftCentral is a highly efficient way to manage, update and backup multiple websites from one place.', 'wp-optimize'),
				'image' => 'notices/updraft_logo.png',
				'button_link' => 'https://updraftcentral.com',
				'button_meta' => 'updraftcentral',
				'dismiss_time' => 'dismiss_page_notice_until',
				'supported_positions' => $this->dashboard_top_or_report,
				'validity_function' => 'is_updraftcentral_installed',
			),
			'rate_plugin' => array(
				'prefix' => '',
				'title' => __('Like WP-Optimize and can spare one minute?', 'wp-optimize'), 
				'text' => __('Please help WP-Optimize by giving a positive review at wordpress.org.', 'wp-optimize'),
				'image' => 'notices/wp_optimize_logo.png',
				'button_link' => 'https://wordpress.org/support/plugin/wp-optimize/reviews/?rate=5#new-post',
				'button_meta' => 'review',
				'dismiss_time' => 'dismiss_page_notice_until',
				'supported_positions' => $this->anywhere,
			),
			'translation_needed' => array(
				'prefix' => '',
				'title' => 'Can you translate? Want to improve WP-Optimize for speakers of your language?',
				'text' => $this->url_start(true,'translate.wordpress.org/projects/wp-plugins/wp-optimize')."Please go here for instructions - it is easy.".$this->url_end(true,'translate.wordpress.org/projects/wp-plugins/wp-optimize'),
				'text_plain' => $this->url_start(false,'translate.wordpress.org/projects/wp-plugins/wp-optimize')."Please go here for instructions - it is easy.".$this->url_end(false,'translate.wordpress.org/projects/wp-plugins/wp-optimize'),
				'image' => 'notices/wp_optimize_logo.png',
				'button_link' => false,
				'dismiss_time' => false,
				'supported_positions' => $this->anywhere,
				'validity_function' => 'translation_needed',
			),
		);

		return array_merge($parent_notice_content, $child_notice_content);
	}
	
	// Call this method to setup the notices
	public function notices_init() {
		if ($this->initialized) return;
		$this->initialized = true;
		$this->notices_content = (defined('WP_OPTIMIZE_NOADS_B') && WP_OPTIMIZE_NOADS_B) ? array() : $this->populate_notices_content();
		$our_version = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? WPO_VERSION.'.'.time() : WPO_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_style('wp-optimize-notices-css',  WPO_PLUGIN_URL.'/css/wp-optimize-notices'.$min_or_not.'.css', array(), $our_version);
	}

	protected function is_updraftplus_installed($product = 'updraftplus', $also_require_active = false) {
		return parent::is_plugin_installed($product, $also_require_active);
	}

	protected function is_updraftcentral_installed($product = 'updraftcentral', $also_require_active = false) {
		return parent::is_plugin_installed($product, $also_require_active);
	}

	public function is_backup_plugin_installed($product = null, $also_require_active = false) {
		$backup_plugins = array('updraftplus' => 'UpdraftPlus', 'backwpup' => 'BackWPup', 'backupwordpress' => 'BackupWordPress', 'vaultpress' => 'VaultPress', 'wp-db-backup' => 'WP-DB-Backup', 'backupbuddy' => 'BackupBuddy');

		foreach ($backup_plugins as $slug => $title) {
			if (!parent::is_plugin_installed($slug, $also_require_active)) {
				return $title;
			}
		}

		return apply_filters('wp_optimize_is_backup_plugin_installed', false);
	}

	protected function translation_needed($plugin_base_dir = null, $product_name = null) {
		return parent::translation_needed(WPO_PLUGIN_MAIN_PATH, 'wp-optimize');
	}
	
	protected function url_start($html_allowed = false, $url, $https = false, $website_home = 'updraftplus.com/wp-optimize') {
		return parent::url_start($html_allowed, $url, $https, $website_home);
	}
	
	protected function check_notice_dismissed($dismiss_time){

		$time_now = defined('WP_OPTIMIZE_NOTICES_FORCE_TIME') ? WP_OPTIMIZE_NOTICES_FORCE_TIME : time(); 
	
		$options = WP_Optimize()->get_options();

		$notice_dismiss = ($time_now < $options->get_option('dismiss_page_notice_until', 0));

		$dismiss = false;

		if ('dismiss_page_notice_until' == $dismiss_time) $dismiss = $notice_dismiss;

		return $dismiss;
	}

	protected function render_specified_notice($advert_information, $return_instead_of_echo = false, $position = 'top') {
	
		if ('bottom' == $position) {
			$template_file = 'bottom-notice.php';
		} elseif ('report' == $position) {
			$template_file = 'report.php';
		} elseif ('report-plain' == $position) {
			$template_file = 'report-plain.php';
		} else {
			$template_file = 'horizontal-notice.php';
		}
		
		$extract_variables = array_merge($advert_information, array('wp_optimize_notices' => $this));

		return WP_Optimize()->include_template('notices/'.$template_file, $return_instead_of_echo, $extract_variables);
	}
}

$GLOBALS['wp_optimize_notices'] = WP_Optimize_Notices::instance();
