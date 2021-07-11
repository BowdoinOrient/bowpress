<!--
Hey! I see you checking out the source code of this site. Think there are
things that should be changed? Think you could do better? If you're a Bowdoin
student and want to join the Orient tech team, email orient@bowdoin.edu.
-->

<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Required -->
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Links -->
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css"/>
	<link rel="stylesheet" href="<?php echo cachebusted_css(); ?>">
	<script type="text/eqcss" src="<?php echo get_stylesheet_directory_uri(); ?>/sass/element-queries.eqcss"></script>

	<!-- Font loading -->
	<script type="text/javascript" src="https://use.typekit.com/rmt0nbm.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<link rel="stylesheet" type="text/css" href="https://bowdoinorient-typography-dot-com.s3.amazonaws.com/files/aws-orient-fonts.css">
	<!-- <link rel="stylesheet" type="text/css" href="https://cloud.typography.com/7613576/7415972/css/fonts.css" /> -->

	<link rel="author" href="/humans.txt">

	<!-- Icons, etc. -->
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/icon/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/icon/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/icon/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/icon/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="60x60" href="/icon/apple-touch-icon-60x60.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/icon/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/icon/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/icon/apple-touch-icon-152x152.png" />

	<link rel="icon" type="image/png" href="/icon/favicon-196x196.png" sizes="196x196" />
	<link rel="icon" type="image/png" href="/icon/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/png" href="/icon/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="/icon/favicon-16x16.png" sizes="16x16" />
	<link rel="icon" type="image/png" href="/icon/favicon-128.png" sizes="128x128" />
	
	<meta name="application-name" content="The Bowdoin Orient"/>
	<meta name="msapplication-TileColor" content="#FFFFFF" />
	<meta name="msapplication-TileImage" content="/icon/mstile-144x144.png" />
	<meta name="msapplication-square70x70logo" content="/icon/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="/icon/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="/icon/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="/icon/mstile-310x310.png" />

	<!-- Begin Wordpress Head Section -->
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<!-- Facebook Page Box Scripts -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- The drawer that comes from the left side of the screen on mobile devices. -->

<div class="mobile-drawer">
	<a href="#visual-jump" class="visually-hidden">Go to content, skip over navigation</a>
	<form class="mobile-drawer__search-form" action="/" method="get">
		<input type="search" name="s" id="drawerSearchInput" placeholder="Search the Orient...">
		<label for="s">Press enter to search</label>
	</form>

	<h1 class="mobile-drawer__title">Sections</h1>

	<ul class="mobile-drawer__list">
		<li><a href="/">Home</a></li>
		<li><a href="/section/news">News</a></li>
		<li><a href="/section/features">Features</a></li>
		<li><a href="/section/arts-entertainment">Arts &amp; Entertainment</a></li>
		<li><a href="/section/sports">Sports</a></li>
		<li><a href="/section/opinion">Opinion</a></li>
	</ul>

	<h1 class="mobile-drawer__title">More Pages</h1>

	<ul class="mobile-drawer__list">
		<li><a href="/about">About</a></li>
		<li><a href="/masthead">Staff</a></li>
		<li><a href="/subscribe">Subscribe</a></li>
		<li><a href="/advertise">Advertise</a></li>
		<li><a href="/policies">Policies</a></li>
		<li><a href="/contact">Contact</a></li>
		<li><a href="<?php echo is_user_logged_in() ? wp_logout_url('/') : wp_login_url('wp-admin') ?>">Admin Log In/Out</a></li>
	</ul>
</div>

<a href="#visual-jump" class="visually-hidden">Go to content, skip over visible header bar</a>

<!-- The static bar on the top of the screen for mobile devices. Contains the
toggle that opens and closes the mobile drawer. -->

<div class="mobile-top-bar">
	<a class="mobile-top-bar__drawer-toggle">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M16.4 9H3.6C3 9 3 9.4 3 10c0 0.6 0 1 0.6 1H16.4c0.6 0 0.6-0.4 0.6-1C17 9.4 17 9 16.4 9zM16.4 13H3.6C3 13 3 13.4 3 14c0 0.6 0 1 0.6 1H16.4c0.6 0 0.6-0.4 0.6-1C17 13.4 17 13 16.4 13zM3.6 7H16.4C17 7 17 6.6 17 6c0-0.6 0-1-0.6-1H3.6C3 5 3 5.4 3 6 3 6.6 3 7 3.6 7z"/></svg>
		<svg class="js-hidden" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 20 20"><path d="M12.452,4.516c0.446,0.436,0.481,1.043,0,1.576L8.705,10l3.747,3.908c0.481,0.533,0.446,1.141,0,1.574c-0.445,0.436-1.197,0.408-1.615,0c-0.418-0.406-4.502-4.695-4.502-4.695C6.112,10.57,6,10.285,6,10s0.112-0.57,0.335-0.789c0,0,4.084-4.287,4.502-4.695C11.255,4.107,12.007,4.08,12.452,4.516z"/></svg>
	</a>

	<a href="/" class="mobile-top-bar__nameplate"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/nameplate.svg" alt=""></a>
