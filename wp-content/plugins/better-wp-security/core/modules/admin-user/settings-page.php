<?php

final class ITSEC_Admin_User_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;
	
	
	public function __construct() {
		$this->id = 'admin-user';
		$this->title = __( 'Admin User', 'better-wp-security' );
		$this->description = __( 'An advanced tool that removes users with a username of "admin" or a user ID of "1".', 'better-wp-security' );
		$this->type = 'advanced';
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'This feature will improve the security of your WordPress installation by removing common user attributes that can be used to target your site.', 'better-wp-security' ); ?></p>
<?php
		
	}
	
	protected function render_settings( $form ) {
		
?>
	<div class="itsec-warning-message"><?php printf( __( '<span>Warning:</span> The changes made by this tool could cause compatibility issues with some plugins, themes, or customizations. Ensure that you <a href="%s">create a database backup</a> before using this tool.', 'better-wp-security' ), esc_url( ITSEC_Core::get_backup_creation_page_url() ) ); ?></div>
	
	<table class="form-table itsec-settings-section">
		<?php if ( username_exists( 'admin' ) ) : ?>
			<tr>
				<th scope="row"><label for="itsec-admin-user-new_username"><?php _e( 'New Admin Username', 'better-wp-security' ); ?></label></th>
				<td>
					<?php $form->add_text( 'new_username', array( 'class' => 'code' ) ); ?>
					<br />
					<p class="description"><?php _e( 'Enter a new username to replace "admin." Please note that if you are logged in as admin you will have to log in again.', 'better-wp-security' ); ?></p>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( ITSEC_Lib::user_id_exists( 1 ) ) { ?>
			<tr>
				<th scope="row"><label for="itsec-admin-user-change_id"><?php _e( 'Change User ID 1', 'better-wp-security' ); ?></label></th>
				<td>
					<?php $form->add_checkbox( 'change_id' ); ?>
					<label for="itsec-admin-user-change_id"><?php _e( 'Change the ID of the user with ID 1.', 'better-wp-security' ); ?></label>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php
		
	}
}

if ( username_exists( 'admin' ) || ITSEC_Lib::user_id_exists( 1 ) ) {
	new ITSEC_Admin_User_Settings_Page();
}
