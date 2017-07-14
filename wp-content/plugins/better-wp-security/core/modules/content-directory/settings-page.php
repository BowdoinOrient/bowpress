<?php

final class ITSEC_Content_Directory_Settings_Page extends ITSEC_Module_Settings_Page {
	private $version = 1;


	public function __construct() {
		$this->id = 'content-directory';
		$this->title = __( 'Change Content Directory', 'better-wp-security' );
		$this->description = __( 'Advanced feature to rename the wp-content directory to a different name.', 'better-wp-security' );
		$this->type = 'advanced';

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php _e( 'Change the location of the <code>wp-content</code> directory so that it uses a different name.', 'better-wp-security' ); ?></p>
<?php

	}

	private function show_current_wp_content_dir() {
		$dir_name = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );

?>
	<p><?php printf( __( 'The <code>wp-content</code> directory is available at <code>%s</code>.', 'better-wp-security' ), esc_html( $dir_name ) ); ?></p>
<?php

	}

	protected function render_settings( $form ) {
		require_once( dirname( __FILE__ ) . '/utility.php' );

		$yes_or_no = array(
			'yes' => __( 'Yes', 'better-wp-security' ),
			'no'  => __( 'No', 'better-wp-security' ),
		);

		$form->set_option( 'undo_change', 'no' );

?>
	<?php if ( ITSEC_Content_Directory_Utility::is_custom_directory() && ! ITSEC_Content_Directory_Utility::is_modified_by_it_security() ) : ?>
		<?php $this->show_current_wp_content_dir(); ?>
		<p><?php _e( 'The content directory was changed by something other than iThemes Security. No further actions are available on this page.', 'better-wp-security' ); ?></p>
	<?php else : ?>
		<div class="itsec-write-files-disabled">
			<div class="itsec-warning-message"><?php _e( 'The "Write to Files" setting is disabled in Global Settings. In order to use this feature, you must enable the "Write to Files" setting.', 'better-wp-security' ); ?></div>
		</div>

		<div class="itsec-write-files-enabled">
			<?php if ( ITSEC_Content_Directory_Utility::is_custom_directory() || ITSEC_Content_Directory_Utility::is_modified_by_it_security() ) : ?>
				<?php $this->show_current_wp_content_dir(); ?>

				<div class="itsec-warning-message"><?php printf( __( '<span>IMPORTANT:</span> Ensure that you <a href="%s">create a database backup</a> before undoing the Content Directory change.', 'better-wp-security' ), ITSEC_Core::get_backup_creation_page_url() ); ?></div>
				<div class="itsec-warning-message"><?php _e( '<span>WARNING:</span> Undoing the Content Directory change when images and other content were added after the change <strong>will break your site</strong>. Only undo the Content Directory change if absolutely necessary.', 'better-wp-security' ); ?></div>

				<table class="form-table itsec-settings-section">
					<tr>
						<th scope="row"><label for="itsec-content-directory-undo_change"><?php _e( 'Undo Content Directory Change', 'better-wp-security' ); ?></label></th>
						<td>
							<?php $form->add_select( 'undo_change', $yes_or_no ); ?>
							<p class="description"><?php _e( 'Select "Yes" and save the settings to undo the content directory change.', 'better-wp-security' ); ?></p>
						</td>
					</tr>
				</table>
			<?php else : ?>
				<p><?php _e( 'By default, WordPress stores files for plugins, themes, and uploads in a directory called <code>wp-content</code>. Some older and less intelligent bots hard coded this directory in order to look for vulnerable files. Modern bots are intelligent enough to locate this folder programmatically, thus changing the Content Directory is no longer a recommended security step.', 'better-wp-security' ); ?></p>
				<p><?php _e( 'This tool provides an undo feature after changing the Content Directory. Since not all plugins, themes, or site contents function properly with a renamed Content Directory, please verify that the site is functioning correctly after the change. If any issues are encountered, the undo feature should be used to undo the change. Please note that the undo feature is only available when the changes added to the <code>wp-config.php</code> file for this feature are unmodified.', 'better-wp-security' ); ?></p>
				<div class="itsec-warning-message"><?php _e( '<span>IMPORTANT:</span> Deactivating or uninstalling this plugin will not revert the changes made by this feature.', 'better-wp-security' ); ?></div>
				<div class="itsec-warning-message"><?php printf( __( '<span>IMPORTANT:</span> Ensure that you <a href="%s">create a database backup</a> before changing the Content Directory.', 'better-wp-security' ), ITSEC_Core::get_backup_creation_page_url() ); ?></div>
				<div class="itsec-warning-message"><?php _e( '<span>WARNING:</span> Changing the name of the Content Directory on a site that already has images and other content referencing it <strong>will break your site</strong>. For this reason, we highly recommend only changing the Content Directory on a fresh WordPress install.', 'better-wp-security' ); ?></div>

				<table class="form-table itsec-settings-section">
					<tr>
						<th scope="row"><label for="itsec-content-directory-new_directory_name"><?php _e( 'New Directory Name', 'better-wp-security' ); ?></label></th>
						<td>
							<?php $form->add_text( 'new_directory_name' ); ?>
							<br />
							<p class="description"><?php _e( 'Supply a new directory name and save the settings to change the location of the <code>wp-content</code> directory. You may need to log in again after performing this operation.', 'better-wp-security' ); ?></p>
						</td>
					</tr>
				</table>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php

	}

	public function handle_form_post( $data ) {
		require_once( dirname( __FILE__ ) . '/utility.php' );

		if ( ! empty( $data['new_directory_name'] ) ) {
			$results = ITSEC_Content_Directory_Utility::change_content_directory( $data['new_directory_name'] );

			if ( is_wp_error( $results ) ) {
				ITSEC_Response::add_error( $results );
				ITSEC_Response::add_error( new WP_Error( 'itsec-content-directory-settings-page-unable-to-change-content-directory', __( 'Unable to change the content directory. If the above error cannot be fixed, you may need to manually change the content directory. Instructions on how to change the content directory manually can be found <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder">here</a>.', 'better-wp-security' ) ) );
				ITSEC_Response::set_success( false );
			} else {
				/* translators: 1: New directory name */
				ITSEC_Response::add_message( sprintf( __( 'The content directory was successfully changed to <code>%1$s</code>.', 'better-wp-security' ), $results ) );

				ITSEC_Response::reload_module( $this->id );
			}
		} else if ( isset( $data['undo_change'] ) && 'yes' === $data['undo_change'] ) {
			$results = ITSEC_Content_Directory_Utility::change_content_directory( 'wp-content' );

			if ( is_wp_error( $results ) ) {
				ITSEC_Response::add_error( $results );
				ITSEC_Response::add_error( new WP_Error( 'itsec-content-directory-settings-page-unable-to-undo-content-directory-change', __( 'Unable to change the content directory back to <code>wp-content</code>. If the above error cannot be fixed, you may need to manually change the content directory. Instructions on how to change the content directory manually can be found <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder">here</a>.', 'better-wp-security' ) ) );
				ITSEC_Response::set_success( false );
			} else {
				/* translators: 1: New directory name */
				ITSEC_Response::add_message( sprintf( __( 'The content directory was successfully changed back to <code>%1$s</code>.', 'better-wp-security' ), $results ) );

				ITSEC_Response::reload_module( $this->id );
			}
		}
	}
}

new ITSEC_Content_Directory_Settings_Page();
