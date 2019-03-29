<?php

if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed');

/**
 * All commands that are intended to be available for calling from any sort of control interface (e.g. wp-admin, UpdraftCentral) go in here. All public methods should either return the data to be returned, or a WP_Error with associated error code, message and error data.
 */
class WP_Optimize_Commands {

	private $optimizer;

	private $options;

	private $wpo_sites; // used in get_optimizations_info command.

	public function __construct() {
		$this->optimizer = WP_Optimize()->get_optimizer();
		$this->options = WP_Optimize()->get_options();
	}

	public function get_version() {
		return WPO_VERSION;
	}

	public function enable_or_disable_feature($data) {
	
		$type = (string) $data['type'];
		$enable = (boolean) $data['enable'];
	
		$options = array($type => $enable);

		return $this->optimizer->trackback_comment_actions($options);
	}
	
	public function save_manual_run_optimization_options($sent_options) {
		return $this->options->save_sent_manual_run_optimization_options($sent_options);
	}

	public function get_status_box_contents() {
		return WP_Optimize()->include_template('settings/status-box-contents.php', true, array('optimize_db' => false));
	}
	
	public function get_optimizations_table() {
		return WP_Optimize()->include_template('database/optimizations-table.php', true);
	}

	/**
	 * Pulls and return the "WP Optimize" template contents. Primarily used for UpdraftCentral
	 * content display through ajax request.
	 *
	 * @return array An array containing the WPO translations and the "WP Optimize" tab's rendered contents
	 */
	public function get_wp_optimize_contents() {
		$content = WP_Optimize()->include_template('database/optimize-table.php', true, array('optimize_db' => false));
		$content .= $this->get_status_box_contents();

		return array(
			'content' => $content,
			'translations' => $this->get_js_translation()
		);
	}

	/**
	 * Pulls and return the "Table Information" template contents. Primarily used for UpdraftCentral
	 * content display through ajax request.
	 *
	 * @return array An array containing the WPO translations and the "Table Information" tab's rendered contents
	 */
	public function get_table_information_contents() {
		$content = WP_Optimize()->include_template('database/tables.php', true, array('optimize_db' => false));

		return array(
			'content' => $content,
			'translations' => $this->get_js_translation()
		);
	}

	/**
	 * Pulls and return the "Settings" template contents. Primarily used for UpdraftCentral
	 * content display through ajax request.
	 *
	 * @return array An array containing the WPO translations and the "Settings" tab's rendered contents
	 */
	public function get_settings_contents() {
		$admin_settings = '<form action="#" method="post" enctype="multipart/form-data" name="settings_form" id="settings_form">';
		$admin_settings .= WP_Optimize()->include_template('settings/settings-general.php', true, array('optimize_db' => false));
		$admin_settings .= WP_Optimize()->include_template('settings/settings-auto-cleanup.php', true, array('optimize_db' => false));
		$admin_settings .= WP_Optimize()->include_template('settings/settings-logging.php', true, array('optimize_db' => false));
		$admin_settings .= '<input id="wp-optimize-settings-save" class="button button-primary" type="submit" name="wp-optimize-settings" value="' . esc_attr('Save settings', 'wp-optimize') .'" />';
		$admin_settings .= '</form>';
		$admin_settings .= WP_Optimize()->include_template('settings/settings-trackback-and-comments.php', true, array('optimize_db' => false));
		$content = $admin_settings;

		return array(
			'content' => $content,
			'translations' => $this->get_js_translation()
		);
	}

	/**
	 * Returns array of translations used by the WPO plugin. Primarily used for UpdraftCentral
	 * consumption.
	 *
	 * @return array The WPO translations
	 */
	public function get_js_translation() {
		$translations = WP_Optimize()->wpo_js_translations();

		// Make sure that we include the loggers classes info whenever applicable before
		// returning the translations to UpdraftCentral.
		if (is_callable(array(WP_Optimize(), 'get_loggers_classes_info'))) {
			$translations['loggers_classes_info'] = WP_Optimize()->get_loggers_classes_info();
		}

		return $translations;
	}

	/**
	 * Save settings command.
	 *
	 * @param string $data
	 * @return array
	 */
	public function save_settings($data) {
		
		parse_str(stripslashes($data), $posted_settings);

		// We now have $posted_settings as an array.
		return array(
			'save_results' => $this->options->save_settings($posted_settings),
			'status_box_contents' => $this->get_status_box_contents(),
			'optimizations_table' => $this->get_optimizations_table(),
		);
	}

	/**
	 * Save lazy load settings.
	 *
	 * @param string $data
	 * @return array
	 */
	public function save_lazy_load_settings($data) {
		parse_str(stripslashes($data), $posted_settings);

		return array(
			'save_result' => $this->options->save_lazy_load_settings($posted_settings)
		);
	}

	/**
	 * This sends the selected tick value over to the save function
	 * within class-wp-optimize-options.php
	 *
	 * @param  array $data An array of data that includes true or false for click option.
	 * @return array
	 */
	public function save_auto_backup_option($data) {
		return array('save_auto_backup_option' => $this->options->save_auto_backup_option($data));
	}

	/**
	 * Save option which sites to optimize in multisite mode.
	 *
	 * @param array $data Array of settings.
	 * @return bool
	 */
	public function save_site_settings($data) {
		return $this->options->save_wpo_sites_option($data['wpo-sites']);
	}
	
