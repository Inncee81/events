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

 <article <?php visita_post_schema(); ?> <?php post_class(); ?>>
  <?php //edit_post_link( __( 'Edit', 'visita' ), '<span class="edit-link">', '</span>' ); ?>

  <?php if ( has_post_thumbnail() ) : ?>
    <figure class="hmedia">
      <a href="<?php esc_url( the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" class="image url enclosure">
       <?php the_post_thumbnail( 'post-thumbnail'); ?>
      </a>
      <meta class="image" itemprop="image" content="<?php the_post_thumbnail_url()?>" />
      <figcaption class="fn"><?php the_title_attribute(); ?></figcaption>
    </figure>
  <?php endif; // has_post_thumbnail() ?>

  <header class="entry-header<?php if ( ! is_single() ) echo ' float' ?>">
    <?php
      printf(
        '<%2$s itemprop="name" class="entry-title">
          <a itemprop="url" class="url" title="%3$s" rel="bookmark">%1$s</a>
        </%2$s>',
        esc_html( get_the_title() ),
        esc_attr( is_single( ) ? 'h1' : 'h2' ),
        esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) )
      )
    ?>
    <span class="author vcard hidden"><em class="fn">Visita.Vegas</em></span>
    <div class="entry-meta">
      <?php visita_get_start_time(); visita_entry_meta(); ?>
      <?php if (is_single()) visita_entry_tax( 'events' ) ?>
    </div><!-- .entry-meta -->
  </header><!-- .entry-header -->

  <?php if ( is_single() ) : ?>
    <div itemprop="description" class="entry-content">
      <?php the_content( __( 'Continue <span class="meta-nav">&rarr;</span>', 'visita' ) ); ?>
    </div><!-- .entry-content -->
  <?php endif; // is_single() ?>

  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="offers">
    <?php visita_entry_dates(); ?>
  </div>

 </article><!-- .post-<?php the_ID(); ?> -->
