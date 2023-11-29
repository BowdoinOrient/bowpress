<article class="module m1">
	<h2 class="kicker kicker--large"><?php the_kicker(); ?></h2>

	<h1 class="article-title article-title--thicc article-title--i article-title--huge"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>

	<figure>
		<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('module') ?></a>
	</figure>

	<?php author_and_date(); ?>

	<p class="excerpt"><?php the_advanced_excerpt(); ?></p>

	<p class="read-more"><a href="<?php the_permalink() ?>">Read&nbsp;more</a></p>
</article>
