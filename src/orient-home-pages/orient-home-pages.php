<?php

/*
 * Plugin Name: Bowdoin Orient — Modular Home Pages
 * Plugin URI: http://bowdoinorient.com/wordpress
 * Description:
 * Author: James Little
 * Author URI: http://jameslittle.me
 * Version: 1.0.0
 */

defined('ABSPATH') or die;

register_activation_hook(__FILE__, function () {
	$upload_dir = get_stylesheet_directory() . 'homepages';
	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0700);
	}
});

add_shortcode('orient_homepage', function () {
	$home_page_query = new WP_Query(
		array(
			'post_type' => 'home_page',
			'posts_per_page' => 1,
			'orderby' => 'date',
			'order' => 'DESC'
		)
	);

	$file = "";
	if ($home_page_query->have_posts()) {
		$home_page_query->the_post();
		$GLOBALS['home_page_post'] = $home_page_query->post;
		$homepages_dir = get_stylesheet_directory() . '/homepages/';
		$file = $homepages_dir . get_field('template');
		include($file);

	}
});

function home_render($module_name, $slot_index = NULL)
{
	$homemodules_dir = get_stylesheet_directory() . '/homemodules/';

	// if the global loop is not pointing to a post, reset the global loop
	// to point to our home page post so that we can use the_fields() below
	// to get the article IDs for each slot.
	if ($GLOBALS['home_page_post'] && $GLOBALS['post']->post_type != 'post') {
		// $GLOBALS['home_page_post'] is set in do_orient_homepage_shortcode()
		$GLOBALS['post'] = $GLOBALS['home_page_post'];
	}

	$ids = get_fields();
	$output = "";

	if ($ids["slot_" . $slot_index]) {
		$article_id = $ids["slot_" . $slot_index];
		$query = new WP_Query(array('p' => $article_id));
		$query->the_post();
		ob_start();
		include($homemodules_dir . $module_name . ".php");
		$output = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();
	} else if (array_key_exists("slot_" . $slot_index, $ids)) {
		if (is_preview()) {
			echo "<div class=\"home-slot\"><p>Slot $slot_index is empty.</p></div>";
		} else {
			echo "<!-- No article selected for slot $slot_index -->";
		}
	} else if (get_post_type() == 'post') {
		// If the current global post type is 'post', we're already in a loop, 
		// such as when home_render is called from within the loop in sections.php.
		// In that case, don't start a new loop, just render the current post
		// with the passed-in module.
		ob_start();
		include($homemodules_dir . $module_name . ".php");
		$output = ob_get_contents();
		ob_end_clean();
	} else {

		// If we're not in a loop, start a new loop that always returns no posts
		// so that we don't end up rendering the module with the current post
		// being the home page.
		$query = new WP_Query(array('post__in' => array(0)));
		$query->the_post();
		ob_start();
		include($homemodules_dir . $module_name . ".php");
		$output = ob_get_contents();
		ob_end_clean();
		wp_reset_postdata();
	}
	return $output;
}

include_once "custom-post-type.php";