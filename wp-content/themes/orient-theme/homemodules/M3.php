<article class="module m3">
	<figure>
		<a href="<?php the_permalink() ?>">
			<?php if (has_post_thumbnail()): ?>
				<a href="<?php the_permalink() ?>">
					<?php the_post_thumbnail('module'); ?>
				</a>
			<?php endif; ?>
		</a>
	</figure>
	<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	<?php author_and_date(); ?>
</article>
