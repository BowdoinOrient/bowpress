<?php

get_header();
if (have_posts()) :
    while (have_posts()) :
        the_post();
    ?>

	<?php
        // If this isn't the home page, do things a little differently
        if (!is_in_front_page_tree()):
    ?>

		<header class="page-header">
			<h1><?php the_title() ?></h1>
		</header>

	<?php endif; ?>

	<div class="content">
		<aside>
			<?php if (get_field("sidebar")) {
        the_field("sidebar");
    } ?>
		</aside>

		<article>
			<?php the_content(); ?>
		</article>
	</div>

<?php
    endwhile;
endif;
get_footer();
