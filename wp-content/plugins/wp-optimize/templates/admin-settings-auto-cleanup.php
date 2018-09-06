<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

	<h3><?php _e('Scheduled clean-up settings', 'wp-optimize'); ?></h3>

	<p>
		<a href="<?php echo WP_Optimize()->premium_version_link; ?>" target="_blank"><?php _e('Take control of clean-ups: Upgrade to Premium for a more powerful and flexible scheduler', 'wp-optimize'); ?></a>
	</p>

	<p>

		<input name="enable-schedule" id="enable-schedule" type="checkbox" value ="true"  <?php checked($options->get_option('schedule'), 'true'); ?>>
		<label for="enable-schedule"><?php _e('Enable scheduled clean-up and optimization (Beta feature)', 'wp-optimize'); ?></label>

	</p>

	<div id="wp-optimize-auto-options">

		<p>

			<?php _e('Select schedule type (default is Weekly)', 'wp-optimize'); ?><br>
			<select id="schedule_type" name="schedule_type">

				<?php
					$schedule_options = array(
						'wpo_daily' => __('Daily', 'wp-optimize'),
						'wpo_weekly' => __('Weekly', 'wp-optimize'),
						'wpo_otherweekly' => __('Fortnightly', 'wp-optimize'),
						'wpo_monthly' => __('Monthly (approx. - every 30 days)', 'wp-optimize'),
					);

					$schedule_type_saved_id = $options->get_option('schedule-type', 'wpo_weekly');

					foreach ($schedule_options as $opt_id => $opt_description) {
					?>
					<option value="<?php echo esc_attr($opt_id); ?>" <?php if ($opt_id == $schedule_type_saved_id) echo 'selected="selected"'; ?>><?php echo htmlspecialchars($opt_description); ?></option>
					<?php
					}

				?>

			</select>

		</p>

		<?php
		$wpo_auto_options = $options->get_option('auto');

			$optimizations = $optimizer->sort_optimizations($optimizer->get_optimizations());

			foreach ($optimizations as $id => $optimization) {
			if (empty($optimization->available_for_auto)) continue;

			$auto_id = $optimization->get_auto_id();

			$auto_dom_id = 'wp-optimize-auto-'.$auto_id;

			$setting_activated = (empty($wpo_auto_options[$auto_id]) || 'false' == $wpo_auto_options[$auto_id]) ? false : true;
			?>
			<p>
			<input name="wp-optimize-auto[<?php echo $auto_id; ?>]" id="<?php echo esc_attr($auto_dom_id); ?>" type="checkbox" value="true" <?php if ($setting_activated) echo 'checked="checked"'; ?>> <label for="<?php echo esc_attr($auto_dom_id); ?>"><?php echo $optimization->get_auto_option_description(); ?></label>
			</p>
			<?php
			}
		?>

		<!-- disabled email notification
		<p>
			<label>
					<input name="enable-email" id="enable-email" type="checkbox" value ="true"  />
											</label>
		</p>
		<p>
			<label for="enable-email-address">
												<input name="enable-email-address" id="enable-email-address" type="text" value ="" />
			</label>
		</p> -->

	</div>
