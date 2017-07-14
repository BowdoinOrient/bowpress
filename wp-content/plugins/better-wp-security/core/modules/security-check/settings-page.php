<?php

final class ITSEC_Security_Check_Settings_Page extends ITSEC_Module_Settings_Page {
	private $script_version = 1;


	public function __construct() {
		$this->id = 'security-check';
		$this->title = __( 'Security Check', 'better-wp-security' );
		$this->description = __( 'Ensure that your site is using the recommended features and settings.', 'better-wp-security' );
		$this->type = 'recommended';
		$this->information_only = true;
		$this->can_save = false;

		parent::__construct();
	}

	public function enqueue_scripts_and_styles() {
		$vars = array(
			'securing_site'                  => __( 'Securing Site...', 'better-wp-security' ),
			'rerun_secure_site'              => __( 'Run Secure Site Again', 'better-wp-security' ),
			'activating_network_brute_force' => __( 'Activating Network Brute Force Protection...', 'better-wp-security' ),
		);

		wp_enqueue_script( 'itsec-security-check-settings-script', plugins_url( 'js/settings-page.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'itsec-security-check-settings-script', 'itsec_security_check_settings', $vars );
	}

	public function handle_ajax_request( $data ) {
		if ( 'secure-site' === $data['method'] ) {
			require_once( dirname( __FILE__ ) . '/scanner.php' );

			ITSEC_Security_Check_Scanner::run();
		} else if ( 'activate-network-brute-force' === $data['method'] ) {
			require_once( dirname( __FILE__ ) . '/scanner.php' );

			ITSEC_Security_Check_Scanner::activate_network_brute_force();
		}
	}

	protected function render_description( $form ) {}

	protected function render_settings( $form ) {
		$available_modules = ITSEC_Modules::get_available_modules();

		$modules_to_activate = array(
			'ban-users'           => __( 'Banned Users', 'better-wp-security' ),
			'backup'              => __( 'Database Backups', 'better-wp-security' ),
			'brute-force'         => __( 'Local Brute Force Protection', 'better-wp-security' ),
			'malware-scheduling'  => __( 'Malware Scan Scheduling', 'better-wp-security' ),
			'network-brute-force' => __( 'Network Brute Force Protection', 'better-wp-security' ),
			'strong-passwords'    => __( 'Strong Passwords', 'better-wp-security' ),
			'two-factor'          => __( 'Two-Factor Authentication', 'better-wp-security' ),
			'user-logging'        => __( 'User Logging', 'better-wp-security' ),
			'wordpress-tweaks'    => __( 'WordPress Tweaks', 'better-wp-security' ),
		);

		foreach ( $modules_to_activate as $module => $val ) {
			if ( ! in_array( $module, $available_modules ) ) {
				unset( $modules_to_activate[$module] );
			}
		}

?>
	<div id="itsec-security-check-details-container">
		<p><?php _e( 'Some features and settings are recommended for every site to run. This tool will ensure that your site is using these recommendations.', 'better-wp-security' ); ?></p>
		<p><?php _e( 'When the button below is clicked the following modules will be enabled and configured:', 'better-wp-security' ); ?></p>
		<ul class="itsec-security-check-list">
			<?php foreach ( $modules_to_activate as $name ) : ?>
				<li><p><?php echo $name; ?></p></li>
			<?php endforeach; ?>
		</ul>
	</div>

	<p><?php $form->add_button( 'secure_site', array( 'value' => 'Secure Site', 'class' => 'button-primary' ) ); ?></p>
<?php

	}
}
new ITSEC_Security_Check_Settings_Page();
