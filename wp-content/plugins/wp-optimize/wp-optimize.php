<?php
/*
Plugin Name: WP-Optimize
Plugin URI: http://updraftplus.com
Description: WP-Optimize is WordPress's #1 most installed optimization plugin. With it, you can clean up your database easily and safely, without manual queries.
Version: 2.1.1
Author: David Anderson, Ruhani Rabin, Team Updraft
Author URI: https://updraftplus.com
Text Domain: wp-optimize
Domain Path: /languages
License: GPLv2 or later
*/

if (!defined('ABSPATH')) die('No direct access allowed');

// Check to make sure if WP_Optimize is already call and returns
if (!class_exists('WP_Optimize')):

define('WPO_VERSION', '2.1.1');
define('WPO_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WPO_PLUGIN_MAIN_PATH', plugin_dir_path( __FILE__ ));

class WP_Optimize {

	private $template_directories;
	
	protected static $_instance = null;
	protected static $_optimizer_instance = null;
	protected static $_options_instance = null;
	protected static $_notices_instance = null;
	protected static $_logger_instance = null;

	public function __construct() {

		//Checks if premium is installed along with plugins needed.
		add_action('plugins_loaded', array($this, 'plugins_loaded'), 1);

		register_activation_hook(__FILE__, 'wpo_activation_actions');
		register_deactivation_hook(__FILE__, 'wpo_deactivation_actions');

		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'admin_menu'));

		add_filter("plugin_action_links_".plugin_basename(__FILE__), array($this, 'plugin_settings_link'));
		add_action('wpo_cron_event2', array($this, 'cron_action'));
		add_filter('cron_schedules', array($this, 'cron_schedules'));
		
		add_action('wp_ajax_wp_optimize_ajax', array($this, 'wp_optimize_ajax_handler'));

        // initialize loggers
        add_action('plugins_loaded', array($this, 'setup_loggers'));

		include_once(WPO_PLUGIN_MAIN_PATH.'/includes/updraftcentral.php');
		
	}
	
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function get_optimizer() {
		if (empty(self::$_optimizer_instance)) {
			if (!class_exists('WP_Optimizer')) require_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimizer.php');
			self::$_optimizer_instance = new WP_Optimizer();
		}
		return self::$_optimizer_instance;
	}
	
	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('WP_Optimize_Options')) require_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimize-options.php');
			self::$_options_instance = new WP_Optimize_Options();
		}
		return self::$_options_instance;
	}
	
	public static function get_notices() {
		if (empty(self::$_notices_instance)) {
			if (!class_exists('WP_Optimize_Notices')) require_once(WPO_PLUGIN_MAIN_PATH.'/includes/wp-optimize-notices.php');
			self::$_notices_instance = new WP_Optimize_Notices();
		}
		return self::$_notices_instance;
	}

    /**
     * Return instance of Updraft_Logger
     * @return null|Updraft_Logger
     */
    public static function get_logger() {
        if (empty(self::$_logger_instance)) {
            require_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-updraft-logger.php');
            self::$_logger_instance = new Updraft_Logger();
        }
        return self::$_logger_instance;
    }

	/**
		* Checks if it is the premium version and loads it. It also ensures if there are any free versions installed
		* to disable them and show an appriprite error if trying to enable
		*/
	public function plugins_loaded() {
		// Check if premium file exists
		if (file_exists(WPO_PLUGIN_MAIN_PATH.'/premium.php')) {
			// Check if class already loaded
			if (!class_exists('WP_Optimize_Premium')) {
				// Require WP-Optimize premium file
				require_once(WPO_PLUGIN_MAIN_PATH.'/premium.php');
			}
		}

		//Check if premium is installed
		$check_premium = $this->is_installed('WP-Optimize Premium');
		// If premium installed, deactivate free version
		if ($check_premium['installed']) {
			//Get activation details on base / free install
			$get_base_install = $this->is_installed('WP-Optimize');
			//Only deactivate if it is active
			if ($get_base_install['active']) {
				//only remove if premium isnt active
				if (!$check_premium['active']) {
					//Removes the admin menu items on the left WP bar
					remove_action('admin_menu', array($this, 'admin_menu'));
					//Removes options displayed on the plugins menu
					remove_filter("plugin_action_links_".plugin_basename($get_base_install['name']), array($this, 'plugin_settings_link'));
				}
				//Deactivates base / free version
				deactivate_plugins(plugin_basename($get_base_install['name']));
				//Returns the notice letting the user know it cannot be active if premium is installed
				return add_action('admin_notices', array($this, 'show_admin_notice_premium'));
			}
		}
		//Loads the langues file
		load_plugin_textdomain('wp-optimize', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	/**
		* This function will allow a check to be done if a specific 
		* plugin is installed.
		* @param  String  $name Specify "Plugin Name" to retuns details about it
		* @return Array         Returns an array of details such as if installed, the name of the plugin and if it is active
		*/
	public function is_installed($name) {
		// Includes the WP Plugin file
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		//Gets all plugins available
		$get_plugins = get_plugins();

		//Gets all active plugins
		$active_plugins = get_option('active_plugins');

		$is_installed['installed'] = false;
		$is_installed['active'] = false;

		//loops around each plugin available 
		foreach ($get_plugins as $key => $value) {
			//if the plugin name matches that of the specified name, it will gather details
			if ($value['Name'] == $name) {
				$is_installed['installed'] = true;
				$is_installed['name'] = $key;
				$is_installed['version'] = $value['Version'];
				//Check if the plugin is active
				if (in_array($key, $active_plugins)) {
					$is_installed['active'] = true;
				}
				break;
			}
		}
		return $is_installed;
	}

	/**
		* This is a notice to show users that premium is installed
		* @return echo error message
		*/
	function show_admin_notice_premium() {
		if( true == true ) {
		echo '<div id="my-custom-warning" class="error fade"><p>WP-Optimze cannot be run when WP-Optimize Premium is installed.</p></div>';
		if (isset($_GET['activate']))
			unset($_GET['activate']);
		}
	}

	public function admin_init() {

		global $pagenow;

		$this->register_template_directories();

		if (($pagenow == 'index.php' && current_user_can('update_plugins')) || ($pagenow == 'index.php' && defined('WP_OPTIMIZE_FORCE_DASHNOTICE') && WP_OPTIMIZE_FORCE_DASHNOTICE)) {

			$options = $this->get_options();

			$dismissed_until = $options->get_option('dismiss_dash_notice_until', 0);

			$installed = @filemtime(WPO_PLUGIN_MAIN_PATH . '/index.html');
			$installed_for = time() - $installed;

			if (($installed && time() > $dismissed_until && $installed_for > 28*86400 && !defined('WP_OPTIMIZE_NOADS_B')) || (defined('WP_OPTIMIZE_FORCE_DASHNOTICE') && WP_OPTIMIZE_FORCE_DASHNOTICE)) {
				
				add_action('all_admin_notices', array($this, 'show_admin_notice_upgradead') );
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
		
		// Some commands that are available via AJAX only
		if ('dismiss_dash_notice_until' == $subaction) {
		
			$options->update_option('dismiss_dash_notice_until', time() + 366*86400);
			
		} elseif ('dismiss_page_notice_until' == $subaction) {
		
			$options->update_option('dismiss_page_notice_until', time() + 84*86400);
			
		} else {
		
			// Other commands, available for any remote method
			if (!class_exists('WP_Optimize_Commands')) require_once(WPO_PLUGIN_MAIN_PATH.'includes/class-commands.php');
			
			$commands = new WP_Optimize_Commands();
			
			if (!method_exists($commands, $subaction)) {
			
				error_log("WP-Optimize: ajax_handler: no such command ($command)");
				die('No such command');
				
			} else {
			
				$results = call_user_func(array($commands, $subaction), $data);
				
				if (is_wp_error($results)) {
					$results = array(
						'result' => false,
						'error_code' => $results->get_error_code(),
						'error_message' => $results->get_error_message(),
						'error_data' => $results->get_error_data(),
					);
				}
			
			}
			
		}
		
		echo json_encode($results);
		
		die;
	}

	public function get_tabs() {
		return apply_filters('wp_optimize_admin_page_tabs', array(
			'optimize' => 'WP-Optimize',
			'tables' => __('Table information', 'wp-optimize'),
			'settings' => __('Settings', 'wp-optimize'),
			'may_also' => __('Plugin family', 'wp-optimize'),
		));
	}
	
	public function wp_optimize_menu() {

		$capability_required = $this->capability_required();
	
		if (!current_user_can($capability_required)) { echo "Permission denied."; return; }
	
		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? WPO_VERSION.'.'.time() : WPO_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		
		wp_register_script('updraft-queue-js', WPO_PLUGIN_URL.'js/queue'.$min_or_not.'.js', array(), $enqueue_version);

		wp_enqueue_script('wp-optimize-admin-js', WPO_PLUGIN_URL.'js/wpadmin'.$min_or_not.'.js', array('jquery', 'updraft-queue-js'), $enqueue_version);

		wp_enqueue_style('wp-optimize-admin-css', WPO_PLUGIN_URL.'css/admin'.$min_or_not.'.css', array(), $enqueue_version);
		
		wp_localize_script('wp-optimize-admin-js', 'wpoptimize', array(
			'error_unexpected_response' => __('An unexpected response was received.', 'wp-optimize'),
			'optimization_complete' => __('Optimization complete', 'wp-optimize'),
		));
		
		$options = $this->get_options();
		
		$tabs = $this->get_tabs();
		
		$default_tab = apply_filters('wp_optimize_admin_default_tab', 'optimize');

		$active_tab = isset($_GET['tab']) ? substr($_GET['tab'], 12) : $default_tab;

		if (!in_array($active_tab, array_keys($tabs))) $active_tab = $default_tab;

		$nonce_passed = (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wpo_optimization')) ? true : false;
		
		if ('optimize' == $active_tab && $nonce_passed && isset($_POST['wp-optimize'])) $options->save_sent_manual_run_optimization_options($_POST, true);

		echo '<div id="wp-optimize-wrap" class="wrap">';

		$this->include_template('admin-page-header.php', false, array('active_tab' => $active_tab, 'tabs' => $tabs));

		$optimize_db = ($nonce_passed && isset($_POST["optimize-db"])) ? true : false;
		
		$optimizer = $this->get_optimizer();
		
		foreach ($tabs as $tab_id => $tab_description) {
		
			echo '<div class="wp-optimize-nav-tab-contents" id="wp-optimize-nav-tab-contents-'.$tab_id.'" '.(($tab_id == $active_tab) ? '' : 'style="display:none;"').'>';
		
			do_action('wp_optimize_admin_tab_render_begin', $active_tab);
		
			switch ($tab_id) {
			
				case 'optimize':
				
					$optimization_results = $nonce_passed ? $optimizer->do_optimizations($_POST) : false;

					if (!empty($optimization_results)) {
						echo '<div id="message" class="updated"><strong>';
						foreach ($optimization_results as $optimization_result) {
							if (!empty($optimization_result->output)) {
								foreach ($optimization_result->output as $line) { echo $line."<br>"; }
							}
						}
						echo '</strong></div>';
					}
					
					$this->include_template('optimize-table.php', false, array('optimize_db' => $optimize_db));
				
					break;
					
				case 'tables':

					$this->include_template('tables.php', false, array('optimize_db' => $optimize_db));

					break;
					
				case 'settings':
				
					if ('POST' == $_SERVER['REQUEST_METHOD']) {

						// Nonce check
						check_admin_referer('wpo_settings');
					
						$output = $options->save_settings($_POST);
						
						foreach ($output['messages'] as $item) {
							echo '<div class="updated fade"><strong>'.$item.'</strong></div>';
						}
						
						foreach ($output['errors'] as $item) {
							echo '<div class="error fade"><strong>'.$item.'</strong></div>';
						}

					}
				
					$this->include_template('admin-settings.php');
					break;
					
				case 'may_also':
					$this->include_template('may-also-like.php');
					break;
			}
			
			echo '</div>';
		}
		
		do_action('wp_optimize_admin_tab_render_end', $active_tab);
		
		echo '</div>';

	}

	public function wpo_admin_bar() {
		global $wp_admin_bar;

		if (defined('WPOPTIMIZE_ADMINBAR_DISABLE') && WPOPTIMIZE_ADMINBAR_DISABLE) return;

		// Add a link called at the top admin bar
		$args = array(
			'id' => 'wp-optimize-node',
			'title' => apply_filters('wpoptimize_admin_node_title', 'WP-Optimize')
		);
		$wp_admin_bar->add_node($args);
		
		$tabs = $this->get_tabs();
		
		foreach ($tabs as $tab_id => $tab_title) {
			$args = array(
				'id' => 'wpoptimize_admin_node_'.$tab_id,
				'title' => ('optimize' == $tab_id) ? __('Optimize', 'wp-optimize') : $tab_title,
				'parent' => 'wp-optimize-node',
				'href' => menu_page_url('WP-Optimize', false). '&tab=wp_optimize_'.$tab_id,
			);
			$wp_admin_bar->add_node($args);
		}
		
	}

	// Add settings link on plugin page
	public function plugin_settings_link($links) {
	
		$admin_page_url = $this->get_options()->admin_page_url();
	
		$settings_link = '<a href="' . esc_url( $admin_page_url ) . '">' . __( 'Settings', 'wp-optimize' ) . '</a>';
		array_unshift($links, $settings_link);

		$optimize_link = '<a href="' . esc_url( $admin_page_url ) . '">' . __( 'Optimizer', 'wp-optimize' ) . '</a>';
		array_unshift($links, $optimize_link);
		return $links;
	}

	public function cron_activate() {
		$gmtoffset = (int) (3600 * ((double) get_option('gmt_offset')));

		$options = $this->get_options();
		
		if ($options->get_option('schedule') === false ) {
			$options->set_default_options();
		} else {
			if ($options->get_option('schedule') == 'true') {
				if (!wp_next_scheduled('wpo_cron_event2')) {

					$schedule_type = $options->get_option('schedule-type', 'wpo_weekly');

					$this_time = 86400*7;
					
					switch ($schedule_type) {
						case "wpo_daily":
						$this_time = 86400;
						break;

						case "wpo_weekly":
						$this_time = 86400*7;
						break;

						case "wpo_otherweekly":
						$this_time = 86400*14;
						break;

						case "wpo_monthly":
						$this_time = 86400*30;
						break;
					}
					
					add_action('wpo_cron_event2', array($this, 'cron_action'));
					wp_schedule_event(current_time( "timestamp", 0 ) + $this_time , $schedule_type, 'wpo_cron_event2');
					WP_Optimize()->log('running wp_schedule_event()');
				}
			}
		}
	}


	// Scheduler public functions to update schedulers
	public function cron_schedules( $schedules ) {
		$schedules['wpo_daily'] = array('interval' => 86400, 'display' => 'Once Daily');
		$schedules['wpo_weekly'] = array('interval' => 86400*7, 'display' => 'Once Weekly');
		$schedules['wpo_otherweekly'] = array('interval' => 86400*14, 'display' => 'Once Every Other Week');
		$schedules['wpo_monthly'] = array('interval' => 86400*30, 'display' => 'Once Every Month');
		return $schedules;
	}


	public function admin_menu() {
	
		$capability_required = $this->capability_required();
	
		if (!current_user_can($capability_required)) return;

		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgdmlld0JveD0iMCAwIDE2IDE2IgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmc0MzE2IgogICBoZWlnaHQ9IjE2IgogICB3aWR0aD0iMTYiPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM0MzE4IiAvPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTQzMjEiPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxnCiAgICAgaWQ9ImxheWVyMSI+CiAgICA8cGF0aAogICAgICAgc3R5bGU9ImZpbGw6I2EwYTVhYTtmaWxsLW9wYWNpdHk6MSIKICAgICAgIGlkPSJwYXRoNTciCiAgICAgICBkPSJtIDEwLjc2ODgwOSw2Ljc2MTYwNTEgMCwwIGMgLTAuMDE2ODgsLTAuMDE2ODc4IC0wLjAyNTMxLC0wLjA0MjE4MSAtMC4wMzM3NCwtMC4wNjc0OTkgLTAuMDA4NCwtMC4wMDgzOSAtMC4wMDg0LC0wLjAxNjg3OCAtMC4wMTY4OCwtMC4wMzM3NDMgQyA5Ljk5MjYxMTIsNS4xOTIzMzY2IDguMjIwODU1Nyw0LjU4NDg3ODEgNi43NDQzOTEyLDUuMjkzNTc5NyA1LjY3MjkwMDUsNS44MDgyMzI4IDUuMDU3MDA0Myw2Ljg4ODE2MTMgNS4wNjU0NDIsOC4wMDE4MzY1IDQuNDU3OTgyMiw3LjMxMDAwNzYgMy42OTg2NTg0LDYuNzk1MzU0NSAyLjg1NDk2NDIsNi40OTE2MjUzIDMuMjY4Mzc0Myw1LjA2NTc4MzEgNC4yNTU0OTYsMy44MTcxMTY2IDUuNjg5Nzc0NiwzLjEyNTI4NzggOC4zNjQyODMyLDEuODM0NDM2OCAxMS41NzAzMTksMi45Mzk2NzQ0IDEyLjg4NjQ4MSw1LjU4ODg3MjYgMTMuNDUxNzU1LDYuNzI3ODU5NiAxNC42NDk4MDEsNy4zNTIxOTIxIDE1Ljg0Nzg0Niw3LjIzNDA3NSAxNS43NjM0ODIsNi4zMzk3NiAxNS41MTg4MDUsNS40MzcwMDg2IDE1LjEwNTM5Niw0LjU3NjQ0MDQgMTMuMjE1NTIxLDAuNjg3MDEzNCA4LjUzMzAyMjYsLTAuOTQxMzE2MjcgNC42NDM1OTQzLDAuOTQwMTIxNzkgMi4zMjM0MzcsMi4wNjIyMzM0IDAuODA0Nzg4MTQsNC4xNzk5MDQ0IDAuMzU3NjMxMzIsNi41MzM4MDk4IDIuNDE2MjQzOCw2LjQyNDEyOSA0LjQzMjY3MTcsNy41MDQwNTc0IDUuNDM2NjY2Miw5LjQzNjExNjcgbCAwLjAwODM5LDAgYyAwLjc1OTMxOTIsMS4zNzUyMjAzIDIuNDcyMDE3OCwxLjk0MDQ5NTMgMy45MDYyOTYsMS4yNDg2NjczIDEuMDQ2MTc5OCwtMC41MDYyMTggMS42NTM2NDA4LC0xLjUzNTUyMzggMS42Nzg5NTA4LC0yLjYxNTQ1MTIgMC41ODIxNDgsMC43MDg3MDE4IDEuMzMzMDM1LDEuMjQ4NjY2OCAyLjE1OTg1NiwxLjU3NzcwNjQgLTAuNDM4NzIxLDEuMzU4MzQ3OCAtMS40MDA1MzMsMi41NDc5NTQ4IC0yLjc5MjYyNywzLjIxNDQ3ODggLTIuNTkwMTM4NywxLjI0ODY1OCAtNS42NzgwNTc0LDAuMjUzMTA0IC03LjA2MTcxNTEsLTIuMjI3MzU3IGwgMCwwIEMgMi43NjIxMDQ4LDkuNDUyOTg5NCAxLjUxMzQzODMsOC44MjAyMTkxIDAuMjgxNjQ1OTIsOC45NzIwODQ0IDAuMzgyODg3NjUsOS43OTg5MDQ2IDAuNjE5MTIzMzEsMTAuNjE3Mjg3IDAuOTk4Nzg1MiwxMS40MDE5MjIgYyAxLjg4MTQzNjgsMy44OTc4NjQgNi41NjM5MzcsNS41MjYxOTggMTAuNDYxODAwOCwzLjY0NDc2IDIuMjQ0MjI2LC0xLjA4ODM2OSAzLjczNzU2MiwtMy4xMDQ3OTYgNC4yMzUzNDIsLTUuMzc0MzMyMyAtMS45OTk1NTQsMC4wNDIxODEgLTMuOTQ4NDg2LC0xLjAyOTMwNjMgLTQuOTI3MTcsLTIuOTEwNzQzMyB6IgogICAgICAgY2xhc3M9InN0MTciIC8+CiAgPC9nPgo8L3N2Zz4K';

		if (function_exists('add_meta_box')) {
			add_menu_page("WP-Optimize", "WP-Optimize", $capability_required, "WP-Optimize", array($this,"wp_optimize_menu"), $icon_svg);
		} else {
			add_submenu_page("index.php", "WP-Optimize", "WP-Optimize", $capability_required, "WP-Optimize", array($this,"wp_optimize_menu"), $icon_svg);
		}
		
		$options = $this->get_options();
		
		if ($options->get_option('enable-admin-menu', 'false' ) == 'true') {
			add_action('wp_before_admin_bar_render', array($this, 'wpo_admin_bar'));
		}

		$options->set_default_options();
		$this->cron_activate();
	}
	
	private function wp_normalize_path($path) {
		// wp_normalize_path is not present before WP 3.9
		if (function_exists('wp_normalize_path')) return wp_normalize_path($path);
		// Taken from WP 4.6
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|(?<=.)/+|', '/', $path );
		if ( ':' === substr( $path, 1, 1 ) ) {
			$path = ucfirst( $path );
		}
		return $path;
	}
	
	public function get_templates_dir() {
		return apply_filters('wp_optimize_templates_dir', $this->wp_normalize_path(WPO_PLUGIN_MAIN_PATH.'/templates'));
	}

	public function get_templates_url() {
		return apply_filters('wp_optimize_templates_url', WPO_PLUGIN_MAIN_PATH.'/templates');
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
			error_log("WP Optimize: template not found: $template_file");
			echo __('Error:', 'wp-optimize').' '.__('template not found', 'wp-optimize')." ($path)";
		} else {
			extract($extract_these);
			global $wpdb;
			$wp_optimize = $this;
			$optimizer = $this->get_optimizer();
			$options = $this->get_options();
			$wp_optimize_notices = $this->get_notices();
			include $template_file;
		}

		do_action('wp_optimize_after_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if ($return_instead_of_echo) return ob_get_clean();
	}

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

		// Optimal hook for most extensions to hook into
		$this->template_directories = apply_filters('wp_optimize_template_directories', $template_directories);

	}
	
	// Not currently used; needs looking at.
	// N.B. The description does not match the actual function
	/**
	* send_email($sendto, $msg)
	* @return success
	* @param $sentdo - eg. who to send it to, abc@def.com
	* @param $msg - the msg in text
	*/
	public function send_email($date, $cleanedup){
	//
		ob_start();
		// this need to work on - currently not using the parameter values
		$myTime = current_time( "timestamp", 0 );
		$myDate = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $myTime );

		//$formattedCleanedup = $wp_optimize->format_size($cleanedup);

		$sendto = $options->get_option('email-address');
		if (!$sendto) $sendto = get_bloginfo ( 'admin_email' );

		//$thiscleanup = $wp_optimize->format_size($cleanedup);

		$subject = get_bloginfo ( 'name' ).": ".__("Automatic Operation Completed","wp-optimize")." ".$myDate;

		$msg  = __("Scheduled optimization was executed at","wp-optimize")." ".$myDate."\r\n"."\r\n";
		//$msg .= __("Recovered space","wp-optimize").": ".$thiscleanup."\r\n";
		$msg .= __("You can safely delete this email.","wp-optimize")."\r\n";
		$msg .= "\r\n";
		$msg .= __("Regards,","wp-optimize")."\r\n";
		$msg .= __("WP-Optimize Plugin","wp-optimize");

		//wp_mail( $sendto, $subject, $msg );

		ob_end_flush();
	}

	/*
	* function log()
	*
	* parameters: message to debug
	*
	* @return none
	*/
	public function log($message, $context = array()) {
        $this->get_logger()->debug($message, $context);
	}
	
	/**
	* $wp_optimize->format_size()
	* Function: Format Bytes Into KB/MB
	* @param mixed $bytes
	* @return
	*/
	public function format_size($bytes) {
		if ($bytes > 1073741824) {
			return number_format_i18n($bytes/1073741824, 2) . ' '.__('GB', 'wp-optimize');
		} elseif ($bytes > 1048576) {
			return number_format_i18n($bytes/1048576, 1) . ' '.__('MB', 'wp-optimize');
		} elseif ($bytes > 1024) {
			return number_format_i18n($bytes/1024, 1) . ' '.__('KB', 'wp-optimize');
		} else {
			return number_format_i18n($bytes, 0) . ' '.__('bytes', 'wp-optimize');
		}
	}
	
	/*
	* function cron_action()
	*
	* parameters: none
	*
	* executed this function on cron event
	*
	* @return none
	*/
	public function cron_action() {
	
		$optimizer = $this->get_optimizer();
		$options = $this->get_options();
		
		$this->log('WPO: Starting cron_action()');
		
		if ('true' == $options->get_option('schedule')) {

			$this_options = $options->get_option('auto');
			
			$optimizations = $optimizer->get_optimizations();
			
			// Currently the output of the optimizations is not saved/used/logged
			$results = $optimizer->do_optimizations($this_options, 'auto');
			
		}
		
	}

	/*
		This will customize a URL with a correct Affiliate link
		This function can be update to suit any URL as longs as the URL is passed
		*/
	public function wp_optimize_url($url, $text, $html=null, $class=null) {

		//check if the URL is UpdraftPlus
		if (false !== strpos($url, '//updraftplus.com')){

			//Set URL with Affiliate ID
			$url = $url.'?afref='.$this->get_notices()->get_affiliate_id();

			//apply filters
			$url = apply_filters('wpoptimize_updraftplus_com_link', $url);
		} 
		//return URL - check if there is HTMl such as Images
		if(!empty($html)){
			echo '<a '.$class.' href="'.esc_attr($url).'">'.$html.'</a>';
		}else{
			echo '<a '.$class.' href="'.esc_attr($url).'">'.htmlspecialchars($text).'</a>';
		}
	}

    /**
     * Setup WPO logger(s)
     */
    public function setup_loggers() {

        $logger = $this->get_logger();
        $loggers = $this->wpo_loggers();

        if (!empty($loggers)) {
            foreach($loggers as $_logger) {
                $logger->add_logger( $_logger );
            }
        }

    }

    /**
     * Returns list of WPO loggers instances
     *
     * apply filter wp_optimize_loggers
     *
     * @return array|mixed|void
     */
    public function wpo_loggers() {

        $loggers = array();

        $loggers_classes = array(
            'Updraft_PHP_Logger' => WPO_PLUGIN_MAIN_PATH . 'includes/class-updraft-php-logger.php',
            'Updraft_Simple_History_Logger' => WPO_PLUGIN_MAIN_PATH . 'includes/class-updraft-simple-history-logger.php'
        );

        $loggers_classes = apply_filters('wp_optimize_loggers_classes', $loggers_classes);

        if (!empty($loggers_classes)) {
            foreach ($loggers_classes as $logger_class => $logger_file) {

                if (!class_exists($logger_class)) {
                    if (is_file($logger_file)) {
                        require_once($logger_file);
                    }
                }

                if (class_exists($logger_class)) {
                    $loggers[] = new $logger_class();
                }
            }
        }

        $loggers = apply_filters('wp_optimize_loggers', $loggers);

        if (empty($loggers)) return array();

        $logger_options = $this->get_options()->get_option('logging');
        
        if (empty($logger_options)) $logger_options = array();

        foreach($loggers as $logger) {
            $logger_class_name = get_class($logger);
            $logger_id = strtolower($logger_class_name);
            if (!array_key_exists($logger_id, $logger_options) || $logger_options[$logger_id] != 'true') {
                $logger->disable();
            }
        }

        return $loggers;
    }
    
}


// plugin activation actions
function wpo_activation_actions() {
	WP_Optimize()->get_options()->set_default_options();
}

// plugin deactivation actions
function wpo_deactivation_actions() {
	wpo_cron_deactivate();
	WP_Optimize()->get_options()->delete_all_options();
}

function wpo_cron_deactivate() {
	WP_Optimize()->log('running wpo_cron_deactivate()');
	wp_clear_scheduled_hook('wpo_cron_event2');
}

function WP_Optimize() {
	return WP_Optimize::instance();
}

endif;

$GLOBALS['wp_optimize'] = WP_Optimize();
