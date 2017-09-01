<div class="nqireg nqireg-square">

<?php

$args = array(
	'post_type'             => 'ad',
	'meta_value'            => 'square',
    'posts_per_page'        => 3,
	'_shuffle_and_pick'     => 1 // <-- our custom argument
);

$query = new WP_Query( $args );



// The Loop
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		echo "<a href=\"";
		the_field('url');
		echo "\">";
		echo "<img src=\"" . get_field('ad_image')['url'] . "\">";
		echo "</a>";
	}
} else {
	// no posts found
}

// Restore original Post Data
wp_reset_postdata();

?>
<p class="nqireg__caption">Advertisement</p>
</div>
