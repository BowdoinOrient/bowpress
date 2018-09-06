<div class="wpo_col wpo_span_1_of_3">
	<div class="postbox">
		<div class="inside">
			<h3><?php _e('Trackback/comments actions', 'wp-optimize'); ?></h3>

			<div id="actions-results-area"></div>

			<p>
			<h4><?php _e('Trackbacks', 'wp-optimize'); ?></h4>

			<p>
				<small><?php _e('Use these buttons to enable or disable any future trackbacks on all your previously published posts.', 'wp-optimize'); ?></small>
			</p>

			<button class="button-primary" type="button" id="wp-optimize-disable-enable-trackbacks-enable" name="wp-optimize-disable-enable-trackbacks-enable"><?php _e('Enable', 'wp-optimize'); ?></button>

			<button class="button-primary" type="button" id="wp-optimize-disable-enable-trackbacks-disable" name="wp-optimize-disable-enable-trackbacks-disable"><?php _e('Disable', 'wp-optimize'); ?></button>

			<img id="trackbacks_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner-2x.gif')); ?>" alt="...">

			</p>
			<p>
			<h4><?php _e('Comments', 'wp-optimize'); ?></h4>

			<p><small><?php _e('Use these buttons to enable or disable any future comments on all your previously published posts.', 'wp-optimize'); ?></small></p>

			<button class="button-primary" type="button" id="wp-optimize-disable-enable-comments-enable" name="wp-optimize-disable-enable-comments-enable"><?php _e('Enable', 'wp-optimize'); ?></button>

			<button class="button-primary" type="button" id="wp-optimize-disable-enable-comments-disable" name="wp-optimize-disable-enable-comments-disable"><?php _e('Disable', 'wp-optimize'); ?></button>

			<img id="comments_spinner" class="wpo_spinner" src="<?php esc_attr_e(admin_url('images/spinner-2x.gif')); ?>" alt="...">

			</p>
		</div>
	</div>
</div>
</div>