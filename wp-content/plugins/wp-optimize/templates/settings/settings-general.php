<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<h3 class="wpo-first-child"><?php _e('General settings', 'wp-optimize'); ?></h3>
<div class="wpo-fieldgroup">
	<p>
		<?php _e('Whether manually or on a schedule, these settings apply whenever a relevant optimization is run.', 'wp-optimize'); ?>
	</p>
	<p>
		<input name="enable-retention" id="enable-retention" type="checkbox" value ="true" <?php echo ($options->get_option('retention-enabled') == 'true') ? 'checked="checked"' : ''; ?> />
		<?php
		$retention_period = max((int) $options->get_option('retention-period', '2'), 1);

		echo '<label for="enable-retention">';
		printf(
			__('Keep last %s weeks data', 'wp-optimize'),
			'</label><input id="retention-period" name="retention-period" type="number" step="1" min="2" max="99" value="'.$retention_period.'"><label for="enable-retention">'
		);
		echo '</label>';
		?>
		<br>
		<small class="wpo-text__dim"><?php _e('This option will, where relevant, retain data from the chosen period, and remove any garbage data before that period.', 'wp-optimize').' '.__('If the option is not active, then all garbage data will be removed.', 'wp-optimize').' '.__('This will also affect Auto Clean-up process', 'wp-optimize'); ?></small>
	</p>
	<p>
		<label>
			<input name="enable-admin-bar" id="enable-admin-bar" type="checkbox" value ="true" <?php echo ($options->get_option('enable-admin-menu', 'false') == 'true') ? 'checked="checked"' : ''; ?> />
			<?php _e('Enable admin bar link', 'wp-optimize'); ?>
		</label>
		<br>
		<small class="wpo-text__dim"><?php _e('This option will put an WP-Optimize link on the top admin bar (default is off). Requires a second page refresh after saving the settings.', 'wp-optimize'); ?></small>
	</p>
</div>