<?php
/**
 * @package The_SEO_Framework\Classes
 */
namespace The_SEO_Framework;

defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

/**
 * The SEO Framework plugin
 * Copyright (C) 2015 - 2019 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
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
 * Class The_SEO_Framework\Detect
 *
 * Detects other plugins and themes
 *
 * @since 2.8.0
 */
class Detect extends Render {

	/**
	 * Determines if we're doing ajax.
	 *
	 * @todo use wp_doing_ajax() in a future version. Requires WP 4.7+.
	 * @since 2.9.0
	 * @staticvar bool $cache
	 *
	 * @return bool True if AJAX
	 */
	public function doing_ajax() {
		static $cache = null;

		return isset( $cache ) ? $cache : $cache = defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Tests if input URL matches current domain.
	 *
	 * @since 2.9.4
	 *
	 * @param string $url The URL to test. Required.
	 * @return bool true on match, false otherwise.
	 */
	public function matches_this_domain( $url ) {

		if ( ! $url )
			return false;

		static $home_domain;

		if ( ! $home_domain ) {
			$home_domain = \esc_url_raw( \get_home_url(), [ 'http', 'https' ] );
			//= Simply convert to HTTPS/HTTP based on is_ssl()
			$home_domain = $this->set_url_scheme( $home_domain );
		}

		$url = \esc_url_raw( $url, [ 'http', 'https' ] );
		//= Simply convert to HTTPS/HTTP based on is_ssl()
		$url = $this->set_url_scheme( $url );

		//= If they start with the same, we can assume it's the same domain.
		if ( 0 === stripos( $url, $home_domain ) )
			return true;

		return false;
	}

	/**
	 * Returns list of active plugins.
	 *
	 * @since 2.6.1
	 * @staticvar array $active_plugins
	 * @credits JetPack for most code.
	 *
	 * @return array List of active plugins.
	 */
	public function active_plugins() {

		static $active_plugins = null;

		if ( isset( $active_plugins ) )
			return $active_plugins;

		$active_plugins = (array) \get_option( 'active_plugins', [] );

		if ( \is_multisite() ) {
			// Due to legacy code, active_sitewide_plugins stores them in the keys,
			// whereas active_plugins stores them in the values.
			$network_plugins = array_keys( \get_site_option( 'active_sitewide_plugins', [] ) );
			if ( $network_plugins ) {
				$active_plugins = array_merge( $active_plugins, $network_plugins );
			}
		}

		sort( $active_plugins );

		return $active_plugins = array_unique( $active_plugins );
	}

	/**
	 * Filterable list of conflicting plugins.
	 *
	 * @since 2.6.0
	 * @credits JetPack for most code.
	 *
	 * @return array List of conflicting plugins.
	 */
	public function conflicting_plugins() {

		$conflicting_plugins = [
			'seo_tools'    => [
				'Yoast SEO'                  => 'wordpress-seo/wp-seo.php',
				'Yoast SEO Premium'          => 'wordpress-seo-premium/wp-seo-premium.php',
				'All in One SEO Pack'        => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
				'SEO Ultimate'               => 'seo-ultimate/seo-ultimate.php',
				'Gregs High Performance SEO' => 'gregs-high-performance-seo/ghpseo.php',
				'SEOPress'                   => 'wp-seopress/seopress.php',
				'Rank Math'                  => 'seo-by-rank-math/rank-math.php',
				'Smart Crawl'                => 'smartcrawl-seo/wpmu-dev-seo.php',
			],
			'sitemaps'     => [
				'Google XML Sitemaps'                  => 'google-sitemap-generator/sitemap.php',
				'Better WordPress Google XML Sitemaps' => 'bwp-google-xml-sitemaps/bwp-simple-gxs.php', // Remove?
				'Google XML Sitemaps for qTranslate'   => 'google-xml-sitemaps-v3-for-qtranslate/sitemap.php', // Remove?
				'XML Sitemap & Google News feeds'      => 'xml-sitemap-feed/xml-sitemap.php',
				'Google Sitemap by BestWebSoft'        => 'google-sitemap-plugin/google-sitemap-plugin.php',
				'Simple Wp Sitemap'                    => 'simple-wp-sitemap/simple-wp-sitemap.php',
				'XML Sitemaps'                         => 'xml-sitemaps/xml-sitemaps.php',
			],
			'open_graph'   => [
				'Facebook Open Graph Meta Tags for WordPress' => 'wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php',
				'Facebook Thumb Fixer'                  => 'facebook-thumb-fixer/_facebook-thumb-fixer.php',
				'NextGEN Facebook OG'                   => 'nextgen-facebook/nextgen-facebook.php',
				'Open Graph'                            => 'opengraph/opengraph.php',
				'Open Graph Protocol Framework'         => 'open-graph-protocol-framework/open-graph-protocol-framework.php',
				'Shareaholic2'                          => 'shareaholic/sexy-bookmarks.php',
				'Social Sharing Toolkit'                => 'social-sharing-toolkit/social_sharing_toolkit.php',
				'WordPress Social Sharing Optimization' => 'wpsso/wpsso.php',
				'WP Facebook Open Graph protocol'       => 'wp-facebook-open-graph-protocol/wp-facebook-ogp.php',
			],
			'twitter_card' => [],
		];

		/**
		 * @since 2.6.0
		 * @param array $conflicting_plugins The conflicting plugin list.
		 */
		return (array) \apply_filters( 'the_seo_framework_conflicting_plugins', $conflicting_plugins );
	}

	/**
	 * Fetches type of conflicting plugins.
	 *
	 * @since 2.6.0
	 *
	 * @param string $type The Key from $this->conflicting_plugins()
	 * @return array
	 */
	public function get_conflicting_plugins( $type = 'seo_tools' ) {

		$conflicting_plugins = $this->conflicting_plugins();

		if ( isset( $conflicting_plugins[ $type ] ) )
			return (array) \apply_filters( 'the_seo_framework_conflicting_plugins_type', $conflicting_plugins[ $type ], $type );

		return [];
	}

	/**
	 * Detect active plugin by constant, class or function existence.
	 *
	 * Note: Class check is 3 times as slow as defined check. Function check is 2 times as slow.
	 *
	 * @since 1.3.0
	 * @since 2.8.0 : 1. Can now check for globals.
	 *                2. Switched detection order from FAST to SLOW.
	 *
	 * @param array $plugins Array of array for constants, classes and / or functions to check for plugin existence.
	 * @return boolean True if plugin exists or false if plugin constant, class or function not detected.
	 */
	public function detect_plugin( $plugins ) {

		if ( isset( $plugins['globals'] ) ) {
			foreach ( $plugins['globals'] as $name ) {
				if ( isset( $GLOBALS[ $name ] ) ) {
					return true;
					break;
				}
			}
		}

		//* Check for constants
		if ( isset( $plugins['constants'] ) ) {
			foreach ( $plugins['constants'] as $name ) {
				if ( defined( $name ) ) {
					return true;
					break;
				}
			}
		}

		//* Check for functions
		if ( isset( $plugins['functions'] ) ) {
			foreach ( $plugins['functions'] as $name ) {
				if ( function_exists( $name ) ) {
					return true;
					break;
				}
			}
		}

		//* Check for classes
		if ( isset( $plugins['classes'] ) ) {
			foreach ( $plugins['classes'] as $name ) {
				if ( class_exists( $name ) ) {
					return true;
					break;
				}
			}
		}

		//* No globals, constant, function, or class found to exist
		return false;
	}

	/**
	 * Detect if you can use the given constants, functions and classes.
	 * All must be available to return true.
	 *
	 * @since 2.5.2
	 * @staticvar array $cache
	 * @uses $this->detect_plugin_multi()
	 *
	 * @param array $plugins Array of array for globals, constants, classes
	 *              and/or functions to check for plugin existence.
	 * @param bool $use_cache Bypasses cache if false
	 */
	public function can_i_use( array $plugins = [], $use_cache = true ) {

		if ( ! $use_cache )
			return $this->detect_plugin_multi( $plugins );

		static $cache = [];

		$mapped = [];

		//* Prepare multidimensional array for cache.
		foreach ( $plugins as $key => $func ) {
			if ( ! is_array( $func ) )
				return false; // doing it wrong...

			//* Sort alphanumeric by value, put values back after sorting.
			$func = array_flip( $func );
			ksort( $func );
			$func = array_flip( $func );

			//* Glue with underscore and space for debugging purposes.
			$mapped[ $key ] = $key . '_' . implode( ' ', $func );
		}

		ksort( $mapped );
		$key = serialize( $mapped ); // phpcs:ignore -- No objects are inserted, nor is this ever unserialized.

		if ( isset( $cache[ $key ] ) )
			return $cache[ $key ];

		return $cache[ $key ] = $this->detect_plugin_multi( $plugins );
	}

	/**
	 * Detect active plugin by constant, class or function existence.
	 * All parameters must match and return true.
	 *
	 * @since 2.5.2
	 *
	 * @param array $plugins Array of array for constants, classes and / or functions to check for plugin existence.
	 * @return bool True if ALL functions classes and constants exists or false if plugin constant, class or function not detected.
	 */
	public function detect_plugin_multi( array $plugins ) {

		//* Check for classes
		if ( isset( $plugins['classes'] ) ) {
			foreach ( $plugins['classes'] as $name ) {
				if ( ! class_exists( $name ) ) {
					return false;
					break;
				}
			}
		}

		//* Check for functions
		if ( isset( $plugins['functions'] ) ) {
			foreach ( $plugins['functions'] as $name ) {
				if ( ! function_exists( $name ) ) {
					return false;
					break;
				}
			}
		}

		//* Check for constants
		if ( isset( $plugins['constants'] ) ) {
			foreach ( $plugins['constants'] as $name ) {
				if ( ! defined( $name ) ) {
					return false;
					break;
				}
			}
		}

		//* All classes, functions and constant have been found to exist
		return true;
	}

	/**
	 * Checks if the (parent) theme name is loaded.
	 *
	 * @since 2.1.0
	 *
	 * @param string|array $themes the current theme name.
	 * @return bool is theme active.
	 */
	public function is_theme( $themes = '' ) {

		if ( empty( $themes ) )
			return false;

		$wp_get_theme = \wp_get_theme();

		$theme_parent = strtolower( $wp_get_theme->get( 'Template' ) );
		$theme_name   = strtolower( $wp_get_theme->get( 'Name' ) );

		if ( is_string( $themes ) ) {
			$themes = strtolower( $themes );
			if ( $themes === $theme_parent || $themes === $theme_name )
				return true;
		} elseif ( is_array( $themes ) ) {
			foreach ( $themes as $theme ) {
				$theme = strtolower( $theme );
				if ( $theme === $theme_parent || $theme === $theme_name ) {
					return true;
					break;
				}
			}
		}

		return false;
	}

	/**
	 * Determines if other SEO plugins are active.
	 *
	 * @since 1.3.0
	 * @since 2.6.0 Uses new style detection.
	 * @since 3.1.0: The filter no longer short-circuits the function when it's false.
	 *
	 * @return bool SEO plugin detected.
	 */
	public function detect_seo_plugins() {

		static $detected = null;

		if ( isset( $detected ) )
			return $detected;

		$active_plugins = $this->active_plugins();

		if ( ! empty( $active_plugins ) ) {
			$conflicting_plugins = $this->get_conflicting_plugins( 'seo_tools' );

			foreach ( $conflicting_plugins as $plugin_name => $plugin ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					/**
					 * @since 2.6.1
					 * @since 3.1.0 Added second and third parameters.
					 * @param bool   $detected    Whether the plugin should be detected.
					 * @param string $plugin_name The plugin name as defined in `$this->conflicting_plugins()`.
					 * @param string $plugin      The plugin that's been detected.
					 */
					$detected = \apply_filters_ref_array(
						'the_seo_framework_seo_plugin_detected',
						[
							true,
							$plugin_name,
							$plugin,
						]
					);
					if ( $detected ) break;
				}
			}
		}

		return $detected = (bool) $detected;
	}

