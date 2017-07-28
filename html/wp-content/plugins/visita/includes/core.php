<?php
/**
* Visita - Core Class
*
* @file core.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/core.php
* @since available since 0.1.0
*/

class VisitaCore {

  /**
   * Constructor
   *
   * @return void
   * @since 0.5.0
   */
  function __construct( ) {

    //
    add_action( 'init', array( $this, 'add_rewrite_rules' ), 200 );
    add_action( 'visita_get_weather', array( $this, 'visita_get_weather' ) );

    //subclasses
    $this->events = new VisitaEvents();

    //speed up wordpress
    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_filter( 'locale', array( $this, 'change_language'), 500 );
    }

    //
    add_filter( 'srm_max_redirects', array( $this, 'srm_max_redirects' ) );

    //register hooks
    register_activation_hook( VISITA_FILE_NAME, array( $this, 'activate' ) );
    register_deactivation_hook( VISITA_FILE_NAME, array( $this, 'deactivate' ) );
  }

  /**
   *
   * @return void
   * @since 0.5.0
   */
  function activate() {
    wp_schedule_event( time(), 'hourly', 'visita_get_weather' );

    if ( ! file_exists( WP_CONTENT_DIR . "/cache/_json/" ) ) {
      @mkdir( WP_CONTENT_DIR . "/cache/_json/", 0755 );
    }

    do_action( 'visita_activate' );
  }

  /**
   *
   * @return void
   * @since 0.5.0
   */
  function deactivate() {
    do_action( 'visita_deactivate' );
    wp_clear_scheduled_hook( 'visita_get_weather' );
  }

  /**
  *
  */
  function change_language( $locale ) {

    if ( isset( $_SERVER['REQUEST_URI'] ) && stripos( $_SERVER['REQUEST_URI'], 'en' ) == 1 ) {
      return "en_US";
    }

    return $locale;
  }

  /**
  *
  */
  function add_rewrite_rules( ) {
    global $wp_rewrite;
    $wp_rewrite->pagination_base = __( 'page', 'admin' );
  }

  /**
  * increase number or redirects
  */
  function srm_max_redirects( ) {
    return 350;
  }

  /**
  * Activite and save default options
  * Activite the expire cron
  *
  * @return void
  * @since 1.0.1
  */
  function visita_get_weather(){
    $responds = wp_remote_get( 'https://api.apixu.com/v1/current.json?key=d5c0c8ccdd194cf4b0003734172201&q=89109' );
    if ( $responds['body'] ) {
      if ( $fh = @fopen(WP_CONTENT_DIR . "/cache/_json/clima.json", "w" ) ){
        fwrite( $fh, $responds['body']);
        fclose( $fh );
      }
    }
  }
}