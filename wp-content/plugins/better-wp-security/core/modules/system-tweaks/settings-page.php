<?php

final class ITSEC_System_Tweaks_Settings_Page extends ITSEC_Module_Settings_Page {
	public function __construct() {
		$this->id = 'system-tweaks';
		$this->title = __( 'System Tweaks', 'better-wp-security' );
		$this->description = __( 'Advanced settings that improve security by changing the server config for this site.', 'better-wp-security' );
		$this->type = 'recommended';

		parent::__construct();
	}

	protected function render_description( $form ) {

?>
	<p><?php esc_html_e( 'These are advanced settings that may be utilized to further strengthen the security of your WordPress site.', 'better-wp-security' ); ?></p>
<?php

	}

	protected function render_settings( $form ) {

?>
	<p><?php esc_html_e( 'Note: These settings are listed as advanced because they block common forms of attacks but they can also block legitimate plugins and themes that rely on the same techniques. When activating the settings below, we recommend enabling them one by one to test that everything on your site is still working as expected.', 'better-wp-security' ); ?></p>
	<p><?php esc_html_e( 'Remember, some of these settings might conflict with other plugins or themes, so test your site after enabling each setting.', 'better-wp-security' ); ?></p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-protect_files"><?php esc_html_e( 'System Files', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'protect_files' ); ?>
				<label for="itsec-system-tweaks-protect_files"><?php esc_html_e( 'Protect System Files', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Prevent public access to readme.html, readme.txt, wp-config.php, install.php, wp-includes, and .htaccess. These files can give away important information on your site and serve no purpose to the public once WordPress has been successfully installed.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-directory_browsing"><?php esc_html_e( 'Directory Browsing', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'directory_browsing' ); ?>
				<label for="itsec-system-tweaks-directory_browsing"><?php esc_html_e( 'Disable Directory Browsing', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Prevents users from seeing a list of files in a directory when no index file is present.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-request_methods"><?php esc_html_e( 'Request Methods', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'request_methods' ); ?>
				<label for="itsec-system-tweaks-request_methods"><?php esc_html_e( 'Filter Request Methods', 'better-wp-security' ); ?></label>
				<p class="description"><?php printf( wp_kses( __( 'Filter out hits with the trace, delete, or track request methods. This should not be enabled if you use the <a href="%s">WordPress REST API</a>.', 'better-wp-security' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( 'https://wordpress.org/plugins/rest-api/' ) ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-suspicious_query_strings"><?php esc_html_e( 'Suspicious Query Strings', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'suspicious_query_strings' ); ?>
				<label for="itsec-system-tweaks-suspicious_query_strings"><?php esc_html_e( 'Filter Suspicious Query Strings in the URL', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'These are very often signs of someone trying to gain access to your site but some plugins and themes can also be blocked.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-non_english_characters"><?php esc_html_e( 'Non-English Characters', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'non_english_characters' ); ?>
				<label for="itsec-system-tweaks-non_english_characters"><?php esc_html_e( 'Filter Non-English Characters', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Filter out non-english characters from the query string. This should not be used on non-english sites and only works when "Filter Suspicious Query String" has been selected.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-long_url_strings"><?php esc_html_e( 'Long URL Strings', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'long_url_strings' ); ?>
				<label for="itsec-system-tweaks-long_url_strings"><?php esc_html_e( 'Filter Long URL Strings', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Limits the number of characters that can be sent in the URL. Hackers often take advantage of long URLs to try to inject information into your database.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-write_permissions"><?php esc_html_e( 'File Writing Permissions', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'write_permissions' ); ?>
				<label for="itsec-system-tweaks-write_permissions"><?php esc_html_e( 'Remove File Writing Permissions', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Prevents scripts and users from being able to write to the wp-config.php file and .htaccess file. Note that in the case of this and many plugins this can be overcome however it still does make the files more secure. Turning this on will set the UNIX file permissions to 0444 on these files and turning it off will set the permissions to 0664.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-uploads_php"><?php esc_html_e( 'PHP in Uploads', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'uploads_php' ); ?>
				<label for="itsec-system-tweaks-uploads_php"><?php esc_html_e( 'Disable PHP in Uploads', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Disable PHP execution in the uploads directory. This blocks requests to maliciously uploaded PHP files in the uploads directory.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-plugins_php"><?php esc_html_e( 'PHP in Plugins', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'plugins_php' ); ?>
				<label for="itsec-system-tweaks-plugins_php"><?php esc_html_e( 'Disable PHP in Plugins', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Disable PHP execution in the plugins directory. This blocks requests to PHP files inside plugin directories that can be exploited directly.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="itsec-system-tweaks-themes_php"><?php esc_html_e( 'PHP in Themes', 'better-wp-security' ); ?></label></th>
			<td>
				<?php $form->add_checkbox( 'themes_php' ); ?>
				<label for="itsec-system-tweaks-themes_php"><?php esc_html_e( 'Disable PHP in Themes', 'better-wp-security' ); ?></label>
				<p class="description"><?php esc_html_e( 'Disable PHP execution in the themes directory. This blocks requests to PHP files inside theme directories that can be exploited directly.', 'better-wp-security' ); ?></p>
			</td>
		</tr>
	</table>
<?php

	}
}

new ITSEC_System_Tweaks_Settings_Page();
