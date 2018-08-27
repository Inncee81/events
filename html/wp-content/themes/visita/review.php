

<div class="entry-reviews">
  <?php if ( get_post_rating() ) : ?>
    <a href="<?php review_link()?>" title="<?php post_review_count() ?>" data-reviews
      itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
      <span itemprop="ratingValue"><?php post_rating_formatted() ?></span>
      <span itemprop="reviewCount" class="review-count"><?php post_review_count() ?></span>
      <?php post_rating_stars() ?>
    </a>
  <?php endif; ?>

  <?php if ( comments_open() || get_comments_number() ) : ?>
    <a href="<?php review_add_link()?>" class="review-action" aria-label="<?php _e('Open add review modal', 'visita')?>" data-reviews>
      <?php get_user_post_comment_ID( ) ? esc_html_e( 'Edit Review', 'visita' ) : esc_html_e( '+ Add Review', 'visita' ) ?>
    </a>
  <?php endif; ?>
</div>

<div class="reveal" id="reveal" aria-hidden="true" data-reveal>
  <button class="close-button" type="button" aria-label="<?php _e('Close review modal', 'visita')?>" data-close >
    <span>&times;</span>
  </button>
  <div class="reveal-content"></div>
</div>
