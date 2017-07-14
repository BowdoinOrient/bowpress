<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

// TODO: Does this need renaming? It is not just auto-drafts, but also trashed posts

class WP_Optimization_autodraft extends WP_Optimization {

	public $available_for_auto = true;
	public $auto_default = true;
	public $setting_default = true;
	public $available_for_saving = true;
	public $ui_sort_order = 3000;

	protected $setting_id = 'drafts';
	protected $auto_id = 'drafts';

	public function optimize() {

		$clean = "DELETE FROM `".$this->wpdb->posts."` WHERE post_status = 'auto-draft'";
				
		if ($this->retention_enabled == 'true') {
			$clean .= ' AND post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		$autodraft = $this->query($clean);

		$this->register_output(sprintf(_n('%d auto draft deleted', '%d auto drafts deleted', $autodraft, 'wp-optimize'), number_format_i18n($autodraft)));

		$clean = "DELETE FROM `".$this->wpdb->posts."` WHERE post_status = 'trash'";

		if ($this->retention_enabled == 'true') {
			$clean .= ' AND post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		$posttrash = $this->query($clean);

        $info_message = sprintf(_n('%d item removed from Trash', '%d items removed from Trash', $posttrash, 'wp-optimize'), number_format_i18n($posttrash));

        $this->logger->info($info_message);
		$this->register_output($info_message);
	}
	
	public function get_info() {

		$sql = "SELECT COUNT(*) FROM `".$this->wpdb->posts."` WHERE post_status = 'auto-draft'";
		if ($this->retention_enabled == 'true') {
			$sql .= ' and post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$autodraft = $this->wpdb->get_var($sql);

		if (0 != $autodraft && null != $autodraft) {
			$message = sprintf(_n('%d auto draft post in your database', '%d auto draft posts in your database', $autodraft, 'wp-optimize'), number_format_i18n($autodraft));
		} else {
			$message =__('No auto draft posts found', 'wp-optimize');
		}
		
		$this->register_output($message);

		$sql2 = "SELECT COUNT(*) FROM `".$this->wpdb->posts."` WHERE post_status = 'trash'";
		if ($this->retention_enabled == 'true') {
			$sql2 .= ' and post_modified < NOW() - INTERVAL ' .  $this->retention_period . ' WEEK';
		}
		$sql2 .= ';';

		$trash = $this->wpdb->get_var($sql2);

		if (0 != $trash && null != $trash) {
			$message2 = sprintf(_n('%d trashed post in your database', '%d trashed posts in your database', $trash, 'wp-optimize'), number_format_i18n($trash));
		} else {
			$message2 =__('No trashed posts found', 'wp-optimize');
		}

		$this->register_output($message2);
	}
	
	public function settings_label() {
	
		if ($this->retention_enabled == 'true') {
			return sprintf(__('Clean auto draft and trashed posts which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Clean all auto-drafts and trashed posts', 'wp-optimize');
		}
	
	}

	public function get_auto_option_description() {
		return __('Remove auto-drafts and trashed posts', 'wp-optimize');
	}

}
