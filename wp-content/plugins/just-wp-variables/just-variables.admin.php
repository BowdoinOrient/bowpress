<?php
	
	/**
	 *	init admin for just variables page
	 */
	function jv_admin_menu(){
		add_options_page(__('Just Variables', JV_TEXTDOMAIN), __('Just Variables', JV_TEXTDOMAIN), 'manage_options', 'just_variables', 'jv_admin_settings_page');
	}
	
	/**
	* Properly enqueue styles and scripts for our theme options page.
	*/
	add_action( 'admin_print_styles', 'jv_admin_styles' );
	function jv_admin_styles( $hook_suffix ) {
		wp_enqueue_style( 'just_variables', plugins_url( 'assets/styles.css' , __FILE__ ) );
	}

	add_action( 'admin_print_scripts', 'jv_admin_scripts' );
	function jv_admin_scripts( $hook_suffix ) {
		wp_enqueue_script( 'just_variables',
				plugins_url( 'assets/settings_page.js' , __FILE__ ),
				array( 'jquery', 'jquery-ui-sortable' ) );
		
		// add text domain
		wp_localize_script( 'just_variables', 'text_just_variables', jv_get_language_strings() );
	}
	
	/**
	 *	translation strings for javascript
	 */
	function jv_get_language_strings(){
		$strings = array(
			'confirm_delete' => __('Are you sure you want to delete selected field?', JV_TEXTDOMAIN),
		);
		return $strings;
	}
	
	/**
	 *	show variables settings page
	 */
	function jv_admin_settings_page(){
		
		// Form submit processing
		if( !empty($_POST['submitted']) && !empty($_POST['jv_settings']) ){

			$post = array_map( 'stripslashes_deep', $_POST['jv_settings']);
			// update database with new values
			$variables = array();
			if( !empty($post['slug']) ){
				foreach($post['slug'] as $key => $slug){
					if( $key == 0 ) continue; // 0 index is empty row for copy
					
					$variables[ $slug ] = array(
						'type' => $post['type'][$key],
						'slug' => $post['slug'][$key],
						'name' => $post['title'][$key],
						'default' => $post['default'][$key],
						'placeholder' => $post['placeholder'][$key],
					);
				}
				//pa($variables,1);

				// update DB
				update_option('jv_variables', $variables);
				
				// check if we have variables - if no - delete all values
				if( empty($variables) ){
					update_option('jv_values', array());
				}
			}
		}
		
		$variables = get_option('jv_variables', array());
		
		// load template
		include( JV_ROOT . '/templates/settings_page.tpl.php' );
	}


?>