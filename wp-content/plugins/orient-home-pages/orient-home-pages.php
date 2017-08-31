<?php

/*
 * Plugin Name: Bowdoin Orient — Modular Home Pages
 * Plugin URI: http://bowdoinorient.com/wordpress
 * Description:
 * Author: James Little
 * Author URI: http://jameslittle.me
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) or die;

/****************************************************************************
 ****************************   ADMIN SECTION  ******************************
 ****************************************************************************/

add_action('admin_menu', 'setup_home_page_menu');
add_action('admin_enqueue_scripts', 'home_page_load_js');

function home_page_load_js() {
	wp_enqueue_script('home_page_admin_js', plugins_url('/home-page-script.js', __FILE__));
}

function setup_home_page_menu() {
	add_menu_page( 'Home Page Options', 'Home Page', 'manage_options', 'home-pages', 'home_page_init' );
}

function home_page_init() {
	if(isset($_POST['file'])) {
		$filename = filter_var ( $_POST['file'], FILTER_SANITIZE_URL);
		update_option('orient_homepage_filename', $filename);

		$articlelist = implode($_POST['page_id'], ',');
		update_option('orient_homepage_articles', $articlelist);
	}

	$filename = get_option('orient_homepage_filename');
	$articlelist = explode(',', get_option('orient_homepage_articles'));

	echo "<h2>Pick a Template</h2>";
	echo "<form id=\"page\" name=\"page\" method=\"POST\">";
	echo_home_page_list($filename);

	echo "<h2>Pick the Articles</h2>";
	echo "<div id=\"article-selects\">...</div>";
	echo "<p><button class=\"button button-primary\" type=\"submit\">Update Home Page</button></p>";
	echo "</form>";
	echo "<div id=\"input-template\" style=\"display: none\">";
	echo_article_select_input($articlelist);
	echo "</div>";
	echo "<script type=\"text/javascript\">var articleIds = " . json_encode($articlelist)  . ";</script>";
}

function echo_home_page_list($filename) {
	$homepages_dir = get_stylesheet_directory() . '/homepages/';
	$files = scandir($homepages_dir);

	foreach($files as $file) {
		if (substr($file, -4) == ".php") {
			$fullpath = $homepages_dir . $file;
			$contents = file_get_contents($fullpath);
			$comment = explode("*/", $contents, 2)[0];
			$keyvalpairs = explode("/*", $comment, 2)[1];
			$keyvalstrs = array_filter(explode("\n * ", $keyvalpairs));
			$keyvals = [];

			foreach($keyvalstrs as $kvstring) {
				$kvarray = explode(": ", $kvstring);
				$keyvals[trim($kvarray[0])] = trim($kvarray[1]);
			}

			$selected = $filename == $file ? "checked" : "";

			echo "<label><input name=\"file\" value=\"$file\" data-count=\"" . $keyvals['Article Count'] ."\"type=\"radio\" $selected>" . $keyvals['Page Name'] . " (" . $keyvals['Article Count'] . ")</input></label>";
		}
	}
}

function echo_article_select_input($articlelist) {
	echo "<select name=\"page_id[]\" id=\"page_id\">";
	echo "<option value=\"\" disabled>Select an article...</option>";
	$args = array( 'numberposts' => 100);
	$posts = get_posts($args);

	echo "<option value=\"\">No article (module should disappear)</option>";

	foreach($posts as $post) {
		setup_postdata($post);
		echo "<option value=\"" . $post->ID . "\">" . substr($post->post_title, 0, 100) . "</option>";
	}

	echo "</select>";
}

register_activation_hook(__FILE__, home_page_activate);

function home_page_activate() {
    $upload_dir = get_stylesheet_directory() . 'homepages';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }
}


/****************************************************************************
 ****************************   FRONT SECTION  ******************************
 ****************************************************************************/

add_shortcode('orient_homepage', 'do_orient_homepage_shortcode');

function do_orient_homepage_shortcode() {
    $homepages_dir = get_stylesheet_directory() . '/homepages/';
	$file = $homepages_dir . get_option('orient_homepage_filename');
	ob_start();
	include($file);
    $output = ob_get_contents();
	ob_end_clean();
	echo $output;
}

function home_render($module_name, $article_index = NULL) {
	$homemodules_dir = get_stylesheet_directory() . '/homemodules/';
	$ids = explode(',', get_option('orient_homepage_articles'));
	$article_index = $article_index - 1;

	if (isset($ids[$article_index]) && $ids[$article_index] === "") {
		return "<!-- No module here today. -->";
	} else if (isset($ids[$article_index])) {
		$id = $ids[$article_index];
		$query = new WP_Query(array('p' => $id));
		while($query->have_posts()) {
			$query->the_post();
			ob_start();
			include($homemodules_dir . $module_name . ".php");
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	} else {
		ob_start();
		include($homemodules_dir . $module_name . ".php");
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}

add_filter( 'the_posts', function( $posts, \WP_Query $query )
{
    if( $pick = $query->get( '_shuffle_and_pick' ) )
    {
        shuffle( $posts );
        $posts = array_slice( $posts, 0, (int) $pick );
    }
    return $posts;
}, 10, 2 );
