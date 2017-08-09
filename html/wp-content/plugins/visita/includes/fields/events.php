<?php
/**
* Visita - Event Fields Class
*
* @file events.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 3.0.0
* @filesource  wp-content/plugins/visita/includes/fields/events.php
* @since available since 3.00
*/

class VisitaEventFields {

  /**
  *
  */
  protected $version;

  /**
  *
  */
  protected $post_type = 'evento';

  /**
  *
  */
  protected $event = array(
    'ID'                  => false,
    'image'               => false,
    'post_title'          => '',
    'post_author'         => 1,
    'post_status'         => 'draft',
    'post_content'        => '',
    'post_type'           => 'evento',
    'tax_input'           => array(),
    'meta_input'          => array(
      '_keywords'         => 'espanol, musica',
      '_description'      => '',
      '_starts'           => '', //start date
      '_ends'             => '', //start time
      '_location'         => '',
      '_street'           => '',
      '_city'             => 'Las Vegas',
      '_state'            => 'NV',
      '_zip'              => '',
      '_phone'            => '',
      '_link'             => '',
      '_price'            => '',
      '_price_max'        => '',
      '_permanent'        => false,
      '_disable_source'   => false,
      '_event_type'       => 'Event',
      '_event_id'         => false,
      '_duration'         => 120, // minutes
      '_currency'         => 'USD',
      '_times'            => array( array(
        '_date'           => '',
        '_time'           => '',
        '_date_link'      => '',
        '_availability'   => 'InStock',
      ) ),
      '_performers'       => array( array(
        '_name'           => '',
        '_type'           => '',
      ) ),
    )
  );

  /**
   *
   */
  function add_admin_pages() {
    add_management_page(
      __( 'Event Import', 'visita' ),
      __( 'Event Import', 'visita' ),
      'manage_options',
      'event-import',
      array( $this, 'event_import_page' )
    );
  }

  /**
   *
   */
  function event_import_page(){
    include_once( VISITA_INC . "/import.php");
  }

