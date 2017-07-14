<?php

class ITSEC_Settings_Page_Sidebar_Widget_Pro_Upsell extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'pro-upsell';
		$this->title = __( 'Get iThemes Security Pro', 'better-wp-security' );
		$this->priority = 5;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p>' . sprintf( __( 'Add an extra layer of protection to your WordPress site with <a href="%s">iThemes Security Pro</a>, including:', 'better-wp-security' ), 'https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta' ) . '</p>';
		echo '<ul>';
		echo '<li>' . __( 'Two-factor authentication', 'better-wp-security' ) . '</li>';
		echo '<li>' . __( 'Scheduled malware scanning', 'better-wp-security' ) . '</li>';
		echo '<li>' . __( 'Google reCAPTCHA integration', 'better-wp-security' ) . '</li>';
		echo '<li>' . __( 'Private, ticketed support', 'better-wp-security' ) . '</li>';
		echo '<li>' . __( '+ more Pro-only features', 'better-wp-security' ) . '</li>';
		echo '</ul>';
		echo '<a href="https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta" class="button-primary" target="_blank" rel="noopener noreferrer">' . __( 'Get iThemes Security Pro', 'better-wp-security' ) . '</a>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Pro_Upsell();
