<?php

/*
 * Page Name: Skyscraper
 * Article Count: 24
 */

?>
<div class="container-fluid">
	<div class="row">
		<div class="col col-md-6 col-sm-8 col-xs-12 col-md-push-2 border-left border-right">
			<?php echo home_render("M1", 1); ?>
			<div class="row">
				<div class="col col-md-6 col-sm-6 col-xs-6">
					<?php echo home_render("M2", 2); ?>
				</div>
				<div class="col col-md-6 col-sm-6 col-xs-6">
					<?php echo home_render("M2", 3); ?>
				</div>
			</div>
		</div>
		<div class="col col-md-4 col-sm-4 col-xs-12 col-md-push-2">
			<div class="module no-border hidden-xs">
				<?php echo home_render("social"); ?>
			</div>
			<?php echo home_render("M3", 4); ?>
			<?php echo home_render("M3", 5); ?>
			<?php echo home_render("M4", 6); ?>
			<?php echo home_render("A-square"); ?>
		</div>
		<div class="col col-md-2 hidden-sm hidden-xs col-md-pull-10">
			<?php for($i = 13; $i<=24; $i++) : ?>
				<?php echo home_render("M6", $i); ?>
			<?php endfor; ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-12">
			<?php echo home_render("A-banner"); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-12">
			<h1 class="block-title">Recently in &lsquo;Talk of the Quad&rsquo;</h1>
			<?php echo home_render("M5", 7); ?>
			<?php echo home_render("M5", 8); ?>
		</div>
	</div>

	<?php echo home_render("sections"); ?>

	<div class="row">
		<div class="col col-md-3">
			<?php echo home_render("M3", 9); ?>
		</div>
		<div class="col col-md-3">
			<?php echo home_render("M3", 10); ?>
		</div>
		<div class="col col-md-3">
			<?php echo home_render("M3", 11); ?>
		</div>
		<div class="col col-md-3">
			<?php echo home_render("M3", 12); ?>
		</div>
	</div>
</div>
