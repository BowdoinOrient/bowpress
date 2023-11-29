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
	date_default_timezone_set("America/New_York");
	if(isset($_POST['file'])) {
		$filename = filter_var ( $_POST['file'], FILTER_SANITIZE_URL);
		$articlelist = implode($_POST['page_id'], ',');

		if(isset($_POST['later']) && $_POST['later'] == true) {
			update_option('orient_homepage_articles_later', $articlelist);
			update_option('orient_homepage_filename_later', $filename);
			if (date('H') < 7) {
				update_option('orient_homepage_articles_later_time', date('U', strtotime("7AM")));
			} else {
				update_option('orient_homepage_articles_later_time', date('U', strtotime("tomorrow 7AM")));
			}
		} else {
			update_option('orient_homepage_articles', $articlelist);
			update_option('orient_homepage_filename', $filename);
		}
	}

	$filename = get_option('orient_homepage_filename');
	$articlelist = explode(',', get_option('orient_homepage_articles'));

	echo "<h2>Pick a Template</h2>";
	echo "<form id=\"page\" name=\"page\" method=\"POST\">";
	echo "<p><i>Note: I got rid of the other home pages because they don't look good. If you want to design a better looking home page be my guest.</i></p>";
	echo_home_page_list($filename);

	echo "<br><br><img src=\"\" style=\"max-width: 400px; float: left; margin: 10px;\" id=\"orient_homepage_image\">";

	echo "<h2>Pick the Articles</h2>";

	if (get_option('orient_homepage_articles_later') != "" && get_option('orient_homepage_articles_later') != get_option('orient_homepage_articles')) {
		echo "<p><b>Note: </b> There is another configuration that will take effect at 7:00 A.M.";
	}

	echo "<div id=\"article-selects\">...</div>";
	echo "<p><input type=\"checkbox\" name=\"later\" id=\"later\"><label for=\"later\">Update at 7:00 AM?</label>";
	echo "<p><button class=\"button button-primary\" type=\"submit\">Update Home Page</button></p>";
	echo "</form>";
	echo "<div id=\"input-template\" style=\"display: none\">";
	echo_article_select_input($articlelist);
	// echo "or enter an ID: ";
	// echo "<input type=\"text\" name=\"article_id\" placeholder=\"1885\"></input>";
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
			list($comment, $nothing) = explode("*/", $contents, 2);
			list($nothing, $keyvalpairs) = explode("/*", $comment, 2);
			$keyvalstrs = array_filter(explode("\n * ", $keyvalpairs));
			$keyvals = array();

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
	echo "<option value=\"\">No article (module should disappear)</option>";
	
	// Start with the drafts
	$args = array( 'numberposts' => wp_count_posts()->draft, 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'draft');

	$posts = get_posts($args);

	foreach($posts as $post) {
		setup_postdata($post);
		echo "<option value=\"" . $post->ID . "\">( " . trim(substr($post->post_title, 0, 50)) . ((strlen($post->post_title)>50)?'...':'') . " )</option>";
	}

	$args = array( 'numberposts' => wp_count_posts()->publish, 'orderby' => 'date', 'order' => 'DESC', 'date_query' => array(
        array(
            'after' => '1 week ago'
        )
    ));

	$posts = get_posts($args);

	foreach($posts as $post) {
		setup_postdata($post);
		echo "<option value=\"" . $post->ID . "\">* " . trim(substr($post->post_title, 0, 50)) . ((strlen($post->post_title)>50)?'...':'') . "</option>";
	}

	$args = array( 'numberposts' => wp_count_posts()->publish, 'orderby' => 'date', 'order' => 'DESC', 'date_query' => array(
        array(
            'before' => '1 week ago',
            'inclusive' => true,
        )
    ));

	$posts = get_posts($args);

	foreach($posts as $post) {
		setup_postdata($post);
		echo "<option value=\"" . $post->ID . "\">" . date_format(date_create($post->post_date), 'n/j/y') . ' ' . trim(substr($post->post_title, 0, 50)) . ((strlen($post->post_title)>50)?'...':'') . "</option>";
	}

	echo "</select>";
}

register_activation_hook(__FILE__, 'home_page_activate');

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

	if (get_option('orient_homepage_articles_later') != "" &&
		date('U') > get_option('orient_homepage_articles_later_time')) {

			update_option('orient_homepage_articles', get_option('orient_homepage_articles_later'));
			update_option('orient_homepage_articles_later', '');

			update_option('orient_homepage_filename', get_option('orient_homepage_filename_later'));
			update_option('orient_homepage_filename_later', '');
	}

    $homepages_dir = get_stylesheet_directory() . '/homepages/';
	$file = $homepages_dir . get_option('orient_homepage_filename');

	if (is_user_logged_in() && get_option('orient_homepage_filename_later') != "") {
		$file = $homepages_dir . get_option('orient_homepage_filename_later');
		echo "<div class=\"alert alert-red\"<h1>This is the home page that will be published at 7:00 A.M.</h1><p>Log out of WordPress to see the current home page.</div>";
	}

	ob_start();
	include($file);
    $output = ob_get_contents();
	ob_end_clean();
	echo $output;
}

function home_render($module_name, $article_index = NULL) {
	date_default_timezone_set("America/New_York");
	$homemodules_dir = get_stylesheet_directory() . '/homemodules/';

	$ids = explode(',', get_option('orient_homepage_articles'));

	if (is_user_logged_in() && get_option('orient_homepage_articles_later') != "") {
		$ids = explode(',', get_option('orient_homepage_articles_later'));
	}

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
