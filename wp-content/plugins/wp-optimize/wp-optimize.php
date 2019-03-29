<?php
/**
Plugin Name: WP-Optimize
Plugin URI: https://getwpo.com
Description: WP-Optimize is WordPress's #1 most installed optimization plugin. With it, you can clean up your database easily and safely, without manual queries.
Version: 2.2.13
Author: David Anderson, Ruhani Rabin, Team Updraft
Author URI: https://updraftplus.com
Text Domain: wp-optimize
Domain Path: /languages
License: GPLv2 or later
 */

if (!defined('ABSPATH')) die('No direct access allowed');

// Check to make sure if WP_Optimize is already call and returns.
if (!class_exists('WP_Optimize')) :
define('WPO_VERSION', '2.2.13');
define('WPO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPO_PLUGIN_MAIN_PATH', plugin_dir_path(__FILE__));
define('WPO_PREMIUM_NOTIFICATION', false);

class WP_Optimize {

	public $premium_version_link = 'https://getwpo.com/buy/';

	private $template_directories;

	protected static $_instance = null;

	protected static $_optimizer_instance = null;

	protected static $_options_instance = null;

	protected static $_notices_instance = null;

	protected static $_logger_instance = null;

	protected static $_browser_cache = null;

	protected static $_db_info = null;

	protected static $_cache = null;

	protected static $_gzip_compression = null;

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Checks if premium is installed along with plugins needed.
		add_action('plugins_loaded', array($this, 'plugins_loaded'), 1);

