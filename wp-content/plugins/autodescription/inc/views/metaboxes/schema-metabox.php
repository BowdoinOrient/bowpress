<?php

defined( 'ABSPATH' ) and $_this = the_seo_framework_class() and $this instanceof $_this or die;

//* Fetch the required instance within this file.
$instance = $this->get_view_instance( 'the_seo_framework_schema_metabox', $instance );

switch ( $instance ) :
	case 'the_seo_framework_schema_metabox_main' :
		?>
		<h4><?php esc_html_e( 'Schema.org Output Settings', 'autodescription' ); ?></h4>
		<?php

		if ( $this->has_json_ld_plugin() ) :
			$this->description( __( 'Another Schema.org plugin has been detected.', 'autodescription' ) );
		else :
			$this->description( __( 'The Schema.org markup is a standard way of annotating structured data for Search Engines. This markup is represented within hidden scripts throughout the website.', 'autodescription' ) );
			$this->description( __( 'When your web pages include structured data markup, search engines can use that data to index your content better, present it more prominently in Search Results, and use it in several different applications.', 'autodescription' ) );
			$this->description( __( 'This is also known as the "Knowledge Graph" and "Structured Data", which is under heavy active development by several Search Engines. Therefore, the usage of the outputted markup is not guaranteed.', 'autodescription' ) );

			/**
			 * Parse tabs content.
			 *
			 * @since 2.8.0
			 *
			 * @param array $default_tabs { 'id' = The identifier =>
			 *    array(
			 *       'name'     => The name
			 *       'callback' => The callback function, use array for method calling
			 *       'dashicon' => Desired dashicon
			 *    )
			 * }
			 */
			$default_tabs = array(
				// 'general' => array(
				// 	'name'     => __( 'General', 'autodescription' ),
				// 	'callback' => array( $this, 'schema_metabox_general_tab' ),
				// 	'dashicon' => 'admin-generic',
				// ),
				'structure' => array(
					'name'     => __( 'Structure', 'autodescription' ),
					'callback' => array( $this, 'schema_metabox_structure_tab' ),
					'dashicon' => 'admin-multisite',
				),
				'presence' => array(
					'name'     => __( 'Presence', 'autodescription' ),
					'callback' => array( $this, 'schema_metabox_presence_tab' ),
					'dashicon' => 'networking',
				),
			);

			/**
			 * Applies filter 'the_seo_framework_schema_settings_tabs' : Array
			 * @since 2.8.0
			 * Used to extend Schema settings tabs
			 */
			$defaults = (array) apply_filters( 'the_seo_framework_schema_settings_tabs', $default_tabs, $args );

			$tabs = wp_parse_args( $args, $defaults );

			$this->nav_tab_wrapper( 'schema', $tabs, '2.8.0' );
		endif;
		break;

	case 'the_seo_framework_schema_metabox_general' :
		//* Emptied @ 3.0.0
		break;

	case 'the_seo_framework_schema_metabox_structure' :
		?>
		<h4><?php esc_html_e( 'Site Structure Options', 'autodescription' ); ?></h4>
		<?php
		$this->description( __( 'The site structure Schema.org output allows search engines to gain knowledge on how your website is built.', 'autodescription' ) );
		$this->description( __( "For example, search engines display your pages' URLs when listed in the Search Results. These options allow you to enhance those URLs output.", 'autodescription' ) );
		?>
		<hr>
		<h4><?php esc_html_e( 'Breadcrumbs', 'autodescription' ); ?></h4>
		<?php
		$this->description( __( "Breadcrumb trails indicate page positions in the site's hierarchy. Using the following option will show the hierarchy within the Search Results when available.", 'autodescription' ) );

		$info = $this->make_info( __( 'Learn how this data is used.', 'autodescription' ), 'https://developers.google.com/search/docs/data-types/breadcrumbs', false );
		$this->wrap_fields( $this->make_checkbox(
			'ld_json_breadcrumbs',
			esc_html__( 'Enable Breadcrumbs?', 'autodescription' ) . ' ' . $info,
			'',
			false
		), true );

		?>
		<hr>
		<h4><?php echo esc_html( _x( 'Sitelinks Searchbox', 'Product name', 'autodescription' ) ); ?></h4>
		<?php
		$this->description( __( 'When Search users search for your brand name, the following option allows them to search through this website directly from the Search Results.', 'autodescription' ) );

		$info = $this->make_info( __( 'Learn how this data is used.', 'autodescription' ), 'https://developers.google.com/search/docs/data-types/sitelinks-searchbox', false );
		$this->wrap_fields( $this->make_checkbox(
			'ld_json_searchbox',
			esc_html_x( 'Enable Sitelinks Searchbox?', 'Sitelinks Searchbox is a Product name', 'autodescription' ) . ' ' . $info,
			'',
			false
		), true );
		break;

	case 'the_seo_framework_schema_metabox_presence' :
		?>
		<h4><?php esc_html_e( 'Authorized Presence Options', 'autodescription' ); ?></h4>
		<?php
		$this->description( __( 'The authorized presence Schema.org output helps Search Engine users find ways to interact with this website.', 'autodescription' ) );

		$info = $this->make_info( __( 'Learn how this data is used.', 'autodescription' ), 'https://developers.google.com/search/docs/guides/enhance-site#add-your-sites-name-logo-and-social-links', false );
		//* Echo checkbox.
		$this->wrap_fields( $this->make_checkbox(
			'knowledge_output',
			esc_html__( 'Output Authorized Presence?', 'autodescription' ) . ' ' . $info,
			'',
			false
		), true );
		?>
		<hr>

		<h4><?php esc_html_e( 'About this website', 'autodescription' ); ?></h4>
		<p>
			<label for="<?php $this->field_id( 'knowledge_type' ); ?>"><?php echo esc_html_x( 'This website represents:', '...Organization or Person.', 'autodescription' ); ?></label>
			<select name="<?php $this->field_name( 'knowledge_type' ); ?>" id="<?php $this->field_id( 'knowledge_type' ); ?>">
				<?php
				$knowledge_type = (array) apply_filters(
					'the_seo_framework_knowledge_types',
					array(
						'organization' => __( 'An Organization', 'autodescription' ),
						'person'       => __( 'A Person', 'autodescription' ),
					)
				);
				foreach ( $knowledge_type as $value => $name ) {
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->get_field_value( 'knowledge_type' ), esc_attr( $value ), false ) . '>' . esc_html( $name ) . '</option>' . "\n";
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php $this->field_id( 'knowledge_name' ); ?>">
				<strong><?php esc_html_e( 'The organization or personal name', 'autodescription' ); ?></strong>
			</label>
		</p>
		<p>
			<input type="text" name="<?php $this->field_name( 'knowledge_name' ); ?>" class="large-text" id="<?php $this->field_id( 'knowledge_name' ); ?>" placeholder="<?php echo esc_attr( $this->get_blogname() ) ?>" value="<?php echo esc_attr( $this->get_field_value( 'knowledge_name' ) ); ?>" />
		</p>
		<hr>

		<h4><?php esc_html_e( 'Website logo', 'autodescription' ); ?></h4>
		<?php
		$this->description( esc_html__( 'These options are used when this site represents an organization. When no logo is outputted, search engine will look elsewhere.', 'autodescription' ) );
		$info = $this->make_info( __( 'Learn how this data is used.', 'autodescription' ), 'https://developers.google.com/search/docs/data-types/logo', false );
		$this->wrap_fields( $this->make_checkbox(
			'knowledge_logo',
			esc_html__( 'Enable logo?', 'autodescription' ) . ' ' . $info,
			'',
			false
		), true );

		$logo_placeholder = $this->get_knowledge_logo( false );
		?>
		<p>
			<label for="knowledge_logo-url">
				<strong><?php esc_html_e( 'Logo URL', 'autodescription' ); ?></strong>
				<?php $this->make_info( __( 'The logo image must be square.', 'autodescription' ) ); ?>
			</label>
		</p>
		<p>
			<span class="hide-if-js attention"><?php esc_html_e( 'Setting a logo requires JavaScript.', 'autodescription' ); ?></span>
			<input class="large-text" type="text" readonly="readonly" data-readonly="1" name="<?php $this->field_name( 'knowledge_logo_url' ); ?>" id="knowledge_logo-url" placeholder="<?php echo esc_url( $logo_placeholder ); ?>" value="<?php echo esc_url( $this->get_field_value( 'knowledge_logo_url' ) ); ?>" />
			<input type="hidden" name="<?php $this->field_name( 'knowledge_logo_id' ); ?>" id="knowledge_logo-id" value="<?php echo absint( $this->get_field_value( 'knowledge_logo_id' ) ); ?>" />
		</p>
		<p class="hide-if-no-js">
			<?php
			//* Already escaped.
			echo $this->get_logo_uploader_form( 'knowledge_logo' );
			?>
		</p>
		<hr>

		<h4><?php esc_html_e( 'Connected Social Pages', 'autodescription' ); ?></h4>
		<?php
		$this->description( __( "Don't have a page at a site or is the profile only privately accessible? Leave that field empty. Unsure? Fill it in anyway.", 'autodescription' ) );
		$this->description( __( 'Add the link that leads directly to the social page of this website.', 'autodescription' ) );
		$this->description( __( 'These settings do not affect sharing behavior with the social networks.', 'autodescription' ) );

		$connectedi18n = _x( 'RelatedProfile', 'No spaces. E.g. https://facebook.com/RelatedProfile', 'autodescription' );
		$profile18n = _x( 'Profile', 'Social Profile', 'autodescription' );

		/**
		 * @todo maybe genericons?
		 */
		$socialsites = array(
			'facebook' => array(
				'option'      => 'knowledge_facebook',
				'dashicon'    => 'dashicons-facebook',
				'desc'        => 'Facebook ' . __( 'Page', 'autodescription' ),
				'placeholder' => 'https://www.facebook.com/' . $connectedi18n,
				'examplelink' => 'https://www.facebook.com/me',
			),
			'twitter' => array(
				'option'      => 'knowledge_twitter',
				'dashicon'    => 'dashicons-twitter',
				'desc'        => 'Twitter ' . $profile18n,
				'placeholder' => 'https://twitter.com/' . $connectedi18n,
				'examplelink' => 'https://twitter.com/home', // No example link available.
			),
			'gplus' => array(
				'option'      => 'knowledge_gplus',
				'dashicon'    => 'dashicons-googleplus',
				'desc'        => 'Google+ ' . $profile18n,
				'placeholder' => 'https://plus.google.com/' . $connectedi18n,
				'examplelink' => 'https://plus.google.com/me',
			),
			'instagram' => array(
				'option'      => 'knowledge_instagram',
				'dashicon'    => 'genericon-instagram',
				'desc'        => 'Instagram ' . $profile18n,
				'placeholder' => 'https://instagram.com/' . $connectedi18n,
				'examplelink' => 'https://instagram.com/', // No example link available.
			),
			'youtube' => array(
				'option'      => 'knowledge_youtube',
				'dashicon'    => 'genericon-youtube',
				'desc'        => 'Youtube ' . $profile18n,
				'placeholder' => 'https://www.youtube.com/channel/' . $connectedi18n,
				'examplelink' => 'https://www.youtube.com/user/%2f', // Yes a double slash.
			),
			'linkedin' => array(
				'option'      => 'knowledge_linkedin',
				'dashicon'    => 'genericon-linkedin-alt',
				'desc'        => 'LinkedIn ' . $profile18n,
				'placeholder' => 'https://www.linkedin.com/in/' . $connectedi18n,
				'examplelink' => 'https://www.linkedin.com/profile/view',
			),
			'pinterest' => array(
				'option'      => 'knowledge_pinterest',
				'dashicon'    => 'genericon-pinterest-alt',
				'desc'        => 'Pinterest ' . $profile18n,
				'placeholder' => 'https://www.pinterest.com/' . $connectedi18n . '/',
				'examplelink' => 'https://www.pinterest.com/me/',
			),
			'soundcloud' => array(
				'option'      => 'knowledge_soundcloud',
				'dashicon'    => 'genericon-cloud', // I know, it's not the real one. D:
				'desc'        => 'SoundCloud ' . $profile18n,
				'placeholder' => 'https://soundcloud.com/' . $connectedi18n,
				'examplelink' => 'https://soundcloud.com/you',
			),
			'tumblr' => array(
				'option'      => 'knowledge_tumblr',
				'dashicon'    => 'genericon-tumblr',
				'desc'        => 'Tumblr ' . __( 'Blog', 'autodescription' ),
				'placeholder' => 'https://www.tumblr.com/blog/' . $connectedi18n,
				'examplelink' => 'https://www.tumblr.com/dashboard',  // No example link available.
			),
		);

		foreach ( $socialsites as $key => $v ) {
			?>
			<p>
				<label for="<?php $this->field_id( $v['option'] ); ?>">
					<strong><?php echo esc_html( $v['desc'] ); ?></strong>
					<?php
					if ( $v['examplelink'] ) {
						$this->make_info(
							__( 'View your profile.', 'autodescription' ),
							$v['examplelink']
						);
					}
					?>
				</label>
			</p>
			<p>
				<input type="text" name="<?php $this->field_name( $v['option'] ); ?>" class="large-text" id="<?php $this->field_id( $v['option'] ); ?>" placeholder="<?php echo esc_attr( $v['placeholder'] ); ?>" value="<?php echo esc_attr( $this->get_field_value( $v['option'] ) ); ?>" />
			</p>
			<?php
		}
		break;

	default :
		break;
endswitch;
