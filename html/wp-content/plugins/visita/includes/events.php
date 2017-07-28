<?php
/**
* Visita - Events Class
*
* @file events.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/events.php
* @since available since 0.1.0
*/

class VisitaEvents {

  private $ticketmater_key = 'WkqW7ahymoVavSl9ljqacoiG2dg4gxzJ';
  protected $default_event = array(
    'post_title'          => '',
    'post_author'         => 1,
    'post_status'         => 'draft',
    'post_content'        => '',
    'post_type'           => 'evento',
    'tax_input'           => array(),
    'meta_input'          => array(
      '_keywords'         => array(
        'espanol', 'musica',
      ),
      '_artista'          => 0,
      '_description'      => '',
      '_fecha'            => '', //start date
      '_horario'          => '', //start time
      '_hasta'            => '', //end date
      '_cierran'          => '', //end time
      '_link'             => '',
      '_price'            => '',
      '_price_max'        => '',
      '_location'         => '',
      '_calle'            => '',
      '_ciudad'           => '',
      '_estado'           => '',
      '_codigo_postal'    => '',
      '_phone'            => '',
      '_disable_source'   => false,
      '_event_type'       => 'event',
      '_priceCurrency'    => 'USD',
      '_availability'     => 'InStock',
      '_eventStatus'      => 'activate',
    )
  );

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( ) {

    //crons
    add_action( 'visita_expire', array( $this, 'expire_events' ) );
    add_action( 'visita_ticketmater_import', array( $this, 'ticketmater_import' ) );

    //basics
    add_action( 'visita_activate', array( $this, 'activate' ) );
    add_action( 'visita_deactivate', array( $this, 'deactivate' ) );

    //speed up wordpress
    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'template_redirect', array( $this, 'redirect_404'), 20, 100 );
    }
  }

  /**
   * Deactivate
   *
   * @return void
   * @since 0.5.0
   */
  function deactivate() {
    wp_clear_scheduled_hook( 'visita_expire' );
    wp_clear_scheduled_hook( 'visita_ticketmater_import' );
  }

  /**
   * Activite and save default options
   * Activite the expire cron
   *
   * @return void
   * @since 0.5.0
   */
  function activate( ) {
    wp_schedule_event( strtotime( '2 AM' ), 'daily', 'visita_expire' );
    wp_schedule_event( strtotime( '3 AM' ), 'twicedaily', 'visita_ticketmater_import');
  }

  /**
  *
  */
  function redirect_404( ) {
    if ( is_404() && isset( $_SERVER['REQUEST_URI'] ) ) {
      if ( preg_match('/\/(evento|event)/i', $_SERVER['REQUEST_URI']) ) {
        wp_redirect( '/', 302 ); die();
      }
    }
  }

  /**
  * Expired events
  * and delete unprocess orders
  *
  * @return void
  * @since 0.5.0
  */
  function expire_events( ) {

  }

  /**
  * Expired events
  * and delete unprocess orders
  *
  * @return void
  * @since 1.0.0
  */
  function ticketmater_import( $keyword = 'latin' ) {

    $data = json_decode( file_get_contents(
      'https://app.ticketmaster.com/discovery/v2/events.json?' .
      http_build_query( array(
        'size'      => 35,
        'stateCode' => 'nv',
        'apikey'    => $this->ticketmater_key,
        'keyword'   => ( empty( $keyword ) ? 'latin' : $keyword ),
      ) )
    ) );

    if ( empty( $data->_embedded->events ) ) {
      return;
    }

    $events = array();
    foreach ( $data->_embedded->events as $event ) {

    }
  }
}
