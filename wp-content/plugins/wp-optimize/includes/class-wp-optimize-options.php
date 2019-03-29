<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

/**
 * The proper way to obtain access to the instance is via WP_Optimize()->get_options().
 */
class WP_Optimize_Options {

	public $default_settings = array(
		'settings' => '',
		'schedule' => 'false',
		'schedule-type' => 'wpo_weekly',
		'retention-enabled' => 'false',
		'retention-period' => '',
		'enable-admin-menu' => 'false',
		'auto' => '',
		'logging' => '',
		'logging-additional' => ''
	);

	/**
	 * Returns url to WP-Optimize admin dashboard.
	 *
	 * @return string
	 */
	public function admin_page_url() {
		if (is_multisite()) {
			return network_admin_url('admin.php?page=WP-Optimize');
		} else {
			return admin_url('admin.php?page=WP-Optimize');
		}
	}

	/**
	 * Returns WP-Optimize option value.
	 *
	 * @param string $option  Option name.
	 * @param bool   $default
	 * @return mixed|void
	 */
	public function get_option($option, $default = false) {
		if (is_multisite()) {
			$blog_changed = false;
			// make sure that we are on main blog.
			if (!is_main_site()) {
				// get main blog is
				if (function_exists('get_network')) {
					$main_blog_id = get_network()->site_id;
				} else {
					global $current_site;
					$main_blog_id = $current_site->blog_id;
				}
				$blog_changed = true;
				switch_to_blog($main_blog_id);
			}
			// check option value for old plugin versions.
			$old_version_option_value = get_option('wp-optimize-'.$option, null);
			// if blog was changed.
			if ($blog_changed) restore_current_blog();
			// check option value for new plugin versions.
			$new_version_option_value = get_site_option('wp-optimize-mu-'.$option, null);
			// if it is exists old version value and doesn't exists new version option then return value.
			if (null !== $old_version_option_value && null === $new_version_option_value) return $old_version_option_value;

			return get_site_option('wp-optimize-mu-'.$option, $default);
		} else {
			return get_option('wp-optimize-'.$option, $default);
		}
	}

	/**
	 * Update WP-Optimize option value.
	 *
	 * @param string $option    Option name.
	 * @param mixed  $value     Option value.
	 * @param bool   $use_cache
	 * @return bool
	 */
	public function update_option($option, $value, $use_cache = true) {
		if (is_multisite()) {
			return update_site_option('wp-optimize-mu-'.$option, $value);
		} else {
			return update_option('wp-optimize-'.$option, $value);
		}
	}

	/**
	 * Delete WP-Optimize.
	 *
	 * @param string $option Option name.
	 */
	public function delete_option($option) {
		if (is_multisite()) {
			delete_site_option('wp-optimize-mu-'.$option);
		} else {
			delete_option('wp-optimize-'.$option);
		}
	}

	public function get_option_keys() {

		return apply_filters(
			'wp_optimize_option_keys',
			array('defaults', 'weekly-schedule', 'schedule', 'retention-enabled', 'retention-period', 'last-optimized', 'enable-admin-menu', 'schedule-type', 'total-cleaned', 'current-cleaned', 'email-address', 'email', 'auto', 'settings', 'dismiss_page_notice_until', 'dismiss_dash_notice_until')
		);
	}
	
	/**
	 * This particular option has its own functions abstracted to make it easier to change the format in future.
	 * To allow callers to always assume the latest format (because get_main_settings() will convert, if needed).
	 *
	 * @param  array $settings Array of optimization settings.
	 * @return array
	 */
	private function save_manual_run_optimizations_settings($settings) {
		$settings['last_saved_in'] = WPO_VERSION;
		return $this->update_option('settings', $settings);
	}
	
	public function get_main_settings() {
		return $this->get_option('settings');
	}

	/**
	 * This saves the tick box options for enabling auto backup
	 *
	 * @param  array $settings Array of information with the state of the tick box selected.
	 * @return array Message   Array for being completed.
	 */
	public function save_auto_backup_option($settings) {
		if (isset($settings['auto_backup']) && 'true' == $settings['auto_backup']) {
			$this->update_option('enable-auto-backup', 'true');
		} else {
			$this->update_option('enable-auto-backup', 'false');
		}

		$this->save_additional_auto_backup_options($settings);

		$output = array('messages' => array());
		
		$output['messages'][] = __('Auto backup option updated.', 'wp-optimize');
		
		return $output;
	}

