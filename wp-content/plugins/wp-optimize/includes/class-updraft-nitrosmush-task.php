<?php
/**
 *  A sample implementation using the NitroSmush API and our tasks library
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Smush_Task')) require_once('class-updraft-smush-task.php');

if (!class_exists('Nitro_Smush_Task')) :

class Nitro_Smush_Task extends Updraft_Smush_Task {

	public $label = 'nitrosmush';

	const MAX_FILESIZE = 104857600;

	const API_URL = 'http://nitrosmush.com/api.php';

	/**
	 * Checks if the server is online, must be called from the task manager only
	 *
	 * @return bool - true if yes, false otherwise
	 */
	public static function is_server_online() {

		global $task_manager;
		$test_image = WPO_PLUGIN_MAIN_PATH . 'images/icon/wpo.png';
		$boundary = wp_generate_password(12);
		$headers  = array( "content-type" => "multipart/form-data; boundary=$boundary" );
		$payload = "";
		
		$payload .= "--" . $boundary . "\n";
		$payload .= "Content-Disposition: form-data; name=\"quality\"\n\n";
		$payload .= "99"."\n";
		$payload .= "--" . $boundary . "\n";
		$payload .= "Content-Disposition: form-data; name=\"image\"; filename=\"" . basename($test_image) . "\"\n";
		$payload .= "Content-Type: " . "image/png" . "\n";
		$payload .= "Content-Transfer-Encoding: binary\n\n";
		$payload .= file_get_contents($test_image)."\n";
		$payload .= "--" . $boundary . "\n";
		

		$request = array(
			'headers' => $headers,
			'timeout' => 30,
			'body' => $payload,
		);

		$response = wp_remote_post(self::API_URL, $request);

		if (is_wp_error($response)) {
			$task_manager->log($response->get_error_message());
			return false;
		}

		$data = json_decode(wp_remote_retrieve_body($response));

		if (empty($data)) {
			$task_manager->log("Empty data returned by server");
			return false;
		}

		if (isset($data->error)) {
			$task_manager->log($data->error);
			return false;
		}

		return true;
	}

	/**
	 * Prepares the image as part of the post data for the specific implementation
	 *
	 * @param String $local_file - The image to e optimised
	 */
	public function prepare_post_request($local_file) {

		$boundary = wp_generate_password(12);
		$lossy = $this->get_option('lossy_compression');

		if ($lossy) {
			if ('best' == $this->get_option('image_quality')) {
				$quality = 99;
			} elseif ('very_good' == $this->get_option('image_quality')) {
				$quality = 96;
			} else {
				$quality = 94;
			}
		} else {
			$quality = 100;
		}

		$headers  = array( "content-type" => "multipart/form-data; boundary=$boundary" );
		$payload = "";
		
		$payload .= "--" . $boundary . "\n";
		$payload .= "Content-Disposition: form-data; name=\"quality\"\n\n";
		$payload .= $quality."\n";
		$payload .= "--" . $boundary . "\n";
		$payload .= "Content-Disposition: form-data; name=\"image\"; filename=\"" . basename($local_file) . "\"\n";
		$payload .= "Content-Type: " . "image/png" . "\n";
		$payload .= "Content-Transfer-Encoding: binary\n\n";
		$payload .= file_get_contents($local_file)."\n";
		$payload .= "--" . $boundary . "\n";

		return array(
			'headers' => $headers,
			'timeout' => $this->get_option('request_timeout'),
			'body' => $payload,
		);
	}

	/**
	 * Processes the response recieved from the remote server
	 *
	 * @param String $response - The response object
	 */
	public function process_server_response($response) {

		$response = parent::process_server_response($response);
		$data = json_decode(wp_remote_retrieve_body($response));

		if (!$data) {
			$this->log("Cannot establish connection with NitroSmush webservice. Please try later");
			return false;
		}

		if (isset($data->error)) {
			$this->fail($data->error_short, $data->error);
			return false;
		}

		$compressed_image = file_get_contents($data->result_file);
		
		if ($compressed_image) {
			return $compressed_image;
		} else {
			$this->fail("invalid_repsonse", "The Smush process failed with an invalid response from the server");
			return false;
		}
	}

	/**
	 * Retrieve features for this service
	 *
	 * @return Array - an array of options
	 */
	public static function get_features() {
		return array(
			'max_filesize' => self::MAX_FILESIZE,
			'lossy_compression' => true,
			'preserve_exif' => false,
		);
	}

	/**
	 * Retrieve default options for this task type.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'allowed_file_types' => array('gif', 'png', 'jpg', 'tif', 'jpeg'),
			'request_timeout' => 30,
			'keep_original' => true,
			'preserve_exif' => false,
			'image_quality' => 100,
			'api_endpoint' => self::API_URL,
			'max_filesize' => self::MAX_FILESIZE,
			'backup_prefix' => '-updraft-pre-smush-original.'
		);
	}
}
endif;
