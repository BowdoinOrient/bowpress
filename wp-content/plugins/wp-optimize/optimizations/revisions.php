<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_revisions extends WP_Optimization {

	public $ui_sort_order = 1000;
	
	public $available_for_auto = true;
	
	public $auto_default = true;

	public $setting_default = true;

	public $available_for_saving = true;

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s post revision deleted', '%s post revisions deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($message);
		$this->register_output($message);
	}


	/**
	 * Do optimization.
	 */
	public function optimize() {
		$clean = "DELETE FROM `" . $this->wpdb->posts . "` WHERE post_type = 'revision'";

		if ('true' == $this->retention_enabled) {
			$clean .= '
				AND post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$clean .= ';';

		$revisions = $this->query($clean);
		$this->processed_count += $revisions;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count > 0) {
			$message = sprintf(_n('%s post revision in your database', '%s post revisions in your database', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No post revisions found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);
	}
	
	public function get_info() {
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->posts . "` WHERE post_type = 'revision'";

		if ('true' == $this->retention_enabled) {
			$sql .= ' and post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$revisions = $this->wpdb->get_var($sql);

		$this->found_count += $revisions;
	}
	
	public function settings_label() {
	
		if ('true' == $this->retention_enabled) {
			return sprintf(__('Clean post revisions which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Clean all post revisions', 'wp-optimize');
		}
	}

	public function get_auto_option_description() {
		return __('Remove auto revisions', 'wp-optimize');
	}
}
