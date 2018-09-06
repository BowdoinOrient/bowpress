<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_transient extends WP_Optimization {

	public $available_for_auto = true;

	public $auto_default = false;

	public $ui_sort_order = 5000;

	public $run_multisite = false;

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
				AND b.meta_value < UNIX_TIMESTAMP()
			";

			$sitemeta_table_transients_deleted = $this->query($clean2);

			$clean2_timeouts = "
				DELETE 
					b
				FROM 
					" . $this->wpdb->options . " b
				 WHERE
					b.option_name LIKE '_site_transient_timeout_%' AND
					b.option_value < UNIX_TIMESTAMP()
			";

			$this->query($clean2_timeouts);

			$message2 = sprintf(_n('%s network-wide transient option deleted', '%s network-wide transient options deleted', $sitemeta_table_transients_deleted, 'wp-optimize'), number_format_i18n($sitemeta_table_transients_deleted));

			$this->logger->info($message2);
			$this->register_output($message2);
		}
	}

	/**
	 * Optimize transients options
	 */
	public function optimize() {

		// clean transients rows.
		$clean = "
			DELETE
				a
			FROM
				" . $this->wpdb->options . " a, " . $this->wpdb->options . " b
			WHERE
				a.option_name LIKE '_transient_%' AND
				b.option_name = CONCAT(
					'_transient_timeout_',
					SUBSTRING(
						a.option_name,
						CHAR_LENGTH('_transient_') + 1
					)
				)
			AND b.option_value < UNIX_TIMESTAMP()
		";

		$options_table_transients_deleted = $this->query($clean);
		$this->processed_count += $options_table_transients_deleted;

		// clean transient timeouts rows.
		$clean_timeouts = "
			DELETE 
				b
			FROM 
			 	" . $this->wpdb->options . " b
			 WHERE
			 	b.option_name LIKE '_transient_timeout_%' AND
			 	b.option_value < UNIX_TIMESTAMP()
		";

		$this->query($clean_timeouts);
	}

	/**
	 * Do actions before get_info() function.
	 */
	public function before_get_info() {
		$this->found_count = 0;
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
				AND b.meta_value < UNIX_TIMESTAMP()
			";

			$sitemeta_table_transients = $this->wpdb->get_var($sitemeta_table_sql);
		} else {
			$sitemeta_table_transients = 0;
		}

		if ($this->found_count > 0) {
			$message = sprintf(_n('%s transient option found', '%s transient options found', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No transient options found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);

		if ($this->is_multisite_mode()) {
			if ($sitemeta_table_transients > 0) {
				$message2 = sprintf(_n('%d network-wide transient option found', '%d network-wide transient options found', $sitemeta_table_transients, 'wp-optimize'), number_format_i18n($sitemeta_table_transients));
			} else {
				$message2 = __('No site-wide transient options found', 'wp-optimize');
			}

			$this->register_output($message2);
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
				" . $this->wpdb->options . " a, " . $this->wpdb->options . " b
			WHERE
				a.option_name LIKE '_transient_%' AND
				a.option_name NOT LIKE '_transient_timeout_%' AND
				b.option_name = CONCAT(
					'_transient_timeout_',
					SUBSTRING(
						a.option_name,
						CHAR_LENGTH('_transient_') + 1
					)
				)
			AND b.option_value < UNIX_TIMESTAMP()";

			$options_table_transients = $this->wpdb->get_var($options_table_sql);
			$this->found_count += $options_table_transients;
			$this->restore_current_blog();
		}

	}

	public function settings_label() {
		return __('Remove expired transient options', 'wp-optimize');
	}

	public function get_auto_option_description() {
		return __('Remove expired transient options', 'wp-optimize');
	}
}
