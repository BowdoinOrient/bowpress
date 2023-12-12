<?php

$query = new WP_Query(array(
    'orderby' => 'rand',
    'numberposts' => 1,
    'post_type' => array(
        'post'
    ),
));

$link = "/";

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $link = get_permalink();

    }
}

wp_redirect($link . '?random=true', 307);
exit;
