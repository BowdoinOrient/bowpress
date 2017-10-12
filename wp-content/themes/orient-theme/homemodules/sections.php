<div class="row">
	<div class="col-md-6 border-right">
		<h1 class="block-title"><a href="/section/news">News</a></h1>

<?php
$args = array(
	'post-type' => 'post',
	'posts_per_page' => 5,
	'category_name' => 'news'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo home_render("M4");
	}
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
}

?>
	</div>

	<div class="col-md-6">
		<h1 class="block-title"><a href="/section/features">Features</a></h1>

<?php
$args = array(
	'post-type' => 'post',
	'posts_per_page' => 5,
	'category_name' => 'features'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo home_render("M4");
	}
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
}

?>
	</div>
</div>

<?php echo home_render("PrintModule"); ?>

<div class="row">
	<div class="col-md-4 border-right">
		<h1 class="block-title"><a href="/section/arts-entertainment">Arts &amp; Entertainment</a></h1>
<?php
$args = array(
	'post-type' => 'post',
	'posts_per_page' => 5,
	'category_name' => 'arts-entertainment'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo home_render("M4");
	}
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
}

?>
	</div>

	<div class="col-md-4 border-right">
		<h1 class="block-title"><a href="/section/sports">Sports</a></h1>
<?php
$args = array(
	'post-type' => 'post',
	'posts_per_page' => 5,
	'category_name' => 'sports'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo home_render("M4");
	}
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
}

?>
	</div>

	<div class="col-md-4">
		<h1 class="block-title"><a href="/section/opinion">Opinion</a></h1>
<?php
$args = array(
	'post-type' => 'post',
	'posts_per_page' => 5,
	'category_name' => 'opinion'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		echo home_render("M4");
	}
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
}

?>
	</div>
</div>

<?php echo home_render("NewsletterModule"); ?>
