<?php

get_header(); ?>

	<?php if (have_posts()) : ?>

		<header class="archive-header category-archive-header">
			<h1 class="category-archive-header-title"><?php single_cat_title() ?></h1>
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
