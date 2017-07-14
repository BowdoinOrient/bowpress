<?php

require_once( 'Postmatic_Social_Generic_Settings_Tab.php' );

class Postmatic_Social_Comments_Plugin {

	private $messages;
	private $tabs;

	public function __construct( $supported_sn ) {
		$this->init( $supported_sn );
		$this->register_actions();
	}

	protected function init( $supported_sn ) {
		$this->messages = array();
		$this->tabs = array();
		$generic_settings_tab = new Postmatic_Social_Generic_Settings_Tab();
		// FK Hide general settings page
		$this->tabs[ $generic_settings_tab->get_id() ] = $generic_settings_tab;
		foreach ( $supported_sn as $sn_id ) {
			$class_name = 'Postmatic_Social_' . ucfirst( $sn_id ) . '_Authenticator';
			include_once( $class_name . '.php' );
			if ( class_exists( $class_name ) ) {
				$this->tabs[ $sn_id ] = new $class_name();
			}
		}
	}

	function get_title() {
		return __( 'Postmatic Social Commenting', 'postmatic-social' );
	}

	function get_slug() {
		return 'postmatic-social';
	}

	function register_actions() {
		if ( is_user_logged_in() ) {
			$form_action = $this->get_slug() . '-action';
			add_action( 'wp_ajax_' . $form_action, array( $this, 'process_form_submission' ) );
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_fontawesome' ), 900 );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'maybe_fontawesome' ), 900 );
			add_filter( 'wp_get_current_commenter', array( $this, 'wp_get_current_commenter' ) );
			add_filter( 'comments_open', array( $this, 'comments_open' ), 10, 2 );
			add_filter( 'comment_form_field_comment', array( $this, 'add_social_options' ), 99 );
			add_action( 'preprocess_comment', array( $this, 'preprocess_comment' ) );
		}
	}

	function register_menu() {
		$title = $this->get_title();
		$slug = $this->get_slug();
		add_options_page( $title, $title, 'manage_options', $slug, array( $this, 'render_plugin_page' ) );
	}

	function render_plugin_page() {
		$page_id = $this->get_slug();
		$form_action = $page_id . '-action';
		$tabs = $this->tabs;
		echo '<div class="wrap">';
		// FK add postmatic image
		printf( '<div style="position: absolute;right: 25px; top: 25px;"><a href="https://gopostmatic.com" target="_blank"><img src="%s" width="125"></a></div>', esc_url( Postmatic_Social::get_plugin_url( '/images/postmatic-logo.png' ) ) );
		echo '<div class="icon32" id="icon-themes"></div>';
		echo '<h2 style="margin: 25px 0;">' . __( 'Postmatic Social Commenting', 'postmatic-social' ) . '</h2>';
		echo '<div class="updated below-h2 pms-flexslider-list-message" style="display: none;"><p></p></div>';
		echo '<div class="error below-h2 pms-flexslider-list-message" style="display: none;"><p></p></div>';

		$selected_tab_id = '';
		if ( array_key_exists( 'tab', $_REQUEST ) ) {
			$selected_tab_id = $_REQUEST['tab'];
		}
		if ( !array_key_exists( $selected_tab_id, $tabs ) ) {
			reset( $tabs );
			$selected_tab_id = key( $tabs );
		}
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab_id => $tab_instance ) {
			$tab_title = $tab_instance->get_title();
			$selected_tab = '';
			if( $selected_tab_id == $tab_id ) {
    			$selected_tab = 'nav-tab-active';
            }
            $tab_url = add_query_arg( array( 'page' => $page_id, 'tab' => $tab_id ), admin_url( 'options-general.php' ) );
			printf( '<a class="nav-tab %s" href="%s">%s</a>', $selected_tab, esc_url( $tab_url ), $tab_title );
		}
		echo '</h2>';
		echo '<div style="margin-left: 10px; margin-top: 10px;">';
		echo '<form id="theme-settings-form" method="post" action="' . esc_url( admin_url( "admin-ajax.php?action=$form_action" ) ) . '">';
		echo '<input type="hidden" name="tab" value="' . esc_attr( $selected_tab_id ) . '">';
		wp_nonce_field( $form_action, $page_id );
		$tabs[ $selected_tab_id ]->render_settings_admin_page();
		echo '<p><input type="submit" class="button-primary" value="' . __( 'Save Settings', 'postmatic-social' ) . '"></p>';
		echo '</form>';
		echo '</div>';