	/**
	 * Save option which sites to optimize in multisite mode
	 *
	 * @param array $settings array of blog ids or "all" item for all sites.
	 * @return bool
	 */
	public function save_wpo_sites_option($settings) {
		return $this->update_option('wpo-sites', $settings);
	}

	/**
	 * Return list of blog ids to optimize in multisite mode
	 *
	 * @return mixed|void
	 */
	public function get_wpo_sites_option() {
		return $this->get_option('wpo-sites', array('all'));
	}

	
	public function save_settings($settings) {
		$optimizer = WP_Optimize()->get_optimizer();
	
		$output = array('messages' => array(), 'errors' => array());
		if (!empty($settings["enable-schedule"])) {
			$this->update_option('schedule', 'true');
			
			wpo_cron_deactivate();
			
			if (isset($settings["schedule_type"])) {
				$schedule_type = (string) $settings['schedule_type'];
				$this->update_option('schedule-type', $schedule_type);
			} else {
				$this->update_option('schedule-type', 'wpo_weekly');
			}
			
			WP_Optimize()->cron_activate();
		} else {
			$this->update_option('schedule', 'false');
			$this->update_option('schedule-type', 'wpo_weekly');
			wpo_cron_deactivate();
		}

		if (!empty($settings["enable-retention"])) {
			$retention_period = (int) $settings['retention-period'];
			$this->update_option('retention-enabled', 'true');
			$this->update_option('retention-period', $retention_period);
		} else {
			$this->update_option('retention-enabled', 'false');
		}

		// Get saved admin menu value before check.
		$saved_admin_bar = $this->get_option('enable-admin-menu', 'false');

		// Set refresh of default false so it doesnt refresh after save.
		$output['refresh'] = false;

		if (!empty($settings['enable-admin-bar'])) {
			$this->update_option('enable-admin-menu', 'true');
		} else {
			$this->update_option('enable-admin-menu', 'false');
		}

		// Make sure inbound input is a string.
		$updated_admin_bar = (isset($settings['enable-admin-bar']) && $settings['enable-admin-bar']) ? 'true' : 'false';
		
		// Check if the value is refreshed .
		if ($saved_admin_bar != $updated_admin_bar) {
			// Set refresh to true as the values have changed.
			$output['refresh'] = true;
		}

		do_action("auto_option_settings", $settings);

		/** Save multisite options */
		if (isset($settings['wpo-sites-cron'])) {
			$this->update_option('wpo-sites-cron', $settings['wpo-sites-cron']);
		}

		if (isset($settings['wpo-sites'])) {
			$this->save_wpo_sites_option($settings['wpo-sites']);
		}

		/** Save logging options */
		$logger_type = isset($settings['wpo-logger-type']) ? $settings['wpo-logger-type'] : '';
		$logger_options = isset($settings['wpo-logger-options']) ? $settings['wpo-logger-options'] : '';
		
		$this->update_option('logging', $logger_type);
		$this->update_option('logging-additional', $logger_options);

		// Save selected optimization settings.
		$this->save_sent_manual_run_optimization_options($settings, true, false);

		// Save auto backup option value.
		$enable_auto_backup = (isset($settings['enable-auto-backup']) ? 'true' : 'false');
		$this->update_option('enable-auto-backup', $enable_auto_backup);

		// Save additional auto backup option values.
		$this->save_additional_auto_backup_options($settings);

		// Save force DB optimization value.
		$enable_db_force_optimize = (isset($settings['innodb-force-optimize']) ? 'true' : 'false');
		$this->update_option('enable-db-force-optimize', $enable_db_force_optimize);

		$output['messages'][] = __('Settings updated.', 'wp-optimize');

		return $output;

	}

	/**
	 * Saves auto optimization settings
	 *
	 * @param array $settings Auto optimization settings array submitted by user
	 *
	 * @return void
	 */
	public function auto_option_settings($settings) {

		$optimizer = WP_Optimize()->get_optimizer();

		if (!empty($settings["schedule_type"])) {
			$options_from_user = isset($settings['wp-optimize-auto']) ? $settings['wp-optimize-auto'] : array();
			
			if (!is_array($options_from_user)) $options_from_user = array();
			
			$new_auto_options = array();
			
			$optimizations = $optimizer->get_optimizations();
			
			foreach ($optimizations as $optimization_id => $optimization) {
				if (empty($optimization->available_for_auto)) continue;
				$auto_id = $optimization->get_auto_id();
				$new_auto_options[$auto_id] = empty($options_from_user[$auto_id]) ? 'false' : 'true';
			}

			$this->update_option('auto', $new_auto_options);
		}

	}

