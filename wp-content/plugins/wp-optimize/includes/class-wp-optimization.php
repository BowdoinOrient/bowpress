<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

/**
 * Parent class for all optimizations.
 */
abstract class WP_Optimization {

	/**
	 * Ideally, these would all be the same. But, historically, some are not; hence, three separate IDs.
	 *
	 * @var $id
	 */
	public $id;
	
	protected $setting_id;

	protected $dom_id;

	protected $auto_id;
	
	protected $available_for_auto;
	
	protected $ui_sort_order;

	protected $run_sort_order = 1000;

	public $run_multisite = true;

	public $support_preview = true; // if true then optimization support preview action for optimization data.

	protected $support_ajax_get_info = false; // set to true if optimization support getting info about optimization asynchronously.

	/**
	 * This property indicates whether running this optimization is likely to change the overall table optimization state. We set this to 'true' on optimizations that run SQL OPTIMIZE commands. It is only used for the UI. Strictly, of course, any optimization that deletes something can cause increased fragmentation; so; in that sense, it would be true for every optimization; but since we are just using it to keep the UI reasonably fresh, and since there is a manual "refresh" button, we set it only on some optimizations.
	 *
	 * @var [$changes_table_data
	 */
	protected $changes_table_data;
	
	protected $optimizer;

	protected $options;

	protected $logger;

	protected $data;

	/**
	 * Blogs ids for optimization.
	 *
	 * @var $blogs_ids
	 */
	public $blogs_ids;

	/**
	 * Count of blogs for optimization.
	 *
	 * @var $blogs_count
	 */
	public $blogs_count;

	/**
	 * Store count of optimized items.
	 *
	 * @var $processed_count
	 */
	public $processed_count;

	/**
	 * Store found items for optimization. Used in get_info() and related functions.
	 *
	 * @var $found_count;
	 */
	public $found_count;
	
	public $retention_enabled;

	public $retention_period;
	
	/**
	 * Results. These should be accessed via get_results()
	 *
	 * @var $output
	 */
	private $output;

	private $meta;

	private $sql_commands;

	protected $wpdb;

	/**
	 * This is abstracted so as to provide future possibilities, e.g. logging.
	 *
	 * @param  string $sql The quesry for SQL to be ran.
	 * @return array       Return array of results
	 */
	protected function query($sql) {
		$this->sql_commands[] = $sql;
		do_action('wp_optimize_optimization_query', $sql, $this);
		$result = $this->wpdb->query($sql);
		return apply_filters('wp_optimize_optimization_query_result', $result, $sql, $this);
	}

	/**
	 * Display or hide optimization in optimizations list.
	 *
	 * @return bool
	 */
	public function display_in_optimizations_list() {
		return true;
	}

	/**
	 * Returns data those should be optimized. Used to display this information in a popup tool
	 * for previewing and removing certain data for optimization.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function preview($params) {
		return array(
			'id_key'	=> 'id', // key used used to identify data.
			'offset'	=> $params['offset'],
			'limit'		=> $params['limit'],
			'total'		=> 0,
			'data'		=> array(), // returned data as associative array where keys are column names and values - database cell values.
		);
	}

	/**
	 * Convert all applicable characters to HTML entities in array. Used to prepare data for output in browser for preview.
	 *
	 * @param  array $array        source array
	 * @param  array $exclude_keys what items shoudn't be encoded.
	 *
	 * @return array
	 */
	public function htmlentities_array($array, $exclude_keys = array()) {
		if (!is_array($array) || empty($array)) return $array;

		foreach ($array as $key => $value) {
			if (in_array($key, $exclude_keys)) continue;

			if (!is_array($value)) {
				$array[$key] = htmlentities($value);
			} else {
				$array[$key] = $this->htmlentities_array($value);
			}
		}

		return $array;
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

	}
	
