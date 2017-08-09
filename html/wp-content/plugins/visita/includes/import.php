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

if ( isset( $_REQUEST['event-import'] ) && ! wp_verify_nonce( $_REQUEST['event-import'], 'visita-import' ) ) {
	wp_die( __( 'Submission timeout, please refreh page.' ) );
}

if ( isset( $_REQUEST['json-import'] ) ) {
	if ( ! empty($_REQUEST['url']) ) {
		$this->import_event_by_url( $_REQUEST['url'] );
	} else if ( ! empty($_REQUEST['json'] ) ) {

	}
}

if ( isset( $_REQUEST['ticketmaster'] ) ) {
	$this->ticketmater_import( sanitize_text_field( $_REQUEST['keyword'] ) );
}

do_action( 'visita_before_import_page' ); ?>

<div class="wrap">
  <h1><?php esc_html_e( 'Import', 'visita' ); ?></h1>
	<div class="postbox">

		<form class="inside" method="post">
			<input type="hidden" name="page" value="event-import" />
			<input class="widefat" type="text" name="keyword" placeholder="keyword.." />
			<p>
				<input type="submit" name="ticketmaster" class="button-primary" value="Ticketmaster" />
			</p>
			<?php wp_nonce_field( 'visita-import', 'event-import' ) ?>
		</form>

		<form class="inside" method="post">
			<p class="mf_field_wrapper">
				<input class="widefat" type="text" name="url" placeholder="site url.." />
				<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
			</p>
			<p class="mf_field_wrapper">
				<textarea class="widefat" name="json" rows="5" placeholder="json..."></textarea>
			</p>
			<input type="submit" name="json-import" class="button-primary" value="Import" />
			<input type="hidden" name="page" value="event-import" />
			<?php wp_nonce_field( 'visita-import', 'event-import' ) ?>
		</form>

		<?php do_action('visita_import_page')?>
	</div>
</div>
