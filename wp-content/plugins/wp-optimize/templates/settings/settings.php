<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<div id="wp-optimize-general-settings" class="wpo_section wpo_group">
	<form action="#" method="post" enctype="multipart/form-data" name="settings_form" id="settings_form">
		<div id="wpo_settings_warnings"></div>
		<?php
		WP_Optimize()->include_template('settings/status-box-contents.php', false, array('optimize_db' => false));
		WP_Optimize()->include_template('settings/settings-general.php');
		WP_Optimize()->include_template('settings/settings-auto-cleanup.php');
		WP_Optimize()->include_template('settings/settings-logging.php');
		?>
		<?php WP_Optimize()->include_template('settings/settings-trackback-and-comments.php'); ?>

		<div id="wp-optimize-settings-save-results"></div>

		<input type="hidden" name="action" value="save_redirect">
		
		<?php wp_nonce_field('wpo_optimization'); ?>

		<input id="wp-optimize-settings-save" class="button button-primary" type="submit" name="wp-optimize-settings" value="<?php esc_attr_e('Save settings', 'wp-optimize'); ?>" />
		
		<img id="save_spinner" class="wpo_spinner" src="<?php echo esc_attr(admin_url('images/spinner-2x.gif')); ?>" alt="...">
		
		<span id="save_done" class="dashicons dashicons-yes display-none"></span>

	</form>
</div><!-- end #wp-optimize-general-settings -->