  /**
  *
  * @return bool
  * @since 3.0.0
  */
  function register_acf_fields( ) {

    $fields = $this->event['meta_input'];

    register_field_group( array(
      'key' => 'event_details',
      'title' => __( 'Event Details' ),
      'menu_order' => 1,
      'options' => array(
        'layout' => 'default',
        'position' => 'normal',
        'hide_on_screen' => array(),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
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
          'key' => '_permanent',
          'name' => '_permanent',
          'type' => 'true_false',
          'message' => __( 'Permanent', 'visita' ),
        ),
        array(
          'key' => '_disable_source',
          'name' => '_disable_source',
          'type' => 'true_false',
          'message' => __( 'Disable UTM', 'visita' ),
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
          'default_value' => $fields['_currency'],
          'type' => 'text',
        ),
        array(
          'key' => '_duration',
          'name' => '_duration',
          'type' => 'number',
          'label' => __( 'Duration (Minutes)', 'visita' ),
          'default_value' => $fields['_duration'],
          'min' => 1,
        ),
        array(
          'key' => '_event_type',
          'name' => '_event_type',
          'type' => 'select',
          'default_value' => 'Event',
          'label' => __( 'Event Type', 'visita' ),
          'choices' => array(
            'Event' => __( 'Event', 'visita' ),
            'ComedyEvent' => __( 'Comedy', 'visita' ),
            'DanceEvent' => __( 'Dance', 'visita' ),
            'FoodEvent' => __( 'Food', 'visita' ),
            'Festival' => __( 'Festival', 'visita' ),
            'ExhibitionEvent' => __( 'Exhibition', 'visita' ),
            'MusicEvent' => __( 'Music', 'visita' ),
            'SportsEvent' => __( 'Sports', 'visita' ),
            'TheaterEvent' => __( 'Theater', 'visita' ),
            'ScreeningEvent' => __( 'Screening', 'visita' ),
            'EducationEvent' => __( 'Education', 'visita' ),
          ),
        ),
        array(
          'key' => '_keywords',
          'name' => '_keywords',
          'type' => 'text',
          'label' => __( 'Keywords', 'visita' ),
          'default_value' => $fields['_keywords'],
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
          'default_value' => $fields['_city'],
          'formatting' => 'none',
        ),
        array(
          'key' => '_state',
          'name' => '_state',
          'label' => __( 'State', 'visita'  ),
          'type' => 'text',
          'default_value' => $fields['_state'],
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
          'label' => __( 'Show Times', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'min'=> 1,
          'key' => '_times',
          'type' => 'repeater',
          'layout' => 'block',
          'sub_fields' => array(
            array(
              'key' => '_date',
              'name' => '_date',
              'type' => 'date_picker',
              'return_format' => 'm/d/y',
              'display_format' => 'm/d/y',
              'label' => __( 'Date', 'visita' ),
            ),
            array(
              'key' => '_time',
              'name' => '_time',
              'type' => 'time_picker',
              'display_format' => 'g:i A',
              'label' => __( 'Time', 'visita' ),
              'default_value' => $fields['_times'][0]['_time'],
            ),
            array(
              'key' => '_availability',
              'name' => '_availability',
              'type' => 'select',
              'label' => __( 'Availability', 'visita' ),
              'default_value' => $fields['_times'][0]['_availability'],
              'choices' => array(
                'PreSale' => __( 'Pre Sale', 'visita' ),
                'SoldOut' => __( 'Pre Sale', 'visita' ),
                'InStock'	=>  __( 'In Stock', 'visita' ),
                'OutOfStock' => __( 'Out Of Stock', 'visita' ),
                'Online Only' => __( 'Online Only', 'visita' ),
                'LimitedAvailability' => __( 'Limited Availability', 'visita' ),
              ),
            ),
            array(
              'key' => '_date_link',
              'name' => '_date_link',
              'type' => 'text',
              'label' => __( 'Link', 'visita' ),
            ),
          ),
        ),
        array(
          'key' => 'tap_performers',
          'label' => __( 'Performers', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'min'=> 1,
          'key' => '_performers',
          'type' => 'repeater',
          'sub_fields' => array(
            array(
              'key' => '_name',
              'name' => '_name',
              'type' => 'text',
              'label' => __( 'Performer', 'visita' ),
            ),
            array(
              'key' => '_type',
              'name' => '_type',
              'type' => 'select',
              'label' => __( 'Performe Type', 'visita' ),
              'default_value' => $fields['_performers'][0]['_type'],
              'choices' => array(
                'Person' => __( 'Person', 'visita' ),
                'SportsTeam'=> __( 'Sports Team', 'visita' ),
                'MusicGroup' => __( 'Music Group', 'visita' ),
                'DanceGroup' => __( 'Dance Group', 'visita' ),
                'TheaterGroup' => __( 'Theater Group', 'visita' ),
              )
            ),
          ),
        ),
      )
    ) );
  }

  /**
  * Save ACF fields
  *
  * @param $post_id int
  * @return void
  * @since 3.0.0
  */
  function save_acf_data( $post_id, $values ) {

    // only affect events
    if ( get_current_screen()->post_type !== $this->post_type ) {
      return;
    }

    //save each field
    foreach ( $values as $meta_key => $meta_value ) {

      update_post_meta( $post_id, $meta_key, $meta_value );

      if ( $meta_key == '_times' ) {

        $starts = $ends = false;

        foreach ( $meta_value as $time ) {
          $time = strtotime( "{$time['_date']} {$time['_time']}");

          $starts = ( $time < $starts || ! $starts ) ? $time : $starts;
          $ends = ( $time >= $ends || ! $ends ) ? ( $time + ( $values['_duration'] * 60 ) ) : $ends;
        }

        update_post_meta( $post_id, '_ends', $ends );
        update_post_meta( $post_id, '_starts', $starts );
      }
    }
  }

  /**
  * Return _times field values
  *
  * @param $post_id mix
  * @param $post_id int
  * @param $field array acf filed array
  * @return array / mix
  * @since 3.0.0
  */
  function load_repeater_values( $value, $post_id, $field ) {
    if ( empty( $field['sub_fields'] ) ) return $value;

    $rows = array();
    $data = get_post_meta( $post_id, $field['key'], true );

    foreach ( (array) $data as $index => $values ) {
      foreach ( $field['sub_fields'] as $sub_field ) {
        $key = $sub_field['key'];
        if ( isset( $values[ $key ] ) ) {
          $rows[$index][ $key ] = $values[ $key ];
        }
      }
    }

    return array_values( $rows );
  }
}
