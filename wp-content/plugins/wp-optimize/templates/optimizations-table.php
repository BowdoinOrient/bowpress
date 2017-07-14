<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<table id="optimizations_list" class="widefat">
	<thead>
		<tr>
			<th></th>
			<th><?php _e('Optimization', 'wp-optimize'); ?></th>
			<th><?php _e('Notes', 'wp-optimize'); ?></th>
			<th></th>
	<!--		<th></th>-->
		</tr>
	</thead>
	<tbody>
	<?php
		$optimizations = $optimizer->sort_optimizations($optimizer->get_optimizations());

		foreach ($optimizations as $id => $optimization) {
		
			// This is an array, with attributes dom_id, activated, settings_label, info; all values are strings
			$html = $optimization->get_settings_html();

			$optimize_table_list_disabled = '';

			//check if the DOM is optimize-db to generate a list of tables
			if ($html['dom_id'] == 'optimize-db') {
				$table_list = $optimizer->get_table_information();

				//make sure that optimization_table_inno_db is set
				if ($table_list['inno_db_tables'] > 0 && $table_list['table_list'] == '') {
					$optimize_table_list_disabled .= 'disabled';
					$html['activated'] = '';
				}

			}
			?><tr class="wp-optimize-settings wp-optimize-settings-<?php echo $html['dom_id'];?>" id="wp-optimize-settings-<?php echo $html['dom_id'];?>" data-optimization_id="<?php echo esc_attr($id);?>" data-optimization_run_sort_order="<?php echo $optimization->get_run_sort_order();?>" >
			<?php
			
				if (!empty($html['settings_label'])) {
					?>
				
					<td class="wp-optimize-settings-optimization-checkbox">
						<input name="<?php echo $html['dom_id'];?>" id="optimization_checkbox_<?php echo $id;?>" class="optimization_checkbox" type="checkbox" value="true" <?php if ($html['activated']) echo 'checked="checked"';?> <?php echo $optimize_table_list_disabled;?> >
						
						<img id="optimization_spinner_<?php echo $id;?>" class="optimization_spinner display-none" src="<?php echo esc_attr(admin_url('images/spinner.gif'));?>" alt="...">
					</td>
				
					<td>
						<label for="optimization_checkbox_<?php echo $id;?>"><?php echo $html['settings_label']; ?></label>
						
					</td>

					<td id="optimization_info_<?php echo $id;?>" class="wp-optimize-settings-optimization-info"><?php
						$info = $html['info'];
						$first_one = true;
						foreach ($info as $key => $line) {
							if ($first_one) { $first_one = false; } else { echo '<br>'; }
							echo $line;
						}
					?></td>
					
					<td class="wp-optimize-settings-optimization-run">
						<button id="optimization_button_<?php echo $id;?>_big" class="button button-secondary wp-optimize-settings-optimization-run-button show_on_default_sizes optimization_button_<?php echo $id;?>" type="button" <?php echo $optimize_table_list_disabled;?> ><?php _e('Run optimization', 'wp-optimize');?></button>
						
						<button id="optimization_button_<?php echo $id;?>_small" class="button button-secondary wp-optimize-settings-optimization-run-button show_on_mobile_sizes optimization_button_<?php echo $id;?>" type="button" <?php echo $optimize_table_list_disabled;?> ><?php _e('Go', 'wp-optimize');?></button>
						
					</td>
					
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>
