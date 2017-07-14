<?php

class ITSEC_Settings_Page_Sidebar_Widget_Support extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'support';
		$this->title = __( 'Need Help Securing Your Site?', 'better-wp-security' );
		$this->priority = 11;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p>' . __( 'Since you are using the free version of iThemes Security from WordPress.org, you can get free support from the WordPress community.', 'better-wp-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="http://wordpress.org/support/plugin/better-wp-security" target="_blank" rel="noopener noreferrer">' . __( 'Get Free Support', 'better-wp-security' ) . '</a></p>';
		echo '<p>' . __( 'Get added peace of mind with professional support from our expert team and pro features with iThemes Security Pro.', 'better-wp-security' ) . '</p>';
		echo '<p><a class="button-secondary" href="https://ithemes.com/security/?utm_source=wordpressadmin&utm_medium=widget&utm_campaign=itsecfreecta" target="_blank" rel="noopener noreferrer">' . __( 'Get iThemes Security Pro', 'better-wp-security' ) . '</a></p>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Support();
