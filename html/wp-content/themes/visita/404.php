<?php
/**
 * Visita - Template for displaying 404 pages (Not Found).
 *
 * @file 404.php
 * @package visita
 * @author Hafid Trujillo
 * @copyright 2010-2015 Xpark Media
 * @license license.txt
 * @version release: 0.1.0
 * @filesource  wp-content/themes/visita/404.php
 * @since available since 0.1.0
 */
 ?>
<?php get_header(); ?>

<div class="row">
  <div class="small-12 columns">

    <?php visita_before_loop(); ?>

    <article class="post no-results not-found">
			<header class="entry-header">
        <h1 class="entry-title"><?php esc_html_e( 'Page not found', 'visita' ); ?></h1>
        <h2><?php esc_html_e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'visita' ); ?></h2>
			</header>

			<div class="entry-content">
				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'visita' ); ?></p>
				<?php echo
        '<form method="get"class="search" action="' . esc_url( home_url( '/' ) ) . '">
       		<label>
       			<span class="screen-reader-text">' . _x( 'Search for:', 'label', 'visita' ) . '</span>
       			<input type="search" name="s" class="search" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder', 'visita' ) . '" title="' . _x( 'Search for:', 'label', 'visita' ) . '" />
       		</label>
       		<span class="screen-reader-text"><input type="submit" /></span>
       	</form>' ?>
			</div><!-- .entry-content -->
		</article><!-- #post-0 -->

    <?php visita_after_loop(); ?>

  </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
