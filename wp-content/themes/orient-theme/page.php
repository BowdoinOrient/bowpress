<?php

get_header();
if (have_posts()) :
	while (have_posts()) :
		the_post();
	?>

	<?php if(!is_in_front_page_tree()): ?>

		<header class="archive-header series-archive-header">
			<h1 class="series-archive-header-title"><?php the_title() ?></h1>
		</header>

	<?php endif; ?>

	<article>
		<div class="content">
			<?php the_content(); ?>
		</div>
	</article>

<?php
	endwhile;
endif;
get_footer(); 