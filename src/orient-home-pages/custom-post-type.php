<?php
add_action('init', function () {
    register_post_type(
        'home_page',
        array(
            'labels' => array(
                'name' => 'Home Page',
                'singular_name' => 'Home Page',
                'add_new_item' => "Add New Home Page Layout",
            ),
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-list-view',
            "supports" => array(""),
        )
    );
});

function get_theme_home_page_list()
{
    $homepages_dir = get_stylesheet_directory() . '/homepages/';
    $files = scandir($homepages_dir);

    $output = array();
    foreach ($files as $file) {
        if (substr($file, -4) == ".php") {
            $fullpath = $homepages_dir . $file;
            $contents = file_get_contents($fullpath);
            list($comment, $nothing) = explode("*/", $contents, 2);
            list($nothing, $keyvalpairs) = explode("/*", $comment, 2);
            $keyvalstrs = array_filter(explode("\n * ", $keyvalpairs));
            $keyvals = array();

            foreach ($keyvalstrs as $kvstring) {
                $kvarray = explode(": ", $kvstring);
                $keyvals[trim($kvarray[0])] = trim($kvarray[1]);
            }

            $output[$file] = $keyvals;
        }
    }
    return $output;
}

add_action('admin_enqueue_scripts', function () {
    echo "<style>
    .home-page-image { float: left !important; clear: none !important; } 
    .home-page-image img {
        width: 100%;
        height: auto;
    }
    .article-field { min-height: 0 !important; }
    </style>";
});

add_action('acf/include_fields', function () {
    $home_page_list = get_theme_home_page_list();
    $articleCounts = array_column($home_page_list, 'Article Count');
    $maxArticleCount = max(array_map('intval', $articleCounts));
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $template_field = array(
        'key' => 'field_659ad93da173f',
        'label' => 'Template',
        'name' => 'template',
        'aria-label' => '',
        'type' => 'select',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => array_map(function ($value) {
            return $value["Page Name"] . " (" . $value["Article Count"] . ")";
        }, $home_page_list),

        'default_value' => 'skyscraper.php',
        'return_format' => 'value',
        'multiple' => 0,
        'allow_null' => 0,
        'ui' => 0,
        'ajax' => 0,
        'placeholder' => '',
    );

    $template_images_fields = array_map(function ($home_page_filename, $home_page) use ($template_field) {
        return array(
            'key' => 'field_659ae3c595eb7_' . $home_page_filename,
            'label' => '',
            'name' => '',
            'aria-label' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => $template_field["key"],
                        'operator' => '==',
                        'value' => $home_page_filename,
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '50',
                'class' => 'home-page-image',
                'id' => '',
            ),
            'message' => '<img src="/wp-content/themes/orient-theme/homepages/' . str_replace(".php", "", $home_page_filename) . '.png" />',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        );
    }, array_keys($home_page_list), $home_page_list);

    $article_fields = array();
    for ($i = 0; $i < $maxArticleCount; $i++) {
        $available_home_pages = array_filter($home_page_list, function ($value) use ($i) {
            return $i < $value["Article Count"];
        });

        $article_fields[] = array(
            'key' => 'field_articleslot_' . ($i + 1),
            'label' => 'Slot ' . ($i + 1),
            'name' => 'slot_' . ($i + 1),
            'aria-label' => '',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array_map(function ($key, $value) use ($template_field) {
                return array(
                    array(
                        'field' => $template_field["key"],
                        'operator' => '==',
                        'value' => $key,
                    )
                );
            }, array_keys($available_home_pages), $available_home_pages),

            'wrapper' => array(
                'width' => '25',
                'class' => 'article-field',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'post',
            ),
            'post_status' => array(
                0 => 'publish',
            ),
            'taxonomy' => '',
            'return_format' => 'id',
            'multiple' => 0,
            'allow_null' => 1,
            'bidirectional' => 0,
            'ui' => 1,
            'bidirectional_target' => array(
            ),
        );
    }


    acf_add_local_field_group(
        array(
            'key' => 'group_659ad8d09cef6',
            'title' => 'Home Page Layout Generator',
            'fields' => array(
                $template_field,
                ...$template_images_fields,
                ...$article_fields,
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'home_page',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'permalink',
                1 => 'the_content',
                2 => 'excerpt',
                3 => 'discussion',
                4 => 'comments',
                5 => 'revisions',
                6 => 'slug',
                7 => 'author',
                8 => 'format',
                9 => 'page_attributes',
                10 => 'featured_image',
                11 => 'categories',
                12 => 'tags',
                13 => 'send-trackbacks',
            ),
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        )
    );
});

