<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_postmeta extends WP_Optimization {

	public $ui_sort_order = 8000;

	public $available_for_auto = false;
	public $auto_default = false;

	public function optimize() {

		$clean = "DELETE pm FROM `".$this->wpdb->postmeta."` pm LEFT JOIN `".$this->wpdb->posts."` wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL;";

		$postmeta = $this->query($clean);

		$message = sprintf(_n('%d orphaned postmeta deleted', '%d orphaned postmeta deleted', $postmeta, 'wp-optimize'), number_format_i18n($postmeta));

        $this->logger->info($message);
		$this->register_output($message);
	}
	
	public function get_info() {
	
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->postmeta."` pm LEFT JOIN `".$this->wpdb->posts."` wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL;";

		$postmeta = $this->wpdb->get_var($sql);
		
		if ($postmeta) {
			$message = sprintf(_n('%d orphaned post meta data in your database', '%d orphaned postmeta in your database', $postmeta, 'wp-optimize'), number_format_i18n($postmeta));
		} else {
			$message = __('No orphaned post meta data in your database', 'wp-optimize');
		}
	
		$this->register_output($message);
	
	}

	public function settings_label() {
		return __('Clean post meta data', 'wp-optimize');
	}

	// N.B. This is not currently used; it was commented out in 1.9.1
	public function get_auto_option_description() {
		return __('Remove orphaned post meta', 'wp-optimize');
	}
}
