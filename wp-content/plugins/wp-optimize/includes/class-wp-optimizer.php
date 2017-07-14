<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

/*
	This class invokes optimiazations. The optimizations themselves live in the 'optimizations' sub-directory of the plugin.
	
	The proper way to obtain access to the instance is via WP_Optimize()->get_optimizer();
*/

class WP_Optimizer {
	
	public function get_retain_info() {
	
		$options = WP_Optimize()->get_options();
	
	    $retain_enabled = $options->get_option('retention-enabled', 'false');
	    $retain_period = $retain_enabled ? $options->get_option('retention-period', '2') : null;

		return array($retain_enabled, $retain_period);
	}
	
	public function get_optimizations_list() {
	
		$optimizations = array();
		
		$optimizations_dir = WPO_PLUGIN_MAIN_PATH.'/optimizations';
		
		if ($dh = opendir($optimizations_dir)) {
			while (($file = readdir($dh)) !== false) {
				if ('.' == $file || '..' == $file || '.php' != substr($file, -4, 4) || !is_file($optimizations_dir.'/'.$file) || 'inactive-' == substr($file, 0, 9)) continue;
				$optimizations[] = substr($file, 0, strlen($file) - 4);
			}
			closedir($dh);
		}
		
		return apply_filters('wp_optimize_get_optimizations_list', $optimizations);

	}
	
	// Currently, there is only one sort rule (so, the parameter's value is ignored)
	// $optimizations should be an array of optimizations (i.e. WP_Optimization instances)
	public function sort_optimizations($optimizations, $sort_on = 'ui_sort_order', $sort_rule = 'traditional') {
		if ('run_sort_order' == $sort_on) {
			uasort($optimizations, array($this, 'sort_optimizations_run_traditional'));
		} else {
			uasort($optimizations, array($this, 'sort_optimizations_ui_traditional'));
		}
		return $optimizations;
	}
	
	public function sort_optimizations_ui_traditional($a, $b) {
		return $this->sort_optimizations_traditional($a, $b, 'ui_sort_order');
	}
	
	public function sort_optimizations_run_traditional($a, $b) {
		return $this->sort_optimizations_traditional($a, $b, 'run_sort_order');
	}
	
	public function sort_optimizations_traditional($a, $b, $sort_on = 'ui_sort_order') {
	
		if (!is_a($a, 'WP_Optimization')) return (!is_a($b, 'WP_Optimization')) ? 0 : 1;
		if (!is_a($b, 'WP_Optimization')) return -1;
	
		$sort_order_a = empty($a->$sort_on) ? 0 : $a->$sort_on;
		$sort_order_b = empty($b->$sort_on) ? 0 : $b->$sort_on;
	
		if ($sort_order_a == $sort_order_b) return 0;
		
		return ($sort_order_a < $sort_order_b) ? -1 : 1;
	
	}
	
	/**
	 * This method returns an array of available optimisations. 
	 * Each array key is an optimization ID, and the value is an object, 
	 * as returned by get_optimization()
	 * @return [array] array of optimizations
	 */
	public function get_optimizations() {
	
		$optimizations = $this->get_optimizations_list();
	
		$optimization_objects = array();
		
		foreach ($optimizations as $optimization) {
			$optimization_objects[$optimization] = $this->get_optimization($optimization);
		}
	
		return apply_filters('wp_optimize_get_optimizations', $optimization_objects);
	
	}
	
	/**
	 * This method returns an object for a specific optimization. 
	 * @param  string $which_optimization an optimization ID
	 * @param  array $data an array of anny options $data
	 * @return array|WP_Error Will return the optimization, or a WP_Error object if it was not found
	 */
	public function get_optimization($which_optimization, $data = array()) {

		$optimization_class = apply_filters('wp_optimize_optimization_class', 'WP_Optimization_'.$which_optimization);
		
		if (!class_exists('WP_Optimization')) require_once(WPO_PLUGIN_MAIN_PATH.'/includes/class-wp-optimization.php');
	
		if (!class_exists($optimization_class)) {
			$optimization_file = WPO_PLUGIN_MAIN_PATH.'/optimizations/'.$which_optimization.'.php';
			$class_file = apply_filters('wp_optimize_optimization_class_file', $optimization_file);
			if (!preg_match('/^[a-z]+$/', $which_optimization) || !file_exists($class_file)) {
				return new WP_Error('no_such_optimization', __('No such optimization', 'wp-optimize'), $which_optimization);
			}
			
			require_once($class_file);
			
			if (!class_exists($optimization_class)) {
				return new WP_Error('no_such_optimization', __('No such optimization', 'wp-optimize'), $which_optimization);
			}
		}
		
		$optimization = new $optimization_class($data);
		
		return $optimization;
	
	}
	
