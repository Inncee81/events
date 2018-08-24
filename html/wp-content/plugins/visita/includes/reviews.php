<?php

/**
*
*/
function review_link() {
  echo Visita_Reviews::get_review_link();
}

/**
*
*/
function review_add_link() {
  echo Visita_Reviews::get_review_add_link();
}

/**
*
*/
function get_user_post_comment() {
  return Visita_Reviews::get_user_post_comment();
}

/**
*
*/
class Visita_Reviews {

  /**
   * Constructor
   *
   * @return void
   * @since 0.5.0
   */
  function __construct( ) {

    add_action( 'comment_form_top',  array( $this, 'comment_form_top' ), 500 );
    add_action( 'wp_insert_comment', array( $this, 'save_review_rating' ), 20, 2 );

    add_filter( 'single_template_hierarchy', array( $this, 'post_templates' ) );
    add_filter( 'registered_post_type', array( $this, 'registered_post_type' ), 300, 2 );

    if ( ! is_admin() ) {
      add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
      return;
    }
  }

  /**
  *
  */
  function save_review_rating( $comment_ID ) {
    update_comment_meta( $comment_ID, '_rating', (int) $_POST['rating'] );
  }

  /**
  *
  */
  function comment_form_top() {
    global $user_identity, $current_user;
    echo '
    <p class="logged-in-as">' .
      sprintf( __( '<a href="%1$s">%2$s</a>' ),
        admin_url( 'profile.php' ),
        get_avatar( $current_user->user_email, 38 ) . $user_identity
      ) .
    '</p>
    <div class="starts">
      <input type="radio" name="rating" id="rating-5" value="5" /><label for="rating-5">'. __('rating 5') . '</label>
      <input type="radio" name="rating" id="rating-4" value="4" /><label for="rating-4">'. __('rating 4') . '</label>
      <input type="radio" name="rating" id="rating-3" value="3" /><label for="rating-3">'. __('rating 3') . '</label>
      <input type="radio" name="rating" id="rating-2" value="2" /><label for="rating-2">'. __('rating 2') . '</label>
      <input type="radio" name="rating" id="rating-1" value="1" /><label for="rating-1">'. __('rating 1') . '</label>
    </div>';
  }

  /**
  *
  */
  function registered_post_type( $post_type, $post_type_object ) {
    if ( post_type_supports( $post_type, 'reviews' ) ) {
      $this->add_reviews_endpoint( $post_type, $post_type_object->rewrite['slug'] );
    }
  }

  /**
  *
  */
  function add_query_vars( $vars ) {
    array_push( $vars, 'reviews' );
    return $vars;
  }

  /**
  * add reviews end point
  *
  * @since 2.0.3
  */
  function add_reviews_endpoint( $post_type, $slug ) {
    add_rewrite_rule(
      "$slug/([^/]+)/reviews/add/?$",
      "index.php?post_type={$post_type}&name=\$matches[1]&reviews=add",
      'top'
    );
    add_rewrite_rule(
      "$slug/([^/]+)/reviews/?$",
      "index.php?post_type={$post_type}&name=\$matches[1]&reviews=1",
      'top'
    );
  }

  /**
  * provide other default templates for reviews
  *
  * @param $templates array
  * @since 2.0.3
  */
  function post_templates( $templates ) {
    if ( ! post_type_supports( get_post_type(), 'reviews' ) ) {
      return $templates;
    }

    if ( get_query_var('reviews') === 'add' ) {
      array_unshift(
        $templates,
        'reviews-form.php'
      );
    }

    return $templates;
  }

  /**
  *
  */
  function comment_form_delete( $submit ) {
    return sprintf(
      '%s <input name="submit" type="submit" id="submit" class="submit button" value="Delete Review">',
      $submit
    );
  }

  /**
  *
  */
  static function get_review_link() {
    return trim( get_permalink() , '/' ) . '/reviews/';
  }

  /**
  *
  */
  static function get_review_add_link() {
    return trim( get_permalink() , '/' ) . '/reviews/add/';
  }

  /**
  *
  */
  static function get_user_post_comment() {
    global $user_ID;
    if ( $posts = get_comments( array( 'post_id' => get_the_ID(), 'author__in' => $user_ID ) ) ) {
      return empty( $posts[0] ) ? '' : $posts[0]->comment_content;
    }
    return;
  }
}
