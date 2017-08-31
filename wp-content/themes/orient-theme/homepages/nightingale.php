<?php

/*
 * Page Name: Nightingale
 * Article Count: 15
 */

?>

<div class="container-fluid">
	<div class="row">
		<div class="col col-md-6 col-xs-12 border-right">
			<?php echo home_render("M1", 1); ?>
		</div>
		<div class="col col-md-6 col-xs-12">
			<div class="row">
				<div class="col col-md-6 col-sm-12 col-xs-12">
					<?php echo home_render("M2", 2); ?>
				</div>
				<div class="col col-md-6 col-sm-12 col-xs-12">
					<?php echo home_render("M2", 3); ?>
				</div>
			</div>
			<div class="col col-md-12">
				<?php echo home_render("M5", 4); ?>
			</div>
			<div class="row">
				<div class="col col-md-4 col-xs-12">
					<?php echo home_render("M4", 5); ?>
				</div>
				<div class="col col-md-4 col-xs-12">
					<?php echo home_render("M4", 6); ?>
				</div>
				<div class="col col-md-4 col-xs-12">
					<?php echo home_render("M4", 7); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col col-md-8 col-xs-12">
			<?php echo home_render("M5", 8); ?>
			<?php echo home_render("M5", 9); ?>
			<?php echo home_render("M5", 10); ?>
		</div>
		<div class="col col-md-4 col-xs-12">
			<?php echo home_render("M4", 11); ?>
			<?php echo home_render("M4", 11); ?>
			<?php echo home_render("M4", 11); ?>
			<?php echo home_render("M4", 11); ?>
			<?php echo home_render("M4", 11); ?>
		</div>
	</div>

	<?php echo home_render('sections'); ?>
