<?php if ( has_post_thumbnail() ) : ?>
  <figure class="hmedia">
    <a href="<?php esc_url( the_permalink() ); ?>" title="<?php the_title_attribute(); ?>" class="image url enclosure">
      <?php the_post_thumbnail( 'post-thumbnail', array( 'class' => 'photo' ) ); ?>
    </a>
    <meta class="image" itemprop="image" content="<?php the_post_thumbnail_url()?>" />
    <figcaption class="fn"><?php the_title_attribute(); ?></figcaption>
  </figure>
<?php endif; // has_post_thumbnail() ?>

<header class="entry-header<?php if ( ! is_single() ) echo ' float' ?>">
  <?php edit_post_link( __( 'Edit', 'visita' ), '<span class="edit-link">', '</span>' ); ?>
  <?php
    printf(
      '<%3$s itemprop="name" class="entry-title">
        <a href="%1$s" itemprop="url" class="url" title="%4$s" rel="bookmark">%2$s</a>
      </%3$s>',
      get_permalink(),
      esc_html( get_the_title() ),
      esc_attr( is_single( ) ? 'h1' : 'h3' ),
      esc_attr( sprintf( __( 'Link to %s', 'visita' ), the_title_attribute( 'echo=0' ) ) )
    )
  ?>
  <span class="author vcard hidden"><em class="fn">Visita.Vegas</em></span>
  <div class="entry-meta">
    <?php visita_price_range( true ); ?>
    <div class="location">
      <?php visita_entry_meta(); ?>
    </div>
    <?php if (is_single()) visita_entry_tax( 'events' ) ?>
  </div><!-- .entry-meta -->
  <?php if ( is_single() ) visita_share_botton(); ?>
</header><!-- .entry-header -->

<div class="dates">
  <?php visita_opening_hours(); ?>
</div>

<?php if ( is_single() ) : ?>
  <div itemprop="description" class="entry-content">
    <?php visita_get_description(); ?>
    <?php the_content( __( 'Continue <span class="meta-nav">&rarr;</span>', 'visita' ) ); ?>
  </div><!-- .entry-content -->
<?php endif; // is_single() ?>

<?php if (is_single()) visita_get_performers(); ?>