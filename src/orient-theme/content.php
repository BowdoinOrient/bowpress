<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" class="archive-template">

		<figure>
		<?php if (has_post_thumbnail()): ?>
			<a href="<?php the_permalink() ?>">
				<?php the_post_thumbnail('module'); ?>
			</a>
		<?php else: ?>
			<img src="<?php echo get_template_directory_uri() ?>/img/archive-placeholder.png" alt="">
		<?php endif; ?>
	</figure>

	<div class="media-body">
		<h2 class="kicker"><?php the_kicker(true); ?></h2>
		<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
		<?php author_and_date(); ?>
		<p class="excerpt"><?php the_advanced_excerpt() ?></p>
		<p class="read-more"><a href="<?php the_permalink() ?>">Read&nbsp;more</a></p>
	</div>

</article>
