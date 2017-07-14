<?php

if (!defined('WPO_VERSION')) die ('No direct access allowed');

// Options handling

// The proper way to obtain access to the instance is via WP_Optimize()->get_options();

class WP_Optimize_Options {

// 	private $opts = array();

// 	public function options_table() {
// 		return 'options';
// 	}
// 
	public function admin_page_url() {
		return admin_url('admin.php?page=WP-Optimize');
	}
// 
// 	public function admin_page() {
// 		return 'options-general.php';
// 	}

	public function get_option($option, $default = false) {
		return get_option('wp-optimize-'.$option, $default);
	}

	public function update_option($option, $value, $use_cache = true) {
		return update_option('wp-optimize-'.$option, $value);
	}

	public function delete_option($option) {
		delete_option('wp-optimize-'.$option);
	}

	public function get_option_keys() {

		return apply_filters(
			'wp_optimize_option_keys',
			array('weekly-schedule', 'schedule', 'retention-enabled', 'retention-period', 'last-optimized', 'enable-admin-menu', 'schedule-type', 'total-cleaned', 'current-cleaned', 'email-address', 'email', 'auto', 'settings', 'dismiss_page_notice_until', 'dismiss_dash_notice_until')
		);
	}
	
	// This particular option has its own functions abstracted to make it easier to change the format in future, and to allow callers to always assume the latest format (because get_main_settings() will convert, if needed)
	private function save_manual_run_optimizations_settings($settings) {
		$settings['last_saved_in'] = WPO_VERSION;
		return $this->update_option('settings', $settings);
	}
	
	public function get_main_settings() {
		return $this->get_option('settings');
	}

	/**
	 * This saves the tick box options for enabling auto backup
	 * @param  Array 	$settings array of information with the state of the tick box selected
	 * @return Array 	Message array for being completed
	 */
	public function save_auto_backup_option($settings) {
		if (isset($settings['auto_backup']) && $settings['auto_backup'] == 'true') {
			$this->update_option('enable-auto-backup', 'true');
		} else {
			$this->update_option('enable-auto-backup', 'false');
		}

		$output = array('messages' => array());
		
		$output['messages'][] = __('Auto backup option updated.', 'wp-optimize');
		
		return $output;
	}
	
