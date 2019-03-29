<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>
<a id="wp-optimize-nav-page-menu" href="#" role="toggle-menu">
	<span class="dashicons dashicons-no-alt"></span>
	<span class="dashicons dashicons-menu"></span>
	<span><?php _e('Menu', 'wp-optimize'); ?></span>
</a>
<div class="wpo-pages-menu">
	<?php
	$active_page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '';
	foreach ($menu_items as $menu) :
	?>

		<?php if (isset($menu['icon']) && 'separator' == $menu['icon']) : ?>
			<span class="separator"></span>
		<?php else : ?>
			<a class="<?php echo ($active_page === $menu['menu_slug']) ? 'active' : ''; ?>" href="<?php esc_attr_e(menu_page_url($menu['menu_slug'], false)); ?>" data-menuslug="<?php echo $menu['menu_slug']; ?>">
				<span class="dashicons dashicons-<?php echo $menu['icon']; ?>"></span>
				<span class="title"><?php esc_html_e($menu['menu_title']); ?></span>
			</a>
		<?php endif; ?>

	<?php endforeach; ?>
	<p class="wpo-header-links__mobile">
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

</div>	