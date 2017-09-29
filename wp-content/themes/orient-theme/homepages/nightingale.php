<?php

/*
 * Page Name: Nightingale
 * Article Count: 27 
 * Version: 1.0.0
 */

?>

<!-- Home Template: Nightingale -->

<div class="container-fluid">
	<div class="row">
		<div class="col col-xs-12 border-bottom">
			<?php echo home_render("M7", 11); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-6 col-xs-12 border-right">
			<?php echo home_render("M1", 1); ?>
			<?php echo home_render("M9", 8); ?>
		</div>
		<div class="col col-md-6 col-xs-12">
			<div class="row">
				<div class="col col-md-6 col-sm-6 col-xs-12">
					<?php echo home_render("M2", 2); ?>
				</div>
				<div class="col col-md-6 col-sm-6 col-xs-12">
					<?php echo home_render("M2", 3); ?>
				</div>
			</div>

			<hr>

			<div class="col col-md-12">
				<?php echo home_render("M9", 4); ?>
			</div>

			<hr>

			<div class="row">
				<div class="col col-md-4 col-sm-4 col-xs-12">
					<?php echo home_render("M10", 5); ?>
				</div>
				<div class="col col-md-4 col-sm-4 col-xs-12">
					<?php echo home_render("M10", 6); ?>
				</div>
				<div class="col col-md-4 col-sm-4 col-xs-12">
					<?php echo home_render("M10", 7); ?>
				</div>
			</div>
		</div>
	</div>

	<?php echo home_render("A-banner"); ?>

	<hr>

	<div class="row">
		<div class="col col-md-8 col-xs-12 border-right">
			<?php echo home_render("M8", 9); ?>
			<?php echo home_render("M8", 10); ?>
		</div>
		<div class="col col-md-4 col-sm-6 col-xs-12">
			<?php echo home_render("M4", 12); ?>
			<?php echo home_render("M4", 13); ?>
			<?php echo home_render("M4", 14); ?>
			<?php echo home_render("M4", 15); ?>
			<?php echo home_render("M4", 16); ?>
			<?php echo home_render("M4", 17); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-12">
			<?php echo home_render("M5", 18); ?>
		</div>
	</div>

	<div class="row">
		<div class="col col-md-8 col-xs-12 border-right">
			<?php echo home_render("M8", 19); ?>
			<?php echo home_render("M8", 20); ?>
		</div>
		<div class="col col-md-4 col-sm-6 col-xs-12">
			<?php echo home_render("M4", 21); ?>
			<?php echo home_render("M4", 22); ?>
			<?php echo home_render("M4", 23); ?>
			<?php echo home_render("A-square"); ?>
		</div>
	</div>

	<hr class="mega-separator">

	<?php echo home_render('sections'); ?>

	<div class="row">
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 24); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 25); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 26); ?>
		</div>
		<div class="col col-md-3 col-xs-6">
			<?php echo home_render("M3", 27); ?>
		</div>
	</div>
