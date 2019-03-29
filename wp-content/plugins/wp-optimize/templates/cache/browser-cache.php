<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>
<div class="wpo_section wpo_group">

<h3 class="wpo-first-child"><?php _e('Browser static file caching settings', 'wp-optimize');?></h3>
<div class="wpo-fieldgroup">
	<?php

	$wpo_browser_cache = $wp_optimize->get_browser_cache();
	$wpo_browser_cache_enabled = $wpo_browser_cache->is_cache_enabled();
	$wpo_browser_cache_settings_added = $wpo_browser_cache->is_browser_cache_section_exists();
	$wpo_browser_cache_expire_days = $options->get_option('browser_cache_expire_days', '28');
	$wpo_browser_cache_expire_hours = $options->get_option('browser_cache_expire_hours', '0');

	$info_link = 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching';
	$faq_link = 'https://www.digitalocean.com/community/tutorials/how-to-implement-browser-caching-with-nginx-s-header-module-on-ubuntu-16-04';

	$class_name = ($wpo_browser_cache_enabled ? 'wpo-enabled' : 'wpo-disabled');
	?>
<p><?php echo __("Browser static file caching uses HTTP response headers to advise a visitor's browser to cache non-changing files for a while, so that it doesn’t need to retrieve them upon every visit.", 'wp-optimize').' '.sprintf('<a href="%s" target="_blank">%s</a>', $info_link, __('Follow this link to get more information.', 'wp_optimize')); ?>
</p>
<div id="wpo_browser_cache_status" class="<?php echo $class_name; ?>">
	<span class="wpo-enabled"><?php printf(__('Browser static file caching headers are currently %s.', 'wp-optimize'), '<strong>'.__('enabled', 'wp-optimize').'</strong>'); ?></span>
	<span class="wpo-disabled"><?php printf(__('Browser static file caching headers are currently %s.', 'wp-optimize'), '<strong>'.__('disabled', 'wp-optimize').'</strong>'); ?></span>
</div>
<br>
<?php

// add browser cache control section only if browser cache disabled or we added cache settings to .htaccess.
if (false == $wpo_browser_cache_enabled || $wpo_browser_cache_settings_added) {

	if ($wp_optimize->is_apache_server()) {
		$button_text = $wpo_browser_cache_enabled ? __('Update', 'wp-optimize') : __('Enable', 'wp-optimize');
		?>
		<form>
			<label><?php _e('Expiration time:', 'wp-optimize'); ?></label>
			<input id="wpo_browser_cache_expire_days" type="number" min="0" step="1" name="browser_cache_expire_days" value="<?php echo esc_attr($wpo_browser_cache_expire_days); ?>">
			<label for="wpo_browser_cache_expire"><?php _e('day(s)', 'wp-optimize'); ?></label>
			<input id="wpo_browser_cache_expire_hours" type="number" min="0" step="1" name="browser_cache_expire_hours" value="<?php echo esc_attr($wpo_browser_cache_expire_hours); ?>">
			<label for="wpo_browser_cache_expire_hours"><?php _e('hour(s)', 'wp-optimize'); ?></label>
			<button class="button-primary" type="button" id="wp_optimize_browser_cache_enable"><?php echo $button_text; ?></button>
			<img class="wpo_spinner display-none" src="<?php echo esc_attr(admin_url('images/spinner-2x.gif')); ?>"
				 width="20" height="20" alt="...">
			<br>
			<small><?php _e('Make the time empty or zero to disable the headers.', 'wp-optimize'); ?></small>
		</form>
		<?php
	} else {
		echo sprintf('<a href="%s" target="_blank">%s</a>', $faq_link, __('Click to read the article about how to enable browser cache with your server software.', 'wp_optimize'));
	}
	?>
	</div>
	<div id="wpo_browser_cache_message"></div>
	<div id="wpo_browser_cache_error_message"><?php
		if (is_wp_error($wpo_browser_cache_enabled)) {
			echo $wpo_browser_cache_enabled->get_error_message();
		}
	?></div>
	<pre id="wpo_browser_cache_output" style="display: none;"></pre>
	<?php
}
?>

</div>
