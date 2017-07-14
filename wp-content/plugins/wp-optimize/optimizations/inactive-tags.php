<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_tags extends WP_Optimization {

	public $available_for_auto = false;
	public $auto_default = false;
	public $ui_sort_order = 12000;

	public function optimize() {
	
// TODO: Ask R why this is commented out
//            $clean = "DELETE t,tt FROM  `".$this->wpdb->terms."` t INNER JOIN `$this->wpdb->term_taxonomy` tt ON t.term_id=tt.term_id WHERE tt.taxonomy='post_tag' AND tt.count=0;";
//
//			$tags = $this->query($clean);
//            $message = sprintf(_n('%d unused tag deleted', '%d unused tags deleted', $tags, 'wp-optimize'), number_format_i18n($tags));
	}
	public function get_info() {

	}

	public function settings_label() {
		return __('Remove unused tags', 'wp-optimize');
	}

	// N.B. This is not currently used
	public function get_auto_option_description() {
		// return __('Remove unused tags', 'wp-optimize');
	}
}
