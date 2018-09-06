<?php
/**
 * @package The_SEO_Framework\Classes
 */
namespace The_SEO_Framework;

defined( 'ABSPATH' ) or die;

/**
 * The SEO Framework plugin
 * Copyright (C) 2015 - 2018 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class The_SEO_Framework\Generate_Ldjson
 *
 * Generates SEO data based on content
 *
 * @since 2.8.0
 */
class Generate_Ldjson extends Generate_Image {

	/**
	 * Constructor, load parent constructor and inits class.
	 */
	protected function __construct() {
		parent::__construct();
	}

	/**
	 * Builds up JSON data.
	 *
	 * NOTE: Array indexes with falsy values will be stripped.
	 *       `[ 'key' => (string) 'false' ]` will pass.
	 *       `[ 'key' => '0' ]` will not pass.
	 *
	 * @since 2.9.3
	 * @see $this->receive_json_data()
	 * @uses $this->build_json_data_cache()
	 *
	 * @param string $key  The JSON data key.
	 * @param array  $data The JSON data.
	 */
	public function build_json_data( $key, array $data ) {

		$key = \sanitize_key( $key );
		$data = array_filter( $data );

		foreach ( $data as $k => $v ) {
			$thing = array( $k => $v );
			$this->build_json_data_cache( $key, $thing );
		}
	}

	/**
	 * Returns built JSON data.
	 *
	 * May return empty values if data is invalid.
	 *
	 * @since 2.9.3
	 * @since 2.9.4 No longer escapes slashes on PHP 5.4+.
	 * @see $this->build_json_data()
	 * @uses $this->cache_json_data()
	 *
	 * @param string $key    The JSON data key.
	 * @param bool   $encode Whether to JSON encode the output.
	 * @return array|string The JSON data for $key. Array if $encode is false, string otherwise.
	 */
	public function receive_json_data( $key, $encode = true ) {

		$key = \sanitize_key( $key );
		$data = $this->cache_json_data( true, $key );

		if ( \has_filter( 'the_seo_framework_receive_json_data' ) ) {
			/**
			 * Applies filters 'the_seo_framework_recieve_json_data'
			 *
			 * @since 2.9.3
			 *
			 * @param array  $data The LD-JSON data.
			 * @param string $key  The data key.
			 */
			$data = (array) \apply_filters_ref_array( 'the_seo_framework_receive_json_data', array( $data, $key ) );
		}


		if ( $encode ) {
			$options = 0;
			//= PHP 5.4+ ( JSON_UNESCAPED_SLASHES === 64 )
			$options |= defined( 'JSON_UNESCAPED_SLASHES' ) ? JSON_UNESCAPED_SLASHES : 0;

			return $data ? (string) json_encode( $data, $options ) : '';
		}

		return $data ?: array();
	}

	/**
	 * Builds up JSON data cache.
	 *
	 * @since 2.9.3
	 * @see $this->build_json_data()
	 *
	 * @param string $key   The JSON data key.
	 * @param array  $entry The JSON data entry.
	 * @return array The JSON data for $key.
	 */
	protected function build_json_data_cache( $key, array $entry ) {
		$this->cache_json_data( false, $key, $entry );
	}

	/**
	 * Builds up JSON data cache.
	 *
	 * @since 2.9.3
	 * @see $this->build_json_data()
	 * @see $this->receive_json_data()
	 *
	 * @param bool   $get   Whether to get or otherwise set the data.
	 * @param string $key   The JSON data key.
	 * @param array  $entry The JSON data entry.
	 * @return array The JSON data for $key.
	 */
	protected function cache_json_data( $get = true, $key = '', array $entry = array() ) {

		static $data = array();

		if ( $get )
			return $data[ $key ];

		$data[ $key ][ key( $entry ) ] = reset( $entry );

		return array();
	}

