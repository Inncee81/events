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

class VisitaShows extends VisitaBase {

  /**
  *
  */
  protected $post_type = 'show';

  /**
  *
  */
  protected $taxonomy = 'shows';

  /**
  *
  */
  protected $supports = array( 'reviews' );

  /**
   * Constructor
   *
   * @return void
   * @since 1.0.0
   */
  function __construct( ) {

    $this->position = 27;
    $this->slug = __( 'show', 'visita' );
    $this->name = __( 'Shows', 'visita' );
    $this->singular = __( 'Show', 'visita' );
    $this->taxonomy_slug = __( 'shows', 'visita' );
    $this->taxonomy_label = __( 'Shows', 'visita' );
    $this->description = __( 'Shows in Las Vegas, Cirque Du Soleil best shows, Broadway, musicals and many more.' , 'visita');

    $this->show_data = array_replace_recursive( $this->default_data, array(
      'post_type'         => $this->post_type,
      'meta_input'        => array(
        '_days'           => array( array(
          '_to'           => '',
          '_from'         => '',
          '_day_link'     => '',
          '_availability' => 'InStock',
        ) ),
        '_event_type'     => 'TheaterEvent',
      )
    ) );

    $this->tabs = array(
      'starts' => __( 'Date', 'visita' ),
      'name' => __( 'Name', 'visita' ),
      'price' => __( 'Price', 'visita' ),
    );

    $defaults = $this->show_data['meta_input'];
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
          'key' => '_duration',
          'name' => '_duration',
          'type' => 'number',
          'label' => __( 'Duration (Minutes)', 'visita' ),
          'default_value' => $defaults['_duration'],
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
          'min'=> 0,
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
              'key' => '_to',
              'name' => '_to',
              'type' => 'select',
              'label' => __( 'To', 'visita' ),
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
        array(
          'key' => 'tap_performers',
          'label' => __( 'Performers', 'visita' ),
          'type' => 'tab',
        ),
        array(
          'min'=> 1,
          'key' => '_performers',
          'name' => '_performers',
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
              'default_value' => $defaults['_performers'][0]['_type'],
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

    //basics
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'init', array( $this, 'add_rewrite_rules' ), 200 );
    add_action( 'document_title_parts', array( $this, 'title_parts' ), 200 );
    add_action( 'document_title_parts', array( $this, 'title_tax_parts' ), 250 );

    //fields
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 5, 2 );
    add_action( 'acf/save_post', array( $this, 'save_unix_time_data' ), 10, 2 );
    add_filter( 'acf/load_value/key=_days', array( $this, 'load_repeater_values' ), 50, 3 );
    add_filter( 'acf/load_value/key=_performers', array( $this, 'load_repeater_values' ), 50, 4 );

    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'pre_get_posts', array( $this, 'sort_tax' ), 50 );
      add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
      add_action( 'wp', array( $this, 'after_posts_selection' ), 20 );
      add_action( 'visita_before_loop', array( $this, 'sort_tabs' ), 50 );
      add_action( 'wp_footer', array( $this, 'schemaorg_breadcrumbs' ), 0 );
      add_action( 'visita_site_metatags', array( $this, 'head_metatags' ), 20 );
      add_action( 'template_redirect', array( $this, 'redirect_404' ), 20, 100 );
      return;
    }

    add_action( 'visita_import_page', array( $this, 'import_form' ), 3 );
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

  /**
  *
  * @return string
  * @since 3.0.0
  */
  function sort_tax( $query ) {
    if ( ! $query->is_main_query() || is_search() ) {
      return;
    }

    if ( is_post_type_archive( $this->post_type ) || is_tax( $this->taxonomy ) ) {

      $orderby = get_query_var( 'orderby' );
      $order = ( $order = get_query_var( 'order' ) ) ? $order : 'ASC';

      $query->set( 'order',  $order );

      if ( ! $orderby ) {
        $query->set( 'orderby',  'name' );
      }

      if ( $orderby == 'starts' ) {
        $query->set( 'orderby', array( '_starts' => $order ) );
        $query->set( 'meta_query', array(
          '_starts'   => array(
            'key'     => '_starts',
            'compare' => 'EXISTS',
          )
        ) );
      }

      if ( $orderby == 'price' ) {
        $key = ( $order == 'ASC' ) ? '_price' : '_price_max';
        $query->set( 'orderby', array( $key => $order ) );
        $query->set( 'meta_query', array(
          $key => array(
            'key'     => $key,
            'compare' => 'EXISTS',
            'type'    => 'DECIMAL',
          )
        ) );
      }
    }
  }

  /**
  *
  *
  * @return void
  * @since 3.0.0
  */
  function import_form( ) {
    printf(
      '<form class="inside" method="post">
        <p class="mf_field_wrapper">
          <textarea class="widefat" name="shows-json" rows="5" placeholder="%1$s"></textarea>
        </p>
        <p><input type="submit" class="button-primary" value="%2$s" /></p>
        <input type="hidden" name="page" value="visita-import" />  %3$s
      </form>',
      esc_attr__( 'json...', 'visita' ),
      esc_attr__( 'Shows', 'visita' ),
      wp_nonce_field( 'visita-import', 'visita-import', false )
    );
  }

  /**
  * Save ACF fields
  *
  * @return void
  * @since 3.0.0
  */
  function save_import_data( ) {
    if ( isset( $_REQUEST['shows-json'] ) ) {
      $json = json_decode( stripslashes( $_REQUEST['shows-json'] ) );
    }
  }

  /**
  * Save ACF fields
  *
  * @param $post_id int
  * @param $values array
  * @return void
  * @since 3.0.0
  */
  function save_unix_time_data( $post_id, $values ) {

    // only affect events
    if ( get_current_screen()->post_type !== $this->post_type ) {
      return;
    }

    $thisweek = strtotime( 'last week 0:00' );

    //save each field
    foreach ( $values as $meta_key => $meta_value ) {

      if ( $meta_key == '_days' ) {

        $starts = $ends = false;

        foreach ( (array) $meta_value as $day ) {
          if ( isset( $day['_to'] ) && isset( $day['_from'] ) ) {
            $_to = strtotime( ( $day['_to'] == 'all' ? 'monday' : $day['_to'] ) . $day['_time'] . ' this week' ) - $thisweek;
            $_from = strtotime( ( $day['_from'] == 'all' ? 'monday' : $day['_from'] ) . $day['_time'] . ' this week' ) - $thisweek;

            $starts = ( $_from < $starts || ! $starts ) ? $_from : $starts;
            $ends = ( $_to >= $_to || ! $_to ) ? ( $_to + ( $values['_duration'] * 60 ) ) : $ends;
          }
        }

        update_post_meta( $post_id, '_ends', $ends );
        update_post_meta( $post_id, '_starts', $starts );
      }
    }
  }
}
