<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_unapproved extends WP_Optimization {

	public $available_for_auto = true;
	public $auto_default = false;
	public $setting_default = true;
	public $available_for_saving = true;
	public $ui_sort_order = 4000;

	// unapproved-comments / unapproved - need to check what IDs were previously used before (dom ID, settings ID), as there was a note about unapproved-comments in the source, which isn't in use now here.

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->comments."` WHERE comment_approved = '0' ";
			
		if ($this->retention_enabled == 'true') {
			$clean .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		
		$clean .= ';';

		$comments = $this->query($clean);

        $info_message = sprintf(_n('%d unapproved comment deleted', '%d unapproved comments deleted', $comments, 'wp-optimize'), number_format_i18n($comments));

        $this->logger->info($info_message);
		$this->register_output($info_message);
	}
	
	public function get_info() {

		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->comments."` WHERE comment_approved = '0'";

		if ($this->retention_enabled == 'true') {
			$sql .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';
		
		$comments = $this->wpdb->get_var($sql);

		if(!$comments == NULL || !$comments == 0){
			$message = sprintf(_n('%d unapproved comment found', '%d unapproved comments found', $comments, 'wp-optimize'), number_format_i18n($comments)).' | <a href="edit-comments.php?comment_status=moderated">'.' '.__('Review', 'wp-optimize').'</a>';;
		} else {
			$message = __('No unapproved comments found', 'wp-optimize');
		}
		
		$this->register_output($message);
	}

	public function settings_label() {
		if ($this->retention_enabled == 'true' ) {
			return sprintf(__('Remove unapproved comments which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Remove unapproved comments', 'wp-optimize');
		}
	}

	public function get_auto_option_description() {
		return __('Remove unapproved comments', 'wp-optimize');
	}
}
