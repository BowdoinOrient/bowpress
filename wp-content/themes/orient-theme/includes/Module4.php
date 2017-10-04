<article class="module module-4">
	<figure>
		<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('module') ?></a>
	</figure>

	<div class="media-body">
		<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
		<p class="byline">By <?php authorList() ?></p>
		<p class="excerpt"><?php the_excerpt() ?></p>
		<p class="read-more"><a href="<?php the_permalink() ?>">Read more</a></p>
	</div>
</article>