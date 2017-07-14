<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_revisions extends WP_Optimization {

	public $ui_sort_order = 1000;
	
	public $available_for_auto = true;
	public $auto_default = true;
	public $setting_default = true;
	public $available_for_saving = true;

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->posts."` WHERE post_type = 'revision'";
				
		if ($this->retention_enabled == 'true') {
			$clean .= '
				AND post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}
		$clean .= ';';

		$revisions = $this->query($clean);

		$message = sprintf(_n('%d post revision deleted', '%d post revisions deleted', $revisions, 'wp-optimize'), number_format_i18n($revisions));

        $this->logger->info($message);
		$this->register_output($message);
	}
	
	public function get_info() {
		
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->posts."` WHERE post_type = 'revision'";

		if ($this->retention_enabled == 'true') {
			$sql .= ' and post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$revisions = $this->wpdb->get_var($sql);

		if(!$revisions == 0 || !$revisions == NULL){
			$message = sprintf(_n('%d post revision in your database', '%d post revisions in your database', $revisions, 'wp-optimize'), number_format_i18n($revisions));
		} else {
			$message = __('No post revisions found', 'wp-optimize');
		}
		
		$this->register_output($message);
	}
	
	public function settings_label() {
	
		if ($this->retention_enabled == 'true') {
			return sprintf(__('Clean post revisions which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Clean all post revisions', 'wp-optimize');
		}
	
	}

	public function get_auto_option_description() {
		return __('Remove auto revisions', 'wp-optimize');
	}
}
