<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_spam extends WP_Optimization {

	public $available_for_auto = true;
	public $auto_default = true;
	public $setting_default = true;
	public $available_for_saving = true;
	public $ui_sort_order = 3000;

	protected $dom_id = 'clean-comments';
	protected $setting_id = 'spams';
	protected $auto_id = 'spams';

	public function optimize() {

        $clean = "DELETE FROM `".$this->wpdb->comments."` WHERE comment_approved = 'spam'";
				
		if ($this->retention_enabled == 'true') {
			$clean .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		$comments = $this->query($clean);

        $info_message = sprintf(_n('%d spam comment deleted', '%d spam comments deleted', $comments, 'wp-optimize'), number_format_i18n($comments));

        $this->logger->info($info_message);
		$this->register_output($info_message);

		// Possible enhancement: query trashed comments and cleanup metadata
		$clean = "DELETE FROM `".$this->wpdb->comments."` WHERE comment_approved = 'trash'";
				
		if ($this->retention_enabled == 'true') {
			$clean .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$clean .= ';';
		$commentstrash = $this->query($clean);

        $info_message = sprintf(_n('%d comment removed from Trash', '%d comments removed from Trash', $commentstrash, 'wp-optimize'), number_format_i18n($commentstrash));

        $this->logger->info($info_message);
		$this->register_output($info_message);
	}
	
	public function get_info() {
		
		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->comments."` WHERE comment_approved = 'spam'";
		if ($this->retention_enabled == 'true') {
			$sql .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$comments = $this->wpdb->get_var($sql);

		if (null != $comments && 0 != $comments) {
			$message = sprintf(_n('%d spam comment found', '%d spam comments found', $comments, 'wp-optimize'), number_format_i18n($comments)).' | <a id="wp-optimize-edit-comments-spam" href="'.admin_url('edit-comments.php?comment_status=spam').'">'.' '.__('Review', 'wp-optimize').'</a>';
		} else {
			$message = __('No spam comments found', 'wp-optimize');
		}
		
		$this->register_output($message);
		
		$sql2 = "SELECT COUNT(*) FROM `".$this->wpdb->comments."` WHERE comment_approved = 'trash'";
		if ($this->retention_enabled == 'true') {
			$sql2 .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql2 .= ';';

		$comments = $this->wpdb->get_var($sql2);

		if (null != $comments && 0 != $comments) {
			$message2 = sprintf(_n('%d trashed comment found', '%d trashed comments found', $comments, 'wp-optimize'), number_format_i18n($comments)).' | <a id="wp-optimize-edit-comments-trash" href="'.admin_url('edit-comments.php?comment_status=trash').'">'.' '.__('Review', 'wp-optimize').'</a>';
		} else {
			$message2 = __('No trashed comments found', 'wp-optimize');
		}
		
		$this->register_output($message2);
		
	}
	
	public function settings_label() {
	
		if ($this->retention_enabled == 'true') {
			return sprintf(__('Remove spam and trashed comments which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Remove spam and trashed comments', 'wp-optimize');
		}
	}

	public function get_auto_option_description() {
		return __('Remove spam and trashed comments', 'wp-optimize');
	}
}
