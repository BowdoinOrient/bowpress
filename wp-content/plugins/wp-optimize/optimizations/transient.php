<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_transient extends WP_Optimization {

	public $available_for_auto = true;

	public $available_for_saving = true;

	public $auto_default = false;

	public $ui_sort_order = 5000;

	public $run_multisite = true;

	public $support_preview = true;

	private $found_count_all = 0;

	/**
	 * Prepare data for preview widget.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function preview($params) {

		// get type of data for return single site or multisite.
		$type = isset($params['type']) && 'multisite' == $params['type'] ? 'multisite' : 'single';

		// set remove_all_transients for correctly handling "all" checkbox for preview transients.
		if (isset($params['remove_all_transients'])) {
			$this->data['remove_all_transients'] = $params['remove_all_transients'];
		}

		// get data requested for preview.
		if ('single' == $type) {
			$sql = $this->wpdb->prepare("
				SELECT
					a.option_id, 
					a.option_name,
					SUBSTR(a.option_value, 1, 128) as option_value
				FROM
					" . $this->wpdb->options . " a
				LEFT JOIN
					 " . $this->wpdb->options . " b
				ON 	 
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)
				WHERE
					a.option_name LIKE '_transient_%' AND
					a.option_name NOT LIKE '_transient_timeout_%'
				". ($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "").
				" ORDER BY a.option_id LIMIT %d, %d;",
				array(
					$params['offset'],
					$params['limit'],
				)
			);

			$sql_count = "
				SELECT
					COUNT(*)
				FROM
					" . $this->wpdb->options . " a 
				LEFT JOIN 	
					" . $this->wpdb->options . " b
				ON 
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)	
				WHERE
					a.option_name LIKE '_transient_%'
				". ($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");
		} else {
			$sql = $this->wpdb->prepare("
				SELECT
					a.meta_id,
					a.meta_key,
					SUBSTR(a.meta_value, 1, 128) as meta_value
				FROM
					".$this->wpdb->sitemeta." a
				LEFT JOIN	
					".$this->wpdb->sitemeta." b
				ON
					b.meta_key = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)	
				WHERE
					a.meta_key LIKE '_site_transient_%' AND
					a.meta_key NOT LIKE '_site_transient_timeout_%'
				".($this->remove_only_expired() ? " AND b.meta_value < UNIX_TIMESTAMP()" : "").
				" ORDER BY a.meta_id LIMIT %d, %d;",
				array(
					$params['offset'],
					$params['limit'],
				)
			);

			$sql_count = "
				SELECT
					COUNT(*)
				FROM
					".$this->wpdb->sitemeta." a
				LEFT JOIN ".$this->wpdb->sitemeta." b
				ON 
					b.meta_key = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)
				WHERE
					a.meta_key LIKE '_site_transient_%' AND
					a.meta_key NOT LIKE '_site_transient_timeout_%'
				".($this->remove_only_expired() ? " AND b.meta_value < UNIX_TIMESTAMP()" : "");
		}

		$posts = $this->wpdb->get_results($sql, ARRAY_A);

		$total = $this->wpdb->get_var($sql_count);

		// define columns array depends on source type of request.
		if ('single' == $type) {
			$columns = array(
				'option_id' => __('ID', 'wp-optimize'),
				'option_name' => __('Name', 'wp-optimize'),
				'option_value' => __('Value', 'wp-optimize'),
			);
		} else {
			$columns = array(
				'meta_id' => __('ID', 'wp-optimize'),
				'meta_key' => __('Name', 'wp-optimize'),
				'meta_value' => __('Value', 'wp-optimize'),
			);
		}

		return array(
			'id_key' => ('single' == $type) ? 'option_id' : 'meta_id',
			'columns' => $columns,
			'offset' => $params['offset'],
			'limit' => $params['limit'],
			'total' => $total,
			'data' => $this->htmlentities_array($posts, array('option_id', 'meta_id')),
			'message' => $total > 0 ? '' : __('No transient options found', 'wp-optimize'),
		);
	}

	/**
	 * Do actions before optimize() function.
	 */
	public function before_optimize() {
		$this->processed_count = 0;
	}

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {

		$message = sprintf(_n('%s transient option deleted', '%s transient options deleted', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($message);
		$this->register_output($message);

		// Delete transients from multisite, if configured as such.
		if (is_multisite() && is_main_network()) {
			if (isset($this->data['ids'])) {
				// clean timeouts rows by transient option ids, before deleting transients.
				// this is done for correct counting deleted transient options.
				$clean2_timeouts = "
				DELETE
					b
				FROM
					" . $this->wpdb->sitemeta . " a
				LEFT JOIN " . $this->wpdb->sitemeta . " b
				ON
					b.meta_key = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)	
				WHERE
				a.meta_id IN (".join(',', $this->data['ids']).")";

				// run clean timeouts query.
				$this->query($clean2_timeouts);

				// reset clean timeouts query to avoid future run.
				$clean2_timeouts = '';

				// clean transients rows by id.
				$clean2 = "
				DELETE
					a
				FROM
					" . $this->wpdb->sitemeta . " a
				WHERE
					a.meta_id IN (".join(',', $this->data['ids']).")
				". ($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");
			} else {
				$clean2 = "
				DELETE
					a
				FROM
					".$this->wpdb->sitemeta." a, ".$this->wpdb->sitemeta." b
				WHERE
					a.meta_key LIKE '_site_transient_%' AND
					a.meta_key NOT LIKE '_site_transient_timeout_%' AND
					b.meta_key = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)
				".($this->remove_only_expired() ? " AND b.meta_value < UNIX_TIMESTAMP()" : "");

				$clean2_timeouts = "
				DELETE 
					b
				FROM 
					" . $this->wpdb->options . " b
				 WHERE
					b.option_name LIKE '_site_transient_timeout_%'
				".($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");
			}

			$sitemeta_table_transients_deleted = $this->query($clean2);
			if ('' != $clean2_timeouts) $this->query($clean2_timeouts);

			$message2 = sprintf(_n('%s network-wide transient option deleted', '%s network-wide transient options deleted', $sitemeta_table_transients_deleted, 'wp-optimize'), number_format_i18n($sitemeta_table_transients_deleted));

			$this->logger->info($message2);
			$this->register_output($message2);
		}
	}

	/**
	 * Optimize transients options
	 */
	public function optimize() {

		// if posted ids then build sql queries for deleting selected data.
		if (isset($this->data['ids'])) {
			// clean timeouts rows by transient option ids, before deleting transients.
			// this is done for correct counting deleted transient options.
			$clean_timeouts = "
				DELETE
					b
				FROM
					" . $this->wpdb->options . " a
				LEFT JOIN " . $this->wpdb->options . " b
				ON
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)	
				WHERE
				a.option_id IN (".join(',', $this->data['ids']).")";

			// run clean timeouts query.
			$this->query($clean_timeouts);

			// reset clean timeouts query to avoid future run.
			$clean_timeouts = '';

			// clean transients rows by id.
			$clean = "
				DELETE
					a
				FROM
					" . $this->wpdb->options . " a
				WHERE
					a.option_id IN (".join(',', $this->data['ids']).")
				". ($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");
		} else {
			// clean transients rows.
			$clean = "
				DELETE
					a
				FROM
					" . $this->wpdb->options . " a
				LEFT JOIN " . $this->wpdb->options . " b
				ON
					b.option_name = CONCAT(
						'_transient_timeout_',
						SUBSTRING(
							a.option_name,
							CHAR_LENGTH('_transient_') + 1
						)
					)	
				WHERE
					a.option_name LIKE '_transient_%' AND
					a.option_name NOT LIKE '_transient_timeout_%'
				". ($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");

			// clean transient timeouts rows.
			$clean_timeouts = "
				DELETE 
					b
				FROM 
					" . $this->wpdb->options . " b
				WHERE
					b.option_name LIKE '_transient_timeout_%'
				".($this->remove_only_expired() ? " AND b.option_value < UNIX_TIMESTAMP()" : "");
		}

		// run clean transients query and get count of deleted rows.
		$options_table_transients_deleted = $this->query($clean);
		$this->processed_count += $options_table_transients_deleted;

		if ('' != $clean_timeouts) $this->query($clean_timeouts);
	}

	/**
	 * Do actions before get_info() function.
	 */
	public function before_get_info() {
		$this->found_count = 0;
		$this->found_count_all = 0;
	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {

		if (is_multisite() && is_main_network()) {
			$sitemeta_table_sql = "
				SELECT
					COUNT(*)
				FROM
					".$this->wpdb->sitemeta." a
				LEFT JOIN 	
				 	".$this->wpdb->sitemeta." b
				ON
					b.meta_key = CONCAT(
						'_site_transient_timeout_',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_') + 1
						)
					)				 	
				WHERE
					a.meta_key LIKE '_site_transient_%' AND
					a.meta_key NOT LIKE '_site_transient_timeout_%'";

			$expired_suffix_sql = " AND b.meta_value < UNIX_TIMESTAMP()";

			// get count of expired transients.
			$sitemeta_table_transients = $this->wpdb->get_var($sitemeta_table_sql . $expired_suffix_sql);
			// get count of all transients.
			$sitemeta_table_transients_all = $this->wpdb->get_var($sitemeta_table_sql);
		} else {
			$sitemeta_table_transients = 0;
			$sitemeta_table_transients_all = 0;
		}

		if ($this->found_count_all > 0) {
			$message = sprintf(_n('%1$d of %2$d transient option expired', '%1$d of %2$d transient options expired', $this->found_count_all, 'wp-optimize'), number_format_i18n($this->found_count), number_format_i18n($this->found_count_all));
		} else {
			$message = __('No transient options found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %d site', 'across %d sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		// add preview link to $message.
		if ($this->found_count_all > 0) {
			$message = $this->get_preview_link($message, array('data-type' => 'single'));
		}

		$this->register_output($message);

		if ($this->is_multisite_mode()) {
			if ($sitemeta_table_transients_all > 0) {
				$message2 = sprintf(_n('%1$d of %2$d network-wide transient option found', '%1$d of %2$d network-wide transient options found', $sitemeta_table_transients_all, 'wp-optimize'), number_format_i18n($sitemeta_table_transients), number_format_i18n($sitemeta_table_transients_all));
				$message2 = $this->get_preview_link($message2, array('data-type' => 'multisite'));
			} else {
				$message2 = __('No site-wide transient options found', 'wp-optimize');
			}

			$this->register_output($message2);
		}

		// If any kind of transients exists then
		if ($this->found_count_all > 0 || ($sitemeta_table_transients + $sitemeta_table_transients_all > 0)) {
			$remove_all_transients = $this->options->get_option('remove_all_transients', 'false');
			$this->register_output('<input id="remove_all_transients" name="remove_all_transients" type="checkbox" value="true" '.checked($remove_all_transients, 'true').'><label for="remove_all_transients" style="color: inherit;">'.__('Remove all transient options (not only expired)', 'wp-optimize').'</label>');
		}
	}

	/**
	 * Returns info about possibility to optimize transient options.
	 */
	public function get_info() {

		$blogs = $this->get_optimization_blogs();

		foreach ($blogs as $blog_id) {
			$this->switch_to_blog($blog_id);

			$options_table_sql = "
			SELECT
				COUNT(*)
			FROM
				" . $this->wpdb->options . " a 
			LEFT JOIN 
				" . $this->wpdb->options . " b
			ON	
				b.option_name = CONCAT(
					'_transient_timeout_',
					SUBSTRING(
						a.option_name,
						CHAR_LENGTH('_transient_') + 1
					)
				)
			WHERE
				a.option_name LIKE '_transient_%' AND
				a.option_name NOT LIKE '_transient_timeout_%'
			";

			$expired_suffix_sql = " AND b.option_value < UNIX_TIMESTAMP()";

			// get count of expired transients.
			$options_table_transients = $this->wpdb->get_var($options_table_sql . $expired_suffix_sql);
			$this->found_count += $options_table_transients;

			// get count of all transients.
			$options_table_transients = $this->wpdb->get_var($options_table_sql);
			$this->found_count_all += $options_table_transients;
			$this->restore_current_blog();
		}

	}

	public function settings_label() {
		return __('Remove expired transient options', 'wp-optimize');
	}

	public function get_auto_option_description() {
		return __('Remove expired transient options', 'wp-optimize');
	}

	/**
	 * Check optimization param and return true if we should remove only expired transients.
	 *
	 * @return bool
	 */
	private function remove_only_expired() {
		if (isset($this->data['remove_all_transients']) && 'true' == $this->data['remove_all_transients']) {
			return false;
		}

		return true;
	}
}
