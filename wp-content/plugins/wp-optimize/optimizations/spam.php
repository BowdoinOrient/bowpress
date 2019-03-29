<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_spam extends WP_Optimization {

	public $available_for_auto = true;
	
	public $auto_default = true;

	public $setting_default = true;

	public $available_for_saving = true;

	public $ui_sort_order = 3500;

	protected $dom_id = 'clean-comments';

	protected $setting_id = 'spams';

	protected $auto_id = 'spams';

	private $processed_spam_count;

	private $processed_trash_count;

	private $found_spam_count;

	private $found_trash_count;

	/**
	 * Prepare data for preview widget.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function preview($params) {

		// get clicked comment type link.
		$type = isset($params['type']) && 'spam' == $params['type'] ? 'spam' : 'trash';

		$retention_subquery = '';

		if ('true' == $this->retention_enabled) {
			$retention_subquery = ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		// get data requested for preview.
		$sql = $this->wpdb->prepare(
			"SELECT comment_ID, comment_author, SUBSTR(comment_content, 1, 128) AS comment_content FROM".
			" `" . $this->wpdb->comments . "`".
			" WHERE comment_approved = '{$type}'".
			$retention_subquery.
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
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_approved = '{$type}' ".$retention_subquery.";";

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
			'message' => $total > 0 ? '' : __('No spam or trashed comments found', 'wp-optimize'),
		);
	}

	/**
	 * Do actions before optimize() function.
	 */
	public function before_optimize() {
		$this->processed_spam_count = 0;
		$this->processed_trash_count = 0;
	}

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s spam comment deleted', '%s spam comments deleted', $this->processed_spam_count, 'wp-optimize'), number_format_i18n($this->processed_spam_count));
		$message1 = sprintf(_n('%s comment removed from Trash', '%s comments removed from Trash', $this->processed_trash_count, 'wp-optimize'), number_format_i18n($this->processed_trash_count));


		if ($this->is_multisite_mode()) {
			$blogs_count_text = sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));

			$message .= ' '.$blogs_count_text;
			$message1 .= ' '.$blogs_count_text;
		}

		$this->logger->info($message);
		$this->logger->info($message1);

		$this->register_output($message);
		$this->register_output($message1);
	}

	/**
	 * Do optimization.
	 */
	public function optimize() {
		// remove spam comments.

		$this->processed_spam_count += $this->get_count_comments('spam');
		$this->delete_comments_by_type('spam');

		$this->processed_trash_count += $this->get_count_comments('trash');
		$this->delete_comments_by_type('trash');
	}

	/**
	 * Delete comments by $type along with comments meta from database.
	 *
	 * @param string $type comment type.
	 * @return array
	 */
	public function delete_comments_by_type($type) {
		$clean = "DELETE c, cm FROM `" . $this->wpdb->comments . "` c LEFT JOIN `" . $this->wpdb->commentmeta . "` cm ON c.comment_ID = cm.comment_id WHERE c.comment_approved = '{$type}'";

		if ('true' == $this->retention_enabled) {
			$clean .= ' and c.comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		// if posted ids in params, then remove only selected items. used by preview widget.
		if (isset($this->data['ids'])) {
			$clean .= ' AND c.comment_ID in ('.join(',', $this->data['ids']).')';
		}

		$clean .= ';';
		return $this->query($clean);
	}

	/**
	 * Do actions before get_info() function.
	 */
	public function before_get_info() {
		$this->found_spam_count = 0;
		$this->found_trash_count = 0;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {

		if ($this->found_spam_count > 0) {
			$message = sprintf(_n('%s spam comment found', '%s spam comments found', $this->found_spam_count, 'wp-optimize'), number_format_i18n($this->found_spam_count));

			// if current version is not premium and Preview feature not supported then
			// add to message Review link to comments page
			if (!WP_Optimize::is_premium()) {
				$message .= ' | <a id="wp-optimize-edit-comments-spam" href="'.admin_url('edit-comments.php?comment_status=spam').'">'.' '.__('Review', 'wp-optimize').'</a>';
			}

		} else {
			$message = __('No spam comments found', 'wp-optimize');
		}

		if ($this->found_trash_count > 0) {
			$message1 = sprintf(_n('%s trashed comment found', '%s trashed comments found', $this->found_trash_count, 'wp-optimize'), number_format_i18n($this->found_trash_count));

			// if current version is not premium and Preview feature not supported then
			// add to message Review link to comments page
			if (!WP_Optimize::is_premium()) {
				$message1 .= ' | <a id="wp-optimize-edit-comments-trash" href="'.admin_url('edit-comments.php?comment_status=trash').'">'.' '.__('Review', 'wp-optimize').'</a>';
			}
		} else {
			$message1 = __('No trashed comments found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$blogs_count_text = sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));

			$message .= ' '.$blogs_count_text;
			$message1 .= ' '.$blogs_count_text;
		}

		// add preview link to message.
		if ($this->found_spam_count > 0) {
			$message = $this->get_preview_link($message, array('data-type' => 'spam'));
		}

		// add preview link to message.
		if ($this->found_trash_count > 0) {
			$message1 = $this->get_preview_link($message1, array('data-type' => 'trash'));
		}

		$this->register_output($message);
		$this->register_output($message1);
	}

	/**
	 * Count records those can be optimized.
	 */
	public function get_info() {
		$this->found_spam_count += $this->get_count_comments('spam');
		$this->found_trash_count += $this->get_count_comments('trash');
	}

	/**
	 * Returns count comments by $type.
	 *
	 * @param string $type comment type.
	 * @return mixed
	 */
	public function get_count_comments($type) {
		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->comments . "` WHERE comment_approved = '{$type}'";
		if ('true' == $this->retention_enabled) {
			$sql .= ' and comment_date < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		return $this->wpdb->get_var($sql);
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function settings_label() {
	
		if ('true' == $this->retention_enabled) {
			return sprintf(__('Remove spam and trashed comments which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Remove spam and trashed comments', 'wp-optimize');
		}
	}

	public function get_auto_option_description() {
		return __('Remove spam and trashed comments', 'wp-optimize');
	}
}
