<?php
if (!defined('WPO_VERSION')) die('No direct access allowed');

?>
<div class="wpo_section wpo_group">
	<form onsubmit="return confirm('<?php echo esc_js(__('Warning: This operation is permanent. Continue?', 'wp-optimize')); ?>')" action="#" method="post" enctype="multipart/form-data" name="optimize_form" id="optimize_form">
	
	<?php wp_nonce_field('wpo_optimization'); ?>
	
	<div class="wpo_col wpo_span_2_of_3">
		<div class="postbox">
			<div class="inside">
			
				<h3><?php _e('Optimizations', 'wp-optimize'); ?></h3>

				<p>
					<?php $button_caption = apply_filters('wpo_run_button_caption', __('Run all selected optimizations', 'wp-optimize')); ?>
					<input class="wpo_primary_big button-primary" type="submit" id="wp-optimize" name="wp-optimize" value="<?php echo esc_attr($button_caption); ?>" />
				</p>

				<?php do_action('wpo_additional_options'); ?>
				<?php require('take-a-backup.php'); ?>
				<?php require('optimizations-table.php'); ?>
				
				<p>
					<?php echo '<span style="color: #9B0000;">'.__('Warning:', 'wp-optimize').'</span> '.__('Items marked in red perform more intensive database operations. In very rare cases, if your database server happened to crash or be forcibly powered down at the same time as an optimization operation was running, data might be corrupted. ', 'wp-optimize').' '.__('You may wish to run a backup before optimizing.', 'wp-optimize'); ?>
				</p>
			
			</div>
		</div>
	 </div>

	<div id="wp_optimize_status_box" class="wpo_col wpo_span_1_of_3">
		<div class="postbox">
			<div class="inside">
				<?php require('status-box-contents.php'); ?>
			</div>
		</div>
	</div>
	</form>
</div>