	abstract public function get_info();

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

	}

	abstract public function optimize();

	abstract public function settings_label();

	/**
	 * WP_Optimization constructor.
	 *
	 * @param array $data initial data for optimization.
	 */
	public function __construct($data = array()) {
		$class_name = get_class($this);
		// Remove the prefixed WP_Optimization_.
		$this->id = substr($class_name, 16);
		$this->data = $data;
		$this->optimizer = WP_Optimize()->get_optimizer();
		$this->options = WP_Optimize()->get_options();
		$this->logger = WP_Optimize()->get_logger();
		$wpdb = $GLOBALS['wpdb'];
		$this->wpdb = $wpdb;

		$this->blogs_ids = $this->get_optimization_blogs();
	}

	/**
	 * This triggers the do_optimization function
	 * within class-wp-optimizer.php to kick off the optimizations.
	 * It also passed the data array from the wpadmin.js.
	 *
	 * @return array array of results that includes sql_commands, output and meta
	 */
	public function do_optimization() {
		return $this->optimizer->do_optimization($this);
	}
	
	/**
	 * This gathers the optimization information to be displayed
	 * before triggering any optimizations
	 *
	 * @return array Returns an array of optimization information
	 */
	public function get_optimization_info() {
		return $this->optimizer->get_optimization_info($this);
	}

	/**
	 * Returns array of blog ids
	 *
	 * @return array
	 */
	public function get_optimization_blogs() {
		$objects = array();

		if ($this->is_multisite_mode()) {
			$all_sites = false;
			$selected_sites = array();

			// support both arrays and single values in data site id parameter.
			if (isset($this->data['site_id']) && !is_array($this->data['site_id'])) {
				$this->data['site_id'] = array($this->data['site_id']);
			}

			$optimization_sites = (isset($this->data['site_id'])) ? $this->data['site_id'] : $this->options->get_wpo_sites_option();

			// check selected sites field.
			if (!empty($optimization_sites)) {
				foreach ($optimization_sites as $site_id) {
					if ('all' == $site_id) {
						$all_sites = true;
					} else {
						$selected_sites[] = $site_id;
					}
				}
			}

			$sites = $this->get_sites();
			if (!empty($sites)) {
				foreach ($sites as $site) {
					if ($all_sites || (in_array($site->blog_id, $selected_sites))) {
						$objects[] = $site->blog_id;
					}
				}
			} else {
				$objects[] = 1;
			}
		} else {
			$objects[] = 1;
		}

		return apply_filters('get_optimization_blogs', $objects);
	}

	/**
	 * Returns true if optimization works in multisite mode
	 *
	 * @return bool
	 */
	public function is_multisite_mode() {
		return WP_Optimize()->is_multisite_mode();
	}

	/**
	 * Returns list of all sites in multisite
	 *
	 * @return array
	 */
	public function get_sites() {
		return WP_Optimize()->get_sites();
	}

	/**
	 * Wrapper for switch_to_blog Wordpress MU function
	 *
	 * @param int $new_blog new blog id.
	 * @return bool|void - if on multisite, then always true (see https://codex.wordpress.org/Function_Reference/switch_to_blog)
	 */
	public function switch_to_blog($new_blog) {
		if (function_exists('switch_to_blog') && $this->is_multisite_mode()) {
			return switch_to_blog($new_blog);
		}
	}

	/**
	 * Wrapper for restore_current_blog Wordpress MU function
	 *
	 * @return bool
	 */
	public function restore_current_blog() {
		if (function_exists('restore_current_blog') && $this->is_multisite_mode()) {
			return restore_current_blog();
		}
	}
	
	/**
	 * This function adds output to the current registered output
	 *
	 * @param array $output Array of various outputs.
	 */
	public function register_output($output) {
		$this->output[] = $output;
	}
	
	/**
	 * This function adds meta-data associated with the result to the registered output
	 *
	 * @param string $key   The key value.
	 * @param string $value The value to be passed.
	 */
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
	

	/**
	 * The next three functions reflect the fact that historically, WP-Optimize has not, for all optimizations, used the same ID consistently throughout forms, saved settings, and saved settings for scheduled clean-ups. Mostly, it has; but some flexibility is needed for the exceptions.
	 */
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
	
	/**
	 * Only used if $available_for_auto is true, in which case this function should be over-ridden
	 *
	 * @return string Error message.
	 */
	public function get_auto_option_description() {
		return 'Error: missing scheduled option description ('.$this->id.')';
	}
	
	/**
	 * What is returned must be at least convertible to an array
	 *
	 * @return array Array of results.
	 */
	public function get_results() {
	
		// As yet, we have no need for a dedicated object type for our results.
		$results = new stdClass;
		
		$results->sql_commands = $this->sql_commands;
		$results->output = $this->output;
		$results->meta = $this->meta;
		
		return apply_filters('wp_optimize_optimization_results', $results, $this->id, $this);
	}

	/**
	 * Generate information about optimization required for show it.
	 *
	 * @param  bool $ajax_get_info if true then information about optimization will not generated, i.e. get_optimization_info() won't call.
	 *
	 * @return array
	 */
	public function get_settings_html($ajax_get_info = false) {

		$wpo_user_selection = $this->options->get_main_settings();
		$setting_id = $this->get_setting_id();
		$dom_id = $this->get_dom_id();

		// N.B. Some of the optimizations used to have an onclick call to fCheck(). But that function was commented out, so did nothing.
		$settings_label = $this->settings_label();

		$setting_activated = ((empty($wpo_user_selection[$setting_id]) || 'false' == $wpo_user_selection[$setting_id]) ? false : true);

		$info = ($ajax_get_info && $this->support_ajax_get_info) ? '...' : $this->get_optimization_info()->output;

		$settings_html = array(
			'dom_id' => $dom_id,
			'activated' => $setting_activated,
			'settings_label' => $settings_label,
			'info' => $info,
			'support_ajax_get_info' => $this->support_ajax_get_info
		);
		
		if (empty($settings_label)) {
			// Error_log, as this is a defect.
			error_log("Optimization with setting ID ".$setting_id." lacks a settings label (method: settings_label())");
		}
		
		return $settings_html;
	}

	/**
	 * Wrap $text as a link for preview action. If preview is not supported then return just $text.
	 *
	 * @param string $text
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public function get_preview_link($text, $attributes = array()) {
		// if preview is not supported then return just $text.
		if (false == $this->support_preview || false == WP_Optimize::is_premium()) return $text;

		$attributes = array_merge(
			array(
				'title' => __('Preview found items', 'wp-optimize'),
				'data-id' => $this->id,
				'data-title' => $this->settings_label(),
			),
			$attributes
		);

		$str_attr = '';

		foreach ($attributes as $key => $value) {
			$str_attr .= ' '.$key.'="'.esc_attr($value).'"';
		}

		$link = '<a href="#" class="wpo-optimization-preview"'.$str_attr.'>'.$text.'</a>';

		return $link;
	}
}
