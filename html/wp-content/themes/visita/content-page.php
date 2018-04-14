<?php
/**
 * Visita -  template for displaying page content
 *
 * @file content-page.php
 * @package visita
 * @author Hafid Trujillo
 * @copyright 2010-2015 Xpark Media
 * @license license.txt
 * @version release: 0.1.0
 * @filesource  wp-content/themes/visita/content-page.php
 * @since available since 0.1.0
 */
 ?>

<article itemscope itemtype="https://schema.org/Article" <?php post_class(); ?>>

  <?php if ( has_post_thumbnail() ) : ?>
    <figure class="hmedia">
      <a href="<?php esc_url( the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" class="image url enclosure">
       <?php the_post_thumbnail( 'post-thumbnail', array( 'class' => 'photo' ) ); ?>
      </a>
      <meta class="image" itemprop="image" content="<?php the_post_thumbnail_url()?>" />
      <figcaption class="fn"><?php the_title_attribute(); ?></figcaption>
    </figure>
  <?php endif; // has_post_thumbnail() ?>

  <header class="entry-header<?php if ( ! is_page() ) echo ' float' ?>">
    <?php edit_post_link( __( 'Edit', 'visita' ), '<span class="edit-link">', '</span>' ); ?>
    <?php
      printf(
        '<%3$s itemprop="name" class="entry-title">
          <a href="%1$s" itemprop="url" class="url" title="%4$s" rel="bookmark">%2$s</a>
        </%3$s>',
        get_permalink(),
        esc_html( get_the_title() ),
        esc_attr( ( is_single() || is_page()) ? 'h1' : 'h3' ),
        esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) )
      )
    ?>
    <span itemprop="author" class="author vcard hidden"><em class="fn">Visita.Vegas</em></span>
  </header><!-- .entry-header -->

  <?php if ( is_page() ) visita_before_loop(); ?>

  <?php if ( is_page() ) : ?>
    <div itemprop="description" class="entry-content">
      <?php the_content( __( 'Continue <span class="meta-nav">&rarr;</span>', 'visita' ) ); ?>
    </div><!-- .entry-content -->
  <?php endif; // is_single() ?>
  
  <?php if ( is_page() ) visita_after_loop(); ?>

</article><!-- #post-<?php the_ID(); ?> -->
