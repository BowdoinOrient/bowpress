<?php

final class ITSEC_SSL_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;
	
	
	public function __construct() {
		$this->id = 'ssl';
		$this->title = __( 'SSL', 'better-wp-security' );
		$this->description = __( 'Configure use of SSL to ensure that communications between browsers and the server are secure.', 'better-wp-security' );
		$this->type = 'recommended';
		
		parent::__construct();
	}
	
	public function enqueue_scripts_and_styles() {
		$vars = array(
			'translations' => array(
				'ssl_warning' => __( 'Are you sure you want to enable SSL? If your server does not support SSL you will be locked out of your WordPress Dashboard.', 'better-wp-security' ),
			),
		);
		
		wp_enqueue_script( 'itsec-ssl-admin-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'itsec-ssl-admin-script', 'itsec_ssl', $vars );
	}
	
	protected function render_description( $form ) {
		
?>
	<p><?php _e( 'Secure Socket Layers (SSL) is a technology that is used to encrypt the data sent between your server or host and a visitor to your web page. When SSL is activated, it makes it almost impossible for an attacker to intercept data in transit, therefore making the transmission of form, password or other encrypted data much safer.', 'better-wp-security' ); ?></p>
	<p><?php _e( 'This plugin gives you the option of turning on SSL (if your server or host supports it) for all or part of your site. The options below allow you to automatically use SSL for major parts of your site such as the login page, the admin dashboard or the site as a whole. You can also turn on SSL for any post or page by editing the content and selecting "Enable SSL" in the publishing options of the content in question.', 'better-wp-security' ); ?></p>
<?php
		
	}
	
	protected function render_settings( $form ) {
		$has_ssl = ITSEC_Lib::get_ssl();
		
		$frontend_modes = array(
			0 => __( 'Off', 'better-wp-security' ),
			1 => __( 'Per Content', 'better-wp-security' ),
			2 => __( 'Whole Site', 'better-wp-security' ),
		);
		
?>
	<p><?php _e( 'Note: While this plugin does give you the option of encrypting everything, SSL may not be for you. SSL does add overhead to your site which will increase download times slightly. Therefore we recommend you enable SSL at a minimum on the login page, then on the whole admin section and finally on individual pages or posts with forms that require sensitive information.', 'better-wp-security' ); ?></p>
	
	<?php if ( $has_ssl ) : ?>
		<div class="itsec-warning-message"><?php _e( '<strong>WARNING:</strong> Your server does appear to support SSL. Using these features without SSL support on your server or host will cause some or all of your site to become unavailable.', 'better-wp-security' ); ?></div>
	<?php else : ?>
		<div class="itsec-warning-message"><?php _e( '<strong>WARNING:</strong> Your server does not appear to support SSL. Your server MUST support SSL to use these features. Using these features without SSL support on your server or host will cause some or all of your site to become unavailable.', 'better-wp-security' ); ?></div>
	<?php endif; ?>
	
	<p><?php _e( 'Note: When turning SSL on you will be logged out and you will have to log back in. This is to prevent possible cookie conflicts that could make it more difficult to get in otherwise.', 'better-wp-security' ); ?></p>
	
	<table class="form-table itsec-settings-section">
		<tr>
			<th scope="row"><label for="itsec-ssl-frontend"><?php _e( 'Front End SSL Mode', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_select( 'frontend', $frontend_modes ); ?>
				<br />
				<label for="itsec-ssl-frontend"><?php _e( 'Front End SSL Mode', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'Enables secure SSL connection for the front-end (public parts of your site). Turning this off will disable front-end SSL control, turning this on "Per Content" will place a checkbox on the edit page for all posts and pages (near the publish settings) allowing you to turn on SSL for selected pages or posts. Selecting "Whole Site" will force the whole site to use SSL.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-ssl-admin"><?php _e( 'SSL for Dashboard', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'admin' ); ?>
				<label for="itsec-ssl-admin"><?php _e( 'Force SSL for Dashboard', 'better-wp-security' ); ?></label>
				<p class="description"><?php _e( 'Forces all dashboard access to be served only over an SSL connection.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
	</table>
<?php
		
	}
}

new ITSEC_SSL_Settings_Page();
