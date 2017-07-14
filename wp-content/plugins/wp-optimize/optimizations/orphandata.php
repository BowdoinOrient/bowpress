<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_orphandata extends WP_Optimization {

	public $ui_sort_order = 10000;

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->term_relationships."` WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM `".$this->wpdb->posts."`);";

		$orphandata = $this->query($clean);

		$message = sprintf(_n('%d orphaned meta data deleted', '%d orphaned meta data deleted', $orphandata, 'wp-optimize'), number_format_i18n($orphandata));

        $this->logger->info($message);
		$this->register_output($message);

	}
	
	public function get_info() {
		
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->term_relationships."` WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM `".$this->wpdb->posts."`);";

		$orphandata = $this->wpdb->get_var($sql);

		if (!$orphandata == 0 || !$orphandata == NULL) {
			$message = sprintf(_n('%d orphaned relationship data in your database', '%d orphaned relationship data in your database', $orphandata, 'wp-optimize'), number_format_i18n($orphandata));
		} else {
			$message =__('No orphaned relationship data in your database', 'wp-optimize');
		}
		
		$this->register_output($message);
	}
	
	public function settings_label() {
		return __('Clean orphaned relationship data', 'wp-optimize');
	}
}
