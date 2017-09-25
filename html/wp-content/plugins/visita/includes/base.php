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

class VisitaBase {

  /**
  *
  */
  protected $name;

  /**
  *
  */
  protected $slug;

  /**
  *
  */
  protected $position;

  /**
  *
  */
  protected $singular;

  /**
  *
  */
  protected $taxonomy;

  /**
  *
  */
  protected $post_type;

  /**
  *
  */
  protected $taxonomy_slug;

  /**
  *
  */
  protected $taxonomy_label;

  /**
  *
  */
  protected $default_fields;

  /**
  *
  */
  protected $is_home = false;

  /**
  *
  */
  protected $tabs = array();

  /**
  *
  */
  protected $fields = array(
    'key' => '',
    'title' => '',
    'menu_order' => 1,
    'fields' => array(),
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
          'value' => '',
        ),
      ),
    ),
  );

  /**
  *
  */
  protected $default_data = array(
    'ID'                  => false,
    'image'               => false,
    'post_title'          => '',
    'post_author'         => 1,
    'post_status'         => 'draft',
    'post_content'        => '',
    'post_type'           => '',
    'tax_input'           => array(),
    'meta_input'          => array(
      '_keywords'         => '',
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
      '_event_id'         => false,
      '_event_type'       => '',
      '_duration'         => 120, // minutes
      '_currency'         => 'USD',
      '_times'            => array( array(
        '_date'           => '',
        '_time'           => '',
        '_date_link'      => '',
        '_availability'   => 'InStock',
      )),
      '_performers'       => array( array(
        '_name'           => '',
        '_type'           => '',
      ) ),
    )
  );

  /**
  *
  */
  function redirect_404( ) {
    if ( is_404() && isset( $_SERVER['REQUEST_URI'] ) && ! empty( $this->slug ) ) {
      if ( preg_match('/\/(' . esc_attr( $this->slug ) . ')/i', $_SERVER['REQUEST_URI'] ) ) {
        wp_safe_redirect( "/{$this->taxonomy_slug}", 302 ); die();
      }
    }
  }

  /**
  *
  */
  function wp_title( $title, $sep ) {
    return $title;
  }

  /**
  * Format event description for SEO
  *
  * @return string
  * @since 3.0.0
  */
  function get_description( $title, $date, $time, $city = 'Las Vegas' ) {
    return ucwords( strtolower( $title ) )
    . " en $city, "
    . sprintf(
       date_i18n( 'l j %\s F Y %\s g:i a.', strtotime( "$date $time" ) ),
       __( 'from', 'visita' ),
       __( 'to', 'visita' )
    );
  }

  /**
   * Add admin styles and scripts
   *
   * @return void
   * @since 3.0.0
   */
  function admin_scripts( ) {
    if ( get_current_screen()->post_type === $this->post_type ) {
      wp_enqueue_style( 'visita-fields', plugins_url( 'css/fields.css', VISITA_FILE_NAME ), NULL, VISITA_VERSION );
      wp_enqueue_script( 'visita-admin', plugins_url( 'js/admin.js', VISITA_FILE_NAME ), array( 'jquery' ), VISITA_VERSION, true );
    }
  }

  /**
  *
  * @return void
  * @since 3.0.0
  */
  function register_post_type( ) {

    register_taxonomy( $this->taxonomy, $this->post_type, array(
      'show_in_rest'      => true,
      'show_admin_column' => true,
      'label'             => $this->taxonomy_label,
      'rewrite'           => array( 'slug' => trim( $this->taxonomy_slug ) ),
      'hierarchical'      => true,
    ) );

    register_post_type( $this->post_type, array(
      'labels'            => array(
          'name'          => $this->name,
          'singular_name' => $this->singular,
        ),
        'show_ui'         => true,
        'public'          => true,
        'hierarchical'    => false,
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'menu_position'   => $this->position,
        'has_archive'     => $this->taxonomy_slug,
        'taxonomies'      => array( $this->taxonomy ),
        'rewrite'         => array( 'slug' => $this->slug, 'with_front' => false ),
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
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_where( $direction ) {
    global $wpdb, $post;
    return $wpdb->prepare(
      " WHERE p.post_title $direction '%s' AND p.post_type = '%s' AND p.post_status = 'publish'",
      $post->post_title,
      get_post_type()
    );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_sort( $direction ) {
    return esc_sql( " ORDER BY p.post_title $direction LIMIT 1 " );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_next_sort( ) {
    return $this->adjacent_post_sort( "ASC" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_next_where( ) {
    return $this->adjacent_post_where( ">" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_previous_sort( ) {
    return $this->adjacent_post_sort( "DESC" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 3.0.0
  */
  function adjacent_post_previous_where( ) {
    return $this->adjacent_post_where( "<" );
  }

  /**
  *
  * @return string
  * @since 3.0.0
  */
  function get_name( ) {
    return $this->name;
  }

  /**
  * Add additional rewrites
  *
  * @return bool|unit
  * @since 3.0.0
  */
  function add_rewrite_rules( ) {
    global $wp_rewrite;

    add_rewrite_rule(
      "$this->taxonomy_slug/$wp_rewrite->pagination_base/([0-9]{1,})/?$",
      "index.php?post_type={$this->post_type}&paged=\$matches[1]",
      'top'
    );

    if ( defined( 'AMP_QUERY_VAR' ) ) {
      add_post_type_support( $this->post_type, AMP_QUERY_VAR );
    }
  }

  /**
  *
  * @return bool
  * @since 3.0.0
  */
  function register_acf_fields( ) {
    if ( $this->fields ) {
      register_field_group( $this->fields );
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
  function save_acf_data( $post_id, $values ) {
    if ( get_current_screen()->post_type !== $this->post_type ) {
      return;
    }

    //save each field
    foreach ( $values as $meta_key => $meta_value ) {
      update_post_meta( $post_id, $meta_key, $meta_value );
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
    if ( get_post_type( $post_id ) !== $this->post_type ) return $value;

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

  /**
  *
  *
  *
  * @return void
  * @since 0.5.0
  */
  function pre_get_posts( $query ) {
    if ( ! $query->is_main_query() ) {
      return;
    }

    if ( ( is_home() && $this->is_home ) ) {
      $query->is_post_type_archive = true;
      $query->set( 'post_type', $this->post_type );
    }

    if ( is_post_type_archive( $this->post_type )) {
      $query->set( 'posts_per_page', 14 );
    }
  }

  /**
  *
  * @return void
  */
  function sort_tabs() {
    if ( ! is_post_type_archive( $this->post_type ) && ! is_tax( $this->taxonomy ) ) {
      return;
    }

    global $wp; $tabs = '';
    $order_var = get_query_var( 'orderby' );

    foreach ( $this->tabs as $tab => $label ) {
      $tabs .= '<li class="tabs-title tab-' . esc_attr( $tab ) . '">
        <a href="%1$s?order=%2$s&orderby=' . esc_attr( $tab ) . '">' . esc_html( $label ) . '</a>
      </li>';
    }

    printf(
      '<ul class="tabs tabs-sort active-%3$s order-%2$s" data-tabs>
        <li class="tabs-title"><span>' . esc_html__( 'Sort:', 'visita' ) . '</span></li>' . $tabs . '
      </ul>',
      home_url( $wp->request ),
      esc_attr( strtolower( get_query_var( 'order') ) == 'asc' ? 'desc' : 'asc' ),
      esc_attr( trim( is_array( $order_var ) ? key( $order_var ) : $order_var, '_' ) )
    );
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
}
