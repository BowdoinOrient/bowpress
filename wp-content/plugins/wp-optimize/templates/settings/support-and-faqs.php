<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<h3><?php _e('Support and feedback', 'wp-optimize'); ?></h3>
<div class="wpo-fieldgroup">
	<ul>
		<li><?php WP_Optimize()->wp_optimize_url('https://getwpo.com/faqs/', __("Read our FAQ here", 'wp-optimize')); ?></li>
		<li><a href="https://wordpress.org/support/plugin/wp-optimize/"><?php _e('Support is available here.', 'wp-optimize'); ?></a></li>
		<li><?php echo __('If you like WP-Optimize,', 'wp-optimize').' <a href="https://wordpress.org/support/plugin/wp-optimize/reviews/?rate=5#new-post" target="_blank">'.__('please give us a positive review, here.', 'wp-optimize'); ?></a> <?php echo __('Or, if you did not like it,', 'wp-optimize').' <a target="_blank" href="https://wordpress.org/support/plugin/wp-optimize/">'.__('please tell us why at this link.', 'wp-optimize'); ?></a></li>
	</ul>
</div>