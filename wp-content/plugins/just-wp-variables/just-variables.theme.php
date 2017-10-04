<?php


/**
 * Add our theme options page to the admin menu
 */
add_action( 'admin_menu', 'jv_theme_variables_menu' );
function jv_theme_variables_menu() {

	// if we don't have theme variables => we don't need this page
	$theme_variables = get_option('jv_variables');
	if( empty($theme_variables) ) return;

	$theme_page = add_theme_page(
		__( 'Theme Variables', JV_TEXTDOMAIN ),   // Name of page
		__( 'Theme Variables', JV_TEXTDOMAIN ),   // Label in menu
		'edit_theme_options',                    // Capability required
		'jv_theme_vars',                         // Menu slug, used to uniquely identify the page
		'jv_theme_vars_admin_page' 			// Function that renders the options page
	);
	//pa($theme_page,1);

	add_action( 'admin_init', 'jv_theme_variables_register_settins' );
}


/**
 *	register new options page settings
 */
function jv_theme_variables_register_settins(){

	register_setting( 'jv_theme_vars', 'jv_values' );

}


/**
 *	page html
 */
function jv_theme_vars_admin_page(){
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Variables', JV_TEXTDOMAIN ), wp_get_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'jv_theme_vars' );
				do_settings_sections( 'jv_theme_vars' );

				$theme_variables = get_option('jv_variables');
				$values = get_option('jv_values');
			?>
			<p><?php _e('You can use the text variables below in your template files or post content with shortcode.', JV_TEXTDOMAIN ); ?></p>
			<p><?php _e('Template files usage example: <code>&lt;?php just_variable( "code for variable" ); ?&gt;</code><br/>Get variable value (no print): <code>&lt;?php just_variable( "code for variable", FALSE ); ?&gt;</code><br/>Shortcode example: <code>[justvar code="code for variable"]</code>', JV_TEXTDOMAIN ); ?></p>
			<table class="form-table">
			<tbody>
				<?php
					foreach($theme_variables as $slug => $var) :
						$value = isset($values[$slug])? $values[$slug] : $var['default'];
				?>
				<tr valign="top">
					<th scope="row"><?php echo $var['name']; ?><br/><small><?php _e('code:', JV_TEXTDOMAIN); echo ' '. $slug; ?></small></th>
					<td>
						<?php if( $var['type'] == 'text' ) : ?>
							<input class="regular-text" type="text" placeholder="<?php echo esc_attr($var['placeholder']); ?>" value="<?php echo esc_attr($value); ?>" name="jv_values[<?php echo $slug; ?>]">
						<?php elseif( $var['type'] == 'textarea' ) : ?>
							<textarea placeholder="<?php echo esc_attr($var['placeholder']); ?>" name="jv_values[<?php echo $slug; ?>]" cols="80" rows="3"><?php echo esc_html($value); ?></textarea>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

?>
