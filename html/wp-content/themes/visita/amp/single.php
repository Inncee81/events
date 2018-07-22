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

<?php visita_amp_post_nav( 'single-top' ); ?>

<?php $this->load_parts( array( 'content-' . get_post_type() ) ); ?>

<?php visita_amp_post_nav( 'single-bottom' ); ?>

<?php $this->load_parts( array( 'meta-ad' ) ); ?>
<?php $this->load_parts( array( 'footer' ) ); ?>
<?php $this->load_parts( array( 'sidebar' ) ); ?>

<?php do_action( 'amp_post_template_footer', $this ); ?>

</body>
</html>
