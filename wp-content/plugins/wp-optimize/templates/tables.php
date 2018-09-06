<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<h3><?php echo __('Database name:', 'wp-optimize')." '".htmlspecialchars(DB_NAME)."'"; ?><span id="wp_optimize_table_list_refresh" class="dashicons dashicons-image-rotate"></span></h3>

<?php
// This next bit belongs somewhere else, I think.
?>
<?php if ($optimize_db) { ?>
	<p><?php _e('Optimized all the tables found in the database.', 'wp-optimize'); ?></p>
<?php } ?>
<?php

$table_information = WP_Optimize()->get_optimizer()->get_table_information();

do_action('wpo_tables_list_before', $table_information);
?>
<input id="wpoptimize_table_list_filter" class="search" type="search" value="" placeholder="<?php esc_attr_e('Search for table', 'wp-optimize'); ?>" data-column="1" />
<table id="wpoptimize_table_list" class="widefat tablesorter">
	<thead>
		<tr>
			<th><?php _e('No.', 'wp-optimize'); ?></th>
			<th><?php _e('Table', 'wp-optimize'); ?></th>
			<th><?php _e('Records', 'wp-optimize'); ?></th>
			<th><?php _e('Data Size', 'wp-optimize'); ?></th>
			<th><?php _e('Index Size', 'wp-optimize'); ?></th>
			<th><?php _e('Type', 'wp-optimize'); ?></th>
			<th><?php _e('Overhead', 'wp-optimize'); ?></th>
			<th><?php _e('Actions', 'wp-optimize'); ?></th>
		</tr>
	</thead>
	
	<?php require('tables-body.php'); ?>

</table>

<div id="wpoptimize_table_list_tables_not_found"><?php _e('Tables not found.', 'wp-optimize'); ?></div>
<h3><?php _e('Total size of database:', 'wp-optimize'); ?> <span id="optimize_current_db_size">
	<?php
	list ($part1, $part2) = $optimizer->get_current_db_size();
	echo $part1;
?>
</span></h3>

<?php
if ($optimize_db) {
	?>

	<h3><?php _e('Optimization results:', 'wp-optimize'); ?></h3>
	<p style="color: #0000ff;" id="optimization_table_total_gain">
	<?php
	if ($total_gain > 0) {
		echo __('Total space saved:', 'wp-optimize').' <span>'.$wp_optimize->format_size($total_gain).'</span> ';
		$optimizer->update_total_cleaned(strval($total_gain));
	}
}
?>
</p>
