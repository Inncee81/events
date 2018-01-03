<!doctype html>
<html amp <?php echo AMP_HTML_Utils::build_attributes_string( $this->get( 'html_tag_attributes' ) ); ?>>
<head>
	<meta charset="utf-8">
	<meta name="amp-google-client-id-api" content="googleanalytics">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
	<?php do_action( 'amp_post_template_head', $this ); ?>
	<style amp-custom>
		<?php $this->load_parts( array( 'style' ) ); ?>
		<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>

<body class="<?php echo esc_attr( $this->get( 'body_class' ) ); ?>">

<?php $this->load_parts( array( 'header-bar' ) ); ?>

<article class="amp-wp-article">

	<?php $this->load_parts( array( 'featured-image' ) ); ?>

	<header class="amp-wp-article-header">
		<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>

		<div class="amp-header-meta">
			<?php visita_price_range(); ?>
			<div class="location" itemscope itemprop="location" itemtype="http://schema.org/Place">
				<?php visita_entry_meta(); ?>
			</div>
			<?php if (is_single()) visita_entry_tax( $post ) ?>
		</div><!-- .entry-meta -->
	</header>

	<div class="amp-dates">
		<?php visita_get_time_range(); ?>
	</div>


	<div class="amp-wp-article-content">
		<?php echo $this->get( 'post_amp_content' ); // amphtml content; no kses ?>
	</div>

	<footer class="amp-wp-article-footer">
		<?php $this->load_parts( apply_filters( 'amp_post_article_footer_meta', array( 'meta-taxonomy' ) ) ); ?>
	</footer>

</article>

<?php $this->load_parts( array( 'meta-ad' ) ); ?>
<?php $this->load_parts( array( 'footer' ) ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>

<?php do_action( 'amp_post_template_footer', $this ); ?>

</body>
</html>