	/**
	 * Determines if other Open Graph or SEO plugins are active.
	 *
	 * @since 1.3.0
	 * @since 2.8.0: No longer checks for old style filter.
	 * @since 3.1.0: The filter no longer short-circuits the function when it's false.
	 *
	 * @return bool True if OG or SEO plugin detected.
	 */
	public function detect_og_plugin() {

		static $detected = null;

		if ( isset( $detected ) )
			return $detected;

		//* Detect SEO plugins beforehand.
		if ( $detected = $this->detect_seo_plugins() )
			return $detected;

		$active_plugins = $this->active_plugins();

		if ( ! empty( $active_plugins ) ) {
			$conflicting_plugins = $this->get_conflicting_plugins( 'open_graph' );

			foreach ( $conflicting_plugins as $plugin_name => $plugin ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					/**
					 * @since 2.6.1
					 * @since 3.1.0 Added second and third parameters.
					 * @param bool   $detected    Whether the plugin should be detected.
					 * @param string $plugin_name The plugin name as defined in `$this->conflicting_plugins()`.
					 * @param string $plugin      The plugin that's been detected.
					 */
					$detected = \apply_filters_ref_array(
						'the_seo_framework_og_plugin_detected',
						[
							true,
							$plugin_name,
							$plugin,
						]
					);
					if ( $detected ) break;
				}
			}
		}

