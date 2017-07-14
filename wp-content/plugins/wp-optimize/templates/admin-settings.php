<?php if (!defined('WPO_VERSION')) die ('No direct access allowed'); ?>

<div class="wpo_section wpo_group">

	<form action="#" method="post" enctype="multipart/form-data" name="settings_form" id="settings_form">

	<input type="hidden" name="action" value="save_redirect">

	<?php wp_nonce_field( 'wpo_optimization' ); ?>

	<div class="wpo_col wpo_span_2_of_3">
		<div class="postbox">
			<div class="inside">
				<h3><?php _e('General settings', 'wp-optimize'); ?></h3>
				<p>
					<?php _e('Whether manually or on a schedule, these settings apply whenever a relevant optimization is run.', 'wp-optimize');?>
				</p>
				<p>
					<input name="enable-retention" id="enable-retention" type="checkbox" value ="true" <?php echo $options->get_option('retention-enabled') == 'true' ? 'checked="checked"':''; ?> />
					<?php

					$retention_period = max((int)$options->get_option('retention-period', '2'), 1);
					
					echo '<label for="enable-retention">';
					printf(__('Keep last %s weeks data', 'wp-optimize'),
					'</label><input id="retention-period" name="retention-period" type="number" step="1" min="2" max="99" value="'.$retention_period.'"><label for="enable-retention">');
				   	echo '</label>'; ?>
				   	<br>
				   	<small><?php _e('This option will, where relevant, retain data from the chosen period, and remove any garbage data before that period.', 'wp-optimize').' '.__('If the option is not active, then all garbage data will be removed.', 'wp-optimize').' '.__('This will also affect Auto Clean-up process', 'wp-optimize');?></small>
				</p>
				<p>
					<label>
						<input name="enable-admin-bar" id="enable-admin-bar" type="checkbox" value ="true" <?php echo $options->get_option('enable-admin-menu', 'false') == 'true' ? 'checked="checked"':''; ?> />
						<?php _e('Enable admin bar link', 'wp-optimize');?>
					</label>
					<br>
					<small><?php _e('This option will put an WP-Optimize link on the top admin bar (default is off). Requires a second page refresh after saving the settings.', 'wp-optimize');?></small>
				</p>
				
				<hr>
				
				<h3><?php _e('Auto clean-up settings', 'wp-optimize'); ?></h3>

				<p>
				
					<input name="enable-schedule" id="enable-schedule" type="checkbox" value ="true" <?php echo $options->get_option('schedule') == 'true' ? 'checked="checked"':''; ?> />
					<label for="enable-schedule"><?php _e('Enable scheduled clean-up and optimization (Beta feature!)', 'wp-optimize'); ?></label>
					
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
									<option value="<?php echo esc_attr($opt_id);?>" <?php if ($opt_id == $schedule_type_saved_id) echo 'selected="selected"';?>><?php echo htmlspecialchars($opt_description);?></option>
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

							?><p>
								<input name="wp-optimize-auto[<?php echo $auto_id;?>]" id="<?php echo $auto_dom_id;?>" type="checkbox" value="true" <?php if ($setting_activated) echo 'checked="checked"'; ?>> <label for="<?php echo $auto_dom_id;?>"><?php echo $optimization->get_auto_option_description(); ?></label>
							</p>
							<?php
						
						}
					?>
					
					<!-- disabled email notification
					<p>
						<label>
								<input name="enable-email" id="enable-email" type="checkbox" value ="true" <?php // echo $options->get_option('enable-email', 'false') == 'true' ? 'checked="checked"':''; ?> />
								<?php //_e('Enable email notification', 'wp-optimize');?>
						</label>
					</p>
					<p>
						<label for="enable-email-address">
								<?php //_e('Send email to', 'wp-optimize');?>
							<input name="enable-email-address" id="enable-email-address" type="text" value ="<?php //echo  // esc_attr( $options->get_option('enable-email-address', get_bloginfo ( 'admin_email' ) ) ); ?>" />
						</label>
					</p> -->
					
				</div>

                <h3><?php _e('Logging settings', 'wp-optimize'); ?></h3>

                <p></p>

                <div id="wp-optimize-logging-options">
                    <?php
                    $wpo_logging_options = $options->get_option('logging');

                    $loggers = $wp_optimize->get_logger()->get_loggers();

                    foreach ($loggers as $logger) {

                        $logger_id = strtolower(get_class($logger));

                        $logger_dom_id = 'wp-optimize-auto-'.$logger_id;

                        $setting_activated = (empty($wpo_logging_options[$logger_id]) || 'false' == $wpo_logging_options[$logger_id]) ? false : true;

                        ?><p>
                        <input name="wp-optimize-logging[<?php echo $logger_id;?>]" id="<?php echo $logger_dom_id;?>" type="checkbox" value="true" <?php if ($setting_activated) echo 'checked="checked"'; ?>> <label for="<?php echo $logger_dom_id;?>"><?php echo $logger->get_description(); ?></label>
                        </p>
                    <?php

                    }
                    ?>
                </div>

                <hr>

				<div id="wp-optimize-settings-save-results"></div>
				
				<input id="wp-optimize-settings-save" class="button button-primary" type="submit" name="wp-optimize-settings" value="<?php esc_attr_e('Save settings', 'wp-optimize'); ?>" />
				
				<img id="save_spinner" class="wpo_spinner" src="<?php echo esc_attr(admin_url('images/spinner.gif'));?>" alt="...">
				
				<span id="save_done" class="dashicons dashicons-yes display-none"></span>
				
			</div>
		</div>
	</div>

	</form>
	<div class="wpo_col wpo_span_1_of_3">
		<div class="postbox">
			<div class="inside">
				<h3><?php _e('Trackback/comments actions', 'wp-optimize'); ?></h3>
				
				<div id="actions-results-area"></div>
				
				<p>
					<h4><?php _e('Trackbacks', 'wp-optimize'); ?></h4>
					
					<p>
						<small><?php _e('Use these buttons to enable or disable any future trackbacks on all your previously published posts.', 'wp-optimize');?></small>
					</p>
					
					<button class="button-primary" type="button" id="wp-optimize-disable-enable-trackbacks-enable" name="wp-optimize-disable-enable-trackbacks-enable"><?php _e('Enable', 'wp-optimize'); ?></button>
					
					<button class="button-primary" type="button" id="wp-optimize-disable-enable-trackbacks-disable" name="wp-optimize-disable-enable-trackbacks-disable"><?php _e('Disable', 'wp-optimize'); ?></button>
					
					<img id="trackbacks_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner.gif'));?>" alt="...">
					
				</p>
				<p>
					<h4><?php _e('Comments', 'wp-optimize'); ?></h4>
					
					<p><small><?php _e('Use these buttons to enable or disable any future comments on all your previously published posts.', 'wp-optimize');?></small></p>

					<button class="button-primary" type="button" id="wp-optimize-disable-enable-comments-enable" name="wp-optimize-disable-enable-comments-enable"><?php _e('Enable', 'wp-optimize'); ?></button>

					<button class="button-primary" type="button" id="wp-optimize-disable-enable-comments-disable" name="wp-optimize-disable-enable-comments-disable"><?php _e('Disable', 'wp-optimize'); ?></button>
					
					<img id="comments_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner.gif'));?>" alt="...">
					
				</p>
			</div>
		</div>		
	</div>
</div>

