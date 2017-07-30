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

  /**
  *
  */
  protected $post_type = 'evento';

  /**
  *
  */
  protected $caldera_form_id = 'CF563eab540f9fd';

  /**
  *
  */
  private $ticketmater_key = 'WkqW7ahymoVavSl9ljqacoiG2dg4gxzJ';

  /**
  *
  */
  protected $default_event = array(
    'ID'                  => false,
    'image'               => false,
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
      '_description'      => '',
      '_starts'           => '', //start date
      '_ends'             => '', //start time
      '_location'         => '',
      '_street'           => '',
      '_link'             => '',
      '_city'             => 'Las Vegas',
      '_state'            => 'NV',
      '_zip'              => '',
      '_phone'            => '',
      '_permanent'        => false,
      '_disable_source'   => false,
      '_event_type'       => 'Event',
      '_event_id'         => false,
      '_offers'           => array(),
      '_performers'       => array(),
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
    add_action( 'init', array( $this, 'register_event_post_type' ), -5 );

    //forms
    add_action( 'caldera_forms_entry_saved', array( $this, 'save_user_event' ), 10, 2 );

    //acf
    add_action( 'acf/register_fields', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/register_fields', array( $this, 'add_event_field_group' ) );

    //speed up wordpress
    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'template_redirect', array( $this, 'redirect_404' ), 20, 100 );
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
  * Merge event data with default
  *
  * @return void
  * @since 1.0.0
  */
  function event_atts( $event ) {

    foreach ( $this->default_event as $name => $default ) {

      if ( in_array( $name, array( 'tax_input', 'meta_input') ) ) {
        foreach ( $default as $meta_key => $meta_value ) {
          if ( ! isset( $event[$name][$meta_key] ) ) {
            $event[$name][$meta_key] = $meta_value;
          }
        }
      }

      else if ( ! isset( $event[$name] ) ) {
        $event[$name] = $default;
      }
    }

    return $event;
  }

  /**
  *
  * @return void
  * @since 3.0.0
  */
  function register_acf_fields() {
    include_once( VISITA_ABSPATH . '/fields/repeater_datetime.php');
    include_once( VISITA_ABSPATH . '/fields/repeater_performer.php');
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


  function add_event_field_group() {


  }

  /**
  * Check if event exists
  *
  * @return bool
  * @since 1.0.0
  */
  function event_exists( $event ) {

    // check for imported id first
    if ( get_post_meta( $event['ID'], '_event_id', true ) ) {
      return true;
    }

    // check event by metadata
    return count(
      get_posts( array(
        'post_status' => 'any',
        'post_type' => $event['type'],
        'meta_query' => array(
          array(
            'key'     => '_performer',
            'value'   => $event['_performer'],
          ),
          array(
            'key'     => '_date',
            'value'   => $event['_date'],
          ),
          array(
            'key'     => '_to',
            'value'   => $event['_to'],
          ),
          array(
            'key'     => '_city',
            'value'   => $event['_city'],
          ),
        )
      ) )
    );
  }

  /**
  *
  */
  function save_user_event( $entry_id, $form ) {

    if ( $form['form_id'] != $this->caldera_form_id ) {
      return;
    }

    $this->insert_event( array(
        'tax_input' => array(
          'eventos' => $_POST['fld_5981410'],
        ),
        'post_title' => sanitize_text_field($_POST['fld_8453987']),
        'post_content' => sanitize_text_field($_POST['fld_5489516']),
        'meta_input' => array(
          '_date' => sanitize_text_field($_POST['fld_2997934']),
          '_to' => sanitize_text_field($_POST['fld_7610679']),
          '_time' => sanitize_text_field($_POST['fld_5010512']),
          '_price' => sanitize_text_field($_POST['fld_598129']),
          '_price_max' => sanitize_text_field($_POST['fld_8942633']),
          '_location' => sanitize_text_field($_POST['fld_1347577']),
          '_street' => sanitize_text_field($_POST['fld_8698786']),
          '_zip' => sanitize_text_field($_POST['fld_9899020']),
        )
    ) );
  }

  /**
  * Save new event
  *
  * @return void
  * @since 1.0.0
  */
  function insert_event( $event ) {

    $postid = wp_insert_post(
      $event = $this->event_atts( $event )
    );

    if ( $postid && $event['image'] ) {

      $media_type = wp_check_filetype( basename( $event['image'] ), null );
      $file_data  = wp_upload_bits(
        sanitize_file_name( $event['name'] . "." . $media_type['ext'] ),
        null,
        @file_get_contents( $event['image'] )
      );

      if ( $file_data['error'] ) return;

      $file = $file_data['file'];
      $wp_upload_dir = wp_upload_dir();

      if ( $attach_id = wp_insert_attachment( array(
        'post_mime_type' => $file_data['type'],
        'guid'           => $wp_upload_dir['url'] . '/' . basename( $file  ),
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file  ) ),
      ), $file , $postid ) ) {
        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file ) );
        set_post_thumbnail( $postid, $attach_id );
      }
    }
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
