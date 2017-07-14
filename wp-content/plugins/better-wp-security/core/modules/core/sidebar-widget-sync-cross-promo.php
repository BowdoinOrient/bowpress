<?php

class ITSEC_Settings_Page_Sidebar_Widget_Sync_Cross_Promo extends ITSEC_Settings_Page_Sidebar_Widget {
	public function __construct() {
		$this->id = 'sync-cross-promo';
		$this->title = __( 'Manage Your Sites Remotely', 'better-wp-security' );
		$this->priority = 11;

		parent::__construct();
	}

	public function render( $form ) {
		?>
		<div style="text-align: center;">
			<img src="<?php echo plugins_url( 'img/sync-logo.png', __FILE__ ) ?>" width="173"
			     height="65" alt="Manage Your Sites Remotely">
		</div>
		<?php

		echo '<p>' . __( 'Manage updates remotely for up to 10 WordPress sites today for free!', 'better-wp-security' ) . '</p>';
		echo '<p>' . __( 'Integrated with iThemes Security, so you can release lockouts and turn Away Mode on or off right from your Sync dashboard or your phone.', 'better-wp-security' ) . '</p>';
		echo '<div style="text-align: center;">';
		echo '<p><a class="button-primary" href="http://www.ithemes.com/sync" target="_blank" rel="noopener noreferrer">' . __( 'Try iThemes Sync for Free', 'better-wp-security' ) . '</a></p>';
		echo '</div>';
	}

}
new ITSEC_Settings_Page_Sidebar_Widget_Sync_Cross_Promo();
