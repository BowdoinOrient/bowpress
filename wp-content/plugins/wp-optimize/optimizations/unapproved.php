<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_unapproved extends WP_Optimization {

	public $available_for_auto = true;

	public $auto_default = false;

	public $setting_default = true;

	public $available_for_saving = true;

	public $ui_sort_order = 4000;

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s unapproved comment deleted', '%s unapproved comments deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($message);
		$this->register_output($message);

		$awaiting_mod = wp_count_comments();
		$awaiting_mod = $awaiting_mod->moderated;

		$this->register_meta('awaiting_mod', $awaiting_mod);
	}

	/**
	 * Unapproved-comments / unapproved
	 * need to check what IDs were previously used before (dom ID, settings ID),
	 * as there was a note about unapproved-comments in the source, which isn't in use now here.
	 */
	public function optimize() {
		$clean = "DELETE c, cm FROM `" . $this->wpdb->comments . "` c LEFT JOIN `" . $this->wpdb->commentmeta . "` cm ON c.comment_ID = cm.comment_id WHERE comment_approved = '0' ";

		if ('true' == $this->retention_enabled) {
			$clean .= ' and c.comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		$comments = $this->query($clean);
		$this->processed_count += $comments;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count) {
			$message = sprintf(_n('%s unapproved comment found', '%s unapproved comments found', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No unapproved comments found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);
	}

	/**
	 * Get count of unoptimized items.
	 */
	public function get_info() {
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_approved = '0'";

		if ('true' == $this->retention_enabled) {
			$sql .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$comments = $this->wpdb->get_var($sql);
		$this->found_count += $comments;
	}

	public function settings_label() {
		if ('true' == $this->retention_enabled) {
			return sprintf(__('Remove unapproved comments which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Remove unapproved comments', 'wp-optimize');
		}
	}

	public function get_auto_option_description() {
		return __('Remove unapproved comments', 'wp-optimize');
	}
}
