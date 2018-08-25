<!doctype html>
<!--[if IE 8 ]> <html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]>--> <html class="no-js" <?php language_attributes(); ?>> <!--[endif]-->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<?php
  if ( comments_open() || get_comments_number() ) :
    $user_comment_ID = get_user_post_comment_ID( );
    comment_form(array(
      'logged_in_as'      => '',
      'title_reply_to'    => __( '%s Review' ),
      'cancel_reply_link' => __( 'Cancel Review' ),
      'title_reply'       => sprintf( __( '%s review', 'noun' ) , get_the_title() ),
      'comment_field'     => sprintf( '<p class="comment-form-comment"><label for="comment">%s</label>
        <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">%s</textarea></p>',
        esc_html( _x( 'Comment', 'noun' ) ),
        esc_textarea( get_user_post_comment()->comment_content )
      ),
      'submit_button'     => sprintf('
        <input name="submit" type="submit" id="submit" class="submit button" value="%s" />
        <input name="submit" type="submit" id="submit" class="button secondary %s" value="%s" />',
        ( $user_comment_ID ) ?  esc_attr__( 'Update Review' ) : esc_attr__( 'Post Review' ),
        ( $user_comment_ID ) ?  '' : 'hide',
         esc_attr__( 'Delete' )
      ),
    ) );
  endif;
?>
</body>
</html>
