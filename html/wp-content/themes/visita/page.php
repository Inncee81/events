<?php
/**
 * Visita - template for displaying all pages
 *
 * @file page.php
 * @package visita
 * @author Hafid Trujillo
 * @copyright 2010-2015 Xpark Media
 * @license license.txt
 * @version release: 0.1.0
 * @filesource  wp-content/themes/visita/page.php
 * @since available since 0.1.0
 */
 ?>
<?php get_header(); ?>

<div class="row">
  <div class="small-12 columns">

    <?php visita_before_loop(); ?>

		<?php while (have_posts()) : the_post(); ?>
      <?php get_template_part( 'content', 'page' ); ?>
		<?php endwhile; ?>

    <?php visita_after_loop(); ?>

  </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
