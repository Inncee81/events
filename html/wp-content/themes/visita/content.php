<?php
/**
 * XClusive -  main template for displaying content
 *
 * @file content.php
 * @package xclusive
 * @author Hafid Trujillo
 * @copyright 2010-2015 Xpark Media
 * @license license.txt
 * @version release: 0.1.0
 * @filesource  wp-content/themes/xclusive/content.php
 * @since available since 0.1.0
 */
 ?>

 <article <?php visita_post_schema(); ?> <?php post_class(); ?>>

  <?php if( has_post_thumbnail() ) : ?>
    <figure itemprop="photo" class="hmedia">
      <a href="<?php esc_url( the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" class="image url enclosure">
       <?php the_post_thumbnail( 'post-thumbnail'); ?>
      </a>
      <meta class="photo" itemprop="image" content="<?php the_post_thumbnail_url()?>" />
      <figcaption itemprop="name" class="fn"><?php the_title_attribute(); ?></figcaption>
    </figure>
  <?php endif; // is_single() ?>

  <header class="entry-header<?php if( ! is_single() ) echo ' float' ?>">
    <h2 itemprop="name" class="entry-title">
  		<a itemprop="url" class="url" href="<?php the_permalink(); ?>" title="<?php echo
  		esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
  	</h2>

    <div itemprop="description" class="entry-meta summary">
      <?php visita_entry_meta(); ?>
  		<?php edit_post_link( __( 'Edit', 'visita' ), '<span class="edit-link">', '</span>' ); ?>
      <?php if (is_single()) visita_entry_tax('atracciones') ?>
    </div><!-- .entry-meta -->
  </header><!-- .entry-header -->

  <?php if ( is_single() ) : ?>
    <div itemprop="description" class="entry-content">
      <?php the_content( __( 'Continue <span class="meta-nav">&rarr;</span>', 'visita' ) ); ?>
    </div><!-- .entry-content -->
  <?php endif; // is_single() ?>

 </article><!-- .post-<?php the_ID(); ?> -->
