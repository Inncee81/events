
<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
  <a href="<?php review_link()?>" data-reviews>
    <span itemprop="ratingValue">5</span> /
    <span itemprop="reviewCount"><?php get_comments_number() ?></span>
  </a>
  <?php if ( comments_open() || get_comments_number() ) : ?>
    <a href="<?php review_add_link()?>" aria-label="<?php _e('Open add review modal')?>" data-reviews-add>
      <?php get_user_post_comment( ) ? esc_html_e( 'Edit Review' ) : esc_html_e( 'Add Review' ) ?>
    </a>
  <?php endif; ?>
</div>

<div class="reveal" id="reveal" aria-hidden="true" data-reveal>
  <button class="close-button" type="button" aria-label="<?php _e('Close review modal')?>" data-close >
    <span>&times;</span>
  </button>
  <div class="reveal-content"></div>
</div>
