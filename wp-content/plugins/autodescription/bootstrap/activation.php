<?php
/**
 * @package The_SEO_Framework
 * @subpackage Bootstrap
 */
namespace The_SEO_Framework\Bootstrap;

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
 * @since 3.2.4 Applied namspacing to this file. All method names have changed.
 */

//! @php7+ convert to IIFE
_activation_setup_sitemap();
_activation_set_options_autoload();
_activation_set_plugin_check_caches();

/**
 * Nudges the plugin to check for conflicting SEO plugins.
 *
 * When found, it'll output a single dismissible notification.
 *
 * @since 3.1.0
 * @access private
 */
function _activation_set_plugin_check_caches() {

	$tsf = \the_seo_framework();

	if ( $tsf->loaded ) {
		$tsf->set_plugin_check_caches();
	}
}

/**
 * Add and Flush rewrite rules on plugin activation.
 *
 * @since 2.6.6
 * @since 2.7.1: 1. Now no longer reinitializes global $wp_rewrite.
 *               2. Now always listens to the preconditions of the sitemap addition.
 *               3. Now flushes the rules on shutdown.
 * @since 2.8.0: Added namespace and renamed function.
 * @access private
 */
function _activation_setup_sitemap() {

	$tsf = \the_seo_framework();

	if ( $tsf->loaded ) {
		$tsf->rewrite_rule_sitemap();
		\add_action( 'shutdown', 'flush_rewrite_rules' );
	}
}

/**
 * Turns on auto loading for The SEO Framework's main options.
 *
 * @since 2.9.2
 * @since 3.1.0 No longer deletes the whole option array, trying to reactivate auto loading.
 * @access private
 */
function _activation_set_options_autoload() {

	$tsf = \the_seo_framework();

	if ( $tsf->loaded ) {
		$options = $tsf->get_all_options();
		$setting = THE_SEO_FRAMEWORK_SITE_OPTIONS;

		\remove_all_filters( "pre_update_option_{$setting}" );
		\remove_all_actions( "update_option_{$setting}" );
		\remove_all_filters( "sanitize_option_{$setting}" );

		$temp_options = $options;
		//? Write a small difference, so the change will be forwarded to the database.
		if ( is_array( $temp_options ) )
			$temp_options['update_buster'] = (int) time();

		$_success = \update_option( $setting, $temp_options, 'yes' );
		if ( $_success )
			\update_option( $setting, $options, 'yes' );
	}
}
