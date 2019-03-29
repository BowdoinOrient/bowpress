<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_postmeta extends WP_Optimization {

	public $ui_sort_order = 8000;

	public $available_for_auto = false;

	public $available_for_saving = true;

	public $auto_default = false;

	/**
	 * Prepare data for preview widget.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function preview($params) {
		// get data requested for preview.
		$sql = $this->wpdb->prepare(
			"SELECT pm.* FROM `" . $this->wpdb->postmeta . "` pm".
			" LEFT JOIN `" . $this->wpdb->posts . "` wp ON wp.ID = pm.post_id".
			" WHERE wp.ID IS NULL".
			" ORDER BY pm.meta_id LIMIT %d, %d;",
			array(
				$params['offset'],
				$params['limit'],
			)
		);

		$posts = $this->wpdb->get_results($sql, ARRAY_A);

		// get total count post meta for optimization.
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->postmeta . "` pm LEFT JOIN `" . $this->wpdb->posts . "` wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL;";

		$total = $this->wpdb->get_var($sql);

		return array(
			'id_key' => 'meta_id',
			'columns' => array(
				'meta_id' => __('ID', 'wp-optimize'),
				'post_id' => __('Post ID', 'wp-optimize'),
				'meta_key' => __('Meta Key', 'wp-optimize'),
				'meta_value' => __('Meta Value', 'wp-optimize'),
			),
			'offset' => $params['offset'],
			'limit' => $params['limit'],
			'total' => $total,
			'data' => $this->htmlentities_array($posts, array('ID')),
			'message' => $total > 0 ? '' : __('No orphaned post meta data in your database', 'wp-optimize'),
		);
	}

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s orphaned post meta data deleted', '%s orphaned post meta data deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

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
		$clean = "DELETE pm FROM `" . $this->wpdb->postmeta . "` pm LEFT JOIN `" . $this->wpdb->posts . "` wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL";

		// if posted ids in params, then remove only selected items. used by preview widget.
		if (isset($this->data['ids'])) {
			$clean .= ' AND pm.meta_id in ('.join(',', $this->data['ids']).')';
		}

		$clean .= ";";

		$postmeta = $this->query($clean);
		$this->processed_count += $postmeta;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count) {
			$message = sprintf(_n('%s orphaned post meta data in your database', '%s orphaned post meta data in your database', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No orphaned post meta data in your database', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		// add preview link to message.
		if ($this->found_count > 0) {
			$message = $this->get_preview_link($message);
		}

		$this->register_output($message);
	}

	/**
	 * Get count of unoptimized items.
	 */
	public function get_info() {
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->postmeta . "` pm LEFT JOIN `" . $this->wpdb->posts . "` wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL;";
		$postmeta = $this->wpdb->get_var($sql);

		$this->found_count += $postmeta;
	}

	public function settings_label() {
		return __('Clean post meta data', 'wp-optimize');
	}

	/**
	 * N.B. This is not currently used; it was commented out in 1.9.1
	 *
	 * @return string Returns the description once auto remove option has ran
	 */
	public function get_auto_option_description() {
		return __('Remove orphaned post meta', 'wp-optimize');
	}
}
