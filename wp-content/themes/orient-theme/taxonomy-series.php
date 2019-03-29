<?php

get_header();

$copy = clone $wp_query;

//find the date for the first and latest articles
$first_article_date = get_the_date('F j, Y', $copy->posts[$copy->post_count - 1]);
$latest_article_date = get_the_date('F j, Y', $copy->posts[0]);

?>
	<?php if (have_posts()) : ?>

		<header class="archive-header series-archive-header">
			<h1 class="series-archive-header-title"><?php single_cat_title() ?></h1>

			<?php if (have_posts()): ?>
				<p><strong>Total Number of Articles: </strong> <?php echo $copy->found_posts; ?></p>
				<p><strong>First Article on this Page: </strong><?php echo $first_article_date; ?></p>
				<p><strong>Latest Article on this Page: </strong><?php echo $latest_article_date; ?></p>
			<?php endif; ?>
		</header><!-- .archive-header -->

		<div class="articles">

		<?php
        $hide_category = true;
        // Start the Loop.
        while (have_posts()) {
            the_post();
            include(locate_template('content.php'));
        }

        ?>

		</div>

		<?php
        // Previous/next page navigation.
        the_posts_pagination(array(
            'prev_text'          => '&larr;',
            'next_text'          => '&rarr;',
            'before_page_number' => '',
            'mid_size'			 => 2,
            ''
        ));

    // If no content, include the "No posts found" template.
    else :
        get_template_part('content', 'none');

    endif;
    ?>

<?php get_footer(); ?>
