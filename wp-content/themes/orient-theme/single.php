<?php

$chevronRight = '<svg version="1.1" id="Chevron_right" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve" style="width: 1.5em;"><path fill="currentcolor" d="M9.163,4.516c0.418,0.408,4.502,4.695,4.502,4.695C13.888,9.43,14,9.715,14,10s-0.112,0.57-0.335,0.787 c0,0-4.084,4.289-4.502,4.695c-0.418,0.408-1.17,0.436-1.615,0c-0.446-0.434-0.481-1.041,0-1.574L11.295,10L7.548,6.092 c-0.481-0.533-0.446-1.141,0-1.576C7.993,4.08,8.745,4.107,9.163,4.516z"/></svg>';

$chevronLeft = '<svg version="1.1" id="Chevron_left" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve" style="width: 1.5em;"><path fill="currentcolor" d="M12.452,4.516c0.446,0.436,0.481,1.043,0,1.576L8.705,10l3.747,3.908c0.481,0.533,0.446,1.141,0,1.574 c-0.445,0.436-1.197,0.408-1.615,0c-0.418-0.406-4.502-4.695-4.502-4.695C6.112,10.57,6,10.285,6,10s0.112-0.57,0.335-0.789 c0,0,4.084-4.287,4.502-4.695C11.255,4.107,12.007,4.08,12.452,4.516z"/></svg>';

get_header();

?>

<?php if(isset($_GET['random'])): ?>
	<div class="random-box">
		<h1>Not random enough for you?</h1>
		<a href="/random" class="button">Hit me again!</a>
	</div>
<?php endif;?>

<!-- Weird print logo block - we could stand to abstract this -->
<!-- <p style="text-align: right; margin-bottom: 0.5in; margin-top: 0.5in; font-size: 14pt; font-family: Verlag" class="only-print">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/nameplate.svg" alt="" style="max-width: 3in;"> online
</p> -->
<!-- End weird print logo block -->

<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="single__article-header">

		<!-- Taxonomy box: category and series -->
		<div class="single__taxonomy">
			<p class="single__taxonomy__section"><?php the_category(' ', 'single'); ?></p>
			<?php if(get_the_terms($post->ID, 'series')): ?>
			<p class="single__taxonomy__series">
				<span class="previous"><?php previous_post_link( '%link', $chevronLeft, TRUE, ' ', 'series' ); ?></span>
				<span class="link"><?php the_terms( $post->ID, 'series', '', ' / ' ); ?></span>
				<span class="next"><?php next_post_link( '%link', $chevronRight, TRUE, ' ', 'series' ); ?></span>
			</p>
			<?php endif; ?>
		</div>

		<!-- Article title -->
		<h1 class="single__article-title"><?php the_title(); ?></h1>

		<!-- Article subtitle -->
		<?php if (the_subtitle('', '', false)): ?><h2 class="single__article-subtitle"><?php the_subtitle() ?></h2><?php endif; ?>

		<!-- Article byline -->
		<div class="single__byline">
			<!-- <ul class="byline-box"> -->
				<p class="byline__authors">By <?php authorList() ?></p>
				<p class="byline__roles"><?php authorRole() ?>
			<!-- </ul> -->
		</div>

		<!-- Date box -->
		<p class="single__pubdate"><?php the_date(); ?></p>

		<!-- Opinion of the author box -->
		<?php if(get_field("opinion", $post->ID)): ?>
			<div class="single__disclaimer"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/bubble.svg">This piece represents the opinion of the author<?php
				// Pluralize authors if there's more than one author for the article
				$iter = new CoAuthorsIterator();
				$count = $iter->count();
				if ($count > 1) {
					echo "s";
				}
			?>.</div>
		<?php endif; ?>

		<!-- Opinion of the editorial board box -->
		<?php if(get_field("editorial", $post->ID)): ?>
			<div class="single__disclaimer"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/megaphone.svg">This piece represents the opinion of the Bowdoin Orient Editorial Board.</div>
		<?php endif; ?>

	</header>

	<div class="single__content">

		<!-- If there's a package for the article, it gets included here. -->
		<?php
			$packages = get_posts(array(
				'post_type' => 'packaging',
				'post_status' => 'publish',
				'limit' => 1,
				'meta_query' => array(
					array(
						'key' => 'articles',
						'value' => '"' . get_the_ID() . '"',
						'compare' => 'LIKE'
					)
				)
			));


		?>

		<?php if(isset($packages[0])): ?>
<?php
			$package = $packages[0];
			$package_articles = get_field('articles', $package->ID, false);
			$args = array(
				'post__in' => $package_articles,
				'orderby' => 'post_date',
			);

			$post_objects = get_posts( $args );
