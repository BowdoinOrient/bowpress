<?php

$posts = get_posts('post_type=post&orderby=rand&numberposts=1');
foreach ($posts as $post) {
    $link = get_permalink($post);
}
wp_redirect($link . '?random=true', 307);
exit;
