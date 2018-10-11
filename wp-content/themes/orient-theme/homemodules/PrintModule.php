<div class="row">
	<div class="col col-xs-12">
		<a class="print-edition-module" href="http://www.scribd.com/bowdoinorient/uploads">
			<img src="<?php echo get_template_directory_uri() ?>/img/print-edition.png" alt="" class="media-image">

			<h1>Explore the print edition.</h1>
			<p>Browse this week's paper and view our archives.<br>
			Last published on <?php $ci = current_issue(); echo date('l, F j, Y', strtotime($ci["date"])); ?>.</p>
		</a>
	</div>
</div>
