<?php

class ITSEC_Multisite_Tweaks_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'multisite-tweaks';
	}
	
	protected function preprocess_settings() {
		$this->sanitize_setting( 'bool', 'theme_updates', __( 'Theme Update Notifications', 'better-wp-security' ) );
		$this->sanitize_setting( 'bool', 'plugin_updates', __( 'Plugin Update Notifications', 'better-wp-security' ) );
		$this->sanitize_setting( 'bool', 'core_updates', __( 'Core Update Notifications', 'better-wp-security' ) );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Multisite_Tweaks_Validator() );
