<?php

// calculate add-ons (non pro only)
$plugins = array();

if( !acf_get_setting('pro') ) {

	if( is_plugin_active('acf-repeater/acf-repeater.php') ) $plugins[] = __("Repeater",'acf');
	if( is_plugin_active('acf-flexible-content/acf-flexible-content.php') ) $plugins[] = __("Flexible Content",'acf');
	if( is_plugin_active('acf-gallery/acf-gallery.php') ) $plugins[] = __("Gallery",'acf');
	if( is_plugin_active('acf-options-page/acf-options-page.php') ) $plugins[] = __("Options Page",'acf');

}

?>
<div id="acf-upgrade-notice">

	<div class="inner">

		<div class="acf-icon logo">
			<i class="acf-sprite-logo"></i>
		</div>

		<div class="content">

			<h2><?php esc_html_e("Database Upgrade Required",'acf'); ?></h2>

			<p><?php printf(esc_html__("Thank you for updating to %s v%s!", 'acf'), acf_get_setting('name'), acf_get_setting('version') ); ?><br /><?php esc_html_e("Before you start using the new awesome features, please update your database to the newest version.", 'acf'); ?></p>

			<?php if( !empty($plugins) ): ?>
				<p><?php printf(esc_html__("Please also ensure any premium add-ons (%s) have first been updated to the latest version.", 'acf'), implode(', ', $plugins) ); ?></p>
			<?php endif; ?>

			<p><a id="acf-notice-action" href="<?php echo esc_attr($button_url); ?>" class="button button-primary"><?php echo esc_html($button_text); ?></a></p>

		<?php if( $confirm ): ?>
			<script type="text/javascript">
			(function($) {

				$("#acf-notice-action").on("click", function(){

					var answer = confirm("<?php esc_html_e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'acf' ); ?>");
					return answer;

				});

			})(jQuery);
			</script>
		<?php endif; ?>

		</div>

		<div class="clear"></div>

	</div>

</div>
