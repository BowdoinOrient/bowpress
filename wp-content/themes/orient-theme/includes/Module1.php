<article class="module module-1">
	<header>
		<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	</header>

	<aside>
		<figure>
			<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('large') ?></a>
		</figure>

		<div class="article-meta">
			<h2 class="article-subtitle">
				<?php
					if (!the_subtitle('', '', false)) {
						echo "This block needs an article with a subtitle.";
					} else {
						the_subtitle();
					}
				?>
			</h2>
			<p class="byline">By <?php authorList() ?></p>
		</div>
	</aside>

	<div class="excerpt">
		<p>
			<?php the_excerpt(); ?> <a href="<?php the_permalink() ?>" class="read-more">Read&nbsp;more&nbsp;&raquo;</a>
		</p>
	</div>
</article>