		return $detected = (bool) $detected;
	}

	/**
	 * Determines if other Twitter Card plugins are active.
	 *
	 * @since 2.6.0
	 * @since 3.1.0: The filter no longer short-circuits the function when it's false.
	 * @staticvar bool $detected
	 *
	 * @return bool Twitter Card plugin detected.
	 */
	public function detect_twitter_card_plugin() {

		static $detected = null;

		if ( isset( $detected ) )
			return $detected;

		//* Detect SEO plugins beforehand.
		if ( $detected = $this->detect_seo_plugins() )
			return $detected;

		$active_plugins = $this->active_plugins();

		if ( ! empty( $active_plugins ) ) {
			$conflicting_plugins = $this->get_conflicting_plugins( 'twitter_card' );

			foreach ( $conflicting_plugins as $plugin_name => $plugin ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					/**
					 * @since 2.6.1
					 * @param bool   $detected    Whether the plugin should be detected.
					 * @param string $plugin_name The plugin name as defined in `$this->conflicting_plugins()`.
					 * @param string $plugin      The plugin that's been detected.
					 */
					$detected = \apply_filters_ref_array(
						'the_seo_framework_twittercard_plugin_detected',
						[
							true,
							$plugin_name,
							$plugin,
						]
					);
					if ( $detected ) break;
				}
			}
		}

		return $detected = (bool) $detected;
	}

	/**
	 * Determines if other Schema.org LD+Json plugins are active.
	 *
	 * @since 1.3.0
	 * @since 2.6.1 Always return false. Let other plugin authors decide its value.
	 * @TODO Make a list of plugins, so the users are well-informed.
	 *
	 * @return bool Whether another Schema.org plugin is active.
	 */
	public function has_json_ld_plugin() {
		/**
		 * @since 2.6.5
		 * @param bool $detected Whether a conflicting schema plugin is detected.
		 */
		return (bool) \apply_filters( 'the_seo_framework_ldjson_plugin_detected', false );
	}

	/**
	 * Determines if other Sitemap plugins are active.
	 *
	 * @since 2.1.0
	 * @since 3.1.0: The filter no longer short-circuits the function when it's false.
	 * @staticvar bool $detected
	 *
	 * @return bool
	 */
	public function detect_sitemap_plugin() {

		static $detected = null;

		if ( isset( $detected ) )
			return $detected;

		//* Detect SEO plugins beforehand.
		if ( $detected = $this->detect_seo_plugins() )
			return $detected;

		$active_plugins = $this->active_plugins();

		if ( ! empty( $active_plugins ) ) {
			$conflicting_plugins = $this->get_conflicting_plugins( 'sitemaps' );

			foreach ( $conflicting_plugins as $plugin_name => $plugin ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					/**
					 * @since 2.6.1
					 * @param bool   $detected    Whether the plugin should be detected.
					 * @param string $plugin_name The plugin name as defined in `$this->conflicting_plugins()`.
					 * @param string $plugin      The plugin that's been detected.
					 */
					$detected = \apply_filters(
						'the_seo_framework_sitemap_plugin_detected',
						[
							true,
							$plugin_name,
							$plugin,
						]
					);
					if ( $detected ) break;
				}
			}
		}

		return $detected = (bool) $detected;
	}

	/**
	 * Determines whether to add a line within robots based by plugin detection, or sitemap output option.
	 *
	 * @since 2.6.0
	 * @since 2.8.0 Added check_option parameter.
	 * @since 2.9.0 Now also checks for subdirectory installations.
	 * @since 2.9.2 Now also checks for permalinks.
	 * @since 2.9.3 Now also checks for sitemap_robots option.
	 * @since 3.1.0 Removed Jetpack's sitemap check -- it's no longer valid.
	 *
	 * @param bool $check_option Whether to check for sitemap option.
	 * @return bool True when no conflicting plugins are detected or when The SEO Framework's Sitemaps are output.
	 */
	public function can_do_sitemap_robots( $check_option = true ) {

		if ( $check_option ) {
			if ( ! $this->get_option( 'sitemaps_output' ) || ! $this->get_option( 'sitemaps_robots' ) )
				return false;
		}

		if ( $this->is_subdirectory_installation() )
			return false;

		if ( ! $this->pretty_permalinks )
			return false;

		return true;
	}

	/**
	 * Detects presence of robots.txt in root folder.
	 *
	 * @since 2.5.2
	 * @staticvar $has_robots
	 *
	 * @return bool Whether the robots.txt file exists.
	 */
	public function has_robots_txt() {

		static $has_robots = null;

		if ( isset( $has_robots ) )
			return $has_robots;

		$path = \get_home_path() . 'robots.txt';

		return $has_robots = file_exists( $path );
	}

	/**
	 * Detects presence of sitemap.xml in root folder.
	 *
	 * @since 2.5.2
	 * @staticvar bool $has_map
	 *
	 * @return bool Whether the sitemap.xml file exists.
	 */
	public function has_sitemap_xml() {

		static $has_map = null;

		if ( isset( $has_map ) )
			return $has_map;

		$path = \get_home_path() . 'sitemap.xml';

		return $has_map = file_exists( $path );
	}

	/**
	 * Determines if WP is above or below a version
	 *
	 * @since 2.2.1
	 * @since 2.3.8 Added caching
	 * @since 2.8.0 No longer overwrites global $wp_version
	 * @since 3.1.0 1. No longer caches.
	 *              2. Removed redundant parameter checks.
	 *              3. Now supports x.yy.zz WordPress versions.
	 *
	 * @param string $version the three part version to compare to WordPress
	 * @param string $compare the comparing operator, default "$version >= Current WP Version"
	 * @return bool True if the WordPress version comparison passes.
	 */
	public function wp_version( $version = '4.3.0', $compare = '>=' ) {

		$wp_version = $GLOBALS['wp_version'];

		/**
		 * Add a .0 if WP outputs something like 4.3 instead of 4.3.0
		 * Does consider 4.xx, which will become 4.xx.0
		 */
		if ( 1 === substr_count( $wp_version, '.' ) )
			$wp_version = $wp_version . '.0';

		return (bool) version_compare( $wp_version, $version, $compare );
	}

	/**
	 * Checks for current theme support.
	 *
	 * Maintains detection cache, array and strings are mixed through foreach loops.
	 *
	 * @since 2.2.5
	 * @since 3.1.0 Removed caching
	 *
	 * @param string|array required $features The features to check for.
	 * @return bool theme support.
	 */
	public function detect_theme_support( $features ) {

		foreach ( (array) $features as $feature ) {
			if ( \current_theme_supports( $feature ) ) {
				return true;
				break;
			}
			continue;
		}

		return false;
	}

	/**
	 * Checks a theme's support for title-tag.
	 *
	 * @since 2.6.0
	 * @since 3.1.0 Removed caching
	 *
	 * @return bool
	 */
	public function current_theme_supports_title_tag() {
		return $this->detect_theme_support( 'title-tag' );
	}

	/**
	 * Detect if the current screen type is a page or taxonomy.
	 *
	 * @since 2.3.1
	 * @staticvar array $is_page
	 *
	 * @param string $type the Screen type
	 * @return bool true if post type is a page or post
	 */
	public function is_post_type_page( $type ) {

		static $is_page = [];

		if ( isset( $is_page[ $type ] ) )
			return $is_page[ $type ];

		$post_page = (array) \get_post_types( [ 'public' => true ] );

		foreach ( $post_page as $screen ) {
			if ( $type === $screen ) {
				return $is_page[ $type ] = true;
				break;
			}
		}

		return $is_page[ $type ] = false;
	}

	/**
	 * Detect WordPress language.
	 * Considers en_UK, en_US, en, etc.
	 *
	 * @since 2.6.0
	 * @since 3.1.0 Removed caching.
	 *
	 * @param string $locale Required, the locale.
	 * @return bool Whether the input $locale is in the current WordPress locale.
	 */
	public function check_wp_locale( $locale = '' ) {
		return false !== strpos( \get_locale(), $locale );
	}

	/**
	 * Determines if the post type is disabled from SEO all optimization.
	 *
	 * @since 3.1.0
	 * @since 3.1.2 Now is fiterable.
	 *
	 * @param string $post_type The post type, optional. Leave empty to autodetermine type.
	 * @return bool True if disabled, false otherwise.
	 */
	public function is_post_type_disabled( $post_type = '' ) {

		$post_type = $post_type ?: \get_post_type() ?: $this->get_admin_post_type();

		/**
		 * @since 3.1.2
		 * @param bool   $disabled
		 * @param string $post_type
		 */
		return \apply_filters( 'the_seo_framework_post_type_disabled',
			isset(
				$this->get_option( 'disabled_post_types' )[ $post_type ]
			),
			$post_type
		);
	}

	/**
	 * Determines if the post type is compatible with The SEO Framework inpost metabox.
	 *
	 * @since 2.3.5
	 * @since 3.1.0 1. The first parameter is now required.
	 *              2. Added caching.
	 * @staticvar bool $has_filter
	 *
	 * @param string $post_type
	 * @return bool True if post type is supported.
	 */
	public function post_type_supports_inpost( $post_type ) {

		if ( ! $post_type ) return false;

		static $has_filter = null;

		if ( is_null( $has_filter ) )
			$has_filter = \has_filter( 'the_seo_framework_custom_post_type_support' );

		if ( $has_filter ) {
			/**
			 * Determines the required post type features before TSF supports it.
			 * @since 2.3.5
			 * @since 3.0.4 Default parameter now is `[]` instead of `['title','editor']`.
			 * @param array $supports The required post type support, like 'title', 'editor'.
			 */
			$supports = (array) \apply_filters( 'the_seo_framework_custom_post_type_support', [] );

			foreach ( $supports as $support ) {
				if ( ! \post_type_supports( $post_type, $support ) ) {
					return false;
					break;
				}
				continue;
			}
		}

		return true;
	}

	/**
	 * Determines if post type supports The SEO Framework.
	 *
	 * @since 2.3.9
	 * @since 3.1.0 1. Removed caching.
	 *              2. Now works in admin.
	 *
	 * @param string $post_type The current post type.
	 * @return bool true of post type is supported.
	 */
	public function post_type_supports_custom_seo( $post_type = '' ) {
		$post_type = $post_type ?: \get_post_type() ?: $this->get_admin_post_type();
		return $post_type && $this->is_post_type_supported( $post_type ) && $this->post_type_supports_inpost( $post_type );
	}

	/**
	 * Determines if the taxonomy supports The SEO Framework.
	 *
	 * Checks if at least one taxonomy objects post type supports The SEO Framework,
	 * and wether the taxonomy is public and rewritable.
	 *
	 * @since 3.1.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return bool True if at least one post type in taxonomy isn't disabled.
	 */
	public function taxonomy_supports_custom_seo( $taxonomy = '' ) {

		$taxonomy = $taxonomy ?: $this->get_current_taxonomy();
		if ( ! $taxonomy ) return false;

		/**
		 * @since 3.1.0
		 * @param bool   $post_type Whether the post type is supported
		 * @param string $post_type_evaluated The evaluated post type.
		 */
		return (bool) \apply_filters_ref_array( 'the_seo_framework_supported_taxonomy',
			[
				$taxonomy
					&& ! $this->is_taxonomy_disabled( $taxonomy )
					&& $this->is_taxonomy_public( $taxonomy ),
				$taxonomy,
			]
		);
	}

	/**
	 * Detects if the current or inputted post type is supported and not disabled.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $post_type
	 * @return bool
	 */
	public function is_post_type_supported( $post_type = '' ) {
		$post_type = $post_type ?: \get_post_type() ?: $this->get_admin_post_type();
		/**
		 * @since 2.6.2
		 * @since 3.1.0 The first parameter is always a boolean now.
		 * @param bool   $supported           Whether the post type is supported.
		 * @param string $post_type_evaluated The evaluated post type.
		 */
		return (bool) \apply_filters_ref_array( 'the_seo_framework_supported_post_type',
			[
				$post_type
					&& ! $this->is_post_type_disabled( $post_type )
					&& in_array( $post_type, $this->get_rewritable_post_types(), true ),
				$post_type,
			]
		);
	}

	/**
	 * Checks (current) Post Type for having taxonomical archives.
	 *
	 * @since 2.9.3
	 * @staticvar array $cache
	 * @global \WP_Screen $current_screen
	 *
	 * @param string $post_type Optional. The post type to check.
	 * @return bool True when the post type has taxonomies.
	 */
	public function post_type_supports_taxonomies( $post_type = '' ) {

		static $cache = [];

		if ( isset( $cache[ $post_type ] ) )
			return $cache[ $post_type ];

		$post_type = $post_type ?: \get_post_type() ?: $this->get_admin_post_type();
		if ( ! $post_type ) return false;

		if ( \get_object_taxonomies( $post_type, 'names' ) )
			return $cache[ $post_type ] = true;

		return $cache[ $post_type ] = false;
	}

	/**
	 * Returns a list of all supported post types.
	 *
	 * @since 3.1.0
	 * @stativar array $cache
	 *
	 * @return array The supported post types.
	 */
	public function get_supported_post_types() {

		static $cache = [];
		// Can't be recursively empty. Right?
		if ( $cache ) return $cache;

		return $cache = array_values(
			array_filter( $this->get_rewritable_post_types(), [ $this, 'is_post_type_supported' ] )
		);
	}

	/**
	 * Gets all post types that could possibly support SEO.
	 *
	 * @since 3.1.0
	 * @since 3.2.1 Added cache.
	 * @staticvar $cache
	 *
	 * @return array The post types with rewrite capabilities.
	 */
	protected function get_rewritable_post_types() {
		static $cache = null;
		//? array_values() because get_post_types() gives a sequential array.
		return isset( $cache ) ? $cache : $cache = array_unique(
			array_merge(
				$this->get_forced_supported_post_types(),
				array_values( (array) \get_post_types( [
					'public'  => true,
					'rewrite' => true,
				] ) )
			)
		);
	}

	/**
	 * Returns a list of supported post types.
	 *
	 * @since 3.1.0
	 * @staticvar $cache
	 *
	 * @return array Forced supported post types
	 */
	protected function get_forced_supported_post_types() {

		static $cache = null;
		/**
		 * @since 3.1.0
		 * @param array $forced Forced supported post types
		 */
		return isset( $cache ) ? $cache : $cache = (array) \apply_filters(
			'the_seo_framework_forced_supported_post_types',
			array_values( \get_post_types( [
				'public'   => true,
				'_builtin' => true,
			] ) )
		);
	}

	/**
	 * Checks if at least one taxonomy objects post type supports The SEO Framework.
	 *
	 * @since 3.1.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return bool True if at least one post type in taxonomy is supported.
	 */
	public function is_taxonomy_disabled( $taxonomy = '' ) {

		$taxonomy = $taxonomy ?: $this->get_current_taxonomy();
		if ( ! $taxonomy ) return true;

		$tax = \get_taxonomy( $taxonomy );

		if ( false === $tax ) return true;

		if ( ! empty( $tax->object_type ) ) {
			foreach ( $tax->object_type as $type ) {
				if ( ! $this->is_post_type_disabled( $type ) )
					return false;
			}
		}

		return true;
	}

	/**
	 * Checks whether the taxonomy is public and rewritable.
	 *
	 * @since 3.1.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return bool
	 */
	public function is_taxonomy_public( $taxonomy = '' ) {

		$taxonomy = $taxonomy ?: $this->get_current_taxonomy();
		if ( ! $taxonomy ) return false;

		$tax = \get_taxonomy( $taxonomy );

		if ( false === $tax ) return false;

		return ! empty( $tax->public )
			&& ( ! empty( $tax->_builtin ) || ! empty( $tax->rewrite ) );
	}

	/**
	 * Determines whether a page or blog is on front.
	 *
	 * @since 2.6.0
	 * @since 3.1.0 Removed caching.
	 *
	 * @return bool
	 */
	public function has_page_on_front() {
		return 'page' === \get_option( 'show_on_front' );
	}

	/**
	 * Detects if we're on a Gutenberg page.
	 *
	 * @since 3.1.0
	 * @since 3.2.0 : 1. Now detects the WP 5.0 block editor.
	 *                2. Method is now public.
	 *
	 * @return bool
	 */
	public function is_gutenberg_page() {
		if ( function_exists( '\\use_block_editor_for_post' ) )
			return ! empty( $GLOBALS['post'] ) && \use_block_editor_for_post( $GLOBALS['post'] );

		if ( function_exists( '\\is_gutenberg_page' ) )
			return \is_gutenberg_page();

		return false;
	}

	/**
	 * Determines if the current theme supports the custom logo addition.
	 *
	 * @since 2.8.0
	 * @since 3.1.0: 1. No longer checks for WP version 4.5+.
	 *               2. No longer uses caching.
	 *
	 * @return bool
	 */
	public function can_use_logo() {
		return $this->detect_theme_support( 'custom-logo' );
	}

	/**
	 * Determines if the current installation is on a subdirectory.
	 *
	 * @since 2.9.0
	 * @staticvar $bool $cache
	 *
	 * @return bool
	 */
	public function is_subdirectory_installation() {

		static $cache = null;

		if ( isset( $cache ) )
			return $cache;

		$parsed_url = \wp_parse_url( \get_option( 'home' ) );

		return $cache = ! empty( $parsed_url['path'] ) && ltrim( $parsed_url['path'], ' \\/' );
	}
}
