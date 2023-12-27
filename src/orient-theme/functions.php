<?php

require_once('required-plugins.php');


add_filter('xmlrpc_enabled', '__return_false');

/* Add JQuery */
wp_enqueue_script('jquery');

/**
 * There's a bunch of crud that fills up wp_head() (called in header.php), and
 * if we can get rid of it as much as possible that would be great.
 */
remove_action('wp_head', 'rsd_link'); //removes EditURI/RSD (Really Simple Discovery) link.
remove_action('wp_head', 'wlwmanifest_link'); //removes wlwmanifest (Windows Live Writer) link.
remove_action('wp_head', 'wp_generator'); //removes meta name generator.
remove_action('wp_head', 'wp_shortlink_wp_head'); //removes shortlink.
remove_action('wp_head', 'feed_links', 2); //removes feed links.
remove_action('wp_head', 'feed_links_extra', 3);  //removes comments feed.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // removes prev/next links

/**
 * Can't see us opting to use emoji on the web (especially since they wouldn't
 * be able to be in print) so we can just disable this functionality and
 * prevent wordpress from loading up some javascript and css that we simply
 * don't need
 */
add_action('init', function () {
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
});

/* Turn on paste_as_text by default */

add_filter('tiny_mce_before_init', function ($mceInit, $editor_id) {
    $mceInit['paste_as_text'] = true;
    return $mceInit;
}, 1, 2);

/* Update CSS within Admin -- show Orient logo on login */
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('admin-styles', get_template_directory_uri() . '/admin.css');
});


/* Redefines the HTML structure for the Popular Posts plugin */
add_filter('wpp_custom_html', function ($mostpopular, $instance) {
    $output = '';
    $i = 0;

    // loop the array of popular posts objects
    foreach ($mostpopular as $popular) {
        $i++;

        $output .= "<article>";
        $output .= '<div class="kicker"><span class="count">' . $i . "</span>" . get_the_kicker_from_id($popular->id) . '</div>';
        $output .= "<h2 class=\"entry-title\"><a href=\"" . get_the_permalink($popular->id) . "\" title=\"" . esc_attr($popular->title) . "\">" . $popular->title . "</a></h2>";
        $output .= '<p class="byline">' . author_and_date(false, $popular->id) . '</p>';
        $output .= "</article>\n";
    }

    return $output;
}, 10, 2);

/* Put all the management of the <title> tag in the hands of WordPress. */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
});

/**
 * Remove theme customizer from Admin sidebar since the Orient theme doesn't
 * use it
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {
    $wp_admin_bar->remove_menu('customize');
}, 999);

/* Remove menu items from the admin sidebar for editors */
add_action('admin_menu', function () {
    if (in_array("editor", wp_get_current_user()->roles)) {
        remove_menu_page('edit-comments.php'); // Page for editing comments
        remove_menu_page('wpcf7'); // Contact Form 7 Options
        remove_menu_page('edit.php?post_type=alert'); // Alert custom post type
        remove_menu_page('tools.php'); // Tools -- this encompasses a bunch
        remove_menu_page('vc-welcome'); // Visual composer options
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=acf'); // Alert custom post type
        remove_menu_page('theseoframework-settings'); // Alert custom post type
        remove_menu_page('home-pages'); // Alert custom post type
        remove_menu_page('WP-Optimize'); // Alert custom post type
    }
}, 999);

/* Remove admin bar items for editors */
add_action('wp_before_admin_bar_render', function () {
    if (in_array("editor", wp_get_current_user()->roles)) {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }
}, 999);

/* Make it so editors can't moderate comments */
$role_object = get_role('editor');
$role_object->add_cap('edit_theme_options');
$role_object->add_cap('manage_options');
$role_object->remove_cap('moderate_comments');

