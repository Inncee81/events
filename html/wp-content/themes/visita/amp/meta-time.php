<div class="amp-wp-meta amp-wp-posted-on">
	<time datetime="<?php echo esc_attr( date( 'c', $this->get( 'post_publish_timestamp' ) ) ); ?>">
		<?php
		echo esc_html( get_the_date() );
		?>
	</time>
</div>
