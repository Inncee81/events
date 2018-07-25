<?php
/**
* Visita - Shows Class
*
* @file shows.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/shows.php
* @since available since 0.1.0
*/

class VisitaClubs extends VisitaBase {

  /**
  *
  */
  protected $post_type = 'club';

  /**
  *
  */
  protected $taxonomy = 'clubs';

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( ) {

    $this->position = 28;
    $this->slug = __( 'club', 'visita' );
    $this->name = __( 'Nightclubs', 'visita' );
    $this->singular = __( 'Nightclub', 'visita' );
    $this->taxonomy_slug = __( 'clubs', 'visita' );
    $this->taxonomy_label = __( 'Nightclubs', 'visita' );
    $this->description = __( 'Noches Latinas en Las Vegas: Nightclubs, Antros, Discotecas, Banda, Salsa, Bachata y más.' , 'visita');

    $this->club_data = array_replace_recursive( $this->default_data, array(
      'post_type'         => $this->post_type,
      'meta_input'        => array(
        '_hours'          => array( array(
          '_day'          => '',
          '_open'         => '',
          '_close'        => '',
          '_24h'          => '',
        ) ),
        '_business_type'  => 'NightClub',
      )
    ) );

    $this->tabs = array(
      'name' => __( 'Name', 'visita' ),
      'price' => __( 'Price', 'visita' ),
    );

    $defaults = $this->club_data['meta_input'];
    $this->fields = array_replace_recursive( $this->fields, array(
      'title' => __( 'Show Details', 'visita' ),
      'location' => array (
        array (
          array (
            'value' => $this->post_type,
          ),
        ),
      ),
      'fields' => array(
        array(
          'key' => 'tap_general',
          'label' => __( 'General', 'visita'  ),
          'type' => 'tab',
        ),
        array(
          'key' => '_link',
          'name' => '_link',
          'label' => __( 'Link', 'visita' ),
          'type' => 'text',
          'formatting' => 'url',
        ),
        array(
          'key' => '_price',
          'name' => '_price',
          'type' => 'text',
          'label' => __( 'Price', 'visita' ),
        ),
        array(
          'key' => '_price_max',
          'name' => '_price_max',
          'type' => 'number',
          'label' => __( 'Max Price', 'visita' ),
          'min' => 1,
        ),
        array(
          'key' => '_currency',
          'name' => '_currency',
          'label' => __( 'Currency', 'visita' ),
          'default_value' => $defaults['_currency'],
          'type' => 'text',
        ),
        array(
          'key' => '_business_type',
          'name' => '_business_type',
          'type' => 'select',
          'default_value' => 'Event',
          'label' => __( 'Business Type', 'visita' ),
          'choices' => array(
            'NightClub' => __( 'Night Club', 'visita' ),
            'ComedyClub' => __( 'Comedy Club', 'visita' ),
          ),
        ),
        array(
          'key' => 'tap_location',
          'label' => __( 'Location', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'key' => '_location',
          'name' => '_location',
          'label' => __( 'Name', 'visita' ),
          'type' => 'text',
        ),
        array(
          'key' => '_street',
          'name' => '_street',
          'label' => __( 'Addresss', 'visita' ),
          'type' => 'text',
        ),
        array(
          'key' => '_city',
          'name' => '_city',
          'label' => __( 'City', 'visita'  ),
          'type' => 'text',
          'default_value' => $defaults['_city'],
          'formatting' => 'none',
        ),
        array(
          'key' => '_state',
          'name' => '_state',
          'label' => __( 'State', 'visita'  ),
          'type' => 'text',
          'default_value' => $defaults['_state'],
          'formatting' => 'none',
        ),
        array(
          'key' => '_zip',
          'name' => '_zip',
          'label' => __( 'Zip Code', 'visita'  ),
          'type' => 'number',
          'min' => '1',
          'max' => '99999'
        ),
        array(
          'key' => '_phone',
          'name' => '_phone',
          'label' => __( 'Phone', 'visita'  ),
          'type' => 'text',
          'formatting' => 'phone',
        ),
        array(
          'key' => 'tap_days',
          'label' => __( 'Business Hours', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'min'=> 1,
          'key' => '_hours',
          'name' => '_hours',
          'type' => 'repeater',
          'layout' => 'block',
          'sub_fields' => array(
            array(
              'key' => '_day',
              'name' => '_day',
              'type' => 'select',
              'label' => __( 'Dia', 'visita' ),
              'choices' => array(
                'all' => __( 'All', 'visita' ),
                'monday' => __( 'Monday', 'visita' ),
                'tuesday' => __( 'Tuesday', 'visita' ),
                'wednesday' => __( 'Wednesday', 'visita' ),
                'thursday' => __( 'Thursday', 'visita' ),
                'friday' => __( 'Friday', 'visita' ),
                'saturday' => __( 'Saturday', 'visita' ),
                'sunday' => __( 'Sunday', 'visita' ),
              )
            ),
            array(
              'key' => '_open',
              'name' => '_open',
              'type' => 'time_picker',
              'display_format' => 'g:i A',
              'label' => __( 'Opens', 'visita' ),
              'default_value' => $defaults['_hours'][0]['_open'],
            ),
            array(
              'key' => '_close',
              'name' => '_close',
              'type' => 'time_picker',
              'display_format' => 'g:i A',
              'label' => __( 'Closes', 'visita' ),
              'default_value' => $defaults['_hours'][0]['_close'],
            ),
            array(
              'key' => '_24h',
              'name' => '_24h',
              'type' => 'true_false',
              'label' => __( '24 Hours', 'visita' ),
              'default_value' => $defaults['_hours'][0]['_24h'],
            )
          ),
        ),
      )
    ) );

    //basics
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'init', array( $this, 'add_rewrite_rules' ), 200 );
    add_action( 'document_title_parts', array( $this, 'title_tax_parts' ), 250 );

    //fields
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 10, 2 );
    add_filter( 'acf/load_value/key=_hours', array( $this, 'load_repeater_values' ), 50, 3 );

    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'pre_get_posts', array( $this, 'sort_tax' ), 50 );
      add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
      add_action( 'wp', array( $this, 'after_posts_selection' ), 20 );
      add_action( 'wp_footer', array( $this, 'schemaorg_breadcrumbs' ), 0 );
      add_action( 'visita_site_metatags', array( $this, 'head_metatags' ), 20 );
      add_action( 'template_redirect', array( $this, 'redirect_404' ), 20, 100 );
      return;
    }

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );
  }

  /**
  *
  *
  * @return void
  * @since 3.0.0
  */
  function after_posts_selection( ) {

    if ( ! is_singular( $this->post_type ) ) {
      return;
    }

    add_filter( 'get_next_post_sort', array( $this, 'adjacent_post_next_sort' ), 20 );
    add_filter( 'get_next_post_where', array( $this, 'adjacent_post_next_where' ), 20 );

    add_filter( 'get_previous_post_sort', array( $this, 'adjacent_post_previous_sort' ), 20 );
    add_filter( 'get_previous_post_where', array( $this, 'adjacent_post_previous_where' ), 20 );
  }
}
