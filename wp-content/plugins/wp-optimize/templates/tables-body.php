<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<tbody id="the-list">
<?php
	$alternate = ' class="alternate"';

	// Read SQL Version and act accordingly
	// Check for InnoDB tables
	// Check for windows servers
	$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
	$total_gain = 0;
	$no = 0;
	$row_usage = 0;
	$data_usage = 0;
	$index_usage = 0;
	$overhead_usage = 0;
	$non_inno_db_tables = 0;
	$inno_db_tables = 0;
	
	$tablesstatus = $optimizer->get_tables();
	
	foreach ($tablesstatus as $tablestatus) {
	
		$style = (0 == $no % 2) ? '' : ' class="alternate"';
		
		$no++;
		echo "<tr$style>\n";
		echo '<td>'.number_format_i18n($no).'</td>'."\n";
		echo "<td>".htmlspecialchars($tablestatus->Name)."</td>\n";
		echo '<td>'.number_format_i18n($tablestatus->Rows).'</td>'."\n";
		echo '<td>'.$wp_optimize->format_size($tablestatus->Data_length).'</td>'."\n";
		echo '<td>'.$wp_optimize->format_size($tablestatus->Index_length).'</td>'."\n";;

		if ($tablestatus->Engine != 'InnoDB') {
			echo '<td data-optimizable="1">'.htmlspecialchars($tablestatus->Engine).'</td>'."\n";

			echo '<td>';
			$font_colour = $optimize_db ? (($tablestatus->Data_free>0) ? '#0000FF' : '#004600') : (($tablestatus->Data_free>0) ? '#9B0000' : '#004600');
			echo '<span style="color:'.$font_colour.';">';
			echo $wp_optimize->format_size($tablestatus->Data_free);
			echo '</span>';
			echo '</td>'."\n";

			$overhead_usage += $tablestatus->Data_free;
			$total_gain += $tablestatus->Data_free;
			$non_inno_db_tables++;
		} else {
			echo '<td data-optimizable="0">'.htmlspecialchars($tablestatus->Engine).'</td>'."\n";
			echo '<td>';
			echo '<span style="color:#0000FF;">-</span>';
			echo '</td>'."\n";

			$inno_db_tables++;
		}

		$row_usage += $tablestatus->Rows;
		$data_usage += $tablestatus->Data_length;
		$index_usage +=  $tablestatus->Index_length;

		echo '</tr>'."\n";
	}

	echo '<tr class="thead">'."\n";
	echo '<th>'.__('Total:', 'wp-optimize').'</th>'."\n";
	echo '<th>'.sprintf(_n('%d Table', '%d Tables', $no, 'wp-optimize'), number_format_i18n($no)).'</th>'."\n";
	echo '<th>'.sprintf(_n('%d Record', '%d Records', $row_usage, 'wp-optimize'), number_format_i18n($row_usage)).'</th>'."\n";
	echo '<th>'.$wp_optimize->format_size($data_usage).'</th>'."\n";
	echo '<th>'.$wp_optimize->format_size($index_usage).'</th>'."\n";
	echo '<th>'.'-'.'</th>'."\n";
	echo '<th>';

	$font_colour = $optimize_db ? (($overhead_usage>0) ? '#0000FF' : '#004600') : (($overhead_usage>0) ? '#9B0000' : '#004600');
	
	echo '<span style="color:'.$font_colour.'">'.$wp_optimize->format_size($overhead_usage).'</span>';
	
	?>
	</th>
	</tr>
</tbody>
