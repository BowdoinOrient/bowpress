<?php
/**
 * @package The_SEO_Framework\Views\Inpost
 */

defined( 'ABSPATH' ) and $_this = the_seo_framework_class() and $this instanceof $_this or die;

//* Get the language the Google page should assume.
$language = $this->google_language();

//* Fetch Term ID and taxonomy.
$term_id = $object->term_id;
$taxonomy = $object->taxonomy;
$data = $this->get_term_meta( $object->term_id );

$title = isset( $data['doctitle'] ) ? $data['doctitle'] : '';
$description = isset( $data['description'] ) ? $data['description'] : '';
$noindex = isset( $data['noindex'] ) ? $data['noindex'] : '';
$nofollow = isset( $data['nofollow'] ) ? $data['nofollow'] : '';
$noarchive = isset( $data['noarchive'] ) ? $data['noarchive'] : '';

$generated_doctitle_args = array(
	'term_id' => $term_id,
	'taxonomy' => $taxonomy,
	'placeholder' => true,
	'get_custom_field' => false,
);

//* Generate title and description.
$generated_doctitle = $this->title( '', '', '', $generated_doctitle_args );
$generated_description = $this->get_generated_description( $term_id );

$blog_name = $this->get_blogname();
$add_additions = $this->add_title_additions();

/**
 * Separator doesn't matter. Since html_entity_decode is used.
 * Order doesn't matter either. Since it's just used for length calculation.
 *
 * @since 2.3.4
 */
$doc_pre_rem = $add_additions ? $title . ' | ' . $blog_name : $title;
$title_len = $title ? $doc_pre_rem : $generated_doctitle;
$description_len = $description ?: $generated_description;

/**
 * Convert to what Google outputs.
 *
 * This will convert e.g. &raquo; to a single length character.
 * @since 2.3.4
 */
$tit_len_parsed = html_entity_decode( $title_len );
$desc_len_parsed = html_entity_decode( $description_len );

/**
 * Generate static placeholder for when title or description is emptied
 *
 * @since 2.2.4
 */
$title_placeholder = $generated_doctitle;
$description_placeholder = $generated_description;

$robots_settings = array(
	'noindex' => array(
		'value' => $noindex,
		'info' => $this->make_info(
			__( 'This tells search engines not to show this page in their search results.', 'autodescription' ),
			'https://support.google.com/webmasters/answer/93710?hl=' . $language,
			false
		),
	),
	'nofollow' => array(
		'value' => $nofollow,
		'info' => $this->make_info(
			__( 'This tells search engines not to follow links on this page.', 'autodescription' ),
			'https://support.google.com/webmasters/answer/96569?hl=' . $language,
			false
		),
	),
	'noarchive' => array(
		'value' => $noarchive,
		'info' => $this->make_info(
			__( 'This tells search engines not to follow links on this page.', 'autodescription' ),
			'https://support.google.com/webmasters/answer/79812?hl=' . $language,
			false
		),
	),
);

?>
<h3>
	<?php
	/* translators: %s = Term type */
	printf( esc_html__( '%s SEO Settings', 'autodescription' ), esc_html( $type ) );
	?>
</h3>

<table class="form-table">
	<tbody>
		<?php if ( 'above' === $this->inpost_seo_bar || $this->is_option_checked( 'display_seo_bar_metabox' ) ) : ?>
		<tr>
			<th scope="row" valign="top"><?php esc_html_e( 'Doing it Right', 'autodescription' ); ?></th>
			<td>
				<?php $this->post_status( $term_id, $taxonomy, true ); ?>
			</td>
		</tr>
		<?php endif; ?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="autodescription-meta[doctitle]">
					<strong>
						<?php
						/* translators: %s = Term type */
						printf( esc_html__( '%s Title', 'autodescription' ), esc_html( $type ) );
						?>
					</strong>
				</label>
				<?php
				$this->get_option( 'display_character_counter' )
					and $this->output_character_counter_wrap( 'autodescription-meta[doctitle]', $tit_len_parsed );
				$this->get_option( 'display_pixel_counter' )
					and $this->output_pixel_counter_wrap( 'autodescription-meta[doctitle]', 'title' );
				?>
			</th>
			<td>
				<div id="tsf-title-wrap">
					<input name="autodescription-meta[doctitle]" id="autodescription-meta[doctitle]" type="text" placeholder="<?php echo esc_attr( $title_placeholder ); ?>" value="<?php echo esc_attr( $title ); ?>" size="40" autocomplete=off />
					<?php $this->output_js_title_elements(); ?>
				</div>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="autodescription-meta[description]">
					<strong>
						<?php
						/* translators: %s = Term type */
						printf( esc_html__( '%s Meta Description', 'autodescription' ), esc_html( $type ) );
						?>
					</strong>
				</label>
				<?php
				$this->get_option( 'display_character_counter' )
					and $this->output_character_counter_wrap( 'autodescription-meta[description]', $desc_len_parsed );
				$this->get_option( 'display_pixel_counter' )
					and $this->output_pixel_counter_wrap( 'autodescription-meta[description]', 'description' );
				?>
			</th>
			<td>
				<textarea name="autodescription-meta[description]" id="autodescription-meta[description]" placeholder="<?php echo esc_attr( $description_placeholder ); ?>" rows="5" cols="50" class="large-text"><?php echo esc_html( $description ); ?></textarea>
				<?php echo $this->output_js_description_elements(); ?>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php esc_html_e( 'Robots Meta Settings', 'autodescription' ); ?></th>
			<td>
				<?php
				foreach ( $robots_settings as $type => $data ) :
					?>
					<p>
						<?php
						vprintf(
							'<label for="%1$s"><input name="%1$s" id="%1$s" type="checkbox" value="1" %2$s />%3$s</label>',
							array(
								sprintf( 'autodescription-meta[%s]', esc_attr( $type ) ),
								checked( $data['value'], true, false ),
								vsprintf(
									'%s %s',
									array(
										sprintf(
											/* translators: %s = noindex/nofollow/noarchive */
											esc_html__( 'Apply %s to this term?', 'autodescription' ),
											//= Already escaped.
											$this->code_wrap( $type )
										),
										//= Already escaped.
										$data['info'],
									)
								),
							)
						);
						?>
					</p>
					<?php
				endforeach;

				// Output saved flag, if set then it won't fetch alternative meta anymore.
				?>
				<label class="hidden" for="autodescription-meta[saved_flag]">
					<input name="autodescription-meta[saved_flag]" id="autodescription-meta[saved_flag]" type="checkbox" value="1" checked='checked' />
				</label>
			</td>
		</tr>

		<?php if ( 'below' === $this->inpost_seo_bar ) : ?>
		<tr>
			<th scope="row" valign="top"><?php esc_html_e( 'Doing it Right', 'autodescription' ); ?></th>
			<td>
				<?php $this->post_status( $term_id, $taxonomy, true ); ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php