	/**
	 * The method to call to perform an optimization.
	 * @param  string|object $which_optimization an optimization ID, or a WP_Optimization object
	 * @return [array]       array of results from the optimization
	 */
	public function do_optimization($which_optimization) {

		$optimization = (is_object($which_optimization) && is_a($which_optimization, 'WP_Optimization')) ? $which_optimization : $this->get_optimization($which_optimization);
		
		if (is_wp_error($optimization)) return $optimization;

		$optimization->init();
	
		if (apply_filters('wp_optimize_do_optimization', true, $which_optimization, $optimization)) {

			$optimization->optimize();
		
		}
		
		do_action('wp_optimize_after_optimization', $which_optimization, $optimization);
			
		$results = $optimization->get_results();
			
		return $results;
	}
	
	/**
	 * The method to call to get information about an optimization.
	 * As with do_optimization, it is somewhat modelled after the template interface
	 * @param  string|object $which_optimization an optimization ID, or a WP_Optimization object
	 * @return array       returns the optimization information
	 */
	public function get_optimization_info($which_optimization) {
	
		$optimization = (is_object($which_optimization) && is_a($which_optimization, 'WP_Optimization')) ? $which_optimization : $this->get_optimization($which_optimization);
		
		if (is_wp_error($optimization)) return $optimization;

		$optimization->init();
		
		$optimization->get_info();
		
		return $optimization->get_results();
	}
	
	// $optimization_options: whether to do an optimization depends on what keys are set (legacy - can be changed hopefully)
	// Returns an array of result objects
	public function do_optimizations($optimization_options, $which_option = 'dom') {
	
		$results = array();
		
		if (empty($optimization_options)) return $results;
	
		$optimizations = $this->sort_optimizations($this->get_optimizations(), 'run_sort_order');
		
		$time_limit = (defined('WP_OPTIMIZE_SET_TIME_LIMIT') && WP_OPTIMIZE_SET_TIME_LIMIT>15) ? WP_OPTIMIZE_SET_TIME_LIMIT : 1800;
		
		foreach ($optimizations as $optimization_id => $optimization) {

			$option_id = call_user_func(array($optimization, 'get_'.$which_option.'_id'));
			
			if (isset($optimization_options[$option_id])) {
			
				if ('auto' == $which_option && empty($optimization->available_for_auto)) continue;
			
				// Try to reduce the chances of PHP self-terminating via reaching max_execution_time
				@set_time_limit($time_limit);
				$results[$optimization_id] = $this->do_optimization($optimization);
				
			}
			
		}
		
		return $results;
		
	}
	
	public function get_table_prefix($allow_override = false) {
		global $wpdb;
		if (is_multisite() && !defined('MULTISITE')) {
			# In this case (which should only be possible on installs upgraded from pre WP 3.0 WPMU), $wpdb->get_blog_prefix() cannot be made to return the right thing. $wpdb->base_prefix is not explicitly marked as public, so we prefer to use get_blog_prefix if we can, for future compatibility.
			$prefix = $wpdb->base_prefix;
		} else {
			$prefix = $wpdb->get_blog_prefix(0);
		}
		return ($allow_override) ? apply_filters('wp_optimize_get_table_prefix', $prefix) : $prefix;
	}

	// Do any InnoDB tables exist in the DB? Returns the number of tables found, or false if the result is undefined.
	public function any_inno_db_tables() {
		$tables = $this->get_tables();
		if (!is_array($tables)) return false;
		$how_many = 0;
		foreach ($tables as $table) {
			if ('InnoDB' == $table->Engine) $how_many++;
		}
		return $how_many;
	}
	
	public function get_tables() {
		global $wpdb;
		
		$table_status = $wpdb->get_results("SHOW TABLE STATUS");
		
		// Filter on the site's DB prefix (was not done in releases up to 1.9.1)
		$table_prefix = $this->get_table_prefix();
		
		if (is_array($table_status)) {
			foreach ($table_status as $index => $table) {
				$table_name = $table->Name;
				
				$include_table = (0 === stripos($table_name, $table_prefix));
				
				$include_table = apply_filters('wp_optimize_get_tables_include_table', $include_table, $table_name, $table_prefix);
				
				if (!$include_table) unset($table_status[$index]);
			}
		}
		
		return apply_filters('wp_optimize_get_tables', $table_status);
	}

