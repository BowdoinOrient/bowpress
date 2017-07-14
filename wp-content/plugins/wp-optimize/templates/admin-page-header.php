<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<?php
	$sqlversion = (string)$wpdb->get_var("SELECT VERSION() AS version");

	echo '<h1>WP-Optimize '.WPO_VERSION.'</h1>';

	//This is to display the notices
	$wp_optimize_notices->do_notice();

?>
<p>
		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/wp-optimize/', __('Home', 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/', __('updraftplus.com', 'wp-optimize'));?> |
		
		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/news/', __('News', 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://twitter.com/updraftplus', __('Twitter', 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://wordpress.org/support/plugin/wp-optimize/', __('Support', 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/newsletter-signup', __('Newsletter sign-up', 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://david.dw-perspective.org.uk', __("Lead developer", 'wp-optimize'));?> |
		
		<?php $wp_optimize->wp_optimize_url('https://wordpress.org/plugins/wp-optimize/faq/', __("FAQs", 'wp-optimize'));?> |

		<?php $wp_optimize->wp_optimize_url('https://www.simbahosting.co.uk/s3/shop/', __("More plugins", 'wp-optimize'));?>
		
</p>

<h2 id="wp-optimize-nav-tab-wrapper" class="nav-tab-wrapper">

		<?php foreach ($tabs as $tab_id => $tab_title) { ?>

			<a id="wp-optimize-nav-tab-<?php echo $tab_id;?>" href="<?php esc_attr_e($options->admin_page_url()); ?>&amp;tab=wp_optimize_<?php echo $tab_id; ?>" class="nav-tab <?php if ($active_tab == $tab_id) echo 'nav-tab-active'; ?>"><?php echo $tab_title;?></span></a>
		
		<?php } ?>

</h2>
<script type="text/javascript">
	var wp_optimize_ajax_nonce='<?php echo wp_create_nonce('wp-optimize-ajax-nonce');?>';
</script>