	/**
	 * Perform the requested optimization
	 *
	 * @param  array $params Should have keys 'optimization_id' and 'data'.
	 * @return array
	 */
	public function do_optimization($params) {
		
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			
			$optimization = $this->optimizer->get_optimization($optimization_id, $data);
	
			$result = is_a($optimization, 'WP_Optimization') ? $optimization->do_optimization() : null;

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array(),
				'status_box_contents' => $this->get_status_box_contents()
			);
			
			if (is_wp_error($optimization)) {
				$results['errors'][] = $optimization->get_error_message().' ('.$optimization->get_error_code().')';
			}
			
			if ($optimization->get_changes_table_data()) {
				$table_list = $this->get_table_list();
				$results['table_list'] = $table_list['table_list'];
				$results['total_size'] = $table_list['total_size'];
			}
		}
		return $results;
	}

	/**
	 * Preview command, used to show information about data should be optimized in popup tool.
	 *
	 * @param array $params Should have keys 'optimization_id', 'offset' and 'limit'.
	 *
	 * @return array
	 */
	public function preview($params) {
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			$params['offset'] = isset($params['offset']) ? (int) $params['offset'] : 0;
			$params['limit'] = isset($params['limit']) ? (int) $params['limit'] : 50;

			$optimization = $this->optimizer->get_optimization($optimization_id, $data);

			if (is_a($optimization, 'WP_Optimization')) {
				if (isset($params['site_id'])) {
					$optimization->switch_to_blog((int) $params['site_id']);
				}
				$result = $optimization->preview($params);
			} else {
				$result = null;
			}

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array()
			);
		}

		return $results;
	}

	/**
	 * Get information about requested optimization.
	 *
	 * @param array $params Should have keys 'optimization_id' and 'data'.
	 * @return array
	 */
	public function get_optimization_info($params) {
		if (!isset($params['optimization_id'])) {
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		} else {
			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();

			$optimization = $this->optimizer->get_optimization($optimization_id, $data);
			$result = is_a($optimization, 'WP_Optimization') ? $optimization->get_optimization_info() : null;

			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array(),
				'status_box_contents' => $this->get_status_box_contents()
			);
		}

		return $results;
	}
		
	public function get_table_list() {
		
		list ($total_size, $part2) = $this->optimizer->get_current_db_size();
	
		return array(
			'table_list' => WP_Optimize()->include_template('database/tables-body.php', true, array('optimize_db' => false)),
			'total_size' => $total_size
		);
	}

	/**
	 * Do action wp_optimize_after_optimizations
	 * used in ajax request after all optimizations completed
	 *
	 * @return boolean
	 */
	public function optimizations_done() {

		$this->options->update_option('total-cleaned', 0);
		// Run action after all optimizations completed.
		do_action('wp_optimize_after_optimizations');

		return true;
	}

	/**
	 * Return information about all optimizations.
	 *
	 * @param  array $params
	 * @return array
	 */
	public function get_optimizations_info($params) {
		$this->wpo_sites = isset($params['wpo-sites']) ? $params['wpo-sites'] : 0;

		add_filter('get_optimization_blogs', array($this, 'get_optimization_blogs_filter'));

		$results = array();
		$optimizations = $this->optimizer->get_optimizations();

		foreach ($optimizations as $optimization_id => $optimization) {
			if (false === $optimization->display_in_optimizations_list()) continue;

			$results[$optimization_id] = $optimization->get_settings_html();
		}

		return $results;
	}

	/**
	 * Filter for get_optimizations_blogs function, used in get_optimizations_info command.
	 * Not intended for direct usage as a command (is used internally as a WP filter)
	 *
	 * The class variable $wpo_sites is used for performing the filtering.
	 *
	 * @param array $sites - unfiltered list of sites
	 * @return array - after filtering
	 */
	public function get_optimization_blogs_filter($sites) {
		$sites = array();

		if (!empty($this->wpo_sites)) {
			foreach ($this->wpo_sites as $site) {
				if ('all' !== $site) $sites[] = $site;
			}
		}

		return $sites;
	}

	/**
	 * Checks overdue crons and return message
	 */
	public function check_overdue_crons() {
		$overdue_crons = WP_Optimize()->howmany_overdue_crons();

		if ($overdue_crons >= 4) {
			return array('m' => WP_Optimize()->show_admin_warning_overdue_crons($overdue_crons));
		}
	}

	/**
	 * Enable or disable Gzip compression.
	 *
	 * @param array $params - ['enable' => true|false]
	 * @return array
	 */
	public function enable_gzip_compression($params) {
		return WP_Optimize()->get_gzip_compression()->enable_gzip_command_handler($params);
	}


	/**
	 * Enable or disable browser cache.
	 *
	 * @param array $params - ['browser_cache_expire' => '1 month 15 days 2 hours' || '' - for disable cache]
	 * @return array
	 */
	public function enable_browser_cache($params) {
		return WP_Optimize()->get_browser_cache()->enable_browser_cache_command_handler($params);
	}

	/**
	 * Import WP-Optimize settings.
	 *
	 * @param array $params array with 'settings' item where 'settings' json-encoded string.
	 *
	 * @return Array - the results of the import operation
	 */
	public function import_settings($params) {
		if (empty($params['settings'])) {
			return array('errors' => array(__('Please upload a valid settings file.', 'wp-optimize')));
		}

		$params['settings'] = stripslashes($params['settings']);

		$settings = json_decode($params['settings'], true);

		// check if valid json file posted (requires PHP 5.3+)
		// @codingStandardsIgnoreLine
		if ((function_exists('json_last_error') && 0 != json_last_error()) || empty($settings)) {
			return array('errors' => array(__('Please upload a valid settings file.', 'wp-optimize')));
		}

		return WP_Optimize()->get_options()->save_settings($settings);
	}
}
