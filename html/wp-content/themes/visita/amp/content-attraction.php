
<article class="amp-wp-article">

	<?php $this->load_parts( array( 'featured-image' ) ); ?>

	<header class="amp-wp-article-header">
		<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>

		<div class="amp-header-meta">
			<?php visita_price_range(); ?>
			<div class="location" itemscope itemprop="location" itemtype="http://schema.org/Place">
				<?php visita_entry_meta(); ?>
			</div>
			<?php if (is_single()) visita_entry_tax( $this->post ) ?>
		</div><!-- .entry-meta -->
	</header>

	<div class="amp-dates">
		<?php visita_opening_hours(); ?>
	</div>


	<div class="amp-wp-article-content">
		<?php echo $this->get( 'post_amp_content' ); // amphtml content; no kses ?>
	</div>

	<footer class="amp-wp-article-footer">
		<?php $this->load_parts( apply_filters( 'amp_post_article_footer_meta', array( 'meta-taxonomy' ) ) ); ?>
	</footer>

</article>