?>
			<div class="article-packaging">
				<h1 class="article-packaging__header"><?php echo $package->post_title; ?></h1>
				<p class="article-packaging__blurb"><?php echo $package->post_content; ?></p>
				<div class="article-packaging__list">
					<?php foreach($post_objects as $packaging_article): ?>
						<?php $curr = false; if($packaging_article->ID == get_the_ID()) {$curr = true;} ?>
						<a href="<?php echo get_permalink($packaging_article->ID); ?>"
						   class="<?php echo $curr ? "current" : ""; ?>">
							<!--<img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($packaging_article->ID)); ?>">-->
							<?php echo $packaging_article->post_title; ?> <span class="article-packaging__date"><?php echo get_the_date( 'F j, Y', $packaging_article->ID); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php the_content() ?>

	</div>

	<aside class="single__sidebar">

		<?php echo home_render("A-square"); ?>
		<h1 class="single__sidebar__heading">Share this article</h1>
		<div class="single__share-box">
			<a href="http://www.facebook.com/sharer.php?u=<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" target="_blank" class="social-link facebook-share" title="Share on Facebook">
				<svg version="1.1" id="Facebook" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve"><path d="M17,1H3C1.9,1,1,1.9,1,3v14c0,1.101,0.9,2,2,2h7v-7H8V9.525h2V7.475c0-2.164,1.212-3.684,3.766-3.684l1.803,0.002v2.605h-1.197C13.378,6.398,13,7.144,13,7.836v1.69h2.568L15,12h-2v7h4c1.1,0,2-0.899,2-2V3C19,1.9,18.1,1,17,1z"/></svg>
			</a>
			<a href="http://twitter.com/share?text=A%20Bowdoin%20Orient%20article&url=<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" target="_blank" class="social-link twitter-share" title="Share on Twitter">
				<svg version="1.1" id="Twitter" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve"><path d="M17.316,6.246c0.008,0.162,0.011,0.326,0.011,0.488c0,4.99-3.797,10.742-10.74,10.742c-2.133,0-4.116-0.625-5.787-1.697
					c0.296,0.035,0.596,0.053,0.9,0.053c1.77,0,3.397-0.604,4.688-1.615c-1.651-0.031-3.046-1.121-3.526-2.621
					c0.23,0.043,0.467,0.066,0.71,0.066c0.345,0,0.679-0.045,0.995-0.131c-1.727-0.348-3.028-1.873-3.028-3.703c0-0.016,0-0.031,0-0.047
					c0.509,0.283,1.092,0.453,1.71,0.473c-1.013-0.678-1.68-1.832-1.68-3.143c0-0.691,0.186-1.34,0.512-1.898
					C3.942,5.498,6.725,7,9.862,7.158C9.798,6.881,9.765,6.594,9.765,6.297c0-2.084,1.689-3.773,3.774-3.773
					c1.086,0,2.067,0.457,2.756,1.191c0.859-0.17,1.667-0.484,2.397-0.916c-0.282,0.881-0.881,1.621-1.66,2.088
					c0.764-0.092,1.49-0.293,2.168-0.594C18.694,5.051,18.054,5.715,17.316,6.246z"/>
				</svg>
			</a>
			<a href="mailto:?subject=A%20Bowdoin%20Orient%20article%20for%20you&amp;body=Check%20out%20this%20article!%0D%0A%0D%0A<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" class="social-link email-share" title="Share via Email">
				<svg version="1.1" id="Mail" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
				<path d="M1.574,5.286c0.488,0.262,7.248,3.894,7.5,4.029C9.326,9.45,9.652,9.514,9.98,9.514c0.328,0,0.654-0.064,0.906-0.199
					s7.012-3.767,7.5-4.029C18.875,5.023,19.337,4,18.44,4H1.521C0.624,4,1.086,5.023,1.574,5.286z M18.613,7.489
					c-0.555,0.289-7.387,3.849-7.727,4.027s-0.578,0.199-0.906,0.199s-0.566-0.021-0.906-0.199S1.941,7.777,1.386,7.488
					C0.996,7.284,1,7.523,1,7.707S1,15,1,15c0,0.42,0.566,1,1,1h16c0.434,0,1-0.58,1-1c0,0,0-7.108,0-7.292S19.004,7.285,18.613,7.489z"
					/>
				</svg>
			</a>
			<a href="javascript:window.print();" class="social-link print-share" title="Print this article">
				<svg version="1.1" id="Print" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve">
				<path d="M1.501,6h17c0.57,0,0.477-0.608,0.193-0.707C18.409,5.194,15.251,4,14.7,4H14V1H6v3H5.301c-0.55,0-3.709,1.194-3.993,1.293
					C1.024,5.392,0.931,6,1.501,6z M19,7H1C0.45,7,0,7.45,0,8v5c0,0.551,0.45,1,1,1h2.283l-0.882,5h15.199l-0.883-5H19
					c0.551,0,1-0.449,1-1V8C20,7.45,19.551,7,19,7z M4.603,17l1.198-7.003H14.2L15.399,17H4.603z"/>
				</svg>
			</a>
		</div>

		<h1 class="single__sidebar__heading">Most Popular</h1>
		<div class="single__sidebar__popular">
		<?php
			$args = array(
				'post_type' => 'post',
				'limit' => 5,
				'range' => 'weekly',
			);
			wpp_get_mostpopular($args)
		?>
		</div>

	</aside>

	<footer class="single__footer">

		<div class="single__article-tags">
			<?php the_tags('<span class="inline-paragraph-title">Read More</span>', ''); ?>
		</div>

		<?php echo home_render("A-banner"); ?>

			<?php
				$tag_ids = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
				$pubdate = get_the_date('r', $post->ID);
				$category = get_the_category($post->ID);
				$category = $category[0];
				$id = $post->ID;

				$query_args = array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => 4,
					'orderby' => 'date',
					'order' => 'DESC',
					'post__not_in' => array($id),
					'date_query' => array(
						array(
							'before' => $pubdate
						)
					),
				);

				$tag_query_args = $query_args;
				$tag_query_args['tag__in'] = $tag_ids;

				$cat_query_args = $query_args;
				$cat_query_args['cat'] = $category->term_id;

				wp_reset_query();
				$tag_query = new WP_Query($tag_query_args);

				if (count($tag__ids) > 0 && $tag_query->post_count >= 4) {
					echo "tags";
					$related_query = $tag_query;
					$category_override = false;
				} else {
					$related_query = new WP_Query($cat_query_args);
					$category_override = true;
				}

				if ($related_query->have_posts() && $related_query->post_count >= 4):
					echo "<h1 class=\"related-article-header\">";
					echo $category_override ? "More from $category->name:" : "Related Articles";
					echo "</h1><div class=\"related-articles\">";
					while ($related_query->have_posts()):
						$related_query->the_post();
				?>

				<article class="module module-3 related-article">
					<div class="text">
						<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
						<?php author_and_date(); ?>

						<?php if(!has_post_thumbnail()) : ?>
							<p class="excerpt"><?php the_advanced_excerpt(); ?></p>
						<?php endif; ?>
					</div>
					<figure>
						<a href="<?php the_permalink() ?>">
							<?php if (has_post_thumbnail()): ?>
								<a href="<?php the_permalink() ?>">
									<?php the_post_thumbnail('module'); ?>
								</a>
							<?php endif; ?>
						</a>
					</figure>
				</article>

				<?php
					endwhile;
					wp_reset_postdata();
					echo "</div>";
				endif;
			?>

		<div class="callout">
			<p class="callout__header">Sign up for our weekly newsletter.</p>
			<p class="callout__description">Catch up on the latest reports, stories and opinions about Bowdoin and Brunswick in your inbox. Always high-quality. Always free.</p>
			<form action="http://bowdoinorient.us4.list-manage.com/subscribe/post?u=eab94f63abe221b2ef4a4baec&amp;id=739fef0bb9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="">
				<input class="email" type="email" value="" name="EMAIL" id="mce-EMAIL" placeholder="Enter your email.">
				<button type="submit" name="subscribe" id="mc-embedded-subscribe">Sign up</button>
			</form>
			<p class="callout__footer">We'll never use your email for anything other than this newsletter. Read our full <a href="/policies/">privacy policy</a> for more.</p>
		</div>

	</footer>

	<div class="article-comments">
		<h1>Comments</h1>
		<p>Before submitting a comment, please review our <a href="/policies/">comment policy</a>. Some key points from the policy:</p>

		<ul>
			<li>No hate speech, profanity, disrespectful or threatening comments.</li>
			<li>No personal attacks on reporters.</li>
			<li>Comments must be under 200 words.</li>
			<li>You are strongly encouraged to use a real name or identifier ("Class of '92").</li>
			<li>Any comments made with an email address that does not belong to you will get removed.</li>
		</ul>

		<?php comments_template(); ?>
	</div>

</article>

<?php

} // while have posts
} // if have posts

get_footer();

?>
