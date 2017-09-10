<?php
/**
* Visita - Index Template
*
* @file index.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/index.php
* @since available since 0.1.0
*/
?>
<?php get_header(); ?>

  <?php if ( have_posts() ) : ?>

  <div class="row">
    <div class="small-12 columns">

      <header class="entry-header">
        <h1 class="entry-title text-center medium-text-left" itemprop="name">
          <?php the_archive_title( );  ?>
        </h1>
      </header><!-- .entry-header -->

      <?php visita_content_nav( 'nav-above' ); ?>

      <div class="entry-content" itemprop="text">
        <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'content', get_post_type() ); ?>
        <?php endwhile; ?>
			</div>

      <?php visita_content_nav( 'nav-below' ); ?>

    </div>
  </div>

  <?php else : get_template_part( 'content', 'none' ); endif; ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
