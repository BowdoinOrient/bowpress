<?php

if (!defined('WPO_VERSION')) die ('No direct access allowed');

global $updraftplus_admin, $updraftplus;

//check if UpdraftPlus is active
$updraftplus_status = $this->is_installed('UpdraftPlus - Backup/Restore');

//If UpdraftPlus Admin exists along with Method and active, then call the update modal
if (is_a($updraftplus_admin, 'UpdraftPlus_Admin') && is_callable(array($updraftplus_admin, 'add_backup_scaffolding'))) {
	$updraftplus_admin->add_backup_scaffolding(__('Backup before running optimizations', 'wp-optimize'), array($updraftplus_admin, 'backupnow_modal_contents'));

	//check version
	if (version_compare($updraftplus->version, '1.12.33', '<')) {
		$disabled_backup = 'disabled';
		$updraftplus_version_check = true;
	} else {
		$disabled_backup = '';
		$updraftplus_version_check = false;
	}
} else {
	//disabled UpdraftPlus
	$disabled_backup = 'disabled';
}
?>
<div class="wpo_section wpo_group">
	<form onsubmit="return confirm('<?php echo esc_js(__('Warning: This operation is permanent. Continue?', 'wp-optimize')); ?>')" action="#" method="post" enctype="multipart/form-data" name="optimize_form" id="optimize_form">
	
	<?php wp_nonce_field('wpo_optimization'); ?>
	
	<div class="wpo_col wpo_span_2_of_3">
		<div class="postbox">
			<div class="inside">
			
				<h3><?php _e('Optimizations', 'wp-optimize'); ?></h3>
				
				<p>
					<small><strong><?php _e('Warning:', 'wp-optimize'); ?></strong> 
						<?php _e('It is best practice to always make a backup of your database before any major operation (optimizing, upgrading, etc.).', 'wp-optimize'); ?>
					</small>

				</p>
				<p>
					<input class="wpo_primary_big button-primary" type="submit" id="wp-optimize" name="wp-optimize" value="<?php esc_attr_e('Run all selected optimizations', 'wp-optimize'); ?>" />
				</p>
				<p>
					<input name="enable-auto-backup" id="enable-auto-backup" type="checkbox" value ="true" <?php echo ($options->get_option('enable-auto-backup', 'false') == 'true') ? 'checked="checked"':''; ?> <?php echo $disabled_backup;?> />
					<label for="enable-auto-backup"> <?php _e('Take a backup with UpdraftPlus before optimizing', 'wp-optimize'); ?> </label>

					<br>

					<?php 
						//UpdraftPlus is not installed
						if ($disabled_backup == 'disabled' && !$updraftplus_status['installed']) {
							echo '<a href="'.wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=updraftplus'), 'install-plugin_updraftplus').'"> '.__('Follow this link to install UpdraftPlus, to take a backup before optimization', 'wp-optimize').' </a>';
						} else {
							//check updraftplus version first
							if (!empty($updraftplus_version_check)) {
								echo '<small>'.__('UpdraftPlus needs to be updated to 1.12.33 or higher in order to backup the database before optimization.', 'wp-optimize').' <a href="'.admin_url('update-core.php').'">'.__('Please update UpdraftPlus to the latest version.', 'wp-optimize').'</a></small>';
							} else {
								if($updraftplus_status['installed'] && !$updraftplus_status['active']){
									echo '<small>'.__('UpdraftPlus is installed but currently not active. Please activate UpdraftPlus to backup the database before optimization.').'</small>';
								}
							}
						}
					?>
				</p>
				
				<?php include('optimizations-table.php'); ?>
				
				<p>
					<?php echo '<span style="color: #9B0000;">'.__('Warning:', 'wp-optimize').'</span> '.__('Items marked in red perform more intensive database operations. In very rare cases, if your database server happened to crash or be forcibly powered down at the same time as an optimization operation was running, data might be corrupted. ', 'wp-optimize').' '.__('You may wish to run a backup before optimizing.', 'wp-optimize'); ?>
				</p>
			
			</div>
		</div>
	 </div>

	<div id="wp_optimize_status_box" class="wpo_col wpo_span_1_of_3">
		<div class="postbox">
			<div class="inside">
				<?php include('status-box-contents.php'); ?>
			</div>
		</div>
	</div>
	</form>
</div>