/* Submenus for the navigation menu */
add_action('init', function () {
    register_nav_menu('news_tax_menu', "News Taxonomy Menu");
    register_nav_menu('news_art_menu', "News Article Menu");
    register_nav_menu('feat_tax_menu', "Features Taxonomy Menu");
    register_nav_menu('feat_art_menu', "Features Article Menu");
    register_nav_menu('arts_tax_menu', "A&E Taxonomy Menu");
    register_nav_menu('arts_art_menu', "A&E Article Menu");
    register_nav_menu('sports_tax_menu', "Sports Taxonomy Menu");
    register_nav_menu('sports_art_menu', "Sports Article Menu");
    register_nav_menu('opinion_tax_menu', "Opinion Taxonomy Menu");
    register_nav_menu('opinion_art_menu', "Opinion Article Menu");
});

function display_orient_tax_menu($menu_name)
{
    $menu = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        return '<div>Menu "' . $menu_name . '" not defined.</div>';
    }
    $menu_items = wp_get_nav_menu_items($menu->term_id);

    $menu_list = '';
    foreach ((array) $menu_items as $key => $menu_item) {
        $title = $menu_item->title;
        $url = $menu_item->url;
        $menu_list .= '<a href="' . $url . '">' . $title . '</a>';
    }
    echo $menu_list;
}

function display_orient_article_menu($menu_name)
{
    $menu = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        return '<div>Menu "' . $menu_name . '" not defined.</div>';
    }
    $menu_items = wp_get_nav_menu_items($menu->term_id);

    $menu_list = '';
    foreach ((array) $menu_items as $key => $menu_item) {
        $id = get_post_meta($menu_item->ID, '_menu_item_object_id', true);
        if ($id) {
            $kicker = get_the_kicker_from_id($id, true);
            $url = $menu_item->url;
            $title = $menu_item->title;
            $menu_list .= '<article class="section-menu__article">';
            if (get_the_post_thumbnail($id)) {
                $menu_list .= '<figure class="section-menu__article__thumbnail">' . get_the_post_thumbnail($id) . '</figure>';
            } else {
                $menu_list .= '<figure class="section-menu__article__thumbnail"><img src="' . get_template_directory_uri() . '/img/archive-placeholder.png"></figure>';
            }
            $menu_list .= '<div class="media-body">';
            $menu_list .= '<h2 class="kicker">' . $kicker . '</h2>';
            $menu_list .= '<h1 class="article-title"><a href="' . $url . '">' . $title . '</a></h1>';
            $menu_list .= '<p>' . author_and_date(false, $id) . '</p>';
            $menu_list .= '</div></article>';
        }
    }

    echo $menu_list;
}

/* Fix photographer Co-Authors Plus pages so they don't 404 */
add_filter('template_redirect', function () {
    global $wp_query;

    add_filter('pre_get_document_title', function ($original) {
        global $wp_query;
        return str_replace("editor", $wp_query->queried_object->display_name, $original);
    }, 10);
    apply_filters('pre_get_document_title', wp_get_document_title());

    if ($wp_query->is_404 && is_object($wp_query->queried_object)) {
        status_header(200);
        $wp_query->is_404 = false;
        $wp_query->is_author = true;
    }
});

function is_in_front_page_tree()
{
    global $post;         // load details about this page
    return (is_page() && ($post->post_parent == get_option('page_on_front') || is_front_page()));
}

function author_and_date($echo = true, $id = null)
{
    if ($id) {
        $query = new WP_Query('p=' . $id);
        $query->the_post();
    }
    $dateTime1 = new DateTime(get_the_time('r'));
    $dateTime2 = new DateTime('now');
    $interval = $dateTime1->diff($dateTime2, true);
    $diff = '';
    if ($interval->y == 0 && $interval->m == 0) {
        if ($interval->d == 0 && $interval->h < 2) {
            $diff = "Just now";
        } elseif ($interval->d == 0) {
            $diff = $interval->format("Today");
        } elseif ($interval->d == 1) {
            $diff = $interval->format("%d day ago");
        } else {
            $diff = $interval->format("%d days ago");
        }
    } else {
        $diff = get_the_time('F j, Y');
    }

    $output = '<p class="byline">By ' . authorList(false) . ' • ' . $diff . '</p>';

    if ($id) {
        wp_reset_postdata();
    }

    if ($echo) {
        echo $output;
    } else {
        return $output;
    }
}

