<?php

	if (!defined('WPO_VERSION')) die ('No direct access allowed');

	$retention_enabled = $options->get_option('retention-enabled', 'false');
	$retention_period = $options->get_option('retention-period', '2');
	$admin_page_url = $options->admin_page_url();

?>

<h3><?php _e('Status', 'wp-optimize'); ?></h3>

<?php

$sqlversion = (string)$wpdb->get_var("SELECT VERSION() AS version");

echo '<em>WP-Optimize '.WPO_VERSION.' - '.__('running on:', 'wp-optimize').' PHP '.htmlspecialchars(PHP_VERSION).', '.__('MySQL', 'wp-optimize').' '.htmlspecialchars($sqlversion).' - '.htmlspecialchars(PHP_OS).'</em><br>';

echo '<p>';

$lastopt = $options->get_option('last-optimized', 'Never');
if ($lastopt !== 'Never') {
	echo __('Last automatic optimization was at', 'wp-optimize').': ';
	echo '<span style="font-color: #004600; font-weight:bold;">';
	echo htmlspecialchars($lastopt);
	echo '</span>';
} else {
	echo __('There was no automatic optimization', 'wp-optimize');
} 
?>
<br>

<?php
if ($options->get_option('schedule', 'false') == 'true') {
	echo '<strong><span style="font-color: #004600">';
	_e('Scheduled cleaning enabled', 'wp-optimize');
	echo ', </span></strong>';
	if (wp_next_scheduled('wpo_cron_event2')) {
		//$timestamp = wp_next_scheduled( 'wpo_cron_event2' );
		$wp_optimize->cron_activate();

		$timestamp = wp_next_scheduled( 'wpo_cron_event2' );
		$date = new DateTime("@$timestamp");
		_e('Next schedule:', 'wp-optimize');
		echo ' ';
		echo '<span style="font-color: #004600">';
		echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $timestamp );
		echo '</span>';
		echo ' - <a id="wp_optimize_status_box_refresh" href="'.esc_attr($admin_page_url).'">'.__('Refresh', 'wp-optimize').'</a>';
		//echo $timestamp;
	}
} else {
	echo '<strong>';
	_e('Scheduled cleaning disabled', 'wp-optimize');
	echo '</strong>';
}
echo '<br>';

if ($retention_enabled == 'true') {
	echo '<strong><span style="font-color: #0000FF;">';
	printf(__('Keeping last %s weeks data', 'wp-optimize'), $retention_period) ;
	echo '</span></strong>';
} else {
	echo '<strong>'.__('Not keeping recent data', 'wp-optimize').'</strong>';
}
?>
</p>

<p>
<strong><?php _e('Current database size:', 'wp-optimize');?></strong>

	<?php
	
	list ($total_size, $total_gain) = $optimizer->get_current_db_size();
	echo ' <span class="current-database-size">'.$total_size.'</span> <br>';
	
	if ($optimize_db) {
		_e('You have saved:', 'wp-optimize');
		echo ' <span style="font-color: #0000FF;">'.$total_gain.'</span>';
	} else {
		if ($total_gain > 0) {
			_e('You can save around:', 'wp-optimize');
			echo ' <span style="font-color: #9B0000;">'.$total_gain.'</span> ';
		}
	}
	?>

</p>
<p>
<?php
	$total_cleaned = $options->get_option('total-cleaned');
	$total_cleaned_num = floatval($total_cleaned);

	if ($total_cleaned_num  > 0) {
		echo '<h5>'.__('Total clean up overall:','wp-optimize').' ';
		echo '<span style="font-color: #004600">';
		echo $wp_optimize->format_size($total_cleaned);
		echo '</span></h5>';
	}
?></p>


<h3><?php _e('Support and feedback', 'wp-optimize');?></h3>
<p>
	<?php echo __('If you like WP-Optimize,', 'wp-optimize').' <a href="https://wordpress.org/support/plugin/wp-optimize/reviews/?rate=5#new-post" target="_blank">'.__('please give us a positive review, here.', 'wp-optimize'); ?></a> <?php echo __('Or, if you did not like it,', 'wp-optimize').' <a target="_blank" href="https://wordpress.org/support/plugin/wp-optimize/">'.__('please tell us why at this link.', 'wp-optimize'); ?></a>
	<a href="https://wordpress.org/support/plugin/wp-optimize/"><?php _e('Support is available here.', 'wp-optimize'); ?></a>
</p>
