<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_trackbacks extends WP_Optimization {

	public $ui_sort_order = 7000;

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->comments."` WHERE comment_type = 'trackback';";

		$comments = $this->query($clean);

        $info_message = sprintf(_n('%d trackback deleted', '%d trackbacks deleted', $comments, 'wp-optimize'), number_format_i18n($comments));

        $this->logger->info($info_message);
		$this->register_output( $info_message );
	}
	
	public function get_info() {
		
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->comments."` WHERE comment_type='trackback';";

		$comments = $this->wpdb->get_var($sql);
		
		if(!$comments == NULL || !$comments == 0){
			$message = sprintf(_n('%d Trackback found', '%d Trackbacks found', $comments, 'wp-optimize'), number_format_i18n($comments));
		} else {
			$message = __('No trackbacks found', 'wp-optimize');
		}
		
		$this->register_output($message);
	}

	public function settings_label() {
		return __('Remove trackbacks', 'wp-optimize');
	}
}
