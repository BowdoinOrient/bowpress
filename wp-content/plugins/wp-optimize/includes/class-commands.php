<?php

if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed');

/*

All commands that are intended to be available for calling from any sort of control interface (e.g. wp-admin, UpdraftCentral) go in here.

All public methods should either return the data to be returned, or a WP_Error with associated error code, message and error data.

*/

class WP_Optimize_Commands {

	private $optimizer;
	private $options;

	public function __construct() {
		$this->optimizer = WP_Optimize()->get_optimizer();
		$this->options = WP_Optimize()->get_options();
	}

	public function get_version() {
		return WPO_VERSION;
	}

	public function enable_or_disable_feature($data) {
	
		$type = (string)$data['type'];
		$enable = (boolean)$data['enable'];
	
		$options = array($type => $enable);

		return $this->optimizer->trackback_comment_actions($options);
			
	}
	
	public function save_manual_run_optimization_options($sent_options) {
		return $this->options->save_sent_manual_run_optimization_options($sent_options);
	}

	public function get_status_box_contents() {
		return WP_Optimize()->include_template('status-box-contents.php', true, array('optimize_db' => false));
	}
	
	public function get_optimizations_table() {
		return WP_Optimize()->include_template('optimizations-table.php', true);
	}
	
	public function save_settings($data) {
		
		parse_str(stripslashes($data), $posted_settings);

		// We now have $posted_settings as an array
		
		return array(
			'save_results' => $this->options->save_settings($posted_settings),
			'status_box_contents' => $this->get_status_box_contents(),
			'optimizations_table' => $this->get_optimizations_table(),
		);
	}

	/**
	 * This sends the selected tick value over to the save function 
	 * within class-wp-optimize-options.php
	 * @param  Array 	$data an array of data that includes true or false for click option
	 * @return Array 	returns an message array
	 */
	public function save_auto_backup_option($data) {
		return array('save_auto_backup_option' => $this->options->save_auto_backup_option($data));
	}
	
	/**
	 * Perform the requested optimization
	 *
	 * @param array $params - Should have keys 'optimization_id' and 'data'
	 */
	public function do_optimization($params) {
		
		if (!isset($params['optimization_id'])) {
		
			$results = array(
				'result' => false,
				'messages' => array(),
				'errors' => array(
					__('No optimization was indicated.', 'wp-optimize')
				)
			);
		
		} else {

			$optimization_id = $params['optimization_id'];
			$data = isset($params['data']) ? $params['data'] : array();
			
			$optimization = $this->optimizer->get_optimization($optimization_id, $data);
	
			$result = is_a($optimization, 'WP_Optimization') ? $optimization->do_optimization() : null;
			
			$results = array(
				'result' => $result,
				'messages' => array(),
				'errors' => array(),
				'status_box_contents' => $this->get_status_box_contents()
			);
			
			if (is_wp_error($optimization)) {
				$results['errors'][] = $optimization->get_error_message().' ('.$optimization->get_error_code().')';
			}
			
			if ($optimization->get_changes_table_data()) {
				$table_list = $this->get_table_list();
				$results['table_list'] = $table_list['table_list'];
				$results['total_size'] = $table_list['total_size'];
			}
			
		}
		
		return $results;
	
	}
		
	public function get_table_list() {
		
		list ($total_size, $part2) = $this->optimizer->get_current_db_size();
	
		return array(
			'table_list' => WP_Optimize()->include_template('tables-body.php', true, array('optimize_db' => false)),
			'total_size' => $total_size
		);
	
	}

}
