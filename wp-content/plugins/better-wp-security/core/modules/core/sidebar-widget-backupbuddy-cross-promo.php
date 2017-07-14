<?php

class ITSEC_Settings_Page_Sidebar_Widget_BackupBuddy_Cross_Promo extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'backupbuddy-cross-promo';
		$this->title = __( 'Complete Your Security Strategy With BackupBuddy', 'better-wp-security' );
		$this->priority = 7;

		parent::__construct();
	}

	public function render( $form ) {
		echo '<p style="text-align: center;"><img src="' . plugins_url( 'img/backupbuddy-logo.png', __FILE__ ) . '" alt="BackupBuddy"></p>';
		echo '<p>' . __( 'BackupBuddy is the complete backup, restore and migration solution for your WordPress site. Schedule automated backups, store your backups safely off-site and restore your site quickly & easily.', 'better-wp-security' ) . '</p>';
		echo sprintf( '<p style="font-weight: bold; font-size: 1em;">%s<span style="display: block; text-align: center; font-size: 1.2em; background: #ebebeb; padding: .5em;">%s</span></p>', __( '25% off BackupBuddy with coupon code', 'better-wp-security' ), __( 'BACKUPPROTECT', 'better-wp-security' ) );
		echo '<a href="http://ithemes.com/better-backups" class="button-secondary" target="_blank" rel="noopener noreferrer">' . __( 'Get BackupBuddy', 'better-wp-security' ) . '</a>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_BackupBuddy_Cross_Promo();
