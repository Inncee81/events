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

include_once( VISITA_INC . "/fields/events.php" );

class VisitaEvents extends VisitaEventFields {

  /**
  *
  */
  protected $caldera_form_id = 'CF563eab540f9fd';

  /**
  *
  */
  private $ticketmater_key = 'WkqW7ahymoVavSl9ljqacoiG2dg4gxzJ';

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( $version ) {

    $this->$version = $version;

    //crons
    add_action( 'visita_expire', array( $this, 'expire_events' ) );
    add_action( 'visita_ticketmater_import', array( $this, 'ticketmater_import' ) );

    //basics
    add_action( 'visita_activate', array( $this, 'activate' ) );
    add_action( 'visita_deactivate', array( $this, 'deactivate' ) );
    add_action( 'init', array( $this, 'register_event_post_type' ), -5 );

    //forms
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 10, 2 );
    add_filter( 'acf/load_value/key=_times', array( $this, 'load_repeater_values' ), 50, 3 );
    add_filter( 'acf/load_value/key=_performers', array( $this, 'load_repeater_values' ), 50, 3 );

    add_action( 'caldera_forms_entry_saved', array( $this, 'save_user_event' ), 10, 2 );

    //speed up wordpress
    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'template_redirect', array( $this, 'redirect_404' ), 20, 100 );
      return;
    }

    add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );
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
   * Add admin styles and scripts
   *
   * @return void
   * @since 3.0.0
   */
  function admin_scripts( ) {
    wp_dequeue_script( 'select2' );

    if ( get_current_screen()->post_type === $this->post_type ) {
      wp_enqueue_style( 'visita-fields', plugins_url( 'css/fields.css', VISITA_FILE_NAME ), NULL, $this->version );
      wp_enqueue_script( 'visita-admin', plugins_url( 'js/admin.js', VISITA_FILE_NAME ), array('jquery'), $this->version, true );
    }
  }

  /**
  *
  * @return void
  * @since 3.0.0
  */
  function register_event_post_type( ) {

    register_taxonomy( 'eventos', 'evento', array(
      'show_in_rest'      => true,
      'show_admin_column' => true,
      'label'             => __( 'Events', 'inmigracion' ),
      'rewrite'           => array( 'slug' => 'eventos' ),
      'hierarchical'      => true,
    ) );

    register_post_type( $this->post_type, array(
      'labels'            => array(
          'name'          => __( 'Events', 'visita' ),
          'singular_name' => __( 'Event', 'visita' ),
        ),
        'show_ui'         => true,
        'public'          => true,
        'hierarchical'    => false,
        'menu_position'   => 22,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'taxonomies'      => array( 'eventos', 'artistas' ),
        'rewrite'         => array( 'slug' => 'evento', 'with_front' => false ),
        'supports'        => array(
          'title',
          'editor',
          'comments',
          'revisions',
          'thumbnail',
        )
    ) );
  }

  /**
  * Create event from user input
  *
  * @return bool|unit
  * @since 1.0.0
  */
  function save_user_event( $entry_id, $form ) {

    if ( $form['form_id'] != $this->caldera_form_id ) {
      return;
    }

    $date     = sanitize_text_field($_POST['fld_2997934']);
    $time     = sanitize_text_field($_POST['fld_5010512']);
    $end      = sanitize_text_field($_POST['fld_7610679']);
    $title    = sanitize_text_field($_POST['fld_8453987']);
    $location = sanitize_text_field($_POST['fld_1347577']);

    return wp_insert_post( array_replace_recursive( $this->event, array(
        'post_status'     => 'pending',
        'tax_input'       => array(
          'eventos'       => $_POST['fld_5981410']
        ),
        'post_title'      => $title,
        'post_content'    => sanitize_text_field($_POST['fld_5489516']),
        'meta_input'      => array(
          '_times'        => array( array(
            '_date'       => $date,
            '_time'       => $time,
          ) ),
          '_starts'       => strtotime( "$date $time" ),
          '_ends'         => strtotime( $end ? "$date $end" : "$date $time + 120 minutes" ),
          '_location'     => sanitize_text_field($_POST['fld_1347577']),
          '_street'       => sanitize_text_field($_POST['fld_8698786']),
          '_zip'          => sanitize_text_field($_POST['fld_9899020']),
          '_price'        => sanitize_text_field($_POST['fld_598129']),
          '_price_max'    => sanitize_text_field($_POST['fld_8942633']),
          '_description'  => $this->get_description( "$title $location", $date, $time ),
        )
      )
    ) );
  }

  /**
  * Format event description for SEO
  *
  * @return string
  * @since 3.0.0
  */
  function get_description( $title, $date, $time, $city = 'Las Vegas' ) {
    return ucwords( strtolower( $title ) )
    . " de $city, "
    . sprintf(
       date_i18n( 'l j %\s F Y %\s g:i a.', strtotime( "$date $time" ) ),
       __( 'de', 'visita' ),
       __( 'a las', 'visita' )
    );
  }

  /**
  * Import events from ticketmaster api
  *
  * @return void
  * @since 1.0.0
  */
  function ticketmater_import( $keyword = 'latin' ) {

    $data = json_decode( file_get_contents(
      'https://app.ticketmaster.com/discovery/v2/events.json?' .
      http_build_query( array(
        'size'      => 20,
        'stateCode' => 'nv',
        'apikey'    => $this->ticketmater_key,
        'keyword'   => ( empty( $keyword ) ? 'latin' : $keyword ),
      ) )
    ) );

    if ( empty( $data->_embedded->events ) ) {
      return;
    }

    foreach ( $data->_embedded->events as $event ) {

      // don't add / update already imported items
      if ( $this->get_id_by_metadata( '_event_id', $event->id ) ) {
        continue;
      }

      // scan images for image that will best fit the theme
      foreach( $event->images as $image ){
        if( $image->ratio == '16_9' && $image->width > 800 && $image->width < 1136 ){
          $event->image = $image->url;
        }
      }

      if ( empty( $event->priceRanges ) ) {
        $event->priceRanges = array(
          (object) array(
            'min' => '',
            'max' => '',
          )
        );
      }

      $date     = $event->dates->start->localDate;
      $time     = $event->dates->start->localTime;

      // try to save event
      $post_id = wp_insert_post(
        array_replace_recursive( $this->event, array(
          'post_title'          => $event->name,
          'post_status'         => 'publish',
          'tax_input'           => array(
            'eventos'           =>  20
          ),
          'meta_input'          => array(
            '_event_id'         => $event->id,
            '_location'         => $event->_embedded->venues[0]->name,
            '_street'           => $event->_embedded->venues[0]->address->line1,
            '_state'            => $event->_embedded->venues[0]->state->stateCode,
            '_city'             => $event->_embedded->venues[0]->city->name,
            '_zip'              => $event->_embedded->venues[0]->postalCode,
            '_link'             => $event->url,
            '_price'            => $event->priceRanges[0]->min,
            '_price_max'        => $event->priceRanges[0]->max,
            '_starts'           => strtotime( "$date $time" ),
            '_ends'             => strtotime( "$date $time + 120 minutes" ),
            '_times'            => array( array(
              '_date'           => $event->dates->start->localDate,
              '_time'           => $event->dates->start->localTime,
              '_date_link'      => '',
              '_availability'   => 'InStock',
            ) ),
            '_description'      => $this->get_description(
                                    "{$event->name} {$event->_embedded->venues[0]->name}",
                                    $event->dates->start->localDate,
                                    $event->dates->start->localTime
            ),
          ),
        ) )
      );

      // post or image could not be created or download it, move on.
      if ( is_wp_error( $post_id ) || is_wp_error( $tmp = download_url( $event->image ) ) ) {
        continue;
      }

      // generate image sizes
      $filetype   = wp_check_filetype( $tmp );
      $attach_id  = media_handle_sideload( array(
        'error'    => 0,
        'tmp_name' => $tmp,
        'size'     => filesize( $tmp ),
        'type'     => $filetype['type'],
        'name'     => basename( $event->image ),
      ), $post_id );

      // attach image
      set_post_thumbnail( $post_id, $attach_id );
    }
  }

  /**
  * Get Post ID by using meta_key and media_value
  *
  * @return bool|unit
  * @since 3.0.0
  */
  function get_id_by_metadata( $meta_key, $media_value ) {

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
      "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",
      $meta_key,
      $media_value
    ) );
  }

  /**
  * Expired events
  * and delete unprocess orders
  *
  * @return void
  * @since 0.5.0
  */
  function expire_events( ) {

    $posts = get_post( array(
      'post_status'  => 'any',
      'posts_per_page' => -1,
      'post_type'    => $this->post_type,
      'meta_query'   => array(
    		array(
    			'key'      => '_ends',
          'compare'  => '<=',
    			'value'    => strtotime( 'Today' ),
    		),
        array(
          'value'    => 0,
    			'key'      => '_permanente',
    		)
    	)
    ) );

    foreach( $posts as $post ) {
      wp_delete_post( $post->ID, true );
    }
  }
}
