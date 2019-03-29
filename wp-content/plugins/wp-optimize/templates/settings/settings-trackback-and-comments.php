<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>
<h3><?php _e('Trackback/comments actions', 'wp-optimize'); ?></h3>
<div class="wpo-fieldgroup">
	<div class="wpo-fieldgroup__subgroup">
		<h3 class="wpo-first-child"><?php _e('Trackbacks', 'wp-optimize'); ?></h3>

		<div id="trackbacks_notice"></div>

		<p>
			<small class="wpo-text__dim"><?php _e('Use these buttons to enable or disable any future trackbacks on all your previously published posts.', 'wp-optimize'); ?></small>
		</p>
		
		<button class="button btn-updraftplus" type="button" id="wp-optimize-disable-enable-trackbacks-enable" name="wp-optimize-disable-enable-trackbacks-enable"><?php _e('Enable', 'wp-optimize'); ?></button>

		<button class="button btn-updraftplus" type="button" id="wp-optimize-disable-enable-trackbacks-disable" name="wp-optimize-disable-enable-trackbacks-disable"><?php _e('Disable', 'wp-optimize'); ?></button>

		<img id="trackbacks_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner-2x.gif')); ?>" alt="...">

	</div>

	<div class="wpo-fieldgroup__subgroup">

		<h3><?php _e('Comments', 'wp-optimize'); ?></h3>
		
		<div id="comments_notice"></div>

		<p><small class="wpo-text__dim"><?php _e('Use these buttons to enable or disable any future comments on all your previously published posts.', 'wp-optimize'); ?></small></p>

		<button class="button btn-updraftplus" type="button" id="wp-optimize-disable-enable-comments-enable" name="wp-optimize-disable-enable-comments-enable"><?php _e('Enable', 'wp-optimize'); ?></button>

		<button class="button btn-updraftplus" type="button" id="wp-optimize-disable-enable-comments-disable" name="wp-optimize-disable-enable-comments-disable"><?php _e('Disable', 'wp-optimize'); ?></button>

		<img id="comments_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner-2x.gif')); ?>" alt="...">

	</div>
</div>