	/**
	 * Renders the LD+JSON scripts.
	 *
	 * @since 2.6.0
	 *
	 * @return string The LD+JSON scripts.
	 */
	public function render_ld_json_scripts() {

		if ( $this->has_json_ld_plugin() )
			return '';

		$this->setup_ld_json_transient( $this->get_the_real_ID() );

		$this->the_seo_framework_debug and $this->debug_init( __METHOD__, true, $debug_key = microtime( true ), array( 'LD Json transient' => $this->ld_json_transient, 'Output from transient' => false !== $this->get_transient( $this->ld_json_transient ) ) );

		$use_cache = $this->is_option_checked( 'cache_meta_schema' );

		$output = $use_cache ? $this->get_transient( $this->ld_json_transient ) : false;
		if ( false === $output ) :
			if ( $this->is_real_front_page() ) {
				//= Home page Schema.
				$output = '';

				$output .= $this->get_ld_json_website() ?: '';
				$output .= $this->get_ld_json_links() ?: '';
			} else {
				//= All other pages' Schema.
				$output = $this->get_ld_json_breadcrumbs() ?: '';
			}

			if ( $use_cache ) {
				/**
				 * Transient expiration: 1 week.
				 * Keep the script for at most 1 week.
				 */
				$expiration = WEEK_IN_SECONDS;

				$this->set_transient( $this->ld_json_transient, $output, $expiration );
			}
		endif;

		$this->the_seo_framework_debug and $this->debug_init( __METHOD__, false, $debug_key, array( 'LD Json transient output' => $output ) );

		return $output;
	}

	/**
	 * Generates LD+JSON Search and Sitename script.
	 *
	 * @since 2.9.3
	 * @since 3.0.0 This whole functions now only listens to the searchbox option.
	 *
	 * @return string escaped LD+JSON Search and Sitename script.
	 */
	public function get_ld_json_website() {

		if ( ! $this->enable_ld_json_searchbox() )
			return '';

		$data = array(
			'@context' => 'http://schema.org',
			'@type' => 'WebSite',
			'url' => $this->get_homepage_permalink(),
		);

		name : {
			$blogname = $this->get_blogname();
			$kn = $this->get_option( 'knowledge_name' );

			$alternate_name = $kn && $kn !== $blogname ? $kn : '';

			$data += array(
				'name' => $this->escape_title( $blogname ),
				'alternateName' => $this->escape_title( $alternate_name ),
			);
		}

		//= The actual searchbox part.
		searchbox : {
			$action_name = 'search_term_string';
			$search_link = $this->pretty_permalinks ? \trailingslashit( \get_search_link() ) : \get_search_link();
			/**
			 * Applies filters 'the_seo_framework_ld_json_search_url' : string
			 * @since 2.7.0
			 * @param string $search_url The default WordPress search URL without query parameters.
			 */
			$search_url = (string) \apply_filters( 'the_seo_framework_ld_json_search_url', $search_link );

			$data += array(
				'potentialAction' => array(
					'@type' => 'SearchAction',
					'target' => sprintf( '%s{%s}', \esc_url( $search_url ), $action_name ),
					'query-input' => sprintf( 'required name=%s', $action_name ),
				),
			);
		}

		$key = 'WebSite';
		$this->build_json_data( $key, $data );
		$json = $this->receive_json_data( $key );

		if ( $json )
			return '<script type="application/ld+json">' . $json . '</script>' . "\r\n";

		return '';
	}

	/**
	 * Generates LD+JSON Social Profile Links script.
	 *
	 * @since 2.9.3
	 *
	 * @return string LD+JSON Social Profile Links script.
	 */
	public function get_ld_json_links() {

		if ( false === $this->enable_ld_json_knowledge() )
			return '';

		$knowledge_type = $this->get_option( 'knowledge_type' );
		$knowledge_name = $this->get_option( 'knowledge_name' ) ?: $this->get_blogname();

		$data = array(
			'@context' => 'http://schema.org',
			'@type' => ucfirst( \esc_attr( $knowledge_type ) ),
			'url' => $this->get_homepage_permalink(),
			'name' => $this->escape_title( $knowledge_name ),
		);

		if ( $this->get_option( 'knowledge_logo' ) && 'organization' === $knowledge_type ) {
			$data += array(
				'logo' => \esc_url_raw( $this->get_knowledge_logo() ),
			);
		}

		/**
		 * Applies filters 'the_seo_framework_json_options' : array The option names
		 * @since Unknown. Definitely 2.7 or later.
		 */
		$sameurls_options = (array) \apply_filters( 'the_seo_framework_json_options', array(
			'knowledge_facebook',
			'knowledge_twitter',
			'knowledge_gplus',
			'knowledge_instagram',
			'knowledge_youtube',
			'knowledge_linkedin',
			'knowledge_pinterest',
			'knowledge_soundcloud',
			'knowledge_tumblr',
		) );

		$sameurls = array();
		foreach ( $sameurls_options as $_o ) {
			$_ov = $this->get_option( $_o ) ?: '';
			//* Sublevel array entries aren't getting caught by array_filter().
			if ( $_ov )
				$sameurls[] = \esc_url_raw( $_ov, array( 'https', 'http' ) );
		}

		if ( $sameurls ) {
			$data += array(
				'sameAs' => $sameurls,
			);
		}

		$key = 'Links';
		$this->build_json_data( $key, $data );
		$json = $this->receive_json_data( $key );

		if ( $json )
			return '<script type="application/ld+json">' . $json . '</script>' . "\r\n";

		return '';
	}

