<?php

final class ITSEC_Multisite_Tweaks_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'multisite-tweaks';
		$this->title = __( 'Multisite Tweaks', 'better-wp-security' );
		$this->description = __( 'Advanced settings that improve security by changing default WordPress Multisite behavior.', 'better-wp-security' );
		$this->type = 'recommended';
		
		parent::__construct();
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'These are advanced settings that may be utilized to further strengthen the security of your WordPress site.', 'better-wp-security' ); ?></p>
<?php
		
	}
	
	protected function render_settings( $form ) {
		
?>
	<p><?php _e( 'Note: These settings are listed as advanced because they block common forms of attacks but they can also block legitimate plugins and themes that rely on the same techniques. When activating the settings below, we recommend enabling them one by one to test that everything on your site is still working as expected.', 'better-wp-security' ); ?></p>
	<p><?php _e( 'Remember, some of these settings might conflict with other plugins or themes, so test your site after enabling each setting.', 'better-wp-security' ); ?></p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-multisite-tweaks-theme_updates"><?php _e( 'Theme Update Notifications', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'theme_updates' ); ?>
				<label for="itsec-multisite-tweaks-theme_updates"><?php _e( 'Hide Theme Update Notifications', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'Hides theme update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-multisite-tweaks-plugin_updates"><?php _e( 'Plugin Update Notifications', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'plugin_updates' ); ?>
				<label for="itsec-multisite-tweaks-plugin_updates"><?php _e( 'Hide Plugin Update Notifications', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'Hides plugin update notifications from users who cannot update plugins. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-multisite-tweaks-core_updates"><?php _e( 'Core Update Notifications', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'core_updates' ); ?>
				<label for="itsec-multisite-tweaks-core_updates"><?php _e( 'Hide Core Update Notifications', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'Hides core update notifications from users who cannot update core. Please note that this only makes a difference in multi-site installations.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_Multisite_Tweaks_Settings_Page();
