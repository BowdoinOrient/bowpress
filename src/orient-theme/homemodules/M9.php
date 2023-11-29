<article class="module m9">
    <?php if (has_post_thumbnail()): ?>
        <figure class="media-figure">
            <a href="<?php the_permalink() ?>">
    			<?php the_post_thumbnail('module'); ?>
            </a>
        </figure>
    <?php endif; ?>
    <div <?php if (has_post_thumbnail()): ?>class="media-body"<?php endif; ?>>
        <h2 class="kicker"><?php the_kicker(true); ?></h2>
        <h1 class="article-title article-title--b article-title--huge"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
        <?php author_and_date(); ?>
    </div>
</article>
