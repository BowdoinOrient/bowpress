<?php if (!defined('WPO_VERSION')) die('No direct access allowed'); ?>

<?php if (1 < count($tabs)) : ?>

<h2 id="wp-optimize-nav-tab-wrapper" class="nav-tab-wrapper">

<?php
	foreach ($tabs as $tab_id => $tab) {
		$tab_icon = '';
		if (is_array($tab)) {
			$tab_title = $tab['title'];
			$tab_icon = isset($tab['icon']) ? $tab['icon'] : '';
		} else {
			$tab_title = $tab;
		}
	?>
	<a id="wp-optimize-nav-tab-<?php echo $page.'-'.$tab_id; ?>" href="<?php esc_attr_e($options->admin_page_url()); ?>&amp;tab=wp_optimize_<?php echo $tab_id; ?>" class="nav-tab <?php if ($active_tab == $tab_id) echo 'nav-tab-active'; ?>">
		<?php if ($tab_icon) : ?>
			<span class="dashicons dashicons-<?php echo $tab_icon; ?>"></span>
		<?php endif; ?>
		<span><?php echo $tab_title; ?></span>
	</a>

	<?php } ?>

	<a id="wp-optimize-nav-tab-menu" href="#" class="nav-tab" role="toggle-menu">
		<?php if ($tab_icon) : ?>
			<span class="dashicons dashicons-menu"></span>
		<?php endif; ?>
		<span><?php _e('Menu', 'wp-optimize'); ?></span>
	</a>

</h2>

<?php endif;
