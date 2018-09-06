<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_pingbacks extends WP_Optimization {

	public $ui_sort_order = 6000;

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s pingback deleted', '%s pingbacks deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$message .= ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($message);
		$this->register_output($message);
	}

	/**
	 * Do optimization.
	 */
	public function optimize() {
		$clean = "DELETE FROM `" . $this->wpdb->comments . "` WHERE comment_type = 'pingback';";

		$comments = $this->query($clean);
		$this->processed_count += $comments;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count > 0) {
			$message = sprintf(_n('%s pingback found', '%s pingbacks found', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No pingbacks found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);
	}

	/**
	 * Get count of unoptimized items.
	 */
	public function get_info() {
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_type='pingback';";

		$comments = $this->wpdb->get_var($sql);
		$this->found_count += $comments;
	}
	
	public function settings_label() {
		return __('Remove pingbacks', 'wp-optimize');
	}
}
