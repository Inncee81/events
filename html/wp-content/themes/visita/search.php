<?php
/**
* XClusive - template for displaying search results pages
*
* @file search.php
* @package xclusive
* @author Hafid Trujillo
* @copyright 2010-2015 Xpark Media
* @license license.txt
* @version release: 0.1.0
* @filesource  wp-content/themes/xclusive/search.php
* @since available since 0.1.0
*/
?>
<?php get_header(); ?>


<div class="row">
  <div class="small-12 columns">

    <header class="entry-header small-text-center">
      <h1 class="page-title">
        <?php printf( __( 'Search Results for: %s', 'visita' ), get_search_query() ); ?>
      </h1>
		</header><!-- .archive-header -->

    <?php visita_content_nav( 'nav-above' ); ?>
    <?php visita_before_loop(); ?>

    <div class="entry-content" itemprop="text">
      <?php if ( have_posts() ) {
        while ( have_posts() ) : the_post();
          get_template_part( 'content', get_post_type() );
        endwhile;
      } else {
        printf( '<div class="row event callout"><h3 class="columns text-center"><strong>%s</strong></h3></div>', __( 'Sorry, but nothing matched your search terms. Please try a different search.', 'visita' ));
      } ?>
    </div>

    <?php visita_after_loop(); ?>
    <?php visita_content_nav( 'nav-below' ); ?>

  </div>
</div>


<?php get_sidebar(); ?>
<?php get_footer(); ?>
