<?php if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed'); ?>

<div id="wp-optimize-dashnotice" class="updated">

	<div style="float:right;"><a href="#" onclick="jQuery('#wp-optimize-dashnotice').slideUp(); jQuery.post(ajaxurl, {action: 'wp_optimize_ajax', subaction: 'dismiss_dash_notice_until', nonce: '<?php echo wp_create_nonce('wp-optimize-ajax-nonce');?>' });"><?php printf(__('Dismiss (for %s months)', 'wp-optimize'), 12); ?></a></div>

	<h3><?php _e("Thank you for using WP-Optimize, the world's most trusted WP optimization plugin!", 'wp-optimize');?></h3>
	
	<a href="https://updraftplus.com/"><img style="border: 0px; float: right; height: 150px; width: 150px;" alt="WP-Optimize" src="<?php echo WPO_PLUGIN_URL.'/images/notices/wp_optimize_logo.png' ?>"></a>

	<p>
		<strong><?php _e('Keep in touch!', 'wp-optimize')?></strong> 
		<br>
		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/news/', __('Blog', 'wp-optimize')); ?> |
		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/newsletter-signup', __('Newsletter', 'wp-optimize')); ?> |
		<?php $wp_optimize->wp_optimize_url('https://twitter.com/updraftplus', __('Twitter', 'wp-optimize')); ?>
	</p>

	<?php
		/*
			Here we see if they have any backup plugins installed and display one of the following lines
		*/
		$backup_plugin_installed = $wp_optimize_notices->is_backup_plugin_installed();

		if (!$backup_plugin_installed || 'UpdraftPlus' != $backup_plugin_installed) {
			echo '<p><strong>'.__('UpdraftPlus - the #1 most-trusted WordPress backup/restore plugin', 'wp-optimize').'</strong><br>'.__("The team behind WP-Optimize also create WordPress's most trusted backup/restore plugin", 'wp-optimize').': '.$wp_optimize->wp_optimize_url('https://updraftplus.com', __('UpdraftPlus', 'wp-optimize'));
			
			
			if (!$backup_plugin_installed) {
				echo __('Hackers, user error and dodgy updates can all ruin your site.', 'wp-optimize').' '.sprintf(__('We recommend that you install %s, which can automatically backup to remote cloud storage like Dropbox so your backup is independent and safe.', 'wp-optimize'), $wp_optimize->wp_optimize_url('https://updraftplus.com', __('UpdraftPlus', 'wp-optimize'))).' '.__('You can also restore a site with a couple of clicks.', 'wp-optimize').'</p>';
			} else {
				echo sprintf(__('Many users of %s have switched to UpdraftPlus WordPress Backup.', 'wp-optimize'), $backup_plugin_installed).' '.__('If you want to see the benefits of doing so too, then take a look at this:'). $wp_optimize->wp_optimize_url('https://updraftplus.com/wordpress-backup-plugin-comparison/', __('backup plugin comparison table', 'wp-optimize'));
			}
			
			echo '</p>';
			
		}
		
		echo '<p><strong>'.__('Do you manage multiple WordPress sites?', 'wp-optimize').'</strong> <br>'.sprintf(__('If so, take a look at %s, our remote site management solution. ','wp-optimize'), '<a href="https://updraftcentral.com">UpdraftCentral</a>').' '.__('Login securely, backup and update all your sites from one place.','wp-optimize').' '.__('Available as a self-install plugin, or hosted for you from updraftplus.com', 'wp-optimize').'</p>';

	?>

	<p>
		<strong><?php _e('More quality plugins', 'wp-optimize'); ?></strong> 
		<br>
		<?php $wp_optimize->wp_optimize_url('https://www.simbahosting.co.uk/s3/shop/', __('Premium WooCommerce plugins', 'wp-optimize')); ?> |
		<?php $wp_optimize->wp_optimize_url('https://wordpress.org/plugins/two-factor-authentication/', __('Free two-factor security plugin', 'wp-optimize')); ?>
	</p>

	<p>&nbsp;</p>
	
	
</div>
