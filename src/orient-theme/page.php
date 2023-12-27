<?php

get_header();
if (have_posts()):
	while (have_posts()):
		the_post();
		?>

		<?php
		// If this isn't the home page, do things a little differently
		if (!is_in_front_page_tree()):
			?>

			<header class="page-header">
				<h1>
					<?php the_title() ?>
				</h1>
			</header>

		<?php endif; ?>

		<?php if (!function_exists('get_field')): ?>
			<div class="alert alert-yellow">
				<p><strong>Warning:</strong> This WordPress theme requires that several plugins be installed and activated. It looks
					like this is not currently the case. Please <a
						href="/wp-admin/themes.php?page=tgmpa-install-plugins&plugin_status=install">install the required
						plugins</a> to continue.</p>
			</div>
			<?php get_footer(); ?>
		<?php endif; ?>

		<div class="content">
			<aside>
				<div style="position: relative; z-index: 10;">
					<?php if (get_field("sidebar")) {
						the_field("sidebar");
					} ?>
				</div>
			</aside>

			<article>
				<?php the_content(); ?>
			</article>
		</div>

		<?php
	endwhile;
endif;
get_footer();
