<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_transient extends WP_Optimization {

	public $available_for_auto = true;
	public $auto_default = false;
	public $ui_sort_order = 5000;

	public function optimize() {

		$clean = "
			DELETE
				a, b
			FROM
				".$this->wpdb->options." a, ".$this->wpdb->options." b
			WHERE
				a.option_name LIKE '%_transient_%' AND
				a.option_name NOT LIKE '%_transient_timeout_%' AND
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
		
		$message = sprintf(_n('%d transient option deleted', '%d transient options deleted', $options_table_transients_deleted, 'wp-optimize'), number_format_i18n($options_table_transients_deleted));

		// Delete transients from multisite, if configured as such
		if (!is_multisite() || !is_main_network()) {
			$final_message = $message;
		} else {

			$clean2 = "
				DELETE
					a, b
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
			
			$final_message = $message . ', '.sprintf(_n('%d site-wide transient option deleted', '%d site-widetransient options deleted', $sitemeta_table_transients_deleted, 'wp-optimize'), number_format_i18n($sitemeta_table_transients_deleted));
		}

        $this->logger->info($final_message);
		$this->register_output($final_message);
	}
	
	public function get_info() {
	
		$options_table_sql = "
			SELECT
				COUNT(*)
			FROM
				".$this->wpdb->options." a, ".$this->wpdb->options." b
			WHERE
				a.option_name LIKE '%_transient_%' AND
				a.option_name NOT LIKE '%_transient_timeout_%' AND
				b.option_name = CONCAT(
					'_transient_timeout_',
					SUBSTRING(
						a.option_name,
						CHAR_LENGTH('_transient_') + 1
					)
				)
			AND b.option_value < UNIX_TIMESTAMP()
		";

		$options_table_transients = $this->wpdb->get_var($options_table_sql);
	
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
			
		$total_transients = (is_numeric($options_table_transients) ? $options_table_transients : 0) + (is_numeric($sitemeta_table_transients) ? $sitemeta_table_transients : 0);
		
		if ($total_transients) {
			$message = sprintf(_n('%d expired transient in your database', '%d expired transient in your database', $total_transients, 'wp-optimize'), number_format_i18n($total_transients));
		} else {
			$message = __('No transient options found', 'wp-optimize');
		}

		$this->register_output($message);
	}

	public function settings_label() {
		return __('Remove expired transient options', 'wp-optimize');
	}

	public function get_auto_option_description() {
		return __('Remove expired transient options', 'wp-optimize');
	}
}