	/**
	 * Returns knowledge logo URL.
	 * It first tries to get the option and then the Customizer icon.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $get_option Whether to fetch the option.
	 * @return string The logo URL.
	 */
	public function get_knowledge_logo( $get_option = true ) {
		/**
		 * Applies filters 'the_seo_framework_knowledge_logo'
		 *
		 * @since 3.0.0
		 * @param string $logo       The current logo URL.
		 * @param bool   $get_option Whether to test the option or just the fallbacks.
		 */
		return \apply_filters_ref_array( 'the_seo_framework_knowledge_logo', array(
			( $get_option ? $this->get_option( 'knowledge_logo_url' ) : false )
				?: $this->get_site_icon()
				?: '',
			$get_option,
		) );
	}

	/**
	 * Generates LD+JSON Breadcrumbs script.
	 *
	 * @since 2.9.3
	 *
	 * @return string LD+JSON Breadcrumbs script.
	 */
	public function get_ld_json_breadcrumbs() {

		if ( false === $this->enable_ld_json_breadcrumbs() )
			return '';

		//* Used to count ancestors and categories.
		$output = '';

		if ( $this->is_single() || $this->is_wc_product() ) {
			$output = $this->get_ld_json_breadcrumbs_post();
		} elseif ( false === $this->is_real_front_page() && $this->is_page() ) {
			$output = $this->get_ld_json_breadcrumbs_page();
		}

		return $output;
	}

	/**
	 * Generates LD+JSON Breadcrumbs script for Pages.
	 *
	 * @since 2.9.3
	 *
	 * @return string LD+JSON breadcrumbs script for Pages.
	 */
	public function get_ld_json_breadcrumbs_page() {

		$page_id = $this->get_the_real_ID();
		//* Get ancestors.
		$parents = \get_post_ancestors( $page_id );

		if ( ! $parents )
			return '';

		$output = '';
		$items = array();
		$parents = array_reverse( $parents );

		foreach ( $parents as $pos => $parent_id ) {

			if ( $this->ld_json_breadcrumbs_use_seo_title() ) {
				$parent_name = $this->get_custom_field( '_genesis_title', $parent_id ) ?: ( $this->post_title_from_ID( $parent_id ) ?: $this->untitled() );
			} else {
				$parent_name = $this->post_title_from_ID( $parent_id ) ?: $this->untitled();
			}

			$position = $pos + 2;

			$crumb = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => array(
					'@id'  => $this->get_schema_url_id(
						'breadcrumb',
						'create',
						array( 'id' => $parent_id )
					),
					'name'  => $this->escape_title( $parent_name ),
				),
			);

			$image = $this->get_schema_image( $parent_id );
			if ( $image )
				$crumb['item']['image'] = $image;

			$items[] = $crumb;
		}

		if ( $items ) {
			array_unshift( $items, $this->get_ld_json_breadcrumb_home_crumb() );
			array_push( $items, $this->get_ld_json_breadcrumb_current( $position ) );
			$output .= $this->make_breadcrumb_script( $items );
		}

