<?php if (!defined('WPO_VERSION')) die ('No direct access allowed'); ?>

<div class="wpo_col wpo_half_width">
	<div class="postbox">
		<div class="inside">
			<img class="addons" alt="WP_Optimize" src="<?php echo WPO_PLUGIN_URL.'images/logo/wpo_logo_small.png'; ?>">
			<h3>WP-Optimize is now part of the UpdraftPlus family!</h3>

			<p>Since version 2.0, WP-Optimize has been owned, developed, supported by <?php $wp_optimize->wp_optimize_url('https://updraftplus.com/', 'UpdraftPlus');?>, the world's most installed WordPress backup/restore plugin.</p>

			<p>Releases 2.0 and 2.1 since then have brought no major changes or features on the outside (apart from a few bug fixes). What they have brought, is a complete re-factorization of the plugin on the inside. The code structure has now been completely renovated to make future development quicker, and to enable a future remote-control facility for users with multiple sites (through <a href="https://updraftcentral.com">UpdraftCentral)</a>. With version 2.1, all dashboard operations now take place without the need for page refreshes, and the user interface has been modified in several ways for better ease of understanding and use.</p>
			
			<p>We are quite excited to have reached this stage which lays a solid foundation for lots of future improvements. Watch this space and sign up to the <?php $wp_optimize->wp_optimize_url('https://updraftplus.com/newsletter-signup', __('UpdraftPlus newsletter', 'wp-optimize'));?> for updates!</p>
			
			<p>Find out more about the acquisition <?php $wp_optimize->wp_optimize_url('https://updraftplus.com/updraftplus-confirms-acquired-wp-optimize', __('here', 'wp-optimize'));?>.</p>
		</div>
	</div>
</div>
<div class="wpo_col wpo_half_width">
	<div class="postbox">
		<div class="inside">
			<?php $wp_optimize->wp_optimize_url('https://updraftplus.com/', null, '<img class="addons" name="UpdraftPlus" src="'. WPO_PLUGIN_URL.'images/logo/udp_logo_small.png' .'">');?>
			<h3>Why do you need a backup plugin?</h3>
			<p>Websites crash, get hacked; hosting companies make mistakes or go bust; plugin and theme authors release code with unwanted errors in; site editors make mistakes too. With a backup, you can be back up and running again in minutes. Without - everything is gone.</p>
			
			<p>Do backups have anything to do with optimising? Yes. When WP-Optimize cleans up part of the database such as unpublished comments, or old post drafts, then the contents get permanently deleted. If there's even a remote chance you'll regret this later, then you ought to take a backup!</p>
			
			<h3>Why choose UpdraftPlus to backup my site?</h3>
			
			<p>Not all backup plugins are created equal. The following are essential features:</p>
			<ol>
				<li>
					<p>The ability to set an <strong>automatic backup schedule</strong>: it's too easy to forget to take the backup you really needed. Much better to set it up and leave it to do the work for you.</p>
				</li>
				<li>
					<p>The ability to <strong>backup to remote storage</strong> like Dropbox or Google Drive: if your backups are only in your webspace, then you can lose both at once (to hackers, server failure, etc.).</p>
				</li>
				<li>
					<p>The ability to <strong>restore your website</strong> for you from your backups. Unfortunately, most WordPress backup plugins force you to learn about how to restore by hand!</p>
				</li>
			</ol>
			<p>
				<?php $wp_optimize->wp_optimize_url('https://wordpress.org/plugins/updraftplus/', __('UpdraftPlus', 'wp-optimize'));?> is the most trusted and installed backup plugin because it does all of this and more reliably, for free. There is also a premium version which can do <?php $wp_optimize->wp_optimize_url('https://updraftplus.com/', __('a lot more', 'wp-optimize'));?>.
			</p>
		</div>
	</div>
</div>