	public function save_settings($settings) {

		$optimizer = WP_Optimize()->get_optimizer();
	
		$output = array('messages' => array(), 'errors' => array());
	
		if (!empty($settings["enable-schedule"])) {
		
			$this->update_option('schedule', 'true');
			
			wpo_cron_deactivate();

			/* if (!wp_next_scheduled('wpo_cron_event2')) {
				wp_schedule_event(time(), 'wpo_weekly', 'wpo_cron_event2');
				add_filter('cron_schedules', 'wpo_cron_update_sched');

			}*/
			
			if (isset($settings["schedule_type"])) {
				$schedule_type = (string)$settings['schedule_type'];
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
			$retention_period = (int)$settings['retention-period'];
			$this->update_option('retention-enabled', 'true');
			$this->update_option('retention-period', $retention_period);
		} else {
			$this->update_option('retention-enabled', 'false');
		}

		//Get saved admin menu value before check
		$saved_admin_bar = $this->get_option('enable-admin-menu', 'false');

		//set refresh of default false so it doesnt refresh after save
		$output['refresh'] = false;

		if (!empty($settings['enable-admin-bar'])) {
			$this->update_option('enable-admin-menu', 'true');
		} else {
			$this->update_option('enable-admin-menu', 'false');
		}

		// make sure inbound input is a string
		$updated_admin_bar = $settings['enable-admin-bar'] ? 'true' : 'false';
		
		//check if the value is refreshed 
		if ($saved_admin_bar != $updated_admin_bar) {
			//set refresh to true as the values have changed
			$output['refresh'] = true;
		}

		if (!empty($settings["enable-email"])) {
	//		$this->update_option('enable-email', 'true');
		} else {
			//$this->update_option('enable-email', 'false');
		}
		
		if (!empty($settings["enable-email-address"])) {
			//$this->update_option('enable-email-address', wp_unslash( $settings["enable-email-address"] ) );
		} else {
			//$this->update_option('enable-email-address', get_bloginfo ( 'admin_email' ) );
		}

		if (!empty($settings["schedule_type"])) {
		
			$new_options = isset($settings['wp-optimize-auto']) ? $settings['wp-optimize-auto'] : array();
			
			if (!is_array($new_options)) $new_options = array();
			
			$new_auto_options = array();
			
			$optimizations = $optimizer->get_optimizations();
			
			foreach ($optimizations as $optimization_id => $optimization) {
			
				if (empty($optimization->available_for_auto)) continue;
				
				$auto_id = $optimization->get_auto_id();
				
				$new_auto_options[$auto_id] = !empty($new_options[$auto_id]) ? 'true' : 'false';
			
			}

			$this->update_option('auto', $new_auto_options);

		}

        /** Save logging options */

        $new_logging_options = isset($settings['wp-optimize-logging']) ? $settings['wp-optimize-logging'] : array();

        if (!is_array($new_logging_options)) $new_logging_options = array();

        $this->update_option('logging', $new_logging_options);

		$output['messages'][] = __('Settings updated.', 'wp-optimize');
		
		return $output;
	
	}
	
	// The $use_dom_id parameter is legacy, for when saving options not with AJAX (in which case the dom ID comes via the $_POST array)
	public function save_sent_manual_run_optimization_options($sent_options, $use_dom_id = false) {
	
		$optimizations = WP_Optimize()->get_optimizer()->get_optimizations();
		$user_options = array();
		foreach ($optimizations as $optimization_id => $optimization) {
			// In current code, not all options can be saved.
			///: revisions, drafts, spams, unapproved, optimize
			if (empty($optimization->available_for_saving)) continue;
			$setting_id = $optimization->get_setting_id();
			$id_in_sent = $use_dom_id ? $optimization->get_dom_id() : $optimization_id;
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
	
	/*
	* function set_default_options()
	*
	* parameters: none
	*
	* setup options if not exists already
	*
	* @return void
	*/
	public function set_default_options() {
		$deprecated = null;
		$autoload_no = 'no';

		if ($this->get_option('schedule') !== false) {
			// The option already exists, so we just update it.

		} else {
			// The option hasn't been added yet. We'll add it with $autoload_no set to 'no'.
			$this->update_option('schedule', 'false', $deprecated, $autoload_no );
			$this->update_option('last-optimized', 'Never', $deprecated, $autoload_no );
			$this->update_option('schedule-type', 'wpo_weekly', $deprecated, $autoload_no );
			// deactivate cron
			wpo_cron_deactivate();
		}
		
		if ($this->get_option('retention-enabled') !== false) {
		//
		}
		else {
			$this->update_option('retention-enabled', 'false', $deprecated, $autoload_no );
			$this->update_option('retention-period', '2', $deprecated, $autoload_no );
		}

		if ($this->get_option('enable-admin-menu') !== false) {
		//
		} else {
			$this->update_option('enable-admin-menu', 'false', $deprecated, $autoload_no );
		}    
			// ---------
		if ($this->get_option('enable-email') !== false) {
		//
		} else {
			//$this->update_option('enable-email', 'true', $deprecated, $autoload_no );
		}    
			// ---------
		if ($this->get_option('enable-email-address') !== '' ) {
		//
		} else {
			//$this->update_option('enable-email-address', get_bloginfo ( 'admin_email' ), $deprecated, $autoload_no );
		}    
			
		if ($this->get_option('total-cleaned') !== false) {
		//
		} else {
			$this->update_option('total-cleaned', '0', $deprecated, $autoload_no );
		}

		if ($this->get_option('auto') !== false) {
			// The option already exists, so we just update it.
		} else {
		
			$optimizer = WP_Optimize()->get_optimizer();
			
			$optimizations = $optimizer->get_optimizations();
			
			$new_auto_options = array();
			
			foreach ($optimizations as $optimization) {
			
				if (empty($optimization->available_for_auto)) continue;
				
				$auto_id = $optimization->get_auto_id();
				
				$new_auto_options[$auto_id] = empty($optimization->auto_default) ? 'false' : 'true';
			
			}
			
			$this->update_option('auto', $new_auto_options );
			
		}

		// settings for main screen
		if ($this->get_main_settings() !== false) {
			// The option already exists, so we just update it.
		} else {

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

}
