<?php

get_header(); ?>

	<?php if ( have_posts() ) : ?>

		<header class="archive-header">
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		</header><!-- .archive-header -->

		<div class="articles">

		<?php
		// Start the Loop.
		while ( have_posts() ) {
			the_post();
			get_template_part( 'content', get_post_format() );
		}

		?>

		</div>

		<?php
		// Previous/next page navigation.
		the_posts_pagination( array(
			'prev_text'          => '&larr;',
			'next_text'          => '&rarr;',
			'before_page_number' => '',
			'mid_size'			 => 6,
			''
		) );

	// If no content, include the "No posts found" template.
	else :
		get_template_part( 'content', 'none' );

	endif;
	?>

<?php get_footer(); ?>
