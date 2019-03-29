<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_repairtables extends WP_Optimization {

	public $available_for_auto = false;

	public $setting_default = true;

	public $changes_table_data = true;

	public $run_multisite = false;

	public $support_preview = false;

	/**
	 * Display or hide optimization in optimizations list.
	 *
	 * @return bool
	 */
	public function display_in_optimizations_list() {
		return false;
	}

	/**
	 * Run optimization.
	 */
	public function optimize() {
		// check if single table name posted or optimize all tables.
		if (isset($this->data['optimization_table']) && '' != $this->data['optimization_table']) {
			$table = $this->optimizer->get_table($this->data['optimization_table']);

			$result = (false === $table) ? false : $this->repair_table($table);

			if ($result) {
				$wp_optimize = WP_Optimize();
				$tablestatus = $wp_optimize->get_db_info()->get_table_status($table->Name, true);

				$is_optimizable = $wp_optimize->get_db_info()->is_table_optimizable($table->Name);

				$tableinfo = array(
					'rows' => number_format_i18n($tablestatus->Rows),
					'data_size' => $wp_optimize->format_size($tablestatus->Data_length),
					'index_size' => $wp_optimize->format_size($tablestatus->Index_length),
					'overhead' => $is_optimizable ? $wp_optimize->format_size($tablestatus->Data_free) : '-',
					'type' => $table->Engine,
					'is_optimizable' => $is_optimizable,
				);

				$this->register_meta('tableinfo', $tableinfo);
			}

			$this->register_meta('success', $result);
		} else {
			$tables = $this->optimizer->get_tables();
			$repaired = $corrupted = 0;

			foreach ($tables as $table) {
				if (false == $table->is_needing_repair) continue;

				if ($this->repair_table($table)) {
					$repaired++;
				} else {
					$corrupted++;
				}
			}

			$this->register_output(sprintf(_n('%s table repaired', '%s tables repaired', $repaired), $repaired));

			if ($corrupted > 0) {
				$this->register_output(sprintf(_n('Repairing %s table was unsuccessful', 'Repairing %s tables were unsuccessful', $corrupted), $corrupted));
			}
		}
	}

	/**
	 * Repair table.
	 *
	 * @param object $table_obj object contains information about database table.
	 *
	 * @return bool
	 */
	private function repair_table($table_obj) {
		global $wpdb;

		$success = false;

		if (false == $table_obj->is_needing_repair) return true;

		$this->logger->info('REPAIR TABLE `'.$table_obj->Name. '`');

		$results = $wpdb->get_results('REPAIR TABLE `'.$table_obj->Name . '`');

		if (!empty($results)) {
			foreach ($results as $row) {
				if ('status' == strtolower($row->Msg_type) && 'ok' == strtolower($row->Msg_text)) {
					$success = true;
				}

				$this->logger->info($row->Msg_text);
			}
		}

		// update info in options table and use it to notify user about corrupted tables.
		$this->get_corrupted_tables_count();

		return $success;
	}

	/**
	 * Returns count of corrupted tables and update corrupted-tables-count value in options used to show
	 * information for user in sidebar about corrupted tables count.
	 */
	public function get_corrupted_tables_count() {
		$tablesinfo = $this->optimizer->get_tables();

		$corrupted_tables = 0;

		if (!empty($tablesinfo)) {
			foreach ($tablesinfo as $tableinfo) {
				if ($tableinfo->is_needing_repair) {
					$corrupted_tables++;
				}
			}
		}

		// save results to options table and use it to notify user about corrupted tables.
		$this->options->update_option('corrupted-tables-count', $corrupted_tables);

		return $corrupted_tables;
	}

	/**
	 * Register info about optimization.
	 */
	public function get_info() {

		$corrupted_tables = $this->get_corrupted_tables_count();

		if (0 == $corrupted_tables) {
			$this->register_output(__('No corrupted tables found', 'wp-optimize'));
		} else {
			$this->register_output(sprintf(_n('%s corrupted table found', '%s corrupted tables found', $corrupted_tables), $corrupted_tables));
		}
	}

	/**
	 * Returns settings label.
	 *
	 * @return string
	 */
	public function settings_label() {
		return __('Repair database tables', 'wp-optimize');
	}
}