		register_activation_hook(__FILE__, 'wpo_activation_actions');
		register_deactivation_hook(__FILE__, 'wpo_deactivation_actions');
		register_uninstall_hook(__FILE__, 'wpo_uninstall_actions');

		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'admin_menu'));

		add_filter("plugin_action_links_".plugin_basename(__FILE__), array($this, 'plugin_settings_link'));
		add_action('wpo_cron_event2', array($this, 'cron_action'));
		add_filter('cron_schedules', array($this, 'cron_schedules'));

		if (!$this->is_premium()) {
			add_action('auto_option_settings', array($this->get_options(), 'auto_option_settings'));
		}

		add_action('wp_ajax_wp_optimize_ajax', array($this, 'wp_optimize_ajax_handler'));

		// Initialize loggers.
		add_action('plugins_loaded', array($this, 'setup_loggers'));

		// Show update to Premium notice for non-premium multisite.
		add_action('wpo_additional_options', array($this, 'show_multisite_update_to_premium_notice'));

		// Action column (show repair button if need).
		add_filter('wpo_tables_list_additional_column_data', array($this, 'tables_list_additional_column_data'), 15, 2);

		include_once(WPO_PLUGIN_MAIN_PATH.'/includes/updraftcentral.php');

		register_shutdown_function(array($this, 'log_fatal_errors'));

		$this->schedule_plugin_cron_tasks();
	}

	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function get_optimizer() {
		if (empty(self::$_optimizer_instance)) {
			if (!class_exists('WP_Optimizer')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimizer.php');
			self::$_optimizer_instance = new WP_Optimizer();
		}
		return self::$_optimizer_instance;
	}

	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('WP_Optimize_Options')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimize-options.php');
			self::$_options_instance = new WP_Optimize_Options();
		}
		return self::$_options_instance;
	}

	public static function get_notices() {
		if (empty(self::$_notices_instance)) {
			if (!class_exists('WP_Optimize_Notices')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/wp-optimize-notices.php');
			self::$_notices_instance = new WP_Optimize_Notices();
		}
		return self::$_notices_instance;
	}

	/**
	 * Create instance of WP_Optimize_Browser_Cache.
	 *
	 * @return WP_Optimize_Browser_Cache
	 */
	public static function get_browser_cache() {
		if (empty(self::$_browser_cache)) {
			if (!class_exists('WP_Optimize_Browser_Cache')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimize-browser-cache.php');
			self::$_browser_cache = new WP_Optimize_Browser_Cache();
		}
		return self::$_browser_cache;
	}

	/**
	 * Returns WP_Optimize_Database_Information instance.
	 *
	 * @return WP_Optimize_Database_Information
	 */
	public function get_db_info() {
		if (empty(self::$_db_info)) {
			if (!class_exists('WP_Optimize_Database_Information')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/wp-optimize-database-information.php');
			self::$_db_info = new WP_Optimize_Database_Information();
		}
		return self::$_db_info;
	}

	/**
	 * Returns instance of WP_Optimize_Gzip_Compression.
	 *
	 * @return WP_Optimize_Gzip_Compression
	 */
	static public function get_gzip_compression() {
		if (empty(self::$_gzip_compression)) {
			if (!class_exists('WP_Optimize_Gzip_Compression')) include_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimize-gzip-compression.php');
			self::$_gzip_compression = new WP_Optimize_Gzip_Compression();
		}
		return self::$_gzip_compression;
	}

	/**
	 * Create instance of WP_Optimize_Htaccess.
	 *
	 * @param string $htaccess_file absolute path to htaccess file, by default it use .htaccess in WordPress root directory.
	 * @return WP_Optimize_Htaccess
	 */
	public static function get_htaccess($htaccess_file = '') {
		if (!class_exists('WP_Optimize_Cache')) {
			include_once(WPO_PLUGIN_MAIN_PATH . '/includes/class-wp-optimize-htaccess.php');
		}

		return new WP_Optimize_Htaccess($htaccess_file);
	}

	/**
	 * Return instance of Updraft_Logger
	 *
	 * @return Updraft_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			include_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-updraft-logger.php');
			self::$_logger_instance = new Updraft_Logger();
		}
		return self::$_logger_instance;
	}

	/**
	 * Load Task Manager
	 */
	public function get_task_manager() {
		include_once(WPO_PLUGIN_MAIN_PATH.'/vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-tasks-activation.php');

		Updraft_Tasks_Activation::check_updates();

		include_once(WPO_PLUGIN_MAIN_PATH . '/vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task-meta.php');
		include_once(WPO_PLUGIN_MAIN_PATH . '/vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task-options.php');
		include_once(WPO_PLUGIN_MAIN_PATH . '/vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task.php');
	}

	/**
	 * Indicate whether we have an associated instance of WP-Optimize Premium or not.
	 *
	 * @returns Boolean
	 */
	public static function is_premium() {
		if (file_exists(WPO_PLUGIN_MAIN_PATH.'/premium.php') && function_exists('WP_Optimize_Premium')) {
			$wp_optimize_premium = WP_Optimize_Premium();
			if (is_a($wp_optimize_premium, 'WP_Optimize_Premium')) return true;
		}
		return false;
	}

	/**
	 * Check if script running on Apache web server.
	 *
	 * @return bool
	 */
	public function is_apache_server() {
		return (preg_match('/apache/i', $_SERVER['SERVER_SOFTWARE']) || (function_exists('apache_get_version') && apache_get_version())) ? true : false;
	}

	/**
	 * Check if Apache module or modules active.
	 *
	 * @param string|array $module single Apache module name or Apache module names.
	 * @return bool
	 */
	public function is_apache_module_loaded($module) {
		if (!$this->is_apache_server()) return false;

		$module_loaded = true;

		if (is_array($module)) {
			foreach ($module as $single_module) {
				if (!in_array($single_module, apache_get_modules())) {
					$module_loaded = false;
					break;
				}
			}
		} else {
			$module_loaded = in_array($module, apache_get_modules());
		}

		return $module_loaded;
	}

	/**
	 * Checks if this is the premium version and loads it. It also ensures that if the free version is installed then it is disabled with an appropriate error message.
	 */
	public function plugins_loaded() {

		if (is_multisite()) {
			add_action('network_admin_menu', array($this, 'admin_menu'));
		}

		// Run Premium loader if it exists
		if (file_exists(WPO_PLUGIN_MAIN_PATH.'/premium.php') && !class_exists('WP_Optimize_Premium')) {
			include_once(WPO_PLUGIN_MAIN_PATH.'/premium.php');
		}

		// load defaults
		WP_Optimize()->get_options()->set_default_options();

		if ($this->is_active('premium') && false !== ($free_plugin = $this->is_active('free'))) {
			if (!function_exists('deactivate_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
			deactivate_plugins($free_plugin);
			// Registers the notice letting the user know it cannot be active if premium is active.
			add_action('admin_notices', array($this, 'show_admin_notice_premium'));
			return;
		}


		// Loads the language file.
		load_plugin_textdomain('wp-optimize', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Check whether one of free/Premium is active (whether it is this instance or not)
	 *
	 * @param String $which - 'free' or 'premium'
	 *
	 * @return String|Boolean - plugin path (if installed) or false if not
	 */
	private function is_active($which = 'free') {
		$active_plugins = $this->get_active_plugins();
		foreach ($active_plugins as $file) {
			if ('wp-optimize.php' == basename($file)) {
				$plugin_dir = WP_PLUGIN_DIR.'/'.dirname($file);
				if (('free' == $which && !file_exists($plugin_dir.'/premium.php')) || ('free' != $which && file_exists($plugin_dir.'/premium.php'))) return $file;
			}
		}
		return false;
	}

	/**
	 * Gets an array of plugins active on either the current site, or site-wide
	 *
	 * @return Array - a list of plugin paths (relative to the plugin directory)
	 */
	private function get_active_plugins() {

		// Gets all active plugins on the current site
		$active_plugins = get_option('active_plugins');

		if (is_multisite()) {
			$network_active_plugins = get_site_option('active_sitewide_plugins');
			if (!empty($network_active_plugins)) {
				$network_active_plugins = array_keys($network_active_plugins);
				$active_plugins = array_merge($active_plugins, $network_active_plugins);
			}
		}

		return $active_plugins;
	}

	/**
	 * This function checks whether a specific plugin is installed, and returns information about it
	 *
	 * @param  string $name Specify "Plugin Name" to return details about it.
	 * @return array        Returns an array of details such as if installed, the name of the plugin and if it is active.
	 */
	public function is_installed($name) {

		// Needed to have the 'get_plugins()' function
		include_once(ABSPATH.'wp-admin/includes/plugin.php');

		// Gets all plugins available
		$get_plugins = get_plugins();

		$active_plugins = $this->get_active_plugins();

		$plugin_info['installed'] = false;
		$plugin_info['active'] = false;

		// Loops around each plugin available.
		foreach ($get_plugins as $key => $value) {
			// If the plugin name matches that of the specified name, it will gather details.
			if ($value['Name'] != $name && $value['TextDomain'] != $name) continue;
			$plugin_info['installed'] = true;
			$plugin_info['name'] = $key;
			$plugin_info['version'] = $value['Version'];
			if (in_array($key, $active_plugins)) {
				$plugin_info['active'] = true;
			}
			break;
		}
		return $plugin_info;
	}

	/**
	 * This is a notice to show users that premium is installed
	 */
	public function show_admin_notice_premium() {
		echo '<div id="wp-optimize-premium-installed-warning" class="error"><p>'.__('WP-Optimize (Free) has been de-activated, because WP-Optimize Premium is active.', 'wp-optimize').'</p></div>';
		if (isset($_GET['activate'])) unset($_GET['activate']);
	}

	/**
	 * Show update to Premium notice for non-premium multisite.
	 */
	public function show_multisite_update_to_premium_notice() {
		if (!is_multisite() || self::is_premium()) return;

		echo '<p><a href="'.$this->premium_version_link.'">'.__('New feature: WP-Optimize Premium can now optimize all sites within a multisite install, not just the main one.', 'wp-optimize').'</a></p>';
	}

	public function admin_init() {
		$pagenow = $GLOBALS['pagenow'];

		$this->register_template_directories();

		if (('index.php' == $pagenow && current_user_can('update_plugins')) || ('index.php' == $pagenow && defined('WP_OPTIMIZE_FORCE_DASHNOTICE') && WP_OPTIMIZE_FORCE_DASHNOTICE)) {
			$options = $this->get_options();

			$dismissed_until = $options->get_option('dismiss_dash_notice_until', 0);

			if (file_exists(WPO_PLUGIN_MAIN_PATH . '/index.html')) {
				$installed = filemtime(WPO_PLUGIN_MAIN_PATH . '/index.html');
				$installed_for = (time() - $installed);
			}

			if (($installed && time() > $dismissed_until && $installed_for > (14 * 86400) && !defined('WP_OPTIMIZE_NOADS_B')) || (defined('WP_OPTIMIZE_FORCE_DASHNOTICE') && WP_OPTIMIZE_FORCE_DASHNOTICE)) {
				add_action('all_admin_notices', array($this, 'show_admin_notice_upgradead'));
			}
		}
	}

	public function show_admin_notice_upgradead() {
		$this->include_template('notices/thanks-for-using-main-dash.php');
	}

	public function capability_required() {
		return apply_filters('wp_optimize_capability_required', 'manage_options');
	}

	public function wp_optimize_ajax_handler() {
		$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'wp-optimize-ajax-nonce') || empty($_POST['subaction'])) die('Security check');

		$subaction = $_POST['subaction'];
		$data = isset($_POST['data']) ? $_POST['data'] : null;

		if (!current_user_can($this->capability_required())) die('Security check');

		$wp_optimize = $this;
		$optimizer = $this->get_optimizer();
		$options = $this->get_options();

		$results = array();

		// Some commands that are available via AJAX only.
		if (in_array($subaction, array('dismiss_dash_notice_until', 'dismiss_season'))) {
			$options->update_option($subaction, (time() + 366 * 86400));
		} elseif (in_array($subaction, array('dismiss_page_notice_until', 'dismiss_notice'))) {
			$options->update_option($subaction, (time() + 84 * 86400));
		} else {
			// Other commands, available for any remote method.
			if (!class_exists('WP_Optimize_Commands')) include_once(WPO_PLUGIN_MAIN_PATH . 'includes/class-commands.php');

			$commands = new WP_Optimize_Commands();

			if (!method_exists($commands, $subaction)) {
				error_log("WP-Optimize: ajax_handler: no such command (".$subaction.")");
				die('No such command');
			} else {
				$results = call_user_func(array($commands, $subaction), $data);

				// clean status box content, it broke json sometimes.
				if (isset($results['status_box_contents'])) {
					$results['status_box_contents'] = str_replace(array("\n", "\t"), '', $results['status_box_contents']);
				}

				if (is_wp_error($results)) {
					$results = array(
						'result' => false,
						'error_code' => $results->get_error_code(),
						'error_message' => $results->get_error_message(),
						'error_data' => $results->get_error_data(),
					);
				}

				// if nothing was returned for some reason, set as result null.
				if (empty($results)) {
					$results = array(
						'result' => null
					);
				}
			}
		}

		$result = json_encode($results);

		// Requires PHP 5.3+
		// @codingStandardsIgnoreLine
		$json_last_error = function_exists('json_last_error') ? json_last_error() : false;

		// if json_encode returned error then return error.
		if ($json_last_error) {
			$result = array(
				'result' => false,
				'error_code' => $json_last_error,
				'error_message' => 'json_encode error : '.$json_last_error,
				'error_data' => '',
			);

			$result = json_encode($result);
		}

		echo $result;

		die;
	}

	/**
	 * Builds the Tabs that should be displayed
	 *
	 * @return array Returns all tabs specified
	 */
	public function get_tabs($page) {
		// define tabs for pages.
		$pages_tabs = array(
			'WP-Optimize' => array(
				'settings' => array(
					'title' => __('Settings', 'wp-optimize'),
				),
			),
			'wpo_database' => array('optimize' => __('Optimizations', 'wp-optimize'), 'tables' => __('Tables', 'wp-optimize')),
			'wpo_cache' => array(
				'gzip' => __('Gzip compression', 'wp-optimize'), // Adds a GZIP tab
				'settings' => __('Static file caching', 'wp-optimize')  // Adds a settings tab
			),
			'wpo_support' => array('support' => __('Support / FAQs', 'wp-optimize')),
			'wpo_mayalso' => array('may_also' => __('Premium / Plugin family', 'wp-optimize')),
		);

		$tabs = (array_key_exists($page, $pages_tabs)) ? $pages_tabs[$page] : array();

		return apply_filters('wp_optimize_admin_page_'.$page.'_tabs', $tabs);
	}

	/**
	 * Main page structure.
	 */
	public function display_admin() {
		$capability_required = $this->capability_required();

		if (!current_user_can($capability_required) || (!$this->can_run_optimizations() && !$this->can_manage_options())) {
			echo "Permission denied.";
			return;
		}

		$this->register_admin_content();

		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? WPO_VERSION.'.'.time() : WPO_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		wp_enqueue_script('jquery-serialize-json', WPO_PLUGIN_URL.'js/serialize-json/jquery.serializejson'.$min_or_not.'.js', array('jquery'), $enqueue_version);

		wp_register_script('updraft-queue-js', WPO_PLUGIN_URL.'js/queue'.$min_or_not.'.js', array(), $enqueue_version);
		wp_enqueue_script('wp-optimize-admin-js', WPO_PLUGIN_URL.'js/wpadmin'.$min_or_not.'.js', array('jquery', 'updraft-queue-js'), $enqueue_version);
		wp_enqueue_style('wp-optimize-admin-css', WPO_PLUGIN_URL.'css/wp-optimize-admin'.$min_or_not.'.css', array(), $enqueue_version);
		// Using tablesorter to help with organising the DB size on Table Information
		// https://github.com/Mottie/tablesorter
		wp_enqueue_script('tablesorter-js', WPO_PLUGIN_URL.'js/tablesorter/jquery.tablesorter'.$min_or_not.'.js', array('jquery'), $enqueue_version);

		wp_enqueue_script('tablesorter-widgets-js', WPO_PLUGIN_URL.'js/tablesorter/jquery.tablesorter.widgets'.$min_or_not.'.js', array('jquery'), $enqueue_version);

		// wp_enqueue_style('tablesorter-css', WPO_PLUGIN_URL.'css/tablesorter/theme.default.min.css', array(), $enqueue_version);

		$js_variables = $this->wpo_js_translations();
		$js_variables['loggers_classes_info'] = $this->get_loggers_classes_info();

		wp_localize_script('wp-optimize-admin-js', 'wpoptimize', $js_variables);

		do_action('wpo_premium_scripts_styles', $min_or_not, $enqueue_version);

		echo '<div id="wp-optimize-wrap">';
		
		$this->include_template('admin-page-header.php', false);

		echo '<div id="actions-results-area"></div>';
		
		$pages = $this->get_submenu_items();

		foreach ($pages as $page) {
			if (isset($page['menu_slug'])) {
				$this->display_admin_page($page['menu_slug']);
			}
		}

		// closes main plugin wrapper div. #wp-optimize-wrap
		echo '</div><!-- END #wp-optimize-wrap -->';

	}

	/**
	 * Prepare and display admin page with $page id.
	 *
	 * @param string $page wp-optimize page id i.e. dashboard, database, images, cache, ...
	 */
	public function display_admin_page($page) {

		$active_page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '';

		echo '<div class="wpo-page' . ($active_page == $page ? ' active' : '') . '" data-whichpage="'.$page.'">';

		echo '<div class="wpo-main">';

		// get defined tabs for $page.
		$tabs = $this->get_tabs($page);

		// if no tabs defined for $page then use $page as $active_tab for load template, doing related actions e t.c.
		if (empty($tabs)) {
			$active_tab = $page;
		} else {
			$tab_keys = array_keys($tabs);
			$default_tab = apply_filters('wp_optimize_admin_'.$page.'_default_tab', $tab_keys[0]);
			$active_tab = isset($_GET['tab']) ? substr($_GET['tab'], 12) : $default_tab;
			if (!in_array($active_tab, array_keys($tabs))) $active_tab = $default_tab;
		}

		do_action('wp_optimize_admin_page_'.$page, $active_tab);

		// if tabs defined then display
		if (!empty($tabs)) {
			$this->include_template('admin-page-header-tabs.php', false, array('page' => $page, 'active_tab' => $active_tab, 'tabs' => $tabs));
		}

		foreach ($tabs as $tab_id => $tab_description) {
			// output wrap div for tab with id #wp-optimize-nav-tab-contents-'.$page.'-'.$tab_id
			echo '<div class="wp-optimize-nav-tab-contents" id="wp-optimize-nav-tab-'.$page.'-'.$tab_id.'-contents" '.(($tab_id == $active_tab) ? '' : 'style="display:none;"').'>';
			
			echo '<div class="postbox wpo-tab-postbox">';
			// call action for generate tab content.
			
			do_action('wp_optimize_admin_page_'.$page.'_'.$tab_id);

			// closes postbox.
			echo '</div><!-- END .postbox -->';
			// closes tab wrapper.
			echo '</div><!-- END .wp-optimize-nav-tab-contents -->';
		}

		echo '</div><!-- END .wpo-main -->';

		do_action('wp_optimize_admin_after_page_'.$page, $active_tab);

		echo '</div><!-- END .wpo-page -->';

	}

	/**
	 * Define required actions for admin pages.
	 */
	public function register_admin_content() {
		/**
		 * SETTINGS
		 */
		add_action('wp_optimize_admin_page_WP-Optimize_settings', array($this, 'output_dashboard_settings_tab'), 20);

		/**
		 * Premium / other plugins
		 */
		add_action('wp_optimize_admin_page_wpo_mayalso_may_also', array($this, 'output_dashboard_other_plugins_tab'), 20);

		/**
		 * DATABSE
		 */
		add_action('wp_optimize_admin_page_wpo_database_optimize', array($this, 'output_database_optimize_tab'), 20);

		add_action('wp_optimize_admin_page_wpo_database_tables', array($this, 'output_database_tables_tab'), 20);

		/**
		 * CACHE
		 */
		add_action('wp_optimize_admin_page_wpo_cache', array($this, 'output_dashboard_cache_tab'), 20);

		/**
		 * CACHE
		 */
		add_action('wp_optimize_admin_page_wpo_cache_gzip', array($this, 'output_cache_gzip_tab'), 20);
		add_action('wp_optimize_admin_page_wpo_cache_settings', array($this, 'output_cache_settings_tab'), 20);

		/**
		 * SUPPORT
		 */
		add_action('wp_optimize_admin_page_wpo_support_support', array($this, 'output_dashboard_support_tab'), 20);
		// Display Support page.

	}

	/**
	 * Dashboard status
	 */
	public function output_status_column() {
	?>
		<div class="postbox wpo-tab-postbox right-col">
			<?php WP_Optimize()->include_template('settings/status-box-contents.php', false, array('optimize_db' => false)); ?>
		</div>
	<?php
	}

	/**
	 * Dashboard settings
	 */
	public function output_dashboard_settings_tab() {
		$options = $this->get_options();

		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			// Nonce check.
			check_admin_referer('wpo_settings');

			$output = $options->save_settings($_POST);

			if (isset($_POST['wp-optimize-settings'])) {
				// save settings request sent.
				$output = $options->save_settings($_POST);
			}

			$this->wpo_render_output_messages($output);
		}

		if ($this->can_manage_options()) {
			$this->include_template('settings/settings.php');
		} else {
			$this->prevent_manage_options_info();
		}
	}

	/**
	 * Dashboard support tab
	 */
	public function output_dashboard_support_tab() {
		WP_Optimize()->include_template('settings/support-and-faqs.php');
	}

	/**
	 * Dashboard Other plugins / premium tab
	 */
	public function output_dashboard_other_plugins_tab() {
		$this->include_template('settings/may-also-like.php');
	}

	/**
	 * Cache tab
	 */
	public function output_cache_gzip_tab() {
		WP_Optimize()->include_template('cache/gzip-compression.php');
	}

	/**
	 * Cache tab
	 */
	public function output_cache_settings_tab() {
		WP_Optimize()->include_template('cache/browser-cache.php');
	}

	/**
	 * Outputs the DB optimize Tab
	 */
	public function output_database_optimize_tab() {
		$optimizer = $this->get_optimizer();
		$options = $this->get_options();

		// check if nonce passed.
		$nonce_passed = (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wpo_optimization')) ? true : false;

		// save options.
		if ($nonce_passed && isset($_POST['wp-optimize'])) $options->save_sent_manual_run_optimization_options($_POST, true);

		$optimize_db = ($nonce_passed && isset($_POST["optimize-db"])) ? true : false;

		$optimization_results = (($nonce_passed) ? $optimizer->do_optimizations($_POST) : false);

		// display optimizations table or restricted access message.
		if ($this->can_run_optimizations()) {
			$this->include_template('database/optimize-table.php', false, array('optimize_db' => $optimize_db, 'optimization_results' => $optimization_results ));
		} else {
			$this->prevent_run_optimizations_message();
		}
	}

	/**
	 * Outputs the DB Tables Tab
	 */
	public function output_database_tables_tab() {
		// check if nonce passed.
		$nonce_passed = (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wpo_optimization')) ? true : false;

		$optimize_db = ($nonce_passed && isset($_POST["optimize-db"])) ? true : false;

		if ($this->can_run_optimizations()) {
			$this->include_template('database/tables.php', false, array('optimize_db' => $optimize_db));
		} else {
			$this->prevent_run_optimizations_message();
		}
	}

	/**
	 * Cache tab
	 */
	public function output_dashboard_cache_tab() {
		WP_Optimize()->include_template('cache/cache.php');
	}

	/**
	 * Returns array of translations used in javascript code.
	 *
	 * @return array
	 */
	public function wpo_js_translations() {
		return apply_filters('wpo_js_translations', array(
			'automatic_backup_before_optimizations' => __('Automatic backup before optimizations', 'wp-optimize'),
			'error_unexpected_response' => __('An unexpected response was received.', 'wp-optimize'),
			'optimization_complete' => __('Optimization complete', 'wp-optimize'),
			'run_optimizations' => __('Run optimizations', 'wp-optimize'),
			'cancel' => __('Cancel', 'wp-optimize'),
			'enable' => __('Enable', 'wp-optimize'),
			'disable' => __('Disable', 'wp-optimize'),
			'please_select_settings_file' => __('Please, select settings file.', 'wp-optimize'),
			'are_you_sure_you_want_to_remove_logging_destination' => __('Are you sure you want to remove this logging destination?', 'wp-optimize'),
			'fill_all_settings_fields' => __('Before saving, you need to complete the currently incomplete settings (or remove them).', 'wp-optimize'),
			'table_was_not_repaired' => __('%s was not repaired. For more details, please check the logs (configured in your logging destinations settings).', 'wp-optimize'),
			'are_you_sure_you_want_to_remove_this_table' => __('Are you sure you want to remove this table?', 'wp-optimize'),
			'table_was_not_deleted' => __('%s was not deleted. For more details, please check your logs configured in logging destinations settings.', 'wp-optimize'),
			'please_use_positive_integers' => __('Please use positive integers.', 'wp-optimize'),
			'please_use_valid_values' => __('Please use valid values.', 'wp-optimize'),
			'enable' => __('Enable', 'wp-optimize'),
			'update' => __('Update', 'wp-optimize'),
			'spinner_src' => esc_attr(admin_url('images/spinner-2x.gif')),
			'sites' => $this->get_sites(),
		));
	}

	public function wpo_admin_bar() {
		$wp_admin_bar = $GLOBALS['wp_admin_bar'];

		if (defined('WPOPTIMIZE_ADMINBAR_DISABLE') && WPOPTIMIZE_ADMINBAR_DISABLE) return;

		// Show menu item in top bar only for super admins.
		if (is_multisite() & !is_super_admin(get_current_user_id())) return;

		// Add a link called at the top admin bar.
		$args = array(
			'id' => 'wp-optimize-node',
			'title' => apply_filters('wpoptimize_admin_node_title', 'WP-Optimize')
		);
		$wp_admin_bar->add_node($args);

		$pages = $this->get_submenu_items();

		foreach ($pages as $page_id => $page) {

			if (!isset($page['create_submenu']) || !$page['create_submenu']) {
				if (isset($page['icon']) && 'separator' == $page['icon']) {
					$args = array(
						'id' => 'wpo-separator-'.$page_id,
						'parent' => 'wp-optimize-node',
						'meta' => array(
							'class' => 'separator',
						),
					);
					$wp_admin_bar->add_node($args);
				}
				continue;
			}

			// 'menu_slug' => 'WP-Optimize',
			
			$menu_page_url = menu_page_url($page['menu_slug'], false);

			if (is_multisite()) {
				$menu_page_url = network_admin_url('admin.php?page='.$page['menu_slug']);
			}

			$args = array(
				'id' => 'wpoptimize_admin_node_'.$page_id,
				'title' => $page['menu_title'],
				'parent' => 'wp-optimize-node',
				'href' => $menu_page_url,
			);
			$wp_admin_bar->add_node($args);
		}

	}

	/**
	 * Add settings link on plugin page
	 *
	 * @param  string $links Passing through the URL to be used within the HREF.
	 * @return string        Returns the Links.
	 */
	public function plugin_settings_link($links) {

		$admin_page_url = $this->get_options()->admin_page_url();

		$settings_link = '<a href="' . esc_url($admin_page_url) . '">' . __('Settings', 'wp-optimize') . '</a>';
		array_unshift($links, $settings_link);

		$optimize_link = '<a href="' . esc_url($admin_page_url) . '">' . __('Optimizer', 'wp-optimize') . '</a>';
		array_unshift($links, $optimize_link);
		return $links;
	}

	/**
	 * Action wpo_tables_list_additional_column_data. Output button Optimize in the action column.
	 *
	 * @param string $content    String for output to column
	 * @param object $table_info Object with table info.
	 *
	 * @return string
	 */
	public function tables_list_additional_column_data($content, $table_info) {
		if ($table_info->is_needing_repair) {
			$content .= '<div class="wpo_button_wrap">'
				. '<button class="button button-secondary run-single-table-repair" data-table="' . esc_attr($table_info->Name) . '">' . __('Repair', 'wp-optimize') . '</button>'
				. '<img class="optimization_spinner visibility-hidden" src="' . esc_attr(admin_url('images/spinner-2x.gif')) . '" width="20" height="20" alt="...">'
				. '<span class="optimization_done_icon dashicons dashicons-yes visibility-hidden"></span>'
				. '</div>';
		}

		// table belongs to plugin.
		if ($table_info->can_be_removed) {
			$content .= '<div>'
				. '<button class="button button-secondary run-single-table-delete" data-table="' . esc_attr($table_info->Name) . '">' . __('Remove', 'wp-optimize') . '</button>'
				. '<img class="optimization_spinner visibility-hidden" src="' . esc_attr(admin_url('images/spinner-2x.gif')) . '" width="20" height="20" alt="...">'
				. '<span class="optimization_done_icon dashicons dashicons-yes visibility-hidden"></span>'
				. '</div>';
		}

		return $content;
	}

	/**
	 * Schedules cron event based on selected schedule type
	 *
	 * @return void
	 */
	public function cron_activate() {
		$gmt_offset = (int) (3600 * get_option('gmt_offset'));

		$options = $this->get_options();

		if ($options->get_option('schedule') === false) {
			$options->set_default_options();
		} else {
			if ('true' == $options->get_option('schedule')) {
				if (!wp_next_scheduled('wpo_cron_event2')) {
					$schedule_type = $options->get_option('schedule-type', 'wpo_weekly');

					$this_time = (86400 * 7);

					switch ($schedule_type) {
						case "wpo_daily":
							$this_time = 86400;
							break;

						case "wpo_weekly":
							$this_time = (86400 * 7);
							break;

						case "wpo_otherweekly":
							$this_time = (86400 * 14);
							break;

						case "wpo_monthly":
							$this_time = (86400 * 30);
							break;
					}

					add_action('wpo_cron_event2', array($this, 'cron_action'));
					wp_schedule_event((current_time("timestamp", 0) + $this_time - $gmt_offset), $schedule_type, 'wpo_cron_event2');
					WP_Optimize()->log('running wp_schedule_event()');
				}
			}
		}
	}

	/**
	 * Clears all cron events
	 *
	 * @return void
	 */
	public function wpo_cron_deactivate() {
		wp_clear_scheduled_hook('wpo_cron_event2');
	}

	/**
	 * Scheduler public functions to update schedulers
	 *
	 * @param  array $schedules An array of schedules being passed.
	 * @return array            An array of schedules being returned.
	 */
	public function cron_schedules($schedules) {
		$schedules['wpo_daily'] = array('interval' => 86400, 'display' => 'Once Daily');
		$schedules['wpo_weekly'] = array('interval' => 86400 * 7, 'display' => 'Once Weekly');
		$schedules['wpo_fortnightly'] = array('interval' => 86400 * 14, 'display' => 'Once Every Fortnight');
		$schedules['wpo_monthly'] = array('interval' => 86400 * 30, 'display' => 'Once Every Month');
		return $schedules;
	}

	/**
	 * Returns count of overdue cron jobs.
	 *
	 * @return integer
	 */
	public function howmany_overdue_crons() {
		$how_many_overdue = 0;
		if (function_exists('_get_cron_array') || (is_file(ABSPATH.WPINC.'/cron.php') && include_once(ABSPATH.WPINC.'/cron.php') && function_exists('_get_cron_array'))) {
			$crons = _get_cron_array();
			if (is_array($crons)) {
				$timenow = time();
				foreach ($crons as $jt => $job) {
					if ($jt < $timenow) {
						$how_many_overdue++;
					}
				}
			}
		}
		return $how_many_overdue;
	}

	/**
	 * Returns warning about overdue crons.
	 *
	 * @param int $howmany count of overdue crons
	 * @return string
	 */
	public function show_admin_warning_overdue_crons($howmany) {
		$ret = '<div class="updated below-h2"><p>';
		$ret .= '<strong>'.__('Warning', 'wp-optimize').':</strong> '.sprintf(__('WordPress has a number (%d) of scheduled tasks which are overdue. Unless this is a development site, this probably means that the scheduler in your WordPress install is not working.', 'wp-optimize'), $howmany).' <a href="'.apply_filters('wpoptimize_com_link', "https://getwpo.com/faqs/the-scheduler-in-my-wordpress-installation-is-not-working-what-should-i-do/").'">'.__('Read this page for a guide to possible causes and how to fix it.', 'wp-optimize').'</a>';
		$ret .= '</p></div>';
		return $ret;
	}

	public function admin_menu() {

		$capability_required = $this->capability_required();

		if (!current_user_can($capability_required) || (!$this->can_run_optimizations() && !$this->can_manage_options())) return;

		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgdmlld0JveD0iMCAwIDE2IDE2IgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmc0MzE2IgogICBoZWlnaHQ9IjE2IgogICB3aWR0aD0iMTYiPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM0MzE4IiAvPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTQzMjEiPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxnCiAgICAgaWQ9ImxheWVyMSI+CiAgICA8cGF0aAogICAgICAgc3R5bGU9ImZpbGw6I2EwYTVhYTtmaWxsLW9wYWNpdHk6MSIKICAgICAgIGlkPSJwYXRoNTciCiAgICAgICBkPSJtIDEwLjc2ODgwOSw2Ljc2MTYwNTEgMCwwIGMgLTAuMDE2ODgsLTAuMDE2ODc4IC0wLjAyNTMxLC0wLjA0MjE4MSAtMC4wMzM3NCwtMC4wNjc0OTkgLTAuMDA4NCwtMC4wMDgzOSAtMC4wMDg0LC0wLjAxNjg3OCAtMC4wMTY4OCwtMC4wMzM3NDMgQyA5Ljk5MjYxMTIsNS4xOTIzMzY2IDguMjIwODU1Nyw0LjU4NDg3ODEgNi43NDQzOTEyLDUuMjkzNTc5NyA1LjY3MjkwMDUsNS44MDgyMzI4IDUuMDU3MDA0Myw2Ljg4ODE2MTMgNS4wNjU0NDIsOC4wMDE4MzY1IDQuNDU3OTgyMiw3LjMxMDAwNzYgMy42OTg2NTg0LDYuNzk1MzU0NSAyLjg1NDk2NDIsNi40OTE2MjUzIDMuMjY4Mzc0Myw1LjA2NTc4MzEgNC4yNTU0OTYsMy44MTcxMTY2IDUuNjg5Nzc0NiwzLjEyNTI4NzggOC4zNjQyODMyLDEuODM0NDM2OCAxMS41NzAzMTksMi45Mzk2NzQ0IDEyLjg4NjQ4MSw1LjU4ODg3MjYgMTMuNDUxNzU1LDYuNzI3ODU5NiAxNC42NDk4MDEsNy4zNTIxOTIxIDE1Ljg0Nzg0Niw3LjIzNDA3NSAxNS43NjM0ODIsNi4zMzk3NiAxNS41MTg4MDUsNS40MzcwMDg2IDE1LjEwNTM5Niw0LjU3NjQ0MDQgMTMuMjE1NTIxLDAuNjg3MDEzNCA4LjUzMzAyMjYsLTAuOTQxMzE2MjcgNC42NDM1OTQzLDAuOTQwMTIxNzkgMi4zMjM0MzcsMi4wNjIyMzM0IDAuODA0Nzg4MTQsNC4xNzk5MDQ0IDAuMzU3NjMxMzIsNi41MzM4MDk4IDIuNDE2MjQzOCw2LjQyNDEyOSA0LjQzMjY3MTcsNy41MDQwNTc0IDUuNDM2NjY2Miw5LjQzNjExNjcgbCAwLjAwODM5LDAgYyAwLjc1OTMxOTIsMS4zNzUyMjAzIDIuNDcyMDE3OCwxLjk0MDQ5NTMgMy45MDYyOTYsMS4yNDg2NjczIDEuMDQ2MTc5OCwtMC41MDYyMTggMS42NTM2NDA4LC0xLjUzNTUyMzggMS42Nzg5NTA4LC0yLjYxNTQ1MTIgMC41ODIxNDgsMC43MDg3MDE4IDEuMzMzMDM1LDEuMjQ4NjY2OCAyLjE1OTg1NiwxLjU3NzcwNjQgLTAuNDM4NzIxLDEuMzU4MzQ3OCAtMS40MDA1MzMsMi41NDc5NTQ4IC0yLjc5MjYyNywzLjIxNDQ3ODggLTIuNTkwMTM4NywxLjI0ODY1OCAtNS42NzgwNTc0LDAuMjUzMTA0IC03LjA2MTcxNTEsLTIuMjI3MzU3IGwgMCwwIEMgMi43NjIxMDQ4LDkuNDUyOTg5NCAxLjUxMzQzODMsOC44MjAyMTkxIDAuMjgxNjQ1OTIsOC45NzIwODQ0IDAuMzgyODg3NjUsOS43OTg5MDQ2IDAuNjE5MTIzMzEsMTAuNjE3Mjg3IDAuOTk4Nzg1MiwxMS40MDE5MjIgYyAxLjg4MTQzNjgsMy44OTc4NjQgNi41NjM5MzcsNS41MjYxOTggMTAuNDYxODAwOCwzLjY0NDc2IDIuMjQ0MjI2LC0xLjA4ODM2OSAzLjczNzU2MiwtMy4xMDQ3OTYgNC4yMzUzNDIsLTUuMzc0MzMyMyAtMS45OTk1NTQsMC4wNDIxODEgLTMuOTQ4NDg2LC0xLjAyOTMwNjMgLTQuOTI3MTcsLTIuOTEwNzQzMyB6IgogICAgICAgY2xhc3M9InN0MTciIC8+CiAgPC9nPgo8L3N2Zz4K';

		// Removes the admin menu items on the left WP bar.
		if (!is_multisite() || (is_multisite() && is_network_admin())) {
			add_menu_page("WP-Optimize", "WP-Optimize", $capability_required, "WP-Optimize", array($this, "display_admin"), $icon_svg);

			$sub_menu_items = $this->get_submenu_items();

			foreach ($sub_menu_items as $menu_item) {
				if ($menu_item['create_submenu']) add_submenu_page('WP-Optimize', $menu_item['page_title'], $menu_item['menu_title'], $capability_required, $menu_item['menu_slug'], $menu_item['function']);
			}
		}

		$options = $this->get_options();

		if ($options->get_option('enable-admin-menu', 'false') == 'true') {
			add_action('wp_before_admin_bar_render', array($this, 'wpo_admin_bar'));
		}

	}

	/**
	 * Get the submenu items
	 *
	 * @return array
	 */
	public function get_submenu_items() {
		$sub_menu_items = array(
			array(
				'page_title' => __('Settings', 'wp-optimize'),
				'menu_title' => __('Settings', 'wp-optimize'),
				'menu_slug' => 'WP-Optimize',
				'function' => array($this, 'display_admin'),
				'icon' => 'admin-settings',
				'create_submenu' => true,
				'order' => 10,
			),
			array(
				'create_submenu' => false,
				'order' => 15,
				'icon' => 'separator',
			),
			array(
				'page_title' => __('Database', 'wp-optimize'),
				'menu_title' => __('Database', 'wp-optimize'),
				'menu_slug' => 'wpo_database',
				'function' => array($this, 'display_admin'),
				'icon' => 'cloud',
				'create_submenu' => true,
				'order' => 20,
			),
			array(
				'create_submenu' => false,
				'order' => 45,
				'icon' => 'separator',
			),
			array(
				'page_title' => __('Support & FAQs', 'wp-optimize'),
				'menu_title' => __('Help', 'wp-optimize'),
				'menu_slug' => 'wpo_support',
				'function' => array($this, 'display_admin'),
				'icon' => 'sos',
				'create_submenu' => true,
				'order' => 50,
			),
			array(
				'page_title' => __('More plugins', 'wp-optimize'),
				'menu_title' => __('More plugins', 'wp-optimize'),
				'menu_slug' => 'wpo_mayalso',
				'function' => array($this, 'display_admin'),
				'icon' => 'admin-plugins',
				'create_submenu' => true,
				'order' => 60,
			),
		);

		// Hide the cache page while developping it
		if (defined('WPO_SHOW_CACHE') && WPO_SHOW_CACHE) {
			$sub_menu_items[] = array(
				'page_title' => __('Cache', 'wp-optimize'),
				'menu_title' => __('Cache', 'wp-optimize'),
				'menu_slug' => 'wpo_cache',
				'function' => array($this, 'display_admin'),
				'icon' => 'archive',
				'create_submenu' => true,
				'order' => 40,
			);
		}

		$sub_menu_items = apply_filters('wp_optimize_sub_menu_items', $sub_menu_items);

		usort($sub_menu_items, array($this, 'order_sort'));

		return $sub_menu_items;
	}

	public function order_sort($a, $b) {
		if ($a['order'] == $b['order']) return 0;
		return ($a['order'] > $b['order']) ? 1 : -1;
	}
	
	private function wp_normalize_path($path) {
		// Wp_normalize_path is not present before WP 3.9.
		if (function_exists('wp_normalize_path')) return wp_normalize_path($path);
		// Taken from WP 4.6.
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('|(?<=.)/+|', '/', $path);
		if (':' === substr($path, 1, 1)) {
			$path = ucfirst($path);
		}
		return $path;
	}

	public function get_templates_dir() {
		return apply_filters('wp_optimize_templates_dir', $this->wp_normalize_path(WPO_PLUGIN_MAIN_PATH.'/templates'));
	}

	public function get_templates_url() {
		return apply_filters('wp_optimize_templates_url', WPO_PLUGIN_URL.'/templates');
	}

	public function include_template($path, $return_instead_of_echo = false, $extract_these = array()) {
		if ($return_instead_of_echo) ob_start();

		if (preg_match('#^([^/]+)/(.*)$#', $path, $matches)) {
			$prefix = $matches[1];
			$suffix = $matches[2];
			if (isset($this->template_directories[$prefix])) {
				$template_file = $this->template_directories[$prefix].'/'.$suffix;
			}
		}

		if (!isset($template_file)) {
			$template_file = WPO_PLUGIN_MAIN_PATH.'/templates/'.$path;
		}

		$template_file = apply_filters('wp_optimize_template', $template_file, $path);

		do_action('wp_optimize_before_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if (!file_exists($template_file)) {
			error_log("WP Optimize: template not found: ".$template_file);
			echo __('Error:', 'wp-optimize').' '.__('template not found', 'wp-optimize')." (".$path.")";
		} else {
			extract($extract_these);
			$wpdb = $GLOBALS['wpdb'];
			$wp_optimize = $this;
			$optimizer = $this->get_optimizer();
			$options = $this->get_options();
			$wp_optimize_notices = $this->get_notices();
			include $template_file;
		}

		do_action('wp_optimize_after_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if ($return_instead_of_echo) return ob_get_clean();
	}

	/**
	 * Build a list of template directories (stored in self::$template_directories)
	 */
	private function register_template_directories() {

		$template_directories = array();

		$templates_dir = $this->get_templates_dir();

		if ($dh = opendir($templates_dir)) {
			while (($file = readdir($dh)) !== false) {
				if ('.' == $file || '..' == $file) continue;
				if (is_dir($templates_dir.'/'.$file)) {
					$template_directories[$file] = $templates_dir.'/'.$file;
				}
			}
			closedir($dh);
		}

		// Optimal hook for most extensions to hook into.
		$this->template_directories = apply_filters('wp_optimize_template_directories', $template_directories);

	}

	/**
	 * Not currently used; needs looking at.
	 * N.B. The description does not match the actual function
	 *
	 * @param integer $date Date of when the optimization was executed.
	 */
	public function send_email($date) {
		ob_start();
		// This need to work on - currently not using the parameter values.
		$my_time = current_time("timestamp", 0);
		$my_date = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $my_time);
		$sendto = (!$options->get_option('email-address') ? get_bloginfo('admin_email') : $options->get_option('email-address'));
		$subject = get_bloginfo('name').": ".__("Automatic Operation Completed", "wp-optimize")." ".$my_date;

		$msg  = __("Scheduled optimization was executed at", "wp-optimize")." ".$my_date."\r\n"."\r\n";
		$msg .= __("You can safely delete this email.", "wp-optimize")."\r\n";
		$msg .= "\r\n";
		$msg .= __("Regards,", "wp-optimize")."\r\n";
		$msg .= __("WP-Optimize Plugin", "wp-optimize");
		ob_end_clean();
	}

	/**
	 * Message to debug
	 *
	 * @param string $message Message to insert into the log.
	 * @param array  $context Context of the log.
	 */
	public function log($message, $context = array()) {
		$this->get_logger()->debug($message, $context);
	}

	/**
	 * Format Bytes Into KB/MB
	 *
	 * @param  mixed $bytes Number of bytes to be converted.
	 * @return integer        return the correct format size.
	 */
	public function format_size($bytes) {
		if ($bytes > 1073741824) {
			return number_format_i18n(($bytes / 1073741824), 2) . ' '.__('GB', 'wp-optimize');
		} elseif ($bytes > 1048576) {
			return number_format_i18n(($bytes / 1048576), 1) . ' '.__('MB', 'wp-optimize');
		} elseif ($bytes > 1024) {
			return number_format_i18n(($bytes / 1024), 1) . ' '.__('KB', 'wp-optimize');
		} else {
			return number_format_i18n($bytes, 0) . ' '.__('bytes', 'wp-optimize');
		}
	}

	/**
	 * Executed this function on cron event.
	 *
	 * @return void
	 */
	public function cron_action() {

		$optimizer = $this->get_optimizer();
		$options = $this->get_options();

		$this->log('WPO: Starting cron_action()');

		if ('true' == $options->get_option('schedule')) {
			$this_options = $options->get_option('auto');

			$optimizations = $optimizer->get_optimizations();

			// Currently the output of the optimizations is not saved/used/logged.
			$results = $optimizer->do_optimizations($this_options, 'auto');
		}

	}

	/**
	 * Schedule cron tasks used by plugin.
	 *
	 * @return void
	 */
	public function schedule_plugin_cron_tasks() {
		if (!wp_next_scheduled('wpo_plugin_cron_tasks')) {
			wp_schedule_event(current_time("timestamp", 0), 'twicedaily', 'wpo_plugin_cron_tasks');
		}

		add_action('wpo_plugin_cron_tasks', array($this, 'do_plugin_cron_tasks'));
	}

	/**
	 * Do plugin background tasks.
	 *
	 * @return void
	 */
	public function do_plugin_cron_tasks() {
		// get information about corrupted tables.
		$this->update_corrupted_tables_count();
	}

	/**
	 * Update corrupted tables count used with wp_optimize_get_tables filter.

	 * @return void
	 */
	public function update_corrupted_tables_count() {
		$tables = $this->get_optimizer()->get_tables();

		$corrupted_tables_count = 0;

		if (!empty($tables)) {
			foreach ($tables as $table) {
				if ($table->is_needing_repair) {
					$corrupted_tables_count++;
				}
			}
		}

		// save results to options table and use it to notify user about corrupted tables.
		$this->get_options()->update_option('corrupted-tables-count', $corrupted_tables_count);
	}

	/**
	 * This will customize a URL with a correct Affiliate link
	 * This function can be update to suit any URL as longs as the URL is passed
	 *
	 * @param String  $url					  - URL to be check to see if it an updraftplus match.
	 * @param String  $text					  - Text to be entered within the href a tags.
	 * @param String  $html					  - Any specific HTML to be added.
	 * @param String  $class				  - Specify a class for the href (including the attribute label)
	 * @param Boolean $return_instead_of_echo - if set, then the result will be returned, not echo-ed.
	 *
	 * @return String|void
	 */
	public function wp_optimize_url($url, $text, $html = '', $class = '', $return_instead_of_echo = false) {
		// Check if the URL is UpdraftPlus.
		if (false !== strpos($url, '//updraftplus.com')) {
			// Set URL with Affiliate ID.
			$url = $url.'?afref='.$this->get_notices()->get_affiliate_id();

			// Apply filters.
			$url = apply_filters('wpoptimize_updraftplus_com_link', $url);
		}
		// Return URL - check if there is HTML such as images.
		if ('' != $html) {
			$result = '<a '.$class.' href="'.esc_attr($url).'">'.$html.'</a>';
		} else {
			$result = '<a '.$class.' href="'.esc_attr($url).'">'.htmlspecialchars($text).'</a>';
		}
		if ($return_instead_of_echo) return $result;
		echo $result;
	}

	/**
	 * Setup WPO logger(s)
	 */
	public function setup_loggers() {

		$logger = $this->get_logger();
		$loggers = $this->wpo_loggers();

		if (!empty($loggers)) {
			foreach ($loggers as $_logger) {
				$logger->add_logger($_logger);
			}
		}

		add_action('wp_optimize_after_optimizations', array($this, 'after_optimizations_logger_action'));
	}

	/**
	 * Run logger actions after all optimizations done
	 */
	public function after_optimizations_logger_action() {
		$loggers = $this->get_logger()->get_loggers();
		if (!empty($loggers)) {
			foreach ($loggers as $logger) {
				if (is_a($logger, 'Updraft_Email_Logger')) {
					$logger->flush_log();
				}
			}
		}
	}

	/**
	 * Returns list of WPO loggers instances
	 * Apply filter wp_optimize_loggers
	 *
	 * @return array
	 */
	public function wpo_loggers() {

		$loggers = array();
		$loggers_classes_by_id = array();
		$options_keys = array();

		$loggers_classes = $this->get_loggers_classes();

		foreach ($loggers_classes as $logger_class => $source) {
			$loggers_classes_by_id[strtolower($logger_class)] = $logger_class;
		}

		$options = $this->get_options();
		
		$saved_loggers = $options->get_option('logging');
		$logger_additional_options = $options->get_option('logging-additional');

		// create loggers classes instances.
		if (!empty($saved_loggers)) {
			// check for previous version options format.
			$keys = array_keys($saved_loggers);

			// if options stored in old format then reformat it.
			if (false == is_numeric($keys[0])) {
				$_saved_loggers = array();
				foreach ($saved_loggers as $logger_id => $enabled) {
					if ($enabled) {
						$_saved_loggers[] = $logger_id;
					}
				}

				// fill email with admin.
				if (array_key_exists('updraft_email_logger', $saved_loggers) && $saved_loggers['updraft_email_logger']) {
					$logger_additional_options['updraft_email_logger'] = array(
						get_option('admin_email')
					);
				}

				$saved_loggers = $_saved_loggers;
			}

			foreach ($saved_loggers as $i => $logger_id) {

				if (!array_key_exists($logger_id, $loggers_classes_by_id)) continue;

				$logger_class = $loggers_classes_by_id[$logger_id];

				$logger = new $logger_class();

				$logger_options = $logger->get_options_list();

				if (!empty($logger_options)) {
					foreach (array_keys($logger_options) as $option_name) {
						if (array_key_exists($option_name, $options_keys)) {
							$options_keys[$option_name]++;
						} else {
							$options_keys[$option_name] = 0;
						}

						$option_value = isset($logger_additional_options[$option_name][$options_keys[$option_name]]) ? $logger_additional_options[$option_name][$options_keys[$option_name]] : '';

						// if options in old format then get correct value.
						if ('' === $option_value && array_key_exists($logger_id, $logger_additional_options)) {
							$option_value = array_shift($logger_additional_options[$logger_id]);
						}

						$logger->set_option($option_name, $option_value);
					}
				}

				// check if logger is active.
				$active = (!is_array($logger_additional_options) || (array_key_exists('active', $logger_additional_options) && empty($logger_additional_options['active'][$i]))) ? false : true;

				if ($active) {
					$logger->enable();
				} else {
					$logger->disable();
				}

				$loggers[] = $logger;
			}
		}

		$loggers = apply_filters('wp_optimize_loggers', $loggers);

		return $loggers;
	}

	/**
	 * Returns associative array with logger class name in a key and path to class file in a value.
	 *
	 * @return array
	 */
	public function get_loggers_classes() {
		$loggers_classes = array(
			'Updraft_PHP_Logger' => WPO_PLUGIN_MAIN_PATH . 'includes/class-updraft-php-logger.php',
			'Updraft_Email_Logger' => WPO_PLUGIN_MAIN_PATH . 'includes/class-updraft-email-logger.php',
			'Updraft_Ring_Logger' => WPO_PLUGIN_MAIN_PATH . 'includes/class-updraft-ring-logger.php'
		);

		$loggers_classes = apply_filters('wp_optimize_loggers_classes', $loggers_classes);

		if (!empty($loggers_classes)) {
			foreach ($loggers_classes as $logger_class => $logger_file) {
				if (!class_exists($logger_class)) {
					if (is_file($logger_file)) {
						include_once($logger_file);
					}
				}
			}
		}

		return $loggers_classes;
	}

	/**
	 * Returns information about all loggers classes.
	 *
	 * @return array
	 */
	public function get_loggers_classes_info() {
		$loggers_classes = $this->get_loggers_classes();

		$loggers_classes_info = array();

		if (!empty($loggers_classes)) {
			foreach (array_keys($loggers_classes) as $logger_class_name) {

				if (!class_exists($logger_class_name)) continue;

				$logger_id = strtolower($logger_class_name);
				$logger_class = new $logger_class_name();

				$loggers_classes_info[$logger_id] = array(
					'description' => $logger_class->get_description(),
					'available' => $logger_class->is_available(),
					'allow_multiple' => $logger_class->is_allow_multiple(),
					'options' => $logger_class->get_options_list()
				);
			}
		}

		return $loggers_classes_info;
	}

	/**
	 * Returns true if optimization works in multisite mode
	 *
	 * @return boolean
	 */
	public function is_multisite_mode() {
		return (is_multisite() && WP_Optimize()->is_premium());
	}

	/**
	 * Returns true if current user can run optimizations.
	 *
	 * @return bool
	 */
	public function can_run_optimizations() {
		// we don't check permissions for cron jobs.
		if (defined('DOING_CRON') && DOING_CRON) return true;

		if (self::is_premium() && false == user_can(get_current_user_id(), 'wpo_run_optimizations')) return false;
		return true;
	}

	/**
	 * Returns true if current user can manage plugin options.
	 *
	 * @return bool
	 */
	public function can_manage_options() {
		if (self::is_premium() && false == user_can(get_current_user_id(), 'wpo_manage_settings')) return false;
		return true;
	}

	/**
	 * Output information message for users who have no permissions to run optimizations.
	 */
	public function prevent_run_optimizations_message() {
		$this->include_template('info-message.php', false, array('message' => __('You have no permissions to run optimizations.', 'wp-optimize')));
	}

	/**
	 * Output information message for users who have no permissions to manage settings.
	 */
	public function prevent_manage_options_info() {
		$this->include_template('info-message.php', false, array('message' => __('You have no permissions to manage WP-Optimize settings.', 'wp-optimize')));
	}

	/**
	 * Returns list of all sites in multisite
	 *
	 * @return array
	 */
	public function get_sites() {
		$sites = array();
		// check if function get_sites exists (since 4.6.0) else use wp_get_sites.
		if (function_exists('get_sites')) {
			$sites = get_sites(array('network_id' => null, 'number' => 99999));
		} elseif (function_exists('wp_get_sites')) {
			// @codingStandardsIgnoreLine
			$sites = wp_get_sites(array('network_id' => null, 'limit' => 99999));
		}
		return $sites;
	}

	/**
	 * Output success/error messages from $output array.
	 *
	 * @param array $output ['messages' => success messages, 'errors' => error messages]
	 */
	private function wpo_render_output_messages($output) {
		foreach ($output['messages'] as $item) {
			echo '<div class="updated fade below-h2"><strong>'.$item.'</strong></div>';
		}

		foreach ($output['errors'] as $item) {
			echo '<div class="error fade below-h2"><strong>'.$item.'</strong></div>';
		}
	}

	/**
	 * Returns script memory limit in megabytes.
	 *
	 * @param bool $memory_limit
	 * @return int
	 */
	public function get_memory_limit($memory_limit = false) {
		// Returns in megabytes
		if (false == $memory_limit) $memory_limit = ini_get('memory_limit');
		$memory_limit = rtrim($memory_limit);

		return $this->return_bytes($memory_limit);
	}

	/**
	 * Returns free memory in bytes.
	 *
	 * @return int
	 */
	public function get_free_memory() {
		return $this->get_memory_limit() - memory_get_usage();
	}

	/**
	 * Checks PHP memory_limit and WP_MAX_MEMORY_LIMIT values and return minimal.
	 *
	 * @return int memory limit in bytes.
	 */
	public function get_script_memory_limit() {
		$memory_limit = $this->get_memory_limit();

		if (defined('WP_MAX_MEMORY_LIMIT')) {
			$wp_memory_limit = $this->get_memory_limit(WP_MAX_MEMORY_LIMIT);

			if ($wp_memory_limit > 0 && $wp_memory_limit < $memory_limit) {
				$memory_limit = $wp_memory_limit;
			}
		}

		return $memory_limit;
	}

	/**
	 * Returns max packet size for database.
	 *
	 * @return int|string
	 */
	public function get_max_packet_size() {
		global $wpdb;
		static $mp = 0;

		if ($mp > 0) return $mp;

		$mp = (int) $wpdb->get_var("SELECT @@session.max_allowed_packet");
		// Default to 1MB
		$mp = (is_numeric($mp) && $mp > 0) ? $mp : 1048576;
		// 32MB
		if ($mp < 33554432) {
			$save = $wpdb->show_errors(false);
			// @codingStandardsIgnoreLine
			$req = @$wpdb->query("SET GLOBAL max_allowed_packet=33554432");
			$wpdb->show_errors($save);

			$mp = (int) $wpdb->get_var("SELECT @@session.max_allowed_packet");
			// Default to 1MB
			$mp = (is_numeric($mp) && $mp > 0) ? $mp : 1048576;
		}

		return $mp;
	}

	/**
	 * Converts shorthand memory notation value to bytes.
	 * From http://php.net/manual/en/function.ini-get.php
	 *
	 * @param string $val shorthand memory notation value.
	 */
	public function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		$val = (int) $val;
		switch ($last) {
			case 'g':
				$val *= 1024;
				// no break
			case 'm':
				$val *= 1024;
				// no break
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	/**
	 * Log fatal errors to defined log destinations.
	 */
	public function log_fatal_errors() {
		$last_error = error_get_last();

		if (E_ERROR === $last_error['type']) {
			$this->get_logger()->critical($last_error['message']);
			die();
		}
	}
}

/**
 * Plugin activation actions.
 */
function wpo_activation_actions() {
	// If plugin activated by not a Network Administrator then deactivate plugin and show message.
	if (is_multisite() && !is_network_admin()) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die(__('Only Network Administrator can activate WP-Optimize plugin.', 'wp-optimize').
			' <a href="'.admin_url('plugins.php').'">'.__('go back', 'wp-optimize').'</a>');
	}

	WP_Optimize()->get_options()->set_default_options();
}

/**
 * Plugin deactivation actions.
 */
function wpo_deactivation_actions() {
	WP_Optimize()->wpo_cron_deactivate();
}

function wpo_cron_deactivate() {
	WP_Optimize()->log('running wpo_cron_deactivate()');
	wp_clear_scheduled_hook('wpo_cron_event2');
}

/**
 * Plugin uninstall actions.
 */
function wpo_uninstall_actions() {
	WP_Optimize()->get_options()->delete_all_options();
	wp_clear_scheduled_hook('wpo_cron_plugin');
}

function WP_Optimize() {
	return WP_Optimize::instance();
}

endif;

$GLOBALS['wp_optimize'] = WP_Optimize();