function packaging_shortcode($atts)
{
    $packages = get_posts(
        array(
            'post_type' => 'packaging',
            'post_status' => 'publish',
            'limit' => 1,
            'meta_query' => array(
                array(
                    'key' => 'articles',
                    'value' => '"' . get_the_ID() . '"',
                    'compare' => 'LIKE'
                )
            )
        )
    );

    if (isset($packages[0])) {
        $package = $packages[0];
        $package_articles = get_field('articles', $package->ID, false);
        $packaging_args = array(
            'post__in' => $package_articles,
            'orderby' => 'post_date',
        );

        $post_objects = get_posts($packaging_args);
        $output = '<div class="article-packaging">
				<h1 class="article-packaging__header">' . $package->post_title . '</h1>
				<p class="article-packaging__blurb">' . $package->post_content . '</p>
				<div class="article-packaging__list">';
        foreach ($post_objects as $packaging_article) {
            $curr = false;
            if ($packaging_article->ID == get_the_ID()) {
                $curr = true;
            }

            $output .= '<a href="' . get_permalink($packaging_article->ID) . '"
				class="' . ($curr ? "current" : "") . '">';
            $output .= $packaging_article->post_title;
            $output .= '<span class="article-packaging__date">' . get_the_date('F j, Y', $packaging_article->ID) . '</span></a>';
        }

        $output .= '</div></div>';
        return $output;
    }
}

function current_issue()
{

    $vol153_issues = array(
        #Orientation issue
        "2023-09-01" => "1",
        "2023-09-08" => "2",
        "2023-09-15" => "3",
        "2023-09-22" => "4",
        "2023-09-29" => "5",
        #Fall Break (Oct 6)
        "2023-10-13" => "6",
        "2023-10-20" => "7",
        "2023-10-27" => "8",
        "2023-11-03" => "9",
        #Thanksgiving Break (Nov 28)
        "2023-11-10" => "10",
        "2023-11-17" => "11",
        "2023-12-01" => "12",
        #Winter Break (Dec 09 - Jan 23)
        "2024-01-26" => "13",
        "2024-02-02" => "14",
        "2024-02-09" => "15",
        "2024-02-16" => "16",
        "2024-02-23" => "17",
        "2024-03-01" => "18",
        #Spring Break (Mar 10 - 17)
        "2024-03-29" => "19",
        "2024-04-05" => "20",
        "2024-04-12" => "21",
        "2024-04-19" => "22",
        "2024-04-26" => "23",
        "2024-05-03" => "24",
    );

    $date = "";
    $issuenum = "";
    foreach ($vol153_issues as $curr_date => $curr_issue_num) {

        // Positive if $date is in the past; neg if in the future. We have to
        // use the WP-specific current_time function because of time zones. :(
        $diff = current_time('timestamp', 0) - strtotime($curr_date);

        // If more than 1 week in the past
        if ($diff < 0) {
            break;
        }

        $date = $curr_date;
        $issuenum = $curr_issue_num;
    }

    return array(
        "issue_num" => $issuenum,
        "date" => $date
    );
}

function cachebust_file($filename)
{
    return $filename . "?" . md5(file_get_contents($filename));
}

function cachebusted_css()
{
    return cachebust_file(get_stylesheet_uri());
}

function cachebusted_js()
{
    return cachebust_file(get_template_directory_uri() . '/js/script.js');
}

add_shortcode('packaging', 'packaging_shortcode');

add_shortcode('interactive', function ($directory) {
    if (!$directory["page"]) {
        return "";
    }

    return file_get_contents(ABSPATH . "static/" . $directory["page"] . "/index.html");
});

add_theme_support('post-thumbnails');
set_post_thumbnail_size(640, 480, array('center', 'center'));
add_image_size('module', 640, 480, array('center', 'center'));

flush_rewrite_rules();
