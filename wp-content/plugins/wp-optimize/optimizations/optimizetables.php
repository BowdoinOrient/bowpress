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

	public function optimize() {
		$this->optimize_tables(true);
	}

	public function get_info() {
		$this->optimize_tables(false);
	}
	
	private function optimize_tables($optimize) {

		//only process the tables if optimization_table isset
		if (isset($this->data['optimization_table']) && $this->data['optimization_table'] != '') {

			$table_status = $this->wpdb->get_row($this->wpdb->prepare("SHOW TABLE STATUS WHERE name = %s", $this->data['optimization_table']));

			$this->logger->info('Optimizing: '.$table_status->Name);
			$result_query  = $this->query('OPTIMIZE TABLE '.$table_status->Name);
			
			$thedate = gmdate(get_option('date_format') . ' ' . get_option('time_format'), current_time( "timestamp", 0 ));
			$this->options->update_option('last-optimized', $thedate);

			$this->optimizer->update_total_cleaned(strval($table_status->Data_free));

			$this->register_output(__('Optimizing Table:', 'wp-optimize').' '.$table_status->Name);
		} else {
			//This gathers information to be displayed onscreen before optimization
			$tablesstatus = $this->optimizer->get_table_information(); 
			
			//make sure that optimization_table_inno_db is set
			if ($tablesstatus['inno_db_tables'] > 0) {
				if (isset($this->data['optimization_table']) && $this->data['optimization_table'] != '') {
					//This is used for grabbing information before optimizations
					$this->register_output(__('Total gain:', 'wp-optimize').' '.WP_Optimize()->format_size(($tablesstatus['total_gain'])));
				}

				//Output message for how many InnoDB tables will not be optimized
				$this->register_output(sprintf(__('Tables using the InnoDB engine (%d) will not be optimized.  Other tables will be optimized (%d).', 'wp-optimize'), $tablesstatus['inno_db_tables'],$tablesstatus['non_inno_db_tables']));
			} else{
				$this->register_output(sprintf(__('Tables will be optimized (%d).', 'wp-optimize'), $tablesstatus['non_inno_db_tables']));
			}	
		}
	}
	
	public function get_auto_option_description() {
		return __('Optimize database tables', 'wp-optimize');
	}
	
	public function settings_label() {
		return __('Optimize database tables', 'wp-optimize');
	}
}
