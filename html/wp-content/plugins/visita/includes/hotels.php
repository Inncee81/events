<?php
/**
* Visita - Hotels Class
*
* @file hotels.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/hotels.php
* @since available since 0.1.0
*/

class VisitaHotels extends VisitaBase {

  /**
  *
  */
  protected $post_type = 'hotel';

  /**
  *
  */
  protected $taxonomy = 'hotels';

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( ) {

    $this->position = 29;
    $this->slug = __( 'hotel', 'visita' );
    $this->name = __( 'Hotels', 'visita' );
    $this->singular = __( 'Hotel', 'visita' );
    $this->taxonomy_slug = __( 'hotels', 'visita' );
    $this->taxonomy_label = __( 'Hotels', 'visita' );

    $this->hotel_data = array_replace_recursive( $this->default_data, array(
      'post_type'         => $this->post_type,
      'meta_input'        => array(
        '_business_type'  => 'Hotel',
        '_keywords'       => __( 'hotel, casino', 'visita' ),
      )
    ) );

    $this->tabs = array(
      'name' => __( 'Name', 'visita' ),
      'price' => __( 'Price', 'visita' ),
    );

    $defaults = $this->hotel_data['meta_input'];
    $this->fields = array_replace_recursive( $this->fields, array(
      'title' => __( 'Hotel Details', 'visita' ),
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
            'Hotel' => __( 'Hotel', 'visita' ),
            'Resort' => __( 'Resort', 'visita' ),
            'Casino' => __( 'Casino', 'visita' ),
          ),
        ),
        array(
          'key' => '_keywords',
          'name' => '_keywords',
          'type' => 'text',
          'label' => __( 'Keywords', 'visita' ),
          'default_value' => $defaults['_keywords'],
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
      )
    ) );

    //basics
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'init', array( $this, 'add_rewrite_rules' ), 200 );

    //fields
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 10, 2 );
    add_filter( 'acf/load_value/key=_days', array( $this, 'load_repeater_values' ), 50, 3 );

    //translation
    add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules_array' ) );

    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'pre_get_posts', array( $this, 'sort_tax' ), 50 );
      add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
      add_action( 'wp', array( $this, 'after_posts_selection' ), 20 );
      add_action( 'visita_before_loop', array( $this, 'sort_tabs'), 50 );
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