	/**
	 * This function grabs a list of tables
	 * and information regarding each table and returns
	 * the results to optimizations-table.php and optimizationstable.php
	 * @return [array] an array of data such as table list, innodb info and data free
	 */
	public function get_table_information() {
		//get table information
		$tablesstatus = $this->get_tables();

		//set defaults
		$table_information = array();
		$table_information['total_gain'] = 0;
		$table_information['inno_db_tables'] = 0;
		$table_information['non_inno_db_tables'] = 0;
		$table_information['table_list'] = '';

		//make a list of tables to optimize
		foreach($tablesstatus as $each_table) {
			if ($each_table->Engine != 'InnoDB') {
				$table_information['total_gain'] += $each_table->Data_free;
				$table_information['table_list'] .= $each_table->Name.'|';
				$table_information['non_inno_db_tables']++;
			} else {
				$table_information['inno_db_tables']++;
			}
		}
		return $table_information;
	}
	
	/*
	* function enable_linkbacks()
	*
	* parameters: what sort of linkback to enable or disable: valid values are 'trackbacks' or 'comments', and whether to enable or disable
	*
	* @return void
	*/
	public function enable_linkbacks($type, $enable = true) {
	
		global $wpdb;
		
		$new_status = $enable ? 'open' : 'closed';
		
		switch ($type) {
			case "trackbacks":
				$thissql = "UPDATE `".$wpdb->posts."` SET ping_status='$new_status' WHERE post_status = 'publish' AND post_type = 'post';";
				$trackbacks = $wpdb->query($thissql);
			break;

			case "comments":
				$thissql = "UPDATE `".$wpdb->posts."` SET comment_status='$new_status' WHERE post_status = 'publish' AND post_type = 'post';";
				$comments = $wpdb->query($thissql);
			break;

			default:
			break;
		}

	}
	
	/*
	* function get_current_db_size()
	*
	* parameters: none
	*
	* this function will return total database size and a possible gain of db in KB
	*
	* @return array $total size, $gain
	*/
	public function get_current_db_size() {

		$wp_optimize = WP_Optimize();

		global $wpdb;
		$total_gain = 0;
		$total_size = 0;
		$no = 0;
		$row_usage = 0;
		$data_usage = 0;
		$index_usage = 0;
		$overhead_usage = 0;
		
		$tablesstatus = $this->get_tables();

		foreach ($tablesstatus as  $tablestatus) {
			$row_usage += $tablestatus->Rows;
			$data_usage += $tablestatus->Data_length;
			$index_usage +=  $tablestatus->Index_length;

			if ($tablestatus->Engine != 'InnoDB'){
				$overhead_usage += $tablestatus->Data_free;
				$total_gain += $tablestatus->Data_free;
			}
		}

		$total_size = $data_usage + $index_usage;
// 		$wp_optimize->log('Total Size .... '.$total_size);
// 		$wp_optimize->log('Total Gain .... '.$total_gain);
		return array ($wp_optimize->format_size($total_size), $wp_optimize->format_size($total_gain));
		//$wpdb->flush();
	}
	
	/*
	* function update_total_cleaned($current)
	*
	* parameters: a string value
	*
	* this function will return total saved data in KB
	*
	* @return total size
	*/
	public function update_total_cleaned($current) {

		$options = WP_Optimize()->get_options();
	
		$previously_saved = floatval($options->get_option('total-cleaned', '0'));
		$converted_current = floatval($current);

		$total_now = strval($previously_saved + $converted_current);

		$options->update_option('total-cleaned', $total_now);
		$options->update_option('current-cleaned', $current);

		return $total_now;
	}
	
	
	public function trackback_comment_actions($options) {
	
		$output = array();
	
		if(isset($options['comments'])) {

			if (!$options['comments']) {
				$this->enable_linkbacks('comments', false);
				$output[] = __('Comments have now been disabled on all current and previously published posts.', 'wp-optimize');
			} else {
				$this->enable_linkbacks('comments');
				$output[] = __('Comments have now been enabled on all current and previously published posts.', 'wp-optimize');
			}
		}
		
		if (isset($options['trackbacks'])) {
			if (!$options['trackbacks']) {
				$this->enable_linkbacks('trackbacks', false);
				$output[] = __('Trackbacks have now been disabled on all current and previously published posts.', 'wp-optimize');
				
			} else {
				$this->enable_linkbacks('trackbacks');
				$output[] = __('Trackbacks have now been enabled on all current and previously published posts.', 'wp-optimize');
			}
		}
		
		return array('output' => $output);
	}
	
}
