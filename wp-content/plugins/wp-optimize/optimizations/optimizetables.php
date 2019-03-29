<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_optimizetables extends WP_Optimization {

	protected $auto_id = 'optimize';
	
	protected $setting_id = 'optimize';

	protected $dom_id = 'optimize-db';

	public $available_for_saving = true;

	public $available_for_auto = true;

	public $setting_default = true;

	public $changes_table_data = true;

	public $ui_sort_order = 500;

	public $run_sort_order = 100000;

	public $run_multisite = false;

	public $support_preview = false;

	/**
	 * Run optimization.
	 */
	public function optimize() {
		// check if force optimize sent.
		$force = (isset($this->data['optimization_force']) && $this->data['optimization_force']) ? true : false;

		// check if single table name posted or optimize all tables.
		if (isset($this->data['optimization_table']) && '' != $this->data['optimization_table']) {
			$table = $this->optimizer->get_table($this->data['optimization_table']);

			if (false !== $table) $this->optimize_table($table, $force);
		} else {
			$tables = $this->optimizer->get_tables();

			foreach ($tables as $table) {
				$this->optimize_table($table, $force);
			}
		}
	}

	/**
	 * Optimize table and generate log and output information.
	 *
	 * @param object $table_obj table object returned by $this->optimizer->get_tables().
	 * @param bool 	 $force		if true then will optimize
	 */
	private function optimize_table($table_obj, $force = false) {

		// if not forced and table is not optimizable then exit.
		if (false == $force && (false == $table_obj->is_optimizable || false == $table_obj->is_type_supported)) return;

		if ($table_obj->is_type_supported) {
			$this->logger->info('Optimizing: ' . $table_obj->Name);
			$this->query('OPTIMIZE TABLE `' . $table_obj->Name . '`');

			// For InnoDB Data_free doesn't contain free size.
			if ('InnoDB' != $table_obj->Engine) {
				$this->optimizer->update_total_cleaned(strval($table_obj->Data_free));
			}

			$this->register_output(__('Optimizing Table:', 'wp-optimize') . ' ' . $table_obj->Name);
		}
	}

	/**
	 * Return info about optimization.
	 */
	public function get_info() {
		// This gathers information to be displayed onscreen before optimization.
		$tablesstatus = $this->optimizer->get_table_information();

		// Check if database is not optimizable.
		if (false === $tablesstatus['is_optimizable']) {
			if (isset($this->data['optimization_table']) && '' != $this->data['optimization_table']) {
				// This is used for grabbing information before optimizations.
				$this->register_output(__('Total gain:', 'wp-optimize').' '.WP_Optimize()->format_size(($tablesstatus['total_gain'])));
			}

			if ($tablesstatus['inno_db_tables'] > 0) {
				// Output message for how many InnoDB tables will not be optimized.
				$this->register_output(sprintf(__('Tables using the InnoDB engine (%d) will not be optimized.'), $tablesstatus['inno_db_tables']));

				if ($tablesstatus['non_inno_db_tables'] > 0) {
					$this->register_output(sprintf(__('Other tables will be optimized (%s).', 'wp-optimize'), $tablesstatus['non_inno_db_tables']));
				}

				$faq_url = apply_filters('wpo_faq_url', 'https://getwpo.com/faqs/');
				$force_db_option = $this->options->get_option('innodb-force-optimize', 'false');
				$this->register_output('<input id="innodb_force_optimize" name="innodb-force-optimize" type="checkbox" value="true" '.checked($force_db_option, 'true').'><label for="innodb_force_optimize">'.__('Optimize InnoDB tables anyway.', 'wp-optimize').'</label><br><a href="'.$faq_url.'" target="_blank">'.__('Warning: you should read the FAQ on the risks of this operation first.', 'wp-optimize').'</a>');
			}
		} else {
			$this->register_output(sprintf(__('Tables will be optimized (%s).', 'wp-optimize'), $tablesstatus['non_inno_db_tables'] + $tablesstatus['inno_db_tables']));
		}
	}

	public function get_auto_option_description() {
		return __('Optimize database tables', 'wp-optimize');
	}
	
	public function settings_label() {
		return __('Optimize database tables', 'wp-optimize');
	}
}
