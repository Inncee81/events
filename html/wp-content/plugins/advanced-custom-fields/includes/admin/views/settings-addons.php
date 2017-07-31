<div class="wrap acf-settings-wrap">

	<h1><?php esc_html_e("Add-ons",'acf'); ?></h1>

	<div class="add-ons-list">

		<?php if( !empty($json) ): ?>

			<?php foreach( $json as $addon ):

				$addon = wp_parse_args($addon, array(
					"title"			=> "",
			        "slug"			=> "",
			        "description"	=> "",
			        "thumbnail"		=> "",
			        "url"			=> "",
			        "btn"			=> __("Download & Install",'acf'),
			        "btn_color"		=> ""
				));

				?>

				<div class="acf-box add-on add-on-<?php echo esc_attr($addon['slug']); ?>">

					<div class="thumbnail">
						<a target="_blank" href="<?php echo esc_url($addon['url']); ?>">
							<img src="<?php echo esc_url($addon['thumbnail']); ?>" />
						</a>
					</div>
					<div class="inner">
						<h3><a target="_blank" href="<?php echo esc_url($addon['url']); ?>"><?php echo esc_html($addon['title']); ?></a></h3>
						<p><?php echo esc_html($addon['description']); ?></p>
					</div>
					<div class="footer">
						<?php if( apply_filters("acf/is_add_on_active/slug={$addon['slug']}", false ) ): ?>
							<a class="button" disabled="disabled"><?php esc_html_e("Installed",'acf'); ?></a>
						<?php else: ?>
							<a class="button <?php echo esc_attr($addon['btn_color']); ?>" target="_blank" href="<?php echo esc_url($addon['url']); ?>" ><?php esc_html_e($addon['btn']); ?></a>
						<?php endif; ?>

						<?php if( !empty($addon['footer']) ): ?>
							<p><?php echo esc_html($addon['footer']); ?></p>
						<?php endif; ?>
					</div>

				</div>

			<?php endforeach; ?>

		<?php endif; ?>

	</div>

</div>
