<article class="module module-3">
	<figure>
		<a href="<?php the_permalink() ?>">
			<?php if (has_post_thumbnail()): ?>
				<a href="<?php the_permalink() ?>">
					<?php the_post_thumbnail('module'); ?>
				</a>
			<?php else: ?>
				<img src="<?php echo get_template_directory_uri() ?>/img/archive-placeholder.png" alt="">
			<?php endif; ?>
		</a>
	</figure>
	<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	<?php author_and_date(); ?>

	<?php if($excerpt) : ?>
		<p class="excerpt"><?php the_advanced_excerpt(); ?></p>
	<?php endif; ?>
</article>
