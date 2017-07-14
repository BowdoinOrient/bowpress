<?php

final class ITSEC_Brute_Force_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'brute-force';
		$this->title = __( 'Local Brute Force Protection', 'better-wp-security' );
		$this->description = __( 'Protect your site against attackers that try to randomly guess login details to your site.', 'better-wp-security' );
		$this->type = 'recommended';
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'If one had unlimited time and wanted to try an unlimited number of password combinations to get into your site they eventually would, right? This method of attack, known as a brute force attack, is something that WordPress is acutely susceptible to as, by default, the system doesn\'t care how many attempts a user makes to login. It will always let you try again. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshold has been reached.', 'better-wp-security' ); ?></p>
<?php
		
	}
	
	protected function render_settings( $form ) {
		
?>
	<?php echo $GLOBALS['itsec_lockout']->get_lockout_description(); ?>
	<table class="form-table" id="brute_force-settings">
		<tr>
			<th scope="row"><label for="itsec-brute-force-max_attempts_host"><?php _e( 'Max Login Attempts Per Host', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_text( 'max_attempts_host', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-brute-force-max_attempts_host"><?php _e( 'Attempts', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'The number of login attempts a user has before their host or computer is locked out of the system. Set to 0 to record bad login attempts without locking out the host.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-brute-force-max_attempts_user"><?php _e( 'Max Login Attempts Per User', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_text( 'max_attempts_user', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-brute-force-max_attempts_user"><?php _e( 'Attempts', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'The number of login attempts a user has before their username is locked out of the system. Note that this is different from hosts in case an attacker is using multiple computers. In addition, if they are using your login name you could be locked out yourself. Set to 0 to log bad login attempts per user without ever locking the user out (this is not recommended).', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-brute-force-check_period"><?php _e( 'Minutes to Remember Bad Login (check period)', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_text( 'check_period', array( 'class' => 'small-text' ) ); ?>
				<label for="itsec-brute-force-check_period"><?php _e( 'Minutes', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'The number of minutes in which bad logins should be remembered.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-brute-force-auto_ban_admin"><?php _e( 'Automatically ban "admin" user', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'auto_ban_admin' ); ?>
				<label for="itsec-brute-force-auto_ban_admin"><?php _e( 'Immediately ban a host that attempts to login using the "admin" username.', 'better-wp-security' ); ?></label>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_Brute_Force_Settings_Page();
