<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_commentmeta extends WP_Optimization {

	public $ui_sort_order = 9000;

	// TODO: The first query here (but not the second) used to be run on a cron run. This needs reviewing when we review the whole cron-run set of options.

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->commentmeta."` WHERE comment_id NOT IN (SELECT comment_id FROM `".$this->wpdb->comments."`);";

		$commentstrash_meta = $this->query($clean);

		$message = sprintf(_n('%d unused comment metadata item removed', '%d unused comment metadata items removed', $commentstrash_meta, 'wp-optimize'), number_format_i18n($commentstrash_meta));

        $this->logger->info($message);
		$this->register_output($message);

		// TODO:  still need to test now cleaning up comments meta tables - removing akismet related settings
		$clean = "DELETE FROM `".$this->wpdb->commentmeta."` WHERE meta_key LIKE '%akismet%';";

		$commentstrash_meta2 = $this->query($clean);

		$message = sprintf(_n('%d unused akismet comment metadata item removed', '%d unused akismet comment metadata items removed', $commentstrash_meta2, 'wp-optimize'), number_format_i18n($commentstrash_meta2));

        $this->logger->info($message);
        $this->register_output($message);
	}
	
	public function get_info() {
	
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->commentmeta."` WHERE comment_id NOT IN (SELECT comment_id FROM `".$this->wpdb->comments."`);";

		$commentmeta = $this->wpdb->get_var($sql);

		if(!$commentmeta == 0 || !$commentmeta == NULL){
			$message = sprintf(_n('%d orphaned comment meta data in your database', '%d orphaned comment meta data in your database', $commentmeta, 'wp-optimize'), number_format_i18n($commentmeta));
		} else {
			$message =__('No orphaned comment meta data in your database', 'wp-optimize'); 
		}
		
		$this->register_output($message);
		
	}

	public function settings_label() {
		return __('Clean comment meta data', 'wp-optimize');
	}

	public function get_auto_option_description() {
		return __('Clean comment meta data', 'wp-optimize');
	}
}