		echo '</div>';
	}

	function process_form_submission() {
		$tabs = $this->tabs;
		$referrer = wp_get_referer();
		$tab_id = $_REQUEST['tab'];
		$page_id = $this->get_slug();
		$form_action = $page_id . '-action';
		if ( !empty( $_POST ) && check_admin_referer( $form_action, $page_id ) ) {
			if ( array_key_exists( $tab_id, $tabs ) ) {
				$tabs[$tab_id]->save_settings();
			}
			header( 'Location: ' . $referrer );
		} else {
			header( 'Location: ' . $referrer );
		}
		die();
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script(
			'postmatic-social-login-admin',
			Postmatic_Social::get_plugin_url( '/js/postmatic-social-login-admin.js' ),
			array( 'jquery' ),
			'20151026',
			true
		);
		//styles		
		wp_enqueue_style(
			'postmatic-social-login-toggles',
			Postmatic_Social::get_plugin_url( '/css/toggles-full.css' ),
			array(),
			'20151026'
		);
		wp_enqueue_style(
			'postmatic-social-login',
			Postmatic_Social::get_plugin_url( '/css/postmatic-social-login.css' ),
			array( 'postmatic-social-login-toggles' ),
			'20151026'
		);
	}

	function wp_enqueue_scripts() {
		wp_enqueue_script(
			'postmatic-social-login',
			Postmatic_Social::get_plugin_url( '/js/postmatic-social-login.js' ),
			array( 'jquery' ),
			'20151125',
			true
		);
		//styles		
		wp_enqueue_style(
			'postmatic-social-login',
			Postmatic_Social::get_plugin_url( '/css/postmatic-social-login.css' ),
			array(),
			'20151102'
		);
	}

	function maybe_fontawesome() {
		global $wp_styles;

		$registered_handle = '';

		foreach ( $wp_styles->registered as $handle => $style ) {
			if ( 'font-awesome.' == substr( $style->src, 0, 13 ) ) {
				$registered_handle = $handle;
			}
		}

		if ( ! $registered_handle or ! wp_script_is( $registered_handle, 'enqueued' ) ) {
			wp_enqueue_style(
				'postmatic-social-font-awesome',
				Postmatic_Social::get_plugin_url( '/css/font-awesome.min.css' ),
				array(),
				'20151102'
			);
		}
	}


	function comments_open( $open, $post_id ) {
		global $pms_post_protected;
		if ( is_user_logged_in() ) {
			return $open;
		}
		if ( $open ) {
			// FK Enable plugin by default
			// FK Always show comments
			$pms_post_protected = true;
			return true;

		} else {
			return $open;
		}
	}

	function add_social_options( $field ) {

		global $pms_session;

		global $pms_post_protected;
		// If not comments Enabled and logged in leave alone
		if ( !$pms_post_protected ) {
			return $field;
		}

		$content = '';
		$commenter = $this->sc_get_current_commenter();
		if ( isset( $commenter ) ) {

			if ( array_key_exists( 'post_id', $_REQUEST ) ) {
				$post_id = $_REQUEST[ 'post_id' ];
			} else {
				$post_id = get_the_ID();
			}

			$content .= $this->disconnect_content( $commenter, $post_id );

		} else {
			$content .= '<div id="postmatic-social-comment-wrapper">';
			$content .= '<div class="postmatic-social-comment-buttons">';
			$tabs = $this->tabs;
			// Get Settings
			$settings = get_option( "postmatic-social" );
			foreach ( $tabs as $id => $instance ) {
				if ( $instance instanceof Postmatic_Social_Network_Authenticator && isset( $settings[ $instance->network ][ 'pms_enabled' ] ) && $settings[ $instance->network ][ 'pms_enabled' ] == "on" ) {
					$content .= $instance->get_auth_button();
				}
			}
			$content .= '</div>';
			$content .= '<p class="postmatic-social-comment-wait" style="display: none;">';
			$content .= '<i class="fa fa-spinner fa-spin"></i> ';
			$content .= __( 'Please wait while you are being authenticated...', 'postmatic-social' ) . '</p>';
			$content .= '</div>';
		}

		return $content . $field;
	}

	function disconnect_content( $commenter, $post_id ) {
		$referrer = esc_attr( get_permalink( $post_id ) );
		$logout_url = admin_url(
			'admin-ajax.php?action=pms-logout&amp;_wp_http_referer=' . $referrer .
			'#postmatic-social-comment-wrapper'
		);
		$content = sprintf(
			'<div id="postmatic-social-comment-wrapper" data-network="%s">',
			$commenter['network']
		);
		$content .= '<p class="postmatic-social-comment-logout">';
		$content .= sprintf(
			__( 'You are authenticated as %s via %s.', 'postmatic-social' ),
			$commenter[ 'display_name' ],
			$commenter[ 'network' ]
		);
		$content .= '<a href="' . esc_url( $logout_url ) . '">' . __( 'Disconnect', 'postmatic-social' ) . '</a>';
		$content .= '</p>';
		$content .= '</div>';

		// FK Hide completed comment fields
		$content .= '<style>.comment-form-author, .comment-form-email, .comment-form-url {display:none;}</style>';

		return $content;
	}

	function preprocess_comment( $comment_data ) {
		if ( is_array( $comment_data ) ) {
			$sc_commenter = $this->sc_get_current_commenter();
			if ( isset( $sc_commenter ) ) {
				return array_merge( $comment_data, array(
					'comment_author' => empty( $sc_commenter['display_name'] ) ? $comment_data['comment_author'] : $sc_commenter['display_name'],
					'comment_author_email' => empty( $sc_commenter['email'] ) ? $comment_data['comment_author_email'] : $sc_commenter['email'],
					'comment_author_url' => empty( $sc_commenter['profile_url'] ) ? $comment_data['comment_author_url'] : $sc_commenter['profile_url'],
				) );
			}
		}
		return $comment_data;
	}

	function wp_get_current_commenter( $wp_commenter ) {
		global $pms_post_protected;
		if ( $pms_post_protected ) {
			$sc_commenter = $this->sc_get_current_commenter();
			return array(
				'comment_author' => empty( $sc_commenter['display_name'] ) ? $wp_commenter['comment_author'] : $sc_commenter['display_name'],
				'comment_author_email' => empty( $sc_commenter['email'] ) ? $wp_commenter['comment_author_email'] : $sc_commenter['email'],
				'comment_author_url' => empty( $sc_commenter['profile_url'] ) ? $wp_commenter['comment_author_url'] : $sc_commenter['profile_url'],
			);
		} else {
			return $wp_commenter;
		}
	}

	function sc_get_current_commenter() {
		global $pms_session;
		$commenter = $pms_session[POSTMATIC_SOCIAL_SESSION_USER];
		return isset( $commenter ) ? $commenter : NULL;
	}

}