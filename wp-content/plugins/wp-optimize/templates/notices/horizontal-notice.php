<?php if (!defined('WPO_PLUGIN_MAIN_PATH')) die('No direct access allowed'); ?>

<div class="updraft-ad-container updated">
	<div class="updraft_notice_container">
		<div class="updraft_advert_content_left">
			<img src="<?php echo WPO_PLUGIN_URL.'/images/'.$image; ?>" width="60" height="60" alt="<?php _e('notice image', 'wp-optimize'); ?>" />
		</div>
		<div class="updraft_advert_content_right">
			<h3 class="updraft_advert_heading">
				<?php
				if (!empty($prefix)) echo $prefix.' ';
					echo $title;
				?>
				<div class="updraft-advert-dismiss">
				<?php if (!empty($dismiss_time)) { ?>
					<a href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'wp_optimize_ajax', subaction: '<?php echo $dismiss_time; ?>', nonce: '<?php echo wp_create_nonce('wp-optimize-ajax-nonce'); ?>' });"><?php _e('Dismiss', 'wp-optimize'); ?></a>
				<?php } else { ?>
					<a href="#" onclick="jQuery('.updraft-ad-container').slideUp();"><?php _e('Dismiss', 'wp-optimize'); ?></a>
				<?php } ?>
				</div>
			</h3>
			<p>
				<?php
				echo $text;
					$button_text = '';
					if (isset($discount_code)) echo ' <b>' . $discount_code . '</b>';

					if (!empty($button_link) && !empty($button_meta)) {
					// Check which Message is going to be used.
					if ('updraftcentral' == $button_meta) {
						$button_text = __('Get UpdraftCentral', 'wp-optimize');
					} elseif ('review' == $button_meta) {
						$button_text = __('Review WP-Optimize', 'wp-optimize');
					} elseif ('updraftplus' == $button_meta) {
						$button_text = __('Get UpdraftPlus', 'wp-optimize');
					} elseif ('signup' == $button_meta) {
						$button_text = __('Sign up', 'wp-optimize');
					} elseif ('go_there' == $button_meta) {
						$button_text = __('Go there', 'wp-optimize');
					} elseif ('wpo-premium' == $button_meta) {
						$button_text = __('Find out more.', 'wp-optimize');
					} elseif ('wp-optimize' == $button_meta) {
						$button_text = __('Find out more.', 'wp-optimize');
					}
					$wp_optimize->wp_optimize_url($button_link, $button_text, null, 'class="updraft_notice_link"');
					}
				?>
			</p>
		</div>
	</div>
	<div class="clear"></div>
</div>