</div>

<!-- The desktop search UI that is revealed from above when the user clicks
on the search link. -->

<div class="desktop-search-bar">
	<form class="desktop-search-bar__form" action="/" method="get">
		<input type="search" name="s" id="searchInput" placeholder="Search the Orient...">
		<button type="submit" class="search-bar-submit">Go!</button>
	</form>
</div>

<div class="drawer-content">

<!-- The bar above the main grid page on desktop pages. Contains social links,
our nameplate on non-home pages, and the search toggle link. -->

<div class="top-bar">
	<div class="top-bar__social-links top-bar__component">
		<a href="https://www.facebook.com/bowdoinorient" class="facebook-icon" target="_blank">
		   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M17 1H3C1.9 1 1 1.9 1 3v14c0 1.1 0.9 2 2 2h7v-7H8V9.5h2V7.5c0-2.2 1.2-3.7 3.8-3.7l1.8 0v2.6h-1.2C13.4 6.4 13 7.1 13 7.8v1.7h2.6L15 12h-2v7h4c1.1 0 2-0.9 2-2V3C19 1.9 18.1 1 17 1z"/></svg>
		</a>

		<a href="https://twitter.com/bowdoinorient" class="twitter-icon" target="_blank">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M17.3 6.2c0 0.2 0 0.3 0 0.5 0 5-3.8 10.7-10.7 10.7 -2.1 0-4.1-0.6-5.8-1.7 0.3 0 0.6 0.1 0.9 0.1 1.8 0 3.4-0.6 4.7-1.6 -1.7 0-3-1.1-3.5-2.6 0.2 0 0.5 0.1 0.7 0.1 0.3 0 0.7 0 1-0.1 -1.7-0.3-3-1.9-3-3.7 0 0 0 0 0 0 0.5 0.3 1.1 0.5 1.7 0.5 -1-0.7-1.7-1.8-1.7-3.1 0-0.7 0.2-1.3 0.5-1.9C3.9 5.5 6.7 7 9.9 7.2 9.8 6.9 9.8 6.6 9.8 6.3c0-2.1 1.7-3.8 3.8-3.8 1.1 0 2.1 0.5 2.8 1.2 0.9-0.2 1.7-0.5 2.4-0.9 -0.3 0.9-0.9 1.6-1.7 2.1 0.8-0.1 1.5-0.3 2.2-0.6C18.7 5.1 18.1 5.7 17.3 6.2z"/></svg>
		</a>

		<a href="https://vimeo.com/bowdoinorient" class="vimeo-icon" target="_blank">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M18.9 5.8c-1 5.8-6.6 10.7-8.3 11.8 -1.7 1.1-3.2-0.4-3.8-1.6C6.2 14.6 4.2 7.3 3.7 6.7 3.2 6.1 1.7 7.3 1.7 7.3L1 6.4c0 0 3.1-3.7 5.4-4.2C8.9 1.7 8.9 6 9.5 8.4c0.6 2.3 1 3.7 1.5 3.7 0.5 0 1.5-1.3 2.5-3.3 1.1-2 0-3.8-2.1-2.5C12.1 1.3 19.9 0.1 18.9 5.8z"/></svg>
		</a>

		<a href="https://www.instagram.com/bowdoinorient/" class="instagram-icon" target="_blank">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18"><path d="M17.95 5.3a6.6 6.6 0 0 0-.42-2.2 4.4 4.4 0 0 0-1.04-1.6A4.4 4.4 0 0 0 14.9.48a6.6 6.6 0 0 0-2.2-.42C11.76 0 11.45 0 9 0S6.25 0 5.3.05a6.6 6.6 0 0 0-2.2.42A4.4 4.4 0 0 0 1.5 1.5 4.4 4.4 0 0 0 .48 3.1a6.6 6.6 0 0 0-.42 2.2C0 6.24 0 6.55 0 9s0 2.75.05 3.7a6.6 6.6 0 0 0 .42 2.2 4.4 4.4 0 0 0 1.04 1.6 4.4 4.4 0 0 0 1.6 1.03 6.6 6.6 0 0 0 2.2.42C6.25 18 6.56 18 9 18s2.75 0 3.7-.05a6.6 6.6 0 0 0 2.2-.42 4.6 4.6 0 0 0 2.63-2.63 6.6 6.6 0 0 0 .42-2.2c.04-.95.05-1.26.05-3.7s0-2.75-.05-3.7zm-1.62 7.34a4.98 4.98 0 0 1-.3 1.67 2.98 2.98 0 0 1-1.72 1.73 4.98 4.98 0 0 1-1.65.3c-.95.05-1.24.06-3.64.06s-2.7 0-3.63-.06a4.98 4.98 0 0 1-1.67-.3 2.8 2.8 0 0 1-1.04-.7 2.8 2.8 0 0 1-.68-1.02 4.98 4.98 0 0 1-.3-1.66C1.62 11.7 1.6 11.4 1.6 9s0-2.7.05-3.64a4.98 4.98 0 0 1 .3-1.67 2.8 2.8 0 0 1 .7-1.05 2.8 2.8 0 0 1 1.02-.68 4.98 4.98 0 0 1 1.66-.3C6.3 1.62 6.6 1.6 9 1.6s2.7 0 3.64.05a4.98 4.98 0 0 1 1.67.3 2.8 2.8 0 0 1 1.05.7 2.8 2.8 0 0 1 .68 1.02 4.98 4.98 0 0 1 .3 1.66c.05.95.06 1.24.06 3.64s0 2.7-.06 3.64zM9 4.38A4.62 4.62 0 1 0 13.62 9 4.62 4.62 0 0 0 9 4.38zM9 12a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm5.88-7.8a1.08 1.08 0 1 1-1.08-1.08 1.08 1.08 0 0 1 1.08 1.08z"/></svg>
		</a>

		<?php if (!is_user_logged_in()): ?>

		<a href="<?php echo wp_login_url('wp-admin') ?>" class="wordpress-login-icon">
			<svg id="Star" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve"><path d="M10,1.3l2.388,6.722H18.8l-5.232,3.948l1.871,6.928L10,14.744l-5.438,4.154l1.87-6.928L1.199,8.022h6.412L10,1.3z"/>
			</svg>
		</a>

		<?php endif; ?>
	</div>

	<?php if (!is_in_front_page_tree()) : ?>
	<div class="top-bar__nameplate top-bar__component">
		<a href="/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/nameplate.svg" alt="">
			<span class="visually-hidden">The Bowdoin Orient - Home</span>
		</a>
	</div>
	<?php endif; ?>

	<div class="top-bar__search-link top-bar__component">
		<a href="#" class="search-icon" id="searchIcon">
			<!-- <span class="icon"> -->
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M17.5 15.5l-3.8-3.8c0.6-0.9 0.9-2 0.9-3.2 0-3.4-3-6.4-6.4-6.4C4.9 2.1 2.1 4.9 2.1 8.3c0 3.4 3 6.4 6.4 6.4 1.1 0 2.2-0.3 3.1-0.8l3.8 3.8c0.4 0.4 1 0.4 1.3 0l0.9-0.9C18.1 16.3 17.9 15.8 17.5 15.5zM4 8.3c0-2.4 1.9-4.3 4.3-4.3 2.4 0 4.5 2.1 4.5 4.5 0 2.4-1.9 4.3-4.3 4.3C6.1 12.8 4 10.7 4 8.3z"/></svg>
			<!-- </span>
			<span class="close js-hidden"> -->
				<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve" class="js-hidden"><path d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697c0.469-0.469,1.228-0.469,1.697,0L10,8.183l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0c0.469,0.469,0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15C14.817,13.62,14.817,14.38,14.348,14.849z"/></svg>
			<!-- </span> -->
		</a>
	</div>
