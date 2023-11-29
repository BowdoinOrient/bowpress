<?php

get_header();

global $wp_query;
$curauth = $wp_query->get_queried_object();

 ?>

 	<?php $num_photos = get_photos_by_author(false) ?>

	<?php if (have_posts() || $num_photos) : ?>

		<header class="archive-header author-archive-header">
			<h1 class="author-archive-header-title"><?php echo $curauth->display_name ?></h1>
			<h2 class="author-archive-header-subtitle">
				<?php echo $curauth->role; ?>
				<?php if ($curauth->class_year): ?>
					&mdash; Class of <?php echo $curauth->class_year; ?>
				<?php endif; ?>
			</h2>
		
			<?php if (have_posts()): ?>
				<p><strong>Number of articles: </strong> <?php print_r(count($wp_query->posts)); ?></p>
				<p><strong>First Article: </strong><?php echo get_the_date('F j, Y', $wp_query->posts[count($wp_query->posts) - 1]->ID); ?></p>
				<p><strong>Latest Article: </strong><?php echo get_the_date('F j, Y', $wp_query->posts[0]->ID); ?></p>
			<?php endif; ?>
			<?php if ($curauth->bonus_url): ?>
				<p><a href="<?php echo $curauth->bonus_url; ?>">See previous content</a></p>
			<?php endif; ?>
		</header><!-- .archive-header -->

		<?php if ($num_photos): ?>
			<div class="author-archive-carousel<?php if (!have_posts()): ?> author-archive-carousel--alone<?php endif; ?>">
				<h1><?php echo $num_photos; ?> photo<?php if ($num_photos > 1): ?>s<?php endif; ?> by <?php echo $curauth->display_name; ?></h1>
				<?php get_photos_by_author(); ?>
			</div>
		<?php endif;?>

		<div class="articles">

		<?php
        // Start the Loop.
        while (have_posts()) {
            the_post();
            get_template_part('content', get_post_format());
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
    ));

    // If no content, include the "No posts found" template.
    else :
        get_template_part('content', 'none');

    endif;
    ?>

<?php get_footer(); ?>
