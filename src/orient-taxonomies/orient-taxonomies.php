<?php

/*
 * Plugin Name: Bowdoin Orient — Taxonomies
 * Plugin URI: http://bowdoinorient.com/wordpress
 * Description: Adds and modifies custom taxonomies for better article organization.
 * Author: James Little
 * Author URI: http://jameslittle.me
 * Version: 1.0.0
 */

defined('ABSPATH') or die;

/**************************************
========= CUSTOM POST TYPES ===========
**************************************/

/* Alert post type */

add_action('init', 'create_packaging_post_type');

function create_packaging_post_type()
{
    register_post_type(
        'packaging',
        array(
            'labels' => array(
                'name' => 'Packaging',
                'singular_name' => 'Package',
                'add_new_item' => "Add New Package",
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-list-view',
        )
    );
}

add_action('init', 'create_alert_post_type');

function create_alert_post_type()
{
    register_post_type(
        'alert',
        array(
            'labels' => array(
                'name' => 'Alerts',
                'singular_name' => 'Alert',
                'add_new_item' => "Add New Alert",
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-warning',
        )
    );
}

add_action('init', 'create_ad_post_type');

function create_ad_post_type()
{
    register_post_type(
        'Ad',
        array(
            'labels' => array(
                'name' => 'Ads',
                'singular_name' => 'Ad',
                'add_new_item' => "Add New Ad",
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-layout',
        )
    );
}

/**************************************
========= CUSTOM TAXONOMIES ==========
**************************************/

/* Series taxonomy */

add_action('init', 'create_series_taxonomy', 0);

function create_series_taxonomy()
{

    // Add new taxonomy
    $labels = array(
        'name' => 'Series',
        'singular_name' => 'Series',
        'search_items' => 'Search Series',
        'popular_items' => 'Popular Series',
        'all_items' => 'All Series',
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => 'Edit Series',
        'update_item' => 'Update Series',
        'add_new_item' => 'Create New Series',
        'new_item_name' => 'New Series Title',
        'separate_items_with_commas' => 'Separate series with commas',
        'add_or_remove_items' => 'Add or remove series',
        'choose_from_most_used' => 'Choose from the most used series',
        'not_found' => 'No series found.',
        'menu_name' => 'Series',
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug' => 'series'),
    );

    register_taxonomy('series', 'post', $args);
}

/**************************************
========= TAXONOMY ADDITIONS ==========
**************************************/

/**
 * Echoes a kicker for an article. Should be used from within the loop.
 * If the article is part of a series, this is the kicker. If it's part of
 * a tag, the first tag will be the kicker. Otherwise it will default to
 * category.
 *
 * @param  boolean $shouldExcludeCategory
 *         If true, the kicker will not fall back to a category, and will be
 *         blank if the article does not have a series or tag associated.
 * @return void
 */
function the_kicker($shouldExcludeCategory = false)
{
    $tag = get_the_terms(get_the_ID(), 'post_tag');
    $series = get_the_terms(get_the_ID(), 'series');
    $category = get_the_terms(get_the_ID(), 'category');
    $tag = $tag[0] ?? null;
    $series = $series[0] ?? null;
    $category = $category[0] ?? null;

    if ($series) {
        echo '<a href="' . get_term_link($series) . '">' . $series->name . '</a>';
    } elseif ($tag) {
        echo '<a href="' . get_term_link($tag) . '">' . $tag->name . '</a>';
    } else {
        if (!$shouldExcludeCategory) {
            echo '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
        }
    }
}

function get_the_kicker_from_id($id, $shouldExcludeCategory = false)
{
    $tag = get_the_terms($id, 'post_tag');
    $series = get_the_terms($id, 'series');
    $category = get_the_terms($id, 'category');
    $tag = $tag[0] ?? null;
    $series = $series[0] ?? null;
    $category = $category[0] ?? null;

    if ($series) {
        return '<a href="' . get_term_link($series) . '">' . $series->name . '</a>';
    } elseif ($tag) {
        return '<a href="' . get_term_link($tag) . '">' . $tag->name . '</a>';
    } else {
        if (!$shouldExcludeCategory) {
            return '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
        }
    }

    return false;
}

/**************************************
======== CO-AUTHORS ADDITIONS =========
**************************************/

/**
 * Adds custom fields for the coauthors, like class year and role. Done here
 * instead of in the custom fields plugin because this was more easily
 * accessible from the coauthors plugin.
 */

add_filter('coauthors_guest_author_fields', 'custom_coauthors_fields', 10, 2);

function custom_coauthors_fields($fields_to_return, $groups)
{
    if (in_array('all', $groups) || in_array('name', $groups)) {
        $fields_to_return[] = array(
            'key' => 'class_year',
            'label' => 'Class of...',
            'group' => 'name',
        );
        $fields_to_return[] = array(
            'key' => 'role',
            'label' => 'Role (Required)',
            'group' => 'name',
        );
        $fields_to_return[] = array(
            'key' => 'bonus_url',
            'label' => 'URL of Bonus Author Page (if applicable)',
            'group' => 'name'
        );
    }

    return $fields_to_return;
}

/* Create a listing of the post's authors. Should be used from single.php */

function formatAuthors()
{
    $authorsIter = new CoAuthorsIterator();
    while ($authorsIter->iterate()) {
        echo '<a href="' . get_author_posts_url($authorsIter->current_author->ID, $authorsIter->current_author->user_nicename) . '">';
        echo '<li class="author">';
        echo '<span class="author__name">' . $authorsIter->current_author->display_name . '</span> ';
        echo '<span class="author__role">' . $authorsIter->current_author->role == "" ? 'Contributor' : $authorsIter->current_author->role . '</span></li></a>';
    }
}

function authorRole()
{
    $output = "";
    $roles = array();
    $authorsIter = new CoAuthorsIterator();
    if ($authorsIter->count() == 1) {
        $authorsIter->iterate();
        $output .= $authorsIter->current_author->role;
    } else {
        while ($authorsIter->iterate()) {
            $roles[] = $authorsIter->current_author->role;
        }

        if (count(array_unique($roles)) == 1) {
            $output .= $roles[0];
        } elseif (in_array("Orient Staff", $roles)) {
            $output .= "Orient Staff";
        } else {
            $output .= implode(", ", $roles);
        }
    }

    echo $output;
}

/**
 * Create a listing of the post's authors. Should be used from the loop.
 */

function authorList($echo = true)
{
    $output = '';
    $authorsIter = new CoAuthorsIterator();
    while ($authorsIter->iterate()) {
        $output .= '<a href="' . get_author_posts_url($authorsIter->current_author->ID, $authorsIter->current_author->user_nicename) . '">';
        $output .= str_replace(' ', '&nbsp;', $authorsIter->current_author->display_name);
        $output .= '</a>';

        if ($authorsIter->count() - $authorsIter->position == 1) {
            // do nothing
        } elseif ($authorsIter->count() - $authorsIter->position == 2) { // second to last
            $output .= " and ";
        } else {
            $output .= ", ";
        }
    }

    if ($echo) {
        echo $output;
    }

    return $output;
}

/**
 * Changes the capabilities of editors so that they can list users, allowing them to
 * add guest authors.
 */
function add_theme_caps()
{
    // gets the author role
    $role = get_role('editor');
    $role->add_cap('list_users');
}
add_action('admin_init', 'add_theme_caps');

/**************************************
=========== PLUGIN UTILITY ============
**************************************/

/**
 * Since this plugin does things with CoAuthors Plus, we want to notify people
 * that CoAuthors Plus should be installed if it isn't already.
 */

add_action('admin_init', function () {
    function check_for_coauthors_plus()
    {
        if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('co-authors-plus/co-authors-plus.php')) {
            add_action('admin_notices', function () {
                echo '<div class="error"><p>The Orient Taxonomies plugin requires the Co-Authors Plus plugin to be installed and active. See the documentation for more details.</p></div>';
            });

            deactivate_plugins(plugin_basename(__FILE__));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }
});

include 'custom-fields.php';
