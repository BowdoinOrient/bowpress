<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_orphandata extends WP_Optimization {

	public $ui_sort_order = 10000;

	public $available_for_saving = true;

	public $support_preview = false;

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s orphaned relationship data deleted', '%s orphaned relationship data deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

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
		$clean = "DELETE FROM `" . $this->wpdb->term_relationships . "` WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM `" . $this->wpdb->posts . "`);";

		$orphandata = $this->query($clean);
		$this->processed_count += $orphandata;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count > 0) {
			$message = sprintf(_n('%s orphaned relationship data in your database', '%s orphaned relationship data in your database', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No orphaned relationship data in your database', 'wp-optimize');
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
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->term_relationships . "` WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM `" . $this->wpdb->posts . "`);";
		$orphandata = $this->wpdb->get_var($sql);

		$this->found_count += $orphandata;
	}
	
	public function settings_label() {
		return __('Clean orphaned relationship data', 'wp-optimize');
	}
}
