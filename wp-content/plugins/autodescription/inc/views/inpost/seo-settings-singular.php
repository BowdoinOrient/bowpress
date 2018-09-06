<?php
/**
 * @package The_SEO_Framework\Views\Inpost
 */

defined( 'ABSPATH' ) and $_this = the_seo_framework_class() and $this instanceof $_this or die;

//* Fetch the required instance within this file.
$instance = $this->get_view_instance( 'inpost', $instance );

//* Setup default vars.
$post_id = $this->get_the_real_ID();
$type = isset( $type ) ? $type : '';
$language = $this->google_language();

switch ( $instance ) :
	case 'inpost_main' :
		/**
		 * Parse inpost tabs content.
		 *
		 * @since 2.9.0
		 * @see $this->call_function()
		 * @see PHP call_user_func_array() For args.
		 *
		 * @param array $default_tabs {
		 *   'id' = The identifier => {
		 *        array(
		 *            'name'     => The name
		 *            'callback' => The callback function, use array for method calling
		 *            'dashicon' => Desired dashicon
		 *            'args'     => Callback parameters
		 *        )
		 *    }
		 * }
		 */
		$default_tabs = array(
			'general' => array(
				'name'     => __( 'General', 'autodescription' ),
				'callback' => array( $this, 'singular_inpost_box_general_tab' ),
				'dashicon' => 'admin-generic',
				'args' => array( $type ),
			),
			'social' => array(
				'name'     => __( 'Social', 'autodescription' ),
				'callback' => array( $this, 'singular_inpost_box_social_tab' ),
				'dashicon' => 'share',
				'args' => array( $type ),
			),
			'visibility' => array(
				'name'     => __( 'Visibility', 'autodescription' ),
				'callback' => array( $this, 'singular_inpost_box_visibility_tab' ),
				'dashicon' => 'visibility',
				'args' => array( $type ),
			),
		);

		/**
		 * Applies filters 'the_seo_framework_inpost_settings_tabs' : array
		 *
		 * Allows for altering the inpost SEO settings metabox tabs.
		 *
		 * @since 2.9.0
		 *
		 * @param array $default_tabs The default tabs.
		 * @param array $type The current post type display name, like "Post", "Page", "Product".
		 */
		$tabs = (array) apply_filters( 'the_seo_framework_inpost_settings_tabs', $default_tabs, $type );

		echo '<div class="tsf-flex tsf-flex-inside-wrap">';
		$this->inpost_flex_nav_tab_wrapper( 'inpost', $tabs, '2.6.0' );
		echo '</div>';
		break;

	case 'inpost_general' :
		//* Temporarily. TODO refactor.
		$tit_len_parsed = $desc_len_parsed = '';
		$doctitle_placeholder = $description_placeholder = '';
		$this->_get_inpost_general_tab_vars( $tit_len_parsed, $doctitle_placeholder, $desc_len_parsed, $description_placeholder );
		//= End temporarily.

		if ( $this->is_option_checked( 'display_seo_bar_metabox' ) ) :
		?>
			<div class="tsf-flex-setting tsf-flex">
				<div class="tsf-flex-setting-label tsf-flex">
					<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
						<div class="tsf-flex-setting-label-item tsf-flex">
							<div><strong><?php esc_html_e( 'Doing it Right', 'autodescription' ); ?></strong></div>
						</div>
					</div>
				</div>
				<div class="tsf-flex-setting-input tsf-flex">
					<div>
						<?php $this->post_status( $post_id, 'inpost', true ); ?>
					</div>
				</div>
			</div>
		<?php
		endif;

		?>
		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_title" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							/* translators: %s = Post type name */
							printf( esc_html__( 'Custom %s Title', 'autodescription' ), esc_html( $type ) );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_title', $tit_len_parsed );
					$this->get_option( 'display_pixel_counter' )
						and $this->output_pixel_counter_wrap( 'autodescription_title', 'title' );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<div id="tsf-title-wrap">
					<input class="large-text" type="text" name="autodescription[_genesis_title]" id="autodescription_title" placeholder="<?php echo esc_attr( $doctitle_placeholder ); ?>" value="<?php echo esc_attr( $this->get_custom_field( '_genesis_title', $post_id ) ); ?>" autocomplete=off />
					<?php echo $this->output_js_title_elements(); ?>
				</div>
			</div>
		</div>

		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_description" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							/* translators: %s = Post type name */
							printf( esc_html__( 'Custom %s Description', 'autodescription' ), esc_html( $type ) );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_description', $desc_len_parsed );
					$this->get_option( 'display_pixel_counter' )
						and $this->output_pixel_counter_wrap( 'autodescription_description', 'description' );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<textarea class="large-text" name="autodescription[_genesis_description]" id="autodescription_description" placeholder="<?php echo esc_attr( $description_placeholder ); ?>" rows="4" cols="4"><?php echo esc_attr( $this->get_custom_field( '_genesis_description', $post_id ) ); ?></textarea>
				<?php echo $this->output_js_description_elements(); ?>
			</div>
		</div>
		<?php
		break;

	case 'inpost_visibility' :
		//* Fetch Canonical URL.
		$canonical = $this->get_custom_field( '_genesis_canonical_uri' );
		//* Fetch Canonical URL Placeholder.
		$canonical_placeholder = $this->create_canonical_url( array( 'id' => $post_id ) );

		?>
		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_canonical" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong><?php esc_html_e( 'Custom Canonical URL', 'autodescription' ); ?></strong></div>
						<div>
						<?php
						$this->make_info(
							__( 'This urges search engines to go to the outputted URL.', 'autodescription' ),
							'https://support.google.com/webmasters/answer/139066?hl=' . $language
						);
						?>
						</div>
					</label>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<input class="large-text" type="text" name="autodescription[_genesis_canonical_uri]" id="autodescription_canonical" placeholder="<?php echo esc_url( $canonical_placeholder ); ?>" value="<?php echo esc_url( $this->get_custom_field( '_genesis_canonical_uri' ) ); ?>" />
			</div>
		</div>

		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<div class="tsf-flex-setting-label-item tsf-flex">
						<div><strong><?php esc_html_e( 'Robots Meta Settings', 'autodescription' ); ?></strong></div>
					</div>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<div class="tsf-checkbox-wrapper">
					<label for="autodescription_noindex">
						<input type="checkbox" name="autodescription[_genesis_noindex]" id="autodescription_noindex" value="1" <?php checked( $this->get_custom_field( '_genesis_noindex' ) ); ?> />
						<?php
						/* translators: 1: Option, 2: Post or Page */
						printf( esc_html__( 'Apply %1$s to this %2$s', 'autodescription' ), $this->code_wrap( 'noindex' ), esc_html( $type ) );
						echo ' ';
						$this->make_info(
							sprintf(
								__( 'This tells search engines not to show this %s in their search results.', 'autodescription' ),
								$type
							),
							'https://support.google.com/webmasters/answer/93710?hl=' . $language
						);
						?>
					</label>
				</div>
				<div class="tsf-checkbox-wrapper">
					<label for="autodescription_nofollow"><input type="checkbox" name="autodescription[_genesis_nofollow]" id="autodescription_nofollow" value="1" <?php checked( $this->get_custom_field( '_genesis_nofollow' ) ); ?> />
					<?php
						/* translators: 1: Option, 2: Post or Page */
						printf( esc_html__( 'Apply %1$s to this %2$s', 'autodescription' ), $this->code_wrap( 'nofollow' ), esc_html( $type ) );
						echo ' ';
						$this->make_info(
							sprintf( __( 'This tells search engines not to follow links on this %s.', 'autodescription' ), $type ),
							'https://support.google.com/webmasters/answer/96569?hl=' . $language
						);
					?>
					</label>
				</div>
				<div class="tsf-checkbox-wrapper">
					<label for="autodescription_noarchive"><input type="checkbox" name="autodescription[_genesis_noarchive]" id="autodescription_noarchive" value="1" <?php checked( $this->get_custom_field( '_genesis_noarchive' ) ); ?> />
					<?php
						/* translators: 1: Option, 2: Post or Page */
						printf(
							esc_html__( 'Apply %1$s to this %2$s', 'autodescription' ),
							$this->code_wrap( 'noarchive' ),
							esc_html( $type )
						);
						echo ' ';
						/* translators: %s = Post type name */
						$this->make_info(
							sprintf(
								__( 'This tells search engines not to save a cached copy of this %s.', 'autodescription' ),
								$type
							),
							'https://support.google.com/webmasters/answer/79812?hl=' . $language
						);
					?>
					</label>
				</div>
			</div>
		</div>

		<?php
		$can_do_archive_query = $this->post_type_supports_taxonomies() && $this->is_option_checked( 'alter_archive_query' );
		$can_do_search_query = $this->is_option_checked( 'alter_search_query' );
		?>

	<?php if ( $can_do_archive_query || $can_do_search_query ) : ?>
		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<div class="tsf-flex-setting-label-item tsf-flex">
						<div><strong><?php esc_html_e( 'Archive Settings', 'autodescription' ); ?></strong></div>
					</div>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<?php if ( $can_do_search_query ) : ?>
				<div class="tsf-checkbox-wrapper">
					<label for="autodescription_exclude_local_search"><input type="checkbox" name="autodescription[exclude_local_search]" id="autodescription_exclude_local_search" value="1" <?php checked( $this->get_custom_field( 'exclude_local_search' ) ); ?> />
						<?php
						/* translators: %s = Post type name */
						printf( esc_html__( 'Exclude this %s from local search', 'autodescription' ), esc_html( $type ) );
						echo ' ';
						/* translators: %s = Post type name */
						$this->make_info( sprintf( __( 'This excludes this %s from local on-site search results.', 'autodescription' ), $type ) );
						?>
					</label>
				</div>
				<?php endif; ?>
				<?php if ( $can_do_archive_query ) : ?>
				<div class="tsf-checkbox-wrapper">
					<label for="autodescription_exclude_from_archive"><input type="checkbox" name="autodescription[exclude_from_archive]" id="autodescription_exclude_from_archive" value="1" <?php checked( $this->get_custom_field( 'exclude_from_archive' ) ); ?> />
						<?php
						/* translators: %s = Post type name */
						printf( esc_html__( 'Exclude this %s from all archive listings.', 'autodescription' ), esc_html( $type ) );
						echo ' ';
						/* translators: %s = Post type name */
						$this->make_info( sprintf( __( 'This excludes this %s from on-site archive pages.', 'autodescription' ), $type ) );
						?>
					</label>
				</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_redirect" class="tsf-flex-setting-label-item tsf-flex">
						<div>
							<strong><?php esc_html_e( 'Custom 301 Redirect URL', 'autodescription' ); ?></strong>
						</div>
						<div>
							<?php
							$this->make_info(
								__( 'This will force visitors to go to another URL.', 'autodescription' ),
								'https://support.google.com/webmasters/answer/93633?hl=' . $language
							);
							?>
						</div>
					</label>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<input class="large-text" type="text" name="autodescription[redirect]" id="autodescription_redirect" value="<?php echo esc_url( $this->get_custom_field( 'redirect' ) ); ?>" />
			</div>
		</div>
		<?php
		break;

	case 'inpost_social' :
		// Gets custom fields.
		$custom_og_title = $this->get_custom_field( '_open_graph_title', $post_id );
		$custom_tw_title = $this->get_custom_field( '_twitter_title', $post_id );
		$custom_og_desc  = $this->get_custom_field( '_open_graph_description', $post_id );
		$custom_tw_desc  = $this->get_custom_field( '_twitter_description', $post_id );

		//! OG input falls back to default input.
		$og_tit_placeholder = $this->get_generated_open_graph_title( $post_id );
		$og_desc_placeholder = $this->get_generated_open_graph_description( $post_id );
		$og_tit_len_parsed = $custom_og_title ? html_entity_decode( $custom_og_title ) : html_entity_decode( $og_tit_placeholder );
		$og_desc_len_parsed = $custom_og_desc ? html_entity_decode( $custom_og_desc ) : html_entity_decode( $og_desc_placeholder );

		//! Twitter input falls back to OG input.
		$tw_tit_placeholder = $custom_og_title ?: $og_tit_placeholder;
		$tw_desc_placeholder = $custom_og_desc ?: $og_desc_placeholder;
		$tw_tit_len_parsed = $custom_tw_title ? html_entity_decode( $custom_tw_title ) : $og_tit_len_parsed;
		$tw_desc_len_parsed = $custom_tw_desc ? html_entity_decode( $custom_tw_desc ) : $og_desc_len_parsed;

		$show_og = $this->is_option_checked( 'og_tags' ) && ! $this->detect_og_plugin();
		$show_tw = $this->is_option_checked( 'twitter_tags' ) && ! $this->detect_twitter_card_plugin();

		?>
		<div class="tsf-flex-setting tsf-flex" <?php echo $show_og ? '' : 'style=display:none'; ?>>
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_og_title" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							esc_html_e( 'Open Graph Title', 'autodescription' );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_og_title', $og_tit_len_parsed );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<div id="tsf-og-title-wrap">
					<input class="large-text" type="text" name="autodescription[_open_graph_title]" id="autodescription_og_title" placeholder="<?php echo esc_attr( $og_tit_placeholder ); ?>" value="<?php echo esc_attr( $this->get_custom_field( '_open_graph_title' ) ); ?>" autocomplete=off />
				</div>
			</div>
		</div>

		<div class="tsf-flex-setting tsf-flex" <?php echo $show_og ? '' : 'style=display:none'; ?>>
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_og_description" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							esc_html_e( 'Open Graph Description', 'autodescription' );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_og_description', $og_desc_len_parsed );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<textarea class="large-text" name="autodescription[_open_graph_description]" id="autodescription_og_description" placeholder="<?php echo esc_attr( $og_desc_placeholder ); ?>" rows="3" cols="4"><?php echo esc_attr( $this->get_custom_field( '_open_graph_description' ) ); ?></textarea>
			</div>
		</div>

		<div class="tsf-flex-setting tsf-flex" <?php echo $show_tw ? '' : 'style=display:none'; ?>>
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_twitter_title" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							esc_html_e( 'Twitter Title', 'autodescription' );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_twitter_title', $tw_tit_len_parsed );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<div id="tsf-twitter-title-wrap">
					<input class="large-text" type="text" name="autodescription[_twitter_title]" id="autodescription_twitter_title" placeholder="<?php echo esc_attr( $tw_tit_placeholder ); ?>" value="<?php echo esc_attr( $this->get_custom_field( '_twitter_title' ) ); ?>" autocomplete=off />
				</div>
			</div>
		</div>

		<div class="tsf-flex-setting tsf-flex" <?php echo $show_tw ? '' : 'style=display:none'; ?>>
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_twitter_description" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong>
							<?php
							esc_html_e( 'Twitter Description', 'autodescription' );
							?>
						</strong></div>
					</label>
					<?php
					$this->get_option( 'display_character_counter' )
						and $this->output_character_counter_wrap( 'autodescription_twitter_description', $tw_desc_len_parsed );
					?>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<textarea class="large-text" name="autodescription[_twitter_description]" id="autodescription_twitter_description" placeholder="<?php echo esc_attr( $tw_desc_placeholder ); ?>" rows="3" cols="4"><?php echo esc_attr( $this->get_custom_field( '_twitter_description' ) ); ?></textarea>
			</div>
		</div>
		<?php

		//* Fetch image placeholder.
		$image_placeholder = $this->get_social_image( array( 'post_id' => $post_id, 'disallowed' => array( 'postmeta' ), 'escape' => false ) );

		?>
		<div class="tsf-flex-setting tsf-flex">
			<div class="tsf-flex-setting-label tsf-flex">
				<div class="tsf-flex-setting-label-inner-wrap tsf-flex">
					<label for="autodescription_socialimage-url" class="tsf-flex-setting-label-item tsf-flex">
						<div><strong><?php esc_html_e( 'Custom Social Image URL', 'autodescription' ); ?></strong></div>
						<div>
						<?php
						$this->make_info(
							sprintf(
								/* translators: %s = Post type name */
								__( 'Set preferred %s Social Image URL location.', 'autodescription' ),
								$type
							),
							'https://developers.facebook.com/docs/sharing/best-practices#images'
						);
						?>
						</div>
					</label>
				</div>
			</div>
			<div class="tsf-flex-setting-input tsf-flex">
				<input class="large-text" type="text" name="autodescription[_social_image_url]" id="autodescription_socialimage-url" placeholder="<?php echo esc_url( $image_placeholder ); ?>" value="<?php echo esc_url( $this->get_custom_field( '_social_image_url' ) ); ?>" />
				<div class="hide-if-no-js tsf-social-image-buttons">
					<?php
					//= Already escaped.
					echo $this->get_social_image_uploader_form( 'autodescription_socialimage' );
					?>
				</div>
				<?php
				/**
				 * Insert form element only if JS is active. If JS is inactive, then this will cause it to be emptied on $_POST
				 * @TODO use disabled and jQuery.removeprop( 'disabled' )?
				 */
				?>
				<script>
					document.getElementById( 'autodescription_socialimage-url' ).insertAdjacentHTML( 'afterend', '<input type="hidden" name="autodescription[_social_image_id]" id="autodescription_socialimage-id" value="<?php echo absint( $this->get_custom_field( '_social_image_id' ) ); ?>" />' );
				</script>
			</div>
		</div>
		<?php
		break;

endswitch;
