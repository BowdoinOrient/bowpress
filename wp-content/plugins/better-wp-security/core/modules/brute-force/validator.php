<?php

class ITSEC_Brute_Force_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'brute-force';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( 'positive-int', 'max_attempts_host', __( 'Max Login Attempts Per Host', 'better-wp-security' ) );
		$this->sanitize_setting( 'positive-int', 'max_attempts_user', __( 'Max Login Attempts Per User', 'better-wp-security' ) );
		$this->sanitize_setting( 'positive-int', 'check_period', __( 'Minutes to Remember Bad Login (check period)', 'better-wp-security' ) );
		
		$this->sanitize_setting( 'bool', 'auto_ban_admin', __( 'Automatically ban "admin" user', 'better-wp-security' ) );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Brute_Force_Validator() );
