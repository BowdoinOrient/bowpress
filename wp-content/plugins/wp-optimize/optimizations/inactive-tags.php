<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimization_tags extends WP_Optimization {

	public $available_for_auto = false;

	public $auto_default = false;

	public $ui_sort_order = 12000;

	public function optimize() {
	
	}
	public function get_info() {

	}

	public function settings_label() {
		return __('Remove unused tags', 'wp-optimize');
	}

	/**
	 * Return description
	 * N.B. This is not currently used; it was commented out in 1.9.1
	 *
	 * @return string|void
	 */
	public function get_auto_option_description() {
	}
}
