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

<article id="article-<?php the_ID()?>" itemscope itemtype="https://schema.org/Article" <?php post_class(); ?>>

  <?php if ( has_post_thumbnail() ) : ?>
    <figure id="entry-media-<?php the_ID()?>" class="hmedia">
      <?php
        printf(
          '<a href="%1$s" title="%3$s" class="image url enclosure" rel="bookmark" tabindex="-1">%2$s</a>',
          get_permalink(),
          get_the_post_thumbnail( null, 'post-thumbnail', array( 'class' => 'photo' ) ),
          esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) )
        )
      ?>
      <meta class="image" itemprop="image" content="<?php the_post_thumbnail_url()?>" />
      <figcaption class="fn"><?php the_title_attribute(); ?></figcaption>
    </figure>
  <?php else: // has_post_thumbnail() ?>
    <meta class="image" itemprop="image" content="<?php stylesheet_directory_uri('/icons/icon-192.png')?>" />
  <?php endif; // has_post_thumbnail() ?>

  <header id="entry-header-<?php the_ID()?>" class="entry-header<?php if ( ! is_single() ) echo ' float' ?>">
    <?php edit_post_link( __( 'Edit', 'visita' ), '<span class="edit-link">', '</span>' ); ?>
    <?php
      printf(
        '<%3$s itemprop="headline" class="entry-title">
          <a href="%1$s" itemprop="url mainEntityOfPage" class="url" title="%4$s" rel="bookmark">%2$s</a>
        </%3$s>',
        get_permalink(),
        esc_html( get_the_title() ),
        esc_attr( is_single() ? 'h1' : 'h3' ),
        esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) )
      )
    ?>
    <div class="entry-meta hidden">
      <span itemprop="author" class="author vcard"><em class="fn"><?php bloginfo('name') ?></em></span>
      <span itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="<?php bloginfo('name') ?>" />
        <meta itemprop="sameAs" content="https://twitter.com/visita_vegas" />
        <meta itemprop="sameAs" content="https://www.facebook.com/VisitaVegas/" />
        <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
          <meta itemprop="url" content="<?php stylesheet_directory_uri('/icons/icon-192.png')?>"  />
        </span>
      </span>
      <?php visita_get_post_date(); ?>
    </div><!-- .entry-meta -->
    <?php if ( is_single() ) visita_share_button(); ?>
  </header><!-- .entry-header -->

  <?php if ( is_single() ) : ?>
    <div class="entry-content">
      <?php the_content( __( 'Continue <span class="meta-nav">&rarr;</span>', 'visita' ) ); ?>
      <div class="entry-meta">
        <?php if ( is_single() ) visita_entry_tax( $post ) ?>
      </div><!-- .entry-meta -->
    </div><!-- .entry-content -->
  <?php endif; // is_single() ?>

</article><!-- .post-<?php the_ID(); ?> -->