	/**
	 * Save lazy load settings
	 *
	 * @param array $settings
	 * @return bool
	 */
	public function save_lazy_load_settings($settings) {
		/** Save Lazy Load settings */
		if (isset($settings['lazyload'])) {
			$this->update_option('lazyload', $settings['lazyload']);
		} else {
			$this->update_option('lazyload', array());
		}

		return true;
	}

	/**
	 * The $use_dom_id parameter is legacy, for when saving options not with AJAX (in which case the dom ID comes via the $_POST array)
	 *
	 * @param  array   $sent_options 			  Options sent from Ajax.
	 * @param  boolean $use_dom_id   			  Parameter is legacy.
	 * @param  boolean $available_for_saving_only Save only available for saving optimization state.
	 * @return array User Options
	 */
	public function save_sent_manual_run_optimization_options($sent_options, $use_dom_id = false, $available_for_saving_only = true) {
		$optimizations = WP_Optimize()->get_optimizer()->get_optimizations();
		$user_options = array();
		foreach ($optimizations as $optimization_id => $optimization) {
			// In current code, not all options can be saved.
			// Revisions, drafts, spams, unapproved, optimize.
			if ($available_for_saving_only && empty($optimization->available_for_saving)) continue;
			$setting_id = $optimization->get_setting_id();
			$id_in_sent = (($use_dom_id) ? $optimization->get_dom_id() : $optimization_id);
			// 'true' / 'false' are indeed strings here; this is the historical state. It may be possible to change later using our abstraction interface.
			$user_options[$setting_id] = isset($sent_options[$id_in_sent]) ? 'true' : 'false';
		}
		return $this->save_manual_run_optimizations_settings($user_options);
	}
	
	public function delete_all_options() {
		$option_keys = $this->get_option_keys();
		foreach ($option_keys as $key) {
			$this->delete_option($key);
		}
	}
	
	/**
	 * Setup options if not exists already.
	 */
	public function set_default_options() {
		$deprecated = null;
		$autoload_no = 'no';

		if (false === $this->get_option('schedule')) {
			// The option hasn't been added yet. We'll add it with $autoload_no set to 'no'.
			$this->update_option('schedule', 'false', $deprecated, $autoload_no);
			$this->update_option('last-optimized', 'Never', $deprecated, $autoload_no);
			$this->update_option('schedule-type', 'wpo_weekly', $deprecated, $autoload_no);
			// Deactivate cron.
			wpo_cron_deactivate();
		}

		if (false === $this->get_option('retention-enabled')) {
			$this->update_option('retention-enabled', 'false', $deprecated, $autoload_no);
			$this->update_option('retention-period', '2', $deprecated, $autoload_no);
		}

		if (false === $this->get_option('enable-admin-menu')) {
			$this->update_option('enable-admin-menu', 'false', $deprecated, $autoload_no);
		}

		if (false === $this->get_option('total-cleaned')) {
			$this->update_option('total-cleaned', '0', $deprecated, $autoload_no);
		}

		$optimizer = WP_Optimize()->get_optimizer();

		$optimizations = $optimizer->get_optimizations();

		$auto_options = $this->get_option('auto');
		$new_auto_options = array();

		// Auto options doesn't exists or invalid. Set default.
		if (empty($auto_options)) {
			foreach ($optimizations as $optimization) {
				if (empty($optimization->available_for_auto)) continue;

				$auto_id = $optimization->get_auto_id();

				$new_auto_options[$auto_id] = empty($optimization->auto_default) ? 'false' : 'true';
			}
			$this->update_option('auto', apply_filters('wpo_default_auto_options', $new_auto_options));
		}



		// Settings for main screen.
		if (false === $this->get_main_settings()) {
			$optimizer = WP_Optimize()->get_optimizer();

			$optimizations = $optimizer->get_optimizations();

			$new_settings = array();

			foreach ($optimizations as $optimization) {
				$setting_id = $optimization->get_setting_id();

				$new_settings[$setting_id] = empty($optimization->setting_default) ? 'false' : 'true';
			}

			$this->save_manual_run_optimizations_settings($new_settings);
		}
	}

	/**
	 * Save additional auto backup checkbox values.
	 *
	 * @param array $settings array with options.
	 */
	private function save_additional_auto_backup_options($settings) {
		// Save additional auto backup option values.
		foreach ($settings as $key => $value) {
			if (preg_match('/enable\-auto\-backup\-/', $key)) {
				$value = ('0' != $value) ? 'true' : 'false';
				$this->update_option($key, $value);
			}
		}
	}
}