		return $output;
	}

	/**
	 * Generates LD+JSON Breadcrumbs script for Posts.
	 *
	 * @since 2.9.3
	 * @since 3.0.0 1: Now only returns one crumb.
	 *              2: Now listens to primary term ID.
	 *
	 * @return string LD+JSON breadcrumbs script for Posts.
	 */
	public function get_ld_json_breadcrumbs_post() {

		$output = '';

		$post_id = $this->get_the_real_ID();
		$post_type = \get_post_type( $post_id );
		$taxonomies = $this->get_hierarchical_taxonomies_as( 'names', \get_post_type( $post_id ) );

		/**
		 * Applies filters 'the_seo_framework_ld_json_breadcrumb_taxonomies'
		 *
		 * @since 3.0.0
		 * @param array|string  $taxonomies The assigned hierarchical taxonomies.
		 * @param string        $post_type  The current post type.
		 * @param int           $post_id    The current Post ID.
		 */
		$taxonomies = \apply_filters( 'the_seo_framework_ld_json_breadcrumb_taxonomies', $taxonomies, $post_type, $post_id );

		if ( is_array( $taxonomies ) ) {
			$taxonomy = reset( $taxonomies );
		} else {
			$taxonomy = $taxonomies;
		}

		//* Test categories.
		$r = \is_object_in_term( $post_id, $taxonomy, '' );
		if ( ! $r || \is_wp_error( $r ) )
			return '';

		/**
		 * Applies filter 'the_seo_framework_ld_json_breadcrumb_terms' : array
		 * @since 2.8.0
		 *
		 * @param array  $terms The candidate terms.
		 * @param int    $post_id  The current Post ID.
		 * @param string $taxonomy The current taxonomy (either category or product_cat).
		 */
		$terms = (array) \apply_filters_ref_array( 'the_seo_framework_ld_json_breadcrumb_terms', array( \get_the_terms( $post_id, $taxonomy ), $post_id, $taxonomy ) );

		if ( empty( $terms ) )
			return '';

		$terms = \wp_list_pluck( $terms, 'parent', 'term_id' );

		$parents = array();
		$assigned_ids = array();

		//* Fetch cats children id's, if any.
		foreach ( $terms as $term_id => $parent_id ) :
			$assigned_ids[ $term_id ] = $parent_id;
			// Check if they have parents (gets them all).
			$ancestors = \get_ancestors( $term_id, $taxonomy );
			if ( $ancestors ) {
				//= Save parents to find duplicates.
				$parents[ $term_id ] = $ancestors;
			} else {
				//= Save current only with empty parent id..
				$parents[ $term_id ] = array();
			}
		endforeach;
		//= Circle of life...
		unset( $terms );

		if ( ! $parents )
			return;

		//* Seed out parents that have multiple assigned children.
		foreach ( $parents as $pa_id => $child_id ) :
			foreach ( $child_id as $ckey => $cid ) :
				if ( isset( $parents[ $cid ] ) ) {
					unset( $parents[ $cid ] );
				}
			endforeach;
		endforeach;

		//* Merge tree list.
		$tree_ids = $this->build_ld_json_breadcrumb_trees( $parents );

		if ( ! $tree_ids )
			return '';

		$primary_term = $this->get_primary_term( $post_id, $taxonomy );
		$primary_term_id = $primary_term ? (int) $primary_term->term_id : 0;

		$filtered = false;
		/**
		 * Only get one crumb.
		 * If a category has multiple trees, it will filter until found.
		 * @since 3.0.0
		 */
		if ( $primary_term_id ) {
			$_trees = $this->filter_ld_json_breadcrumb_trees( $tree_ids, $primary_term_id );
			if ( $_trees ) {
				$tree_ids = $_trees;
				$filtered = true;
			}
		}
		if ( ! $filtered ) {
			//= Only get the first tree through numeric ordering.
			ksort( $assigned_ids, SORT_NUMERIC );
			$tree_ids = $this->filter_ld_json_breadcrumb_trees( $tree_ids, key( $assigned_ids ) );
		}

		if ( is_scalar( $tree_ids ) )
			$tree_ids = array( $tree_ids );

		$items = array();

		foreach ( $tree_ids as $pos => $child_id ) :
			$position = $pos + 2;

			if ( $this->ld_json_breadcrumbs_use_seo_title() ) {
				$data = $this->get_term_meta( $child_id );
				if ( empty( $data['doctitle'] ) ) {
					$cat = \get_term( $child_id, $taxonomy );
					//* Note: WordPress Core translation.
					$cat_name = empty( $cat->name ) ? \__( 'Uncategorized' ) : $cat->name;
				} else {
					$cat_name = $data['doctitle'];
				}
			} else {
				$cat = \get_term( $child_id, $taxonomy );
				//* Note: WordPress Core translation.
				$cat_name = empty( $cat->name ) ? \__( 'Uncategorized' ) : $cat->name;
			}

			//* Store in cache.
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'item'     => array(
					'@id'  => $this->get_schema_url_id(
						'breadcrumb',
						'create',
						array( 'id' => $child_id, 'taxonomy' => $taxonomy )
					),
					'name' => $this->escape_title( $cat_name ),
					// 'image' => $this->get_schema_image( $child_id ),
				),
			);
		endforeach;

		if ( $items ) {
			array_unshift( $items, $this->get_ld_json_breadcrumb_home_crumb() );
			array_push( $items, $this->get_ld_json_breadcrumb_current( $position ) );
			$output .= $this->make_breadcrumb_script( $items );
		}

		return $output;
	}

	/**
	 * Builds breadcrumb trees.
	 *
	 * @since 2.9.3
	 *
	 * @param array $kittens The breadcrumb trees, with the key as parent.
	 * @param array $previous_tree A previous set tree to compare to, if set.
	 * @return array Trees in order.
	 */
	protected function build_ld_json_breadcrumb_trees( $kittens, array $previous_tree = array() ) {

		$trees = $previous_tree;

		foreach ( $kittens as $parent => $kitten ) {
			if ( empty( $kitten ) ) {
				//* Final cat.
				$trees[] = $parent;
			} else {
				if ( 1 === count( $kitten ) ) {
					//* Single tree.
					$trees[] = array( reset( $kitten ), $parent );
				} else {
					//* Nested categories.
					$add = array();

					foreach ( $kitten as $kit_id => $child_id ) {
						//* Only add if non-existent in $trees.
						if ( ! in_array( $child_id, $trees, true ) )
							$add[] = $child_id;
					}

					//* Put children in right order.
					$add = array_reverse( $add );

					$trees[] = array_merge( $add, array( $parent ) );
				}
			}
		}

		return $trees;
	}

	/**
	 * Filters breadcrumb tree until $until is found.
	 *
	 * @since 3.0.0
	 *
	 * @param array|int $trees
	 * @param int       $until
	 * @return array    $trees. Empty if $until is nowhere to be found.
	 */
	protected function filter_ld_json_breadcrumb_trees( $trees, $until ) {

		$found = array();

		if ( in_array( $until, (array) $trees, true ) ) {
			$found = array( $until );
		} elseif ( is_array( $trees ) ) {
			foreach ( $trees as $tree ) {
				if ( $this->filter_ld_json_breadcrumb_trees( $tree, $until ) ) {
					$found = array_splice(
						$tree,
						0,
						array_search( $until, $tree, true ) + 1
					);
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * Generates homepage LD+JSON breadcrumb.
	 *
	 * @since 2.9.3
	 * @staticvar array $crumb
	 *
	 * @return array The HomePage crumb entry.
	 */
	public function get_ld_json_breadcrumb_home_crumb() {

		static $crumb = null;

		if ( isset( $crumb ) )
			return $crumb;

		$front_id = $this->get_the_front_page_ID();

		$custom_name = '';
		if ( $this->ld_json_breadcrumbs_use_seo_title() ) {

			$home_title = $this->get_option( 'homepage_title' );

			if ( $home_title ) {
				$custom_name = $home_title;
			} elseif ( $this->has_page_on_front() ) {
				$custom_name = $this->get_custom_field( '_genesis_title', $front_id ) ?: $this->get_blogname();
			}
		}

		$custom_name = $custom_name ?: $this->get_blogname();

		$crumb = array(
			'@type'    => 'ListItem',
			'position' => 1,
			'item'     => array(
				'@id'  => $this->get_schema_url_id( 'breadcrumb', 'homepage' ),
				'name' => $this->escape_title( $custom_name ),
			),
		);

		if ( $image = $this->get_schema_image( $front_id, true ) )
			$crumb['item']['image'] = $image;

		return $crumb;
	}

	/**
	 * Generates current Page/Post LD+JSON breadcrumb.
	 *
	 * @since 2.9.3
	 * @since 3.0.0 Removed @id output to allow for more same-page schema items.
	 * @staticvar array $crumb
	 *
	 * @param int $position The previous crumb position.
	 * @return array The Current Page/Post crumb entry.
	 */
	public function get_ld_json_breadcrumb_current( $position ) {

		static $crumb = null;

		$position++;

		if ( isset( $crumb ) ) {
			$crumb['position'] = $position;
			return $crumb;
		}

		$post_id = $this->get_the_real_ID();

		if ( $this->ld_json_breadcrumbs_use_seo_title() ) {
			$name = $this->get_custom_field( '_genesis_title', $post_id ) ?: ( $this->post_title_from_ID( $post_id ) ?: $this->untitled() );
		} else {
			$name = $this->post_title_from_ID( $post_id ) ?: $this->untitled();
		}

		$crumb = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'item'     => array(
				// '@id'  => $this->get_schema_url_id( 'breadcrumb', 'currentpage' ),
				'name' => $this->escape_title( $name ),
			),
		);

		if ( $image = $this->get_schema_image( $post_id, true ) )
			$crumb['item']['image'] = $image;

		return $crumb;
	}

	/**
	 * Creates LD+JSON Breadcrumb script from items.
	 *
	 * @since 2.9.0
	 * @since 2.9.3 : Rewritten to conform to the new generator.
	 * @staticvar int $it The iteration count for script generation cache busting.
	 *
	 * @param array $items The LD+JSON breadcrumb items.
	 * @return string The LD+JSON Breadcrumb script.
	 */
	protected function make_breadcrumb_script( $items ) {

		if ( ! $items )
			return '';

		static $it = 0;

		$key = 'Breadcrumbs_' . $it;

		$data = array(
			'@context' => 'http://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $items,
		);

		$this->build_json_data( $key, $data );
		$json = $this->receive_json_data( $key );

		$it++;

		if ( $json )
			return '<script type="application/ld+json">' . $json . '</script>' . "\r\n";

		return '';
	}

	/**
	 * Returns Schema.org '@id' part from URL.
	 *
	 * @since 3.0.0
	 *
	 * @param string $id The type of script. Must be escaped.
	 * @param string $from Where to generate from.
	 * @param array $args The URL generation args.
	 * @return string The JSON URL '@id'
	 */
	public function get_schema_url_id( $id, $from, $args = array() ) {

		switch ( $from ) {
			case 'currentpage' :
				$url = $this->get_current_permalink();
				break;

			case 'homepage' :
				$url = $this->get_homepage_permalink();
				break;

			case 'create' :
				$url = $this->create_canonical_url( $args );
				break;

			default :
				$url = '';
				break;
		}

		return $url;
	}

	/**
	 * Determines whether to use the SEO title or only the fallback page title.
	 *
	 * NOTE: Does not affect transient cache.
	 *
	 * @since 2.9.0
	 * @staticvar bool $cache
	 *
	 * @return bool
	 */
	public function ld_json_breadcrumbs_use_seo_title() {

		static $cache = null;

		/**
		 * Applies filters 'the_seo_framework_use_breadcrumb_seo_title' : boolean
		 *
		 * Determines whether to use the SEO title or only the fallback page title
		 * in breadcrumbs.
		 *
		 * @since 2.9.0
		 * @param bool $retval
		 */
		return isset( $cache ) ? $cache : $cache = (bool) \apply_filters( 'the_seo_framework_use_breadcrumb_seo_title', true );
	}

	/**
	 * Determines if breadcrumbs scripts are enabled.
	 *
	 * @since 2.6.0
	 * @staticvar bool $cache
	 *
	 * @return bool
	 */
	public function enable_ld_json_breadcrumbs() {

		static $cache = null;

		if ( isset( $cache ) )
			return $cache;

		/**
		 * Applies filters the_seo_framework_json_breadcrumb_output
		 * @since 2.4.2
		 * @param bool $output
		 */
		$filter = (bool) \apply_filters( 'the_seo_framework_json_breadcrumb_output', true );
		$option = $this->is_option_checked( 'ld_json_breadcrumbs' );

		return $cache = $filter && $option;
	}

	/**
	 * Determines if searchbox script is enabled.
	 *
	 * @since 2.6.0
	 * @staticvar bool $cache
	 *
	 * @return bool
	 */
	public function enable_ld_json_searchbox() {

		static $cache = null;

		if ( isset( $cache ) )
			return $cache;

		/**
		 * Applies filters 'the_seo_framework_json_search_output'
		 * @since 2.3.9
		 */
		$filter = (bool) \apply_filters( 'the_seo_framework_json_search_output', true );
		$option = $this->is_option_checked( 'ld_json_searchbox' );

		return $cache = $filter && $option;
	}

	/**
	 * Determines if Knowledge Graph Script is enabled.
	 *
	 * @since 2.6.5
	 * @staticvar bool $cache
	 *
	 * @return bool
	 */
	public function enable_ld_json_knowledge() {

		static $cache = null;

		if ( isset( $cache ) )
			return $cache;

		/**
		 * Applies filters 'the_seo_framework_json_search_output'
		 * @since 2.6.5
		 */
		$filter = (bool) \apply_filters( 'the_seo_framework_json_knowledge_output', true );
		$option = $this->is_option_checked( 'knowledge_output' );

		return $cache = $filter && $option;
	}
}
