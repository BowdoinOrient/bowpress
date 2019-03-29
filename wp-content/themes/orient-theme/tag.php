<?php

get_header(); ?>



	<?php if (have_posts()) : ?>

		<header class="archive-header tag-archive-header">
			<?php if (single_cat_title("", false) == "andross"): ?>
				<h1 class="tag-archive-header-special-title">Finding Fort Andross: A closer look inside Brunswick’s former textile mill</h1>
			<?php else: ?>
				<h1 class="tag-archive-header-title"><?php single_cat_title() ?></h1>
			<?php endif; ?>
			<?php if (tag_description()): ?><?php echo tag_description(); ?><?php endif; ?>
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
