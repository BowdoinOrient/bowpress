<?php
/**
 *  A Smush Task manager class
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_Manager_Commands_1_0')) require_once(WPO_PLUGIN_MAIN_PATH . 'vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task-manager-commands.php');

if (!class_exists('Updraft_Smush_Manager_Commands')) :

class Updraft_Smush_Manager_Commands extends Updraft_Task_Manager_Commands_1_0 {

	/**
	 * The commands constructor
	 *
	 * @param mixed $task_manager - A task manager instance
	 */
	public function __construct($task_manager) {
		parent::__construct($task_manager);
	}

	/**
	 * Returns a list of commands available for smush related operations
	 */
	public static function get_allowed_ajax_commands() {

		$commands = apply_filters('updraft_task_manager_allowed_ajax_commands', array());

		$smush_commands = array(
			'compress_single_image',
			'restore_single_image',
			'process_bulk_smush',
			'update_smush_options',
			'get_ui_update',
			'process_pending_images',
			'clear_pending_images',
			'clear_smush_stats',
			'check_server_status',
			'get_smush_logs',
		);

		return array_merge($commands, $smush_commands);
	}

	/**
	 * Process the compression of a single image
	 *
	 * @param mixed $data - sent in via AJAX
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function compress_single_image($data) {

		$options = isset($data['smush_options']) ? $data['smush_options'] : false;
		$image = isset($data['selected_image']) ? $data['selected_image'] : false;
		$server = filter_var($options['compression_server'], FILTER_SANITIZE_STRING);

		$lossy = filter_var($options['lossy_compression'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$backup = filter_var($options['back_up_original'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$exif = filter_var($options['preserve_exif'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$quality = filter_var($options['image_quality'], FILTER_SANITIZE_STRING);

		$options = array(
			'attachment_id' 	=> $image,
			'image_quality' 	=> $quality,
			'keep_original'		=> $backup,
			'lossy_compression' => $lossy,
		);

		if (filesize(get_attached_file($image)) > 5242880) {
			$options['request_timeout'] = 180;
		}

		$success = $this->task_manager->compress_single_image($image, $options, $server);

		if (!$success) {
			return new WP_Error('compress_failed', get_post_meta($image, 'smush-info', true));
		}

		$response['status'] = true;
		$response['operation'] = 'compress';
		$response['options'] = $options;
		$response['server'] = $server;
		$response['success'] = $success;
		$response['restore_possible'] = $backup;
		$response['summary'] = get_post_meta($image, 'smush-info', true);

		return $response;
	}

	/**
	 * Restores a single image, if backup is available
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return WP_Error|array - information about the operation or a WP_Error object on failure
	 */
	public function restore_single_image($data) {

		$image = isset($data['selected_image']) ? $data['selected_image'] : false;

		$success = $this->task_manager->restore_single_image($image);

		if (is_wp_error($success)) {
			return $success;
		}

		$response['status'] = true;
		$response['operation'] = 'restore';
		$response['image']	 = $image;
		$response['success'] = $success;
		$response['summary'] = __('Image restored successfully', 'wp-optimize');
		
		return $response;
	}

	/**
	 * Process the compression of multiple images
	 *
	 * @param mixed $data - Sent in via AJAX
	 */
	public function process_bulk_smush($data = array()) {
		$images = isset($data['selected_images']) ? $data['selected_images'] : array();
		$this->get_ui_update();
		$this->task_manager->process_bulk_smush($images);
	}

	/**
	 * Returns useful information for the UI and closes the connection
	 *
	 * @return mixed $data - Information for the UI
	 */
	public function get_ui_update() {
		
		$ui_update['status'] = true;
		$ui_update['pending_tasks'] = count($this->task_manager->get_pending_tasks());
		$ui_update['unsmushed_images'] = $this->task_manager->get_uncompressed_images();
		$ui_update['completed_task_count'] = $this->task_manager->options->get_option('completed_task_count', 0);
		$ui_update['bytes_saved'] = $this->format_filesize($this->task_manager->options->get_option('total_bytes_saved', 0));
		$ui_update['percent_saved'] = number_format($this->task_manager->options->get_option('total_percent_saved', 1), 2).'%';
		$ui_update['failed_task_count'] = $this->task_manager->get_failed_task_count();

		$ui_update['summary'] = sprintf(__("A total of %d image(s) were compressed on this site, saving approximately %s of space at an average of %02d percent per image.", 'wp-optimize'), $ui_update['completed_task_count'], $ui_update['bytes_saved'], number_format($ui_update['percent_saved'], 2));
		$ui_update['failed'] = sprintf(__("%d image(s) could not be compressed. Please see the logs for more information or try again later.", 'wp-optimize'), $ui_update['failed_task_count']);
		$ui_update['pending'] = sprintf(__("%d image(s) images were selected for compressing previously, but were not all processed. You can either complete them now or cancel and retry later.", 'wp-optimize'), $ui_update['pending_tasks']);
		$ui_update['smush_complete'] = $this->task_manager->is_queue_processed();
		
		$this->close_browser_connection(json_encode($ui_update));
	}

	/**
	 * Updates smush related options
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function update_smush_options($data) {
		
		$options['compression_server'] = filter_var($data['compression_server'], FILTER_SANITIZE_STRING);
		$options['lossy_compression'] = filter_var($data['lossy_compression'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$options['back_up_original'] = filter_var($data['back_up_original'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$options['preserve_exif'] = filter_var($data['preserve_exif'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$options['autosmush'] = filter_var($data['autosmush'], FILTER_VALIDATE_BOOLEAN) ? true : false;
		$options['image_quality'] = filter_var($data['image_quality'], FILTER_SANITIZE_STRING);

		$success = $this->task_manager->update_smush_options($options);

		if (!$success) {
			return new WP_Error('update_failed', __('Options could not be updated', 'wp-optimize'));
		}

		$response['status'] = true;
		$response['summary'] = __('Options updated successfully', 'wp-optimize');
		
		return $response;
	}

	/**
	 * Clears any smush related stats
	 *
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function clear_smush_stats() {

		$success = $this->task_manager->clear_smush_stats();

		if (!$success) {
			return new WP_Error('update_failed', __('Stats could not be cleared', 'wp-optimize'));
		}

		$response['status'] = true;
		$response['summary'] = __('Stats cleared successfully', 'wp-optimize');

		return $response;
	}

	/**
	 * Checks if the selected server is online
	 *
	 * @param mixed $data - Sent in via AJAX
	 */
	public function check_server_status($data) {
		$server = filter_var($data['server'], FILTER_SANITIZE_STRING);
		$response['status'] = true;
		$response['online'] = $this->task_manager->check_server_online($server);
		return $response;
	}

	/**
	 * Completes any pending tasks
	 */
	public function process_pending_images() {
		$this->process_bulk_smush();
	}

	/**
	 * Deletes and removes any pending tasks from queue
	 *
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function clear_pending_images() {

		$success = $this->task_manager->clear_pending_images();

		if (!$success) {
			return new WP_Error('error_deleting_tasks', __('Pending tasks could not be cleared', 'wp-optimize'));
		}

		$response['status'] = true;
		$response['summary'] = __('Pending tasks cleared successfully', 'wp-optimize');
		
		return $response;
	}

	/**
	 * Returns the log file
	 *
	 * @return WP_Error|file - logfile or WP_Error object on failure
	 */
	public function get_smush_logs() {

		$logfile = $this->task_manager->get_logfile_path();

		if (file_exists($logfile)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($logfile).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($logfile));
			//@codingStandardsIgnoreLine
			readfile($logfile);
			exit;
		} else {
			return new WP_Error('log_file_error', __('Log file does not exist or could not be read', 'wp-optimize'));
		}
		
		return $response;
	}

	/**
	 * Helper function to format bytes to a human readable value
	 *
	 * @param int $bytes - the filesize in bytes
	 * @return string - the filesize
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

	/**
	 * Close browser connection so that it can resume AJAX polling
	 *
	 * @param array $txt Response to browser
	 * @return void
	 */
	public function close_browser_connection($txt = '') {
		header('Content-Length: '.((!empty($txt)) ? 4+strlen($txt) : '0'));
		header('Connection: close');
		header('Content-Encoding: none');
		if (session_id()) session_write_close();
		echo "\r\n\r\n";
		echo $txt;

		$levels = ob_get_level();
		
		for ($i = 0; $i < $levels; $i++) {
			ob_end_flush();
		}

		flush();
		
		if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
	}
}

endif;
