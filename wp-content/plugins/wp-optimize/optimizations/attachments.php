<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

/**
 * Class WP_Optimization_attachments
 */
class WP_Optimization_attachments extends WP_Optimization {

	public $ui_sort_order = 4500;

	public $available_for_auto = false;

	public $auto_default = false;

	/**
	 * Display or hide optimization in optimizations list.
	 *
	 * @return bool
	 */
	public function display_in_optimizations_list() {
		return false;
	}

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {

		$message = sprintf(_n('%s orphaned attachment deleted', '%s orphaned attachments deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

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

		$sql = "SELECT p.ID FROM `".$this->wpdb->posts."` p LEFT JOIN `".$this->wpdb->posts."` pp ON pp.ID = p.post_parent WHERE p.post_parent > 0 AND p.post_type = 'attachment' AND pp.ID IS NULL;";

		$attachment_ids = $this->wpdb->get_col($sql);
		$count_ids = count($attachment_ids);

		if ($count_ids > 0) {
			foreach ($attachment_ids as $attachment_id) {
				wp_delete_attachment($attachment_id, true);
			}
		}

		$this->processed_count += $count_ids;

	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {

		if ($this->found_count) {
			$message = sprintf(_n('%s orphaned attachment found', '%s orphaned attachments found', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No orphaned attachments found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' '.sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);

	}
	/**
	 * Estimate count of unoptimized items.
	 */
	public function get_info() {

		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->posts . "` p LEFT JOIN `" . $this->wpdb->posts . "` pp ON pp.ID = p.post_parent WHERE p.post_parent > 0 AND p.post_type = 'attachment' AND pp.ID IS NULL;";
		$postmeta = $this->wpdb->get_var($sql);

		$this->found_count += $postmeta;

	}

	/**
	 * Returns settings label
	 *
	 * @return string|void
	 */
	public function settings_label() {
		return __('Remove orphaned attachments', 'wp-optimize');
	}
	
	/**
	 * Return description
	 * N.B. This is not currently used; it was commented out in 1.9.1
	 *
	 * @return string|void
	 */
	public function get_auto_option_description() {
		return __('Remove orphaned attachments', 'wp-optimize');
	}
}
