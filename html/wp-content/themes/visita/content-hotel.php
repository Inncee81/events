<?php
/**
 * Visita -  main template for displaying content
 *
 * @file content.php
 * @package visita
 * @author Hafid Trujillo
 * @copyright 2010-2018 Xpark Media
 * @license license.txt
 * @version release: 0.1.0
 * @filesource  wp-content/themes/visita/content.php
 * @since available since 0.1.0
 */
 ?>

 <article itemscope itemtype="https://schema.org/Hotel" <?php post_class(); ?>>

   <?php get_template_part( 'parts/location' ); ?>

 </article><!-- .post-<?php the_ID(); ?> -->
