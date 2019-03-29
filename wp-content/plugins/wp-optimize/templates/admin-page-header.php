<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<header class="wpo-main-header">
	<p class="wpo-header-links">
		<span class="wpo-header-links__label"><?php _e('Useful links', 'wp-optimize'); ?></span>
		<?php $wp_optimize->wp_optimize_url('https://getwpo.com/', __('Home', 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/', 'UpdraftPlus'); ?> |
		
		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/news/', __('News', 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://twitter.com/updraftplus', __('Twitter', 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://wordpress.org/support/plugin/wp-optimize/', __('Support', 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/newsletter-signup', __('Newsletter', 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://david.dw-perspective.org.uk', __("Team lead", 'wp-optimize')); ?> |
		
		<?php $wp_optimize->wp_optimize_url('https://getwpo.com/faqs/', __("FAQs", 'wp-optimize')); ?> |

		<?php $wp_optimize->wp_optimize_url('https://www.simbahosting.co.uk/s3/shop/', __("More plugins", 'wp-optimize')); ?>				
	</p>

	<div class="wpo-logo__container">
		<img class="wpo-logo" src="<?php echo trailingslashit(WPO_PLUGIN_URL); ?>images/notices/wp_optimize_logo.png" alt="" />
		<?php
			$sqlversion = (string) $wp_optimize->get_db_info()->get_version();
			echo '<strong>WP-Optimize '.($wp_optimize->is_premium() ? __('Premium', 'wp-optimize') : '' ).' <span class="wpo-version">'.WPO_VERSION.'</span></strong>';
		?>
		<span class="wpo-subheader"><?php _e('The #1 optimization plugin!', 'wp-optimize'); ?></span>
	</div>
	<?php
	$wp_optimize->include_template('pages-menu.php', false, array('menu_items' => WP_Optimize()->get_submenu_items()));
	?>
</header>
<?php
	// This is to display the notices.
	$wp_optimize_notices->do_notice();
?>

<script type="text/javascript">
	var wp_optimize_ajax_nonce='<?php echo wp_create_nonce('wp-optimize-ajax-nonce'); ?>';
</script>
