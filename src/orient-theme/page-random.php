<?php

$args = array(
    'orderby' => 'rand',
    'numberposts' => 1,
    'post_type' => array(
        'post'
    ),
);
query_posts($args);
foreach ($posts as $post) {
    $link = get_permalink($post);
}
wp_redirect($link . '?random=true', 307);
exit;
