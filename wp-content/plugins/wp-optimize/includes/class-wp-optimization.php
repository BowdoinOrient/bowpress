<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

// Parent class for all optimizations

abstract class WP_Optimization {

	// Ideally, these would all be the same. But, historically, some are not; hence, three separate IDs.
	public $id;
	
	protected $setting_id;
	protected $dom_id;
	protected $auto_id;
	
	protected $available_for_auto;
	
	protected $ui_sort_order;
	protected $run_sort_order = 1000;
	// This property indicates whether running this optimization is likely to change the overall table optimization state. We set this to 'true' on optimizations that run SQL OPTIMIZE commands. It is only used for the UI. Strictly, of course, any optimization that deletes something can cause increased fragmentation; so; in that sense, it would be true for every optimization; but since we are just using it to keep the UI reasonably fresh, and since there is a manual "refresh" button, we set it only on some optimisations.
	protected $changes_table_data;
	
	protected $optimizer;
	protected $options;
    protected $logger;
	protected $data;
	
	public $retention_enabled;
	public $retention_period;
	
	// Results. These should be accessed via get_results()
	private $output;
	private $meta;
	private $sql_commands;

	protected $wpdb;

	// This is abstracted so as to provide future possibilities, e.g. logging
	protected function query($sql) {
		$this->sql_commands[] = $sql;
		do_action('wp_optimize_optimization_query', $sql, $this);
		$result = $this->wpdb->query($sql);
		return apply_filters('wp_optimize_optimization_query_result', $result, $sql, $this);
	}
	
	abstract public function get_info();
	
	abstract public function optimize();
	
	abstract public function settings_label();
	
	public function __construct($data) {
		$class_name = get_class($this);
		// Remove the prefixed WP_Optimization_
		$this->id = substr($class_name, 16);
		$this->data = $data;
		$this->optimizer = WP_Optimize()->get_optimizer();
		$this->options = WP_Optimize()->get_options();
		$this->logger = WP_Optimize()->get_logger();
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * This triggers the do_optimization function
	 * within class-wp-optimizer.php to kick off the optimizations.
	 * It also passed the data array from the wpadmin.js.
	 * @return [array] array of results that includes sql_commands, output and meta
	 */
	public function do_optimization() {
		return $this->optimizer->do_optimization($this);
	}
	
	/**
	 * This gathers the optimization information to be displayed
	 * before triggering any optimizations
	 * @return [array] Returns an array of optimization information
	 */
	public function get_optimization_info() {
		return $this->optimizer->get_optimization_info($this);
	}
	
	// This function adds output to the current registered output
	public function register_output($output) {
		$this->output[] = $output;
	}
	
	// This function adds meta-data associated with the result to the registered output
	public function register_meta($key, $value) {
		$this->meta[$key] = $value;
	}
	
	public function init() {
	
		$this->output = array();
		$this->meta = array();
		$this->sql_commands = array();
		
		list ($retention_enabled, $retention_period) = $this->optimizer->get_retain_info();
		
		$this->retention_enabled = $retention_enabled;
		$this->retention_period = $retention_period;
		
	}
	
	// The next three functions reflect the fact that historically, WP-Optimize has not, for all optimizations, used the same ID consistently throughout forms, saved settings, and saved settings for automatic clean-ups. Mostly, it has; but some flexibility is needed for the exceptions.
	public function get_setting_id() {
		return empty($this->setting_id) ? 'user-'.$this->id : 'user-'.$this->setting_id;
	}
	
	public function get_dom_id() {
		return empty($this->dom_id) ? 'clean-'.$this->id : $this->dom_id;
	}
	
	public function get_auto_id() {
		return empty($this->auto_id) ? $this->id : $this->auto_id;
	}
	
	public function get_changes_table_data() {
		return empty($this->changes_table_data) ? false : true;
	}
	
	public function get_run_sort_order() {
		return empty($this->run_sort_order) ? 0 : $this->run_sort_order;
	}
	
	// Only used if $available_for_auto is true, in which case this function should be over-ridden
	public function get_auto_option_description() {
		return 'Error: missing automatic option description ('.$this->id.')';
	}
	
	// What is returned must be at least convertible to an array
	public function get_results() {
	
		// As yet, we have no need for a dedicated object type for our results
		$results = new stdClass;
		
		$results->sql_commands = $this->sql_commands;
		$results->output = $this->output;
		$results->meta = $this->meta;
		
		return apply_filters('wp_optimize_optimization_results', $results, $this->id, $this);
	}
	
	public function get_settings_html() {

		$wpo_user_selection = $this->options->get_main_settings();
		$setting_id = $this->get_setting_id();
		$dom_id = $this->get_dom_id();

		// N.B. Some of the optimizations used to have an onclick call to fCheck(). But that function was commented out, so did nothing.

		$settings_label = $this->settings_label();

		$setting_activated = (empty($wpo_user_selection[$setting_id]) || 'false' == $wpo_user_selection[$setting_id]) ? false : true;
		
		$settings_html = array(
			'dom_id' => $dom_id,
			'activated' => $setting_activated,
			'settings_label' => $settings_label,
			'info' => $this->get_optimization_info()->output,
		);
		
		if (empty($settings_label)) {
			// error_log, as this is a defect
			error_log("Optimization with setting ID ".$setting_id." lacks a settings label (method: settings_label())");
		}
		
		return $settings_html;
	}
	
}
