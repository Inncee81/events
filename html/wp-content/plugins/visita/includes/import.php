<?php
/**
 * Import WordPress Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to import content in this site.' ) );
}

if ( isset( $_REQUEST['visita-import'] ) && ! wp_verify_nonce( $_REQUEST['visita-import'], 'visita-import' ) ) {
	wp_die( __( 'Submission timeout, please refreh page.' ) );
}

do_action( 'visita_import_page_before' ); ?>

<div class="wrap">
  <h1><?php esc_html_e( 'Import', 'visita' ); ?></h1>
	<div class="postbox">

		<?php do_action( 'visita_import_page' )?>
	</div>
</div>
