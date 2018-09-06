<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_autodraft extends WP_Optimization {

	public $available_for_auto = true;
	
	public $auto_default = true;

	public $setting_default = true;

	public $available_for_saving = true;

	public $ui_sort_order = 3000;

	protected $setting_id = 'drafts';

	protected $auto_id = 'drafts';

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$info_message = sprintf(_n('%s auto draft deleted', '%s auto drafts deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$info_message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($info_message);
		$this->register_output($info_message);

	}

	/**
	 * Do optimization.
	 */
	public function optimize() {
		$clean = "DELETE FROM `" . $this->wpdb->posts . "` WHERE post_status = 'auto-draft'";

		if ('true' == $this->retention_enabled) {
			$clean .= ' AND post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		$this->processed_count += $this->query($clean);
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {

		if (0 != $this->found_count && null != $this->found_count) {
			$message = sprintf(_n('%s auto draft post in your database', '%s auto draft posts in your database', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No auto draft posts found', 'wp-optimize');
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
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->posts . "` WHERE post_status = 'auto-draft'";

		if ('true' == $this->retention_enabled) {
			$sql .= ' and post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$this->found_count += $this->wpdb->get_var($sql);
	}

	/**
	 * Return settings label
	 *
	 * @return string|void
	 */
	public function settings_label() {

		if ('true' == $this->retention_enabled) {
			return sprintf(__('Clean auto draft posts which are older than %s weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Clean all auto-draft posts', 'wp-optimize');
		}

	}

	/**
	 * Return description
	 *
	 * @return string|void
	 */
	public function get_auto_option_description() {
		return __('Remove auto-draft posts', 'wp-optimize');
	}
}
