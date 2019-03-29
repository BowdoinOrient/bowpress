<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_pingbacks extends WP_Optimization {

	public $ui_sort_order = 6000;

	public $available_for_saving = true;

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
			"SELECT comment_ID, comment_author, SUBSTR(comment_content, 1, 128) AS comment_content FROM `" . $this->wpdb->comments . "`".
			" WHERE comment_type = 'pingback'".
			" ORDER BY `comment_ID` LIMIT %d, %d;",
			array(
				$params['offset'],
				$params['limit'],
			)
		);

		$posts = $this->wpdb->get_results($sql, ARRAY_A);

		// fix empty revision titles.
		if (!empty($posts)) {
			foreach ($posts as $key => $post) {
				$posts[$key]['post_title'] = array(
					'text' => '' == $post['post_title'] ? '('.__('no title', 'wp-optimize').')' : $post['post_title'],
					'url' => get_edit_post_link($post['ID']),
				);
			}
		}

		// get total count comments for optimization.
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_type = 'pingback';";

		$total = $this->wpdb->get_var($sql);

		return array(
			'id_key' => 'comment_ID',
			'columns' => array(
				'comment_ID' => __('ID', 'wp-optimize'),
				'comment_author' => __('Author', 'wp-optimize'),
				'comment_content' => __('Comment', 'wp-optimize'),
			),
			'offset' => $params['offset'],
			'limit' => $params['limit'],
			'total' => $total,
			'data' => $this->htmlentities_array($posts, array('comment_ID')),
			'message' => $total > 0 ? '' : __('No pingbacks found', 'wp-optimize'),
		);
	}

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
		$clean = "DELETE FROM `" . $this->wpdb->comments . "` WHERE comment_type = 'pingback'";

		// if posted ids in params, then remove only selected items. used by preview widget.
		if (isset($this->data['ids'])) {
			$clean .= ' AND comment_ID in ('.join(',', $this->data['ids']).')';
		}

		$clean .= ";";

		$comments = $this->query($clean);
		$this->processed_count += $comments;

		// clean orphaned comment meta
		$clean = "DELETE cm FROM `" . $this->wpdb->commentmeta . "` cm LEFT JOIN `" . $this->wpdb->comments . "` c ON cm.comment_id = c.comment_ID WHERE c.comment_ID IS NULL";
		$this->query($clean);
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
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_type='pingback';";

		$comments = $this->wpdb->get_var($sql);
		$this->found_count += $comments;
	}
	
	public function settings_label() {
		return __('Remove pingbacks', 'wp-optimize');
	}
}
