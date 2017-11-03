<?php

/*
 * Page Name: Bigger Skyscraper
 * Article Count: 40
 * Version: 1.0.0
 */

?>

<!-- Home Template: Skyscraper -->

<div class="container-fluid">
	<div class="row">
		<div class="col col-xs-12 border-bottom">
			<?php echo home_render("M7", 16); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-6 col-sm-8 col-xs-12 col-md-push-2 border-left border-right">
			<?php echo home_render("M1", 1); ?>
			<?php echo home_render("M4", 2); ?>
			<?php echo home_render("M4", 3); ?>
			<?php echo home_render("M4", 4); ?>

			<div class="row">
				<div class="col col-md-6 col-sm-6 col-xs-6">
					<?php echo home_render("M2", 5); ?>
				</div>
				<div class="col col-md-6 col-sm-6 col-xs-6">
					<?php echo home_render("M2", 6); ?>
				</div>
			</div>
		</div>
		<div class="col col-md-4 col-sm-4 col-xs-12 col-md-push-2">
			<div class="module no-border hidden-xs">
				<?php echo home_render("social"); ?>
			</div>
			<?php echo home_render("M3", 7); ?>

			<?php echo home_render("M4", 8); ?>
			<?php echo home_render("M4", 9); ?>
			<?php echo home_render("M4", 10); ?>
			<?php echo home_render("M4", 11); ?>
			<?php echo home_render("M4", 12); ?>
			<?php echo home_render("M4", 13); ?>

			<?php echo home_render("A-square"); ?>
		</div>
		<div class="col col-md-2 hidden-sm hidden-xs col-md-pull-10">
			<?php for($i = 21; $i<=40; $i++) : ?>
				<?php echo home_render("M6", $i); ?>
			<?php endfor; ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-12">
			<!-- <h1 class="block-title"><a href="/series/talk-of-the-quad/">Recently in &lsquo;Talk of the Quad&rsquo;</a></h1> -->
			<?php echo home_render("M5", 14); ?>
			<?php echo home_render("M5", 15); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-12">
			<?php echo home_render("A-banner"); ?>
		</div>
	</div>

	<hr class="mega-separator">

	<?php echo home_render("sections"); ?>

	<div class="row">
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 17); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 18); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 19); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 20); ?>
		</div>
	</div>
</div>
