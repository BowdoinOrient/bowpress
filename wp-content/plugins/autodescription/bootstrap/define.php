<?php
/**
 * @package The_SEO_Framework
 * @subpackage Bootstrap
 * No need to annotate namespacing here... there are only plain PHP queries.
 */

defined( 'THE_SEO_FRAMEWORK_DB_VERSION' ) or die;

/**
 * The SEO Framework plugin
 * Copyright (C) 2018 - 2019 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
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
 * Tells the world the plugin is present and to be used.
 * @since 3.1.0
 */
define( 'THE_SEO_FRAMEWORK_PRESENT', true );

/**
 * The plugin options database option_name key.
 *
 * Used for storing the SEO options array.
 *
 * @since 2.2.2
 * @param string THE_SEO_FRAMEWORK_SITE_OPTIONS
 */
define( 'THE_SEO_FRAMEWORK_SITE_OPTIONS', (string) apply_filters( 'the_seo_framework_site_options', 'autodescription-site-settings' ) );

/**
 * The plugin network options.
 *
 * Unused in the code.
 *
 * @since 2.2.2
 * @param string THE_SEO_FRAMEWORK_NETWORK_OPTIONS
 */
define( 'THE_SEO_FRAMEWORK_NETWORK_OPTIONS', (string) apply_filters( 'the_seo_framework_network_settings', 'autodescription-network-settings' ) );

/**
 * Plugin term options key.
 * @since 2.7.0
 * @param string THE_SEO_FRAMEWORK_TERM_OPTIONS
 */
define( 'THE_SEO_FRAMEWORK_TERM_OPTIONS', (string) apply_filters( 'the_seo_framework_term_options', 'autodescription-term-settings' ) );

/**
 * Plugin user term options key.
 * @since 2.7.0
 * @param string THE_SEO_FRAMEWORK_USER_OPTIONS
 */
define( 'THE_SEO_FRAMEWORK_USER_OPTIONS', (string) apply_filters( 'the_seo_framework_user_options', 'autodescription-user-settings' ) );

/**
 * Plugin updates cache key.
 * @since 3.1.0
 * @param string THE_SEO_FRAMEWORK_SITE_CACHE
 */
define( 'THE_SEO_FRAMEWORK_SITE_CACHE', (string) apply_filters( 'the_seo_framework_site_cache', 'autodescription-updates-cache' ) );

/**
 * The plugin map URL. Has a trailing slash.
 * Used for calling browser files.
 * @since 2.2.2
 */
define( 'THE_SEO_FRAMEWORK_DIR_URL', plugin_dir_url( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE ) );

/**
 * The plugin file relative to the plugins dir. Does not have a trailing slash.
 * @since 2.2.8
 */
define( 'THE_SEO_FRAMEWORK_PLUGIN_BASENAME', plugin_basename( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE ) );

/**
 * The plugin map absolute path.
 * Used for calling php files.
 * @since 2.2.2
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH', dirname( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE ) . DIRECTORY_SEPARATOR );

/**
 * The plugin views map absolute path.
 * @since 2.7.0
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_VIEWS', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR );

/**
 * The plugin class map absolute path.
 * @since 2.2.9
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_CLASS', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR );

/**
 * The plugin trait map absolute path.
 * @since 3.1.0
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_TRAIT', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'traits' . DIRECTORY_SEPARATOR );

/**
 * The plugin interface map absolute path.
 * @since 2.8.0
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_INTERFACE', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR );

/**
 * The plugin function map absolute path.
 * @since 2.2.9
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_FUNCT', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR );

/**
 * The plugin function map absolute path.
 * @since 2.8.0
 */
define( 'THE_SEO_FRAMEWORK_DIR_PATH_COMPAT', THE_SEO_FRAMEWORK_DIR_PATH . 'inc' . DIRECTORY_SEPARATOR . 'compat' . DIRECTORY_SEPARATOR );
