<?php
/**
 * @package The_SEO_Framework
 * @subpackage Bootstrap
 *
 * @NOTE This file MUST be written according to WordPress' minimum PHP requirements.
 *       Which is PHP 5.2.
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
 * This file holds functions for testing the plugin after upgrade.
 * This file will only be called ONCE if the required version option is lower
 * compared to The SEO Framework version constant.
 *
 * @since 3.1.0
 * @access private
 */

the_seo_framework_pre_boot_test();
/**
 * Tests plugin upgrade.
 *
 * @since 3.1.0
 * @access private
 * @link http://php.net/eol.php
 * @link https://codex.wordpress.org/WordPress_Versions
 */
function the_seo_framework_pre_boot_test() {

	$ms = is_multisite();

	if ( $ms && function_exists( 'get_network' ) ) {
		//* Try bypassing testing and deactivation gaming when the main blog has already been tested.

		/**
		 * @since 2.9.4
		 * Delete old and redundant network option.
		 */
		delete_site_option( 'the_seo_framework_tested_upgrade_version' );

		$nw = get_network();
		if ( $nw instanceof WP_Network ) {
			if ( get_blog_option( $nw->site_id, 'the_seo_framework_tested_upgrade_version' ) ) {
				update_option( 'the_seo_framework_tested_upgrade_version', THE_SEO_FRAMEWORK_DB_VERSION );
				return;
			}
		}
		//= Free memory.
		unset( $nw );
	}

	$requirements = array(
		'php' => '50400',
		'wp'  => '37965',
	);

	// phpcs:disable Generic.Formatting.MultipleStatementAlignment.NotSameWarning
	   ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < $requirements['php'] and $test = 1 // precision alignment ok.
	or $GLOBALS['wp_db_version'] < $requirements['wp'] and $test = 2
	or $test = true;
	// phpcs:enable Generic.Formatting.MultipleStatementAlignment.NotSameWarning

	//* All good.
	if ( true === $test ) {
		update_option( 'the_seo_framework_tested_upgrade_version', THE_SEO_FRAMEWORK_DB_VERSION );
		return;
	}

	if ( $ms ) {
		$_plugins     = get_site_option( 'active_sitewide_plugins' );
		$network_mode = isset( $_plugins[ plugin_basename( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE ) ] );
	} else {
		$network_mode = false;
	}

	if ( ! function_exists( 'deactivate_plugins' ) )
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$admin  = is_admin();
	$silent = ! $admin;

	//* Not good. Deactivate plugin.
	deactivate_plugins( plugin_basename( THE_SEO_FRAMEWORK_PLUGIN_BASE_FILE ), $silent, $network_mode );

	//* Don't die on front-end. Live, my friend.
	if ( ! $admin )
		return;

	switch ( $test ) :
		case 1:
			//* PHP requirements not met, always count up to encourage best standards.
			$requirement = 'PHP 5.4.0 or later';
			$issue       = 'PHP version';
			$version     = PHP_VERSION;
			$subtitle    = 'Server Requirements';
			break;

		case 2:
			//* WordPress requirements not met.
			$requirement = 'WordPress 4.6 or later';
			$issue       = 'WordPress version';
			$version     = $GLOBALS['wp_version'];
			$subtitle    = 'WordPress Requirements';
			break;

		default:
			wp_die( 'oi' );
			break;
	endswitch;

	//* network_admin_url() falls back to admin_url() on single. But networks can enable single too.
	$pluginspage = $network_mode ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );

	//* Let's have some fun with teapots.
	$response = floor( time() / DAY_IN_SECONDS ) === floor( strtotime( 'first day of April ' . date( 'Y' ) ) / DAY_IN_SECONDS ) ? 418 : 500;

	wp_die(
		sprintf(
			'<p><strong>The SEO Framework</strong> requires <em>%s</em>. Sorry about that!<br>Your %s is: <code>%s</code></p>
			<p>Do you want to <strong><a onclick="window.history.back()" href="%s">go back</a></strong>?</p>',
			esc_html( $requirement ), esc_html( $issue ), esc_html( $version ), esc_url( $pluginspage )
		),
		sprintf( 'The SEO Framework &laquo; %s', esc_attr( $subtitle ) ),
		array( 'response' => intval( $response ) )
	);
}
