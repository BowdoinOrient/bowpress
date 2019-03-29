<?php
/**
 *  A sample implementation using the Resmush.it API and our tasks library
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_1_0')) require_once(WPO_PLUGIN_MAIN_PATH . 'vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task.php');

if (!class_exists('Smush_Task')) :

abstract class Updraft_Smush_Task extends Updraft_Task_1_0 {

	/**
	 * A flag indicating if the operation was succesful
	 *
	 * @var bool
	 */
	protected $success = false;

	/**
	 * A text descriptor describing the stage of the task
	 *
	 * @var string
	 */
	protected $stage;

	/**
	 * Initialise the task
	 *
	 * @param Array $options - options to use
	 */
	public function initialise($options = array()) {
		parent::initialise($options);
		$this->set_current_stage('initialised');
		do_action('ud_task_initialised', $this);
	}

	/**
	 * Runs the task
	 *
	 * @return bool - true if complete, false otherwise
	 */
	public function run() {
		
		do_action('ud_task_started', $this);

		$attachment_id	= $this->get_option('attachment_id');
		$file_path = get_attached_file($attachment_id);
		
		if (!$this->validate_file($file_path)) return false;

		$this->update_option('original_filesize', filesize($file_path));
		$this->log($this->get_description());


		$post_data = $this->prepare_post_request($file_path);
		$api_endpoint = $this->get_option('api_endpoint');

		if (false === filter_var($api_endpoint, FILTER_VALIDATE_URL)) {
			$this->fail("invalid_api_url", "The API endpoint supplied {$api_endpoint} is invalid");
		}

		$response = $this->post_to_remote_server($api_endpoint, $post_data);
		$optimised_image = $this->process_server_response($response);

		if ($optimised_image) {
			$this->save_optimised_image($file_path, $optimised_image);
		}

		return $this->success;
	}

	/**
	 * Posts the supplied data to the API url and returns a response
	 *
	 * @param String $api_endpoint - the url to post the form to
	 * @param String $post_data    - the post data as specified by the server
	 * @return mixed - the response
	 */
	public function post_to_remote_server($api_endpoint, $post_data) {

		$this->set_current_stage('connecting');
		$response = wp_remote_post($api_endpoint, $post_data);
		
		if (is_wp_error($response)) {
			$this->fail($response->get_error_code(), $response->get_error_message());
			return false;
		}

		return $response;
	}

	/**
	 * Processes the response recieved from the remote server
	 *
	 * @param mixed $response - the response object
	 * @return mixed - the response
	 */
	public function process_server_response($response) {
		$this->set_current_stage('processing_response');
		return $response;
	}

	/**
	 * Checks if a file is valid and capable of being smushed
	 *
	 * @param String $file_path - the path of the original image
	 * @return bool - true on success, false otherwise
	 */
	public function validate_file($file_path) {

		$allowed_file_types = $this->get_option('allowed_file_types');

		if (!file_exists($file_path)) {
			$this->fail("invalid_file_path", "The linked attachment ID does not have a valid file path");
			return false;
		}

		if (filesize($file_path) > $this->get_option('max_filesize')) {
			$this->fail("exceeded_max_filesize", "$file_path - cannot be optimized, file size is above service provider limit");
			return false;
		}

		if (!in_array(pathinfo($file_path, PATHINFO_EXTENSION), $allowed_file_types)) {
			$this->fail("invalid_file_type", "$file_path - cannot be optimized, it has an invalid file type");
			return false;
		}

		return true;
	}

	/**
	 * Creates a backup of the original image
	 *
	 * @param String $file_path - the path of the original image
	 * @return bool - true on success, false otherwise
	 */
	public function backup_original_image($file_path) {
				
		$this->set_current_stage('backup_original');

		$file = pathinfo($file_path);
		$back_up = $file['dirname'].'/'.basename($file['filename'].$this->get_option('backup_prefix').$file['extension']);
		
		update_post_meta($this->get_option('attachment_id'), 'original-file', $back_up);
		$this->log("Backing up the original image - {$back_up}");

		return copy($file_path, $back_up);
	}

	/**
	 * Creates a backup of the original image
	 *
	 * @param String $file_path 	  - the path of the original image
	 * @param Mixes  $optimised_image - the contents of the image
	 * @return bool - true on success, false otherwise
	 */
	public function save_optimised_image($file_path, $optimised_image) {
		
		$this->set_current_stage('saving_image');

		if ($this->get_option('keep_original'))
			$this->backup_original_image($file_path);

		if (false !== file_put_contents($file_path, $optimised_image)) {
			$this->success = true;
		} else {
			$this->success = false;
		}

		return $this->success;
	}

	/**
	 * Fires if the task succeds, any clean up code and logging goes here
	 */
	public function complete() {

		$attachment_id	= $this->get_option('attachment_id');
		$file_path = get_attached_file($attachment_id);
		$original_size = $this->get_option('original_filesize');
		$this->set_current_stage('completed');

		clearstatcache(true, $file_path); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctionParameters.clearstatcache_clear_realpath_cacheFound,PHPCompatibility.FunctionUse.NewFunctionParameters.clearstatcache_filenameFound
		$saved = round((($original_size - filesize($file_path)) / $original_size * 100), 2);
		$info = sprintf(__("The file was compressed from %s to %s saving %s percent using WP-Optimize", 'wp-optimize'), $this->format_filesize($original_size), $this->format_filesize(filesize($file_path)), $saved);

		$stats = array(
			'smushed-with'  	=> $this->label,
			'original-size' 	=> $original_size,
			'smushed-size'		=> filesize($file_path),
			'savings-percent' 	=> $saved,
		);

		update_post_meta($attachment_id, 'smush-complete', true);
		update_post_meta($attachment_id, 'smush-info', $info);
		update_post_meta($attachment_id, 'smush-stats', $stats);

		$this->log("Successfully optimized the image - {$file_path}." . $info);

		return parent::complete();
	}

	/**
	 * Fires if the task fails, any clean up code and logging goes here
	 *
	 * @param String $error_code    - A code for the failure
	 * @param String $error_message - A description for the failure
	 */
	public function fail($error_code = "Unknown", $error_message = "Unknown") {

		$attachment_id = $this->get_option('attachment_id');

		$info = sprintf(__("Failed with error code %s - %s", 'wp-optimize'), $error_code, $error_message);

		update_post_meta($attachment_id, 'smush-info', $info);
		update_post_meta($attachment_id, 'smush-complete', false);

		do_action('ud_smush_task_failed', $this, $error_code, $error_message);

		return parent::fail($error_code, $error_message);
	}
	
	/**
	 * Get all the supported task stages.
	 *
	 * @return array - list of task stages.
	 */
	public function get_allowed_stages() {
		
		$stages = array(
			'initialised' => __('Initialised', 'wp-optimize'),
			'connecting'   => __('Connecting to API server', 'wp-optimize'),
			'processing_response' => __('Processing response', 'wp-optimize'),
			'backup_original' => __('Backing up original image', 'wp-optimize'),
			'saving_image' => __('Saving optimized image', 'wp-optimize'),
			'completed' => __('Successful', 'wp-optimize'),
		);

		return apply_filters('allowed_task_stages', $stages);
	}

	/**
	 * Get features available with this service
	 *
	 * @return Array - an array of features
	 */
	public static function get_features() {
		return array(
			'max_filesize' => self::MAX_FILESIZE,
			'lossy_compression' => true,
			'preserve_exif' => true,
		);
	}

	/**
	 * Retrieve default options for this task.
	 * This method should normally be over-ridden by the child.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {

		return array(
			'allowed_file_types' => array('gif', 'png', 'jpg', 'tif', 'jpeg'),
			'request_timeout' => 15,
			'image_quality' => 90,
			'backup_prefix' => '-updraft-pre-smush-original.'
		);
	}

	/**
	 * Sets the task stage.
	 *
	 * @param String $stage - the current stage of the task
	 * @return bool - the result of the  update
	 */
	public function set_current_stage($stage) {
		
		if (array_key_exists($stage, self::get_allowed_stages())) {
			$this->stage = $stage;
			return $this->update_option('current_stage', $this->stage);
		}
		
		return false;
	}

	/**
	 * Gets the task stage
	 *
	 * @return String $stage - the current stage of the task
	 */
	public function get_current_stage() {
		if (isset($this->stage))
			return $this->stage;
		else return $this->get_option('current_stage');
	}


	/**
	 * Helper function to format bytes to a human readable value
	 *
	 * @param int $bytes - the filesize in bytes
	 */
	public function format_filesize($bytes) {
		
		if (1073741824 <= $bytes) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif (1048576 <= $bytes) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif (1024 <= $bytes) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif (1 < $bytes) {
			$bytes = $bytes . ' bytes';
		} elseif (1 == $bytes) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}
}
endif;