</div>

<?php wp_reset_query(); if (!is_in_front_page_tree()) : ?>
<!-- The set of navigation links underneath the top menu. Substitutes for
the home page navigation menu on non-home pages. -->
<div class="mini-nav">
	<a href="/">Home</a>
	<a href="/section/news">News</a>
	<a href="/section/features">Features</a>
	<a href="/section/arts-entertainment">Arts &amp; Entertainment</a>
	<a href="/section/sports">Sports</a>
	<a href="/section/opinion">Opinion</a> &bullet;
	<a href="/about">About</a>
	<a href="/contact">Contact</a>
	<a href="/advertise">Advertise</a>
</div>
<?php endif; ?>

<!-- The start of the main page. All WordPress content should be contained
within the page-wrap div. -->
<div class="page-wrap">

	<?php wp_reset_query(); if (is_in_front_page_tree()) : ?>
	<!-- The header that appears before the dynamic home page content -->
	<div class="home-header">
		<h1 class="home-header__nameplate">
			<a href="/">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/nameplate.svg" alt="">
				<span class="visually-hidden">The Bowdoin Orient - Home</span>
			</a>
		</h1>

		<p class="home-header__date-info">
			<span class="date"><?php echo current_time('l, F j, Y') ?></span>
			<span class="issue">Volume 150, Issue <?php $ci = current_issue(); echo $ci["issue_num"]; ?></span>
		</p>

		<nav class="home-nav">
			<div class="home-nav__links home-nav__section-links ">
				<a href="/section/news" class="news"  data-section="news">News</a>
				<a href="/section/features" class="features" data-section="features">Features</a>
				<a href="/section/arts-entertainment" class="arts-entertainment" data-section="arts-entertainment">Arts &amp; Entertainment</a>
				<a href="/section/sports" class="sports" data-section="sports">Sports</a>
				<a href="/section/opinion" class="opinion" data-section="opinion">Opinion</a>
			</div>

			<div class="home-nav__links home-nav__more-links">
				<a href="/about" class="home-nav__more-links__link">About</a>
				<a href="/subscribe" class="home-nav__more-links__link">Subscribe</a>
				<a href="/advertise" class="home-nav__more-links__link">Advertise</a>
				<a href="/contact" class="home-nav__more-links__link">Contact</a>
				<a href="#" class="more-menu-toggle">More
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/chevron-down.svg" class="more-menu-toggle__chevron-down">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/chevron-up.svg" class="more-menu-toggle__chevron-up js-hidden">
				</a>
			</div>

			<div class="home-nav-menu more-menu js-hidden">
				<a href="/about" class="more-menu__link more-menu__link--hidden">About</a>
				<a href="/subscribe" class="more-menu__link more-menu__link--hidden">Subscribe</a>
				<a href="/advertise" class="more-menu__link more-menu__link--hidden">Advertise</a>
				<a href="/contact" class="more-menu__link more-menu__link--hidden">Contact</a>
				<a href="/random" class="more-menu__link">Random</a>
				<a href="/masthead" class="more-menu__link">Staff</a>
				<a href="/policies" class="more-menu__link">Policies</a>
				<a href="/investigation" class="more-menu__link">Tipline</a>
				<div class="hover-area"></div>
			</div>

			<div class="home-nav-menu section-menu js-hidden">
				<div class="section-menu__content js-hidden" data-section="news">
					<div class="section-menu__tax-list">
						<?php display_orient_tax_menu('News Taxonomy Menu') ?>
					</div>

					<div class="section-menu__article-list">
						<?php display_orient_article_menu('News Article Menu') ?>
					</div>
				</div>

				<div class="section-menu__content js-hidden" data-section="features">
					<div class="section-menu__tax-list">
						<?php display_orient_tax_menu('Features Taxonomy Menu') ?>
					</div>

					<div class="section-menu__article-list">
						<?php display_orient_article_menu('Features Article Menu') ?>
					</div>
				</div>

				<div class="section-menu__content js-hidden" data-section="arts-entertainment">
					<div class="section-menu__tax-list">
						<?php display_orient_tax_menu('A&E Taxonomy Menu') ?>
					</div>

					<div class="section-menu__article-list">
						<?php display_orient_article_menu('A&E Article Menu') ?>
					</div>
				</div>

				<div class="section-menu__content js-hidden" data-section="sports">
					<div class="section-menu__tax-list">
						<?php display_orient_tax_menu('Sports Taxonomy Menu') ?>
					</div>

					<div class="section-menu__article-list">
						<?php display_orient_article_menu('Sports Article Menu') ?>
					</div>
				</div>

				<div class="section-menu__content js-hidden" data-section="opinion">
					<div class="section-menu__tax-list">
						<?php display_orient_tax_menu('Opinion Taxonomy Menu') ?>
					</div>

					<div class="section-menu__article-list">
						<?php display_orient_article_menu('Opinion Article Menu') ?>
					</div>
				</div>

				<div class="hover-area"></div>
			</div>
		</nav>

	</div>
	<?php endif; ?>

	<!-- If there are alerts, they will be displayed here. -->
	<?php

    if (is_front_page()) {
        $args = array(
            'post_type' => 'alert',
            // @TODO: Add date stuff
        );

        // The Query
        $query1 = new WP_Query($args);

        if ($query1->have_posts()) {
            echo "<div class=\"alerts\">";
            // The Loop
            while ($query1->have_posts()) {
                $query1->the_post();
                echo "<div class=\"alert alert-" . get_field("color", $post->ID) . "\">";
                echo "<h1>";
                the_title();
                echo "</h1>";
                the_content();
                echo "</div>";
            }
            echo "</div>";

            wp_reset_postdata();
        }
    }
    ?>

	<div class="alert alert-red small-screen-alert">
		<h1>Note about Unsupported Devices:</h1>
		<p>You seem to be browsing on a screen size, browser, or device that this website cannot support. Some things might look and act a little weird.</p>
	</div>

	<!-- Begin the WordPress content. Should be the only use of the Main tag. -->
	<main id="visual-jump">
