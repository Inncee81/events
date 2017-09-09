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

class VisitaAttractions extends VisitaBase {

  /**
  *
  */
  protected $post_type = 'attraction';

  /**
  *
  */
  protected $taxonomy = 'attractions';

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( ) {

    $this->position = 30;
    $this->slug = __( 'attraction', 'visita' );
    $this->name = __( 'Attractions', 'visita' );
    $this->singular = __( 'Attraction', 'visita' );
    $this->taxonomy_slug = __( 'attractions', 'visita' );
    $this->taxonomy_label = __( 'Attractions', 'visita' );

    $this->club_data = array_replace_recursive( $this->default_data, array(
      'post_type'         => $this->post_type,
      'meta_input'        => array(
        '_days'           => array( array(
          '_to'           => '',
          '_from'         => '',
          '_day_link'     => '',
          '_availability' => 'InStock',
        ) ),
        '_business_type'  => 'NightClub',
        '_keywords'       => __( 'attraction, activity', 'visita' ),
      )
    ) );

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
          'type' => 'number',
          'label' => __( 'Price', 'visita' ),
          'min' => 1,
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
            'Park' => __( 'Park', 'visita' ),
            'Museum' => __( 'Museum', 'visita' ),
            'Aquarium' => __( 'Aquarium', 'visita' ),
            'EventVenue' => __( 'Event Venue', 'visita' ),
            'ArtGallery' => __( 'Art Gallery', 'visita' ),
            'MovieTheater' => __( 'Movie Theater', 'visita' ),
            'AmusementPark' => __( 'Amusement Park', 'visita' ),
            'ShoppingCenter' => __( 'Shopping Center', 'visita' ),
            'EntertainmentBusiness' => __( 'Entertainment', 'visita' ),
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
        array(
          'key' => 'tap_times',
          'label' => __( 'Business Hours', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'min'=> 1,
          'key' => '_days',
          'name' => '_days',
          'type' => 'repeater',
          'layout' => 'block',
          'sub_fields' => array(
            array(
              'key' => '_from',
              'name' => '_from',
              'type' => 'select',
              'label' => __( 'From', 'visita' ),
              'choices' => array(
                __( 'All', 'visita' ),
                __( 'Monday', 'visita' ),
                __( 'Tuesday', 'visita' ),
                __( 'Wednesday', 'visita' ),
                __( 'Thursday', 'visita' ),
                __( 'Friday', 'visita' ),
                __( 'Saturday', 'visita' ),
                __( 'Sunday', 'visita' ),
              )
            ),
            array(
              'key' => '_to',
              'name' => '_to',
              'type' => 'select',
              'label' => __( 'To', 'visita' ),
              'choices' => array(
                __( 'All', 'visita' ),
                __( 'Monday', 'visita' ),
                __( 'Tuesday', 'visita' ),
                __( 'Wednesday', 'visita' ),
                __( 'Thursday', 'visita' ),
                __( 'Friday', 'visita' ),
                __( 'Saturday', 'visita' ),
                __( 'Sunday', 'visita' ),
              )
            ),
            array(
              'key' => '_time',
              'name' => '_time',
              'type' => 'time_picker',
              'display_format' => 'g:i A',
              'label' => __( 'Time', 'visita' ),
              'default_value' => $defaults['_times'][0]['_time'],
            ),
            array(
              'key' => '_day_link',
              'name' => '_day_link',
              'type' => 'text',
              'label' => __( 'Link', 'visita' ),
            ),
          ),
        ),
      )
    ) );

    //fields
    add_action( 'init', array( $this, 'register_event_post_type' ) );
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 10, 2 );
    add_filter( 'acf/load_value/key=_days', array( $this, 'load_repeater_values' ), 50, 3 );

    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'template_redirect', array( $this, 'redirect_404' ), 20, 100 );
      return;
    }

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );
  }
}

// UPDATE `visit_posts` set post_type = 'attraction' WHERE post_type = 'atraccion';
// UPDATE `visit_term_taxonomy`  SET taxonomy = 'attractions' WHERE taxonomy = 'atracciones';
// UPDATE `visit_postmeta` SET meta_value = 'attractions' WHERE meta_key = '_menu_item_object' AND meta_value = 'atracciones';
