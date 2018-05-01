<?php
/**
* Visita - Footer Template
*
* @file footer.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/footer.php
* @since available since 0.1.0
*/
?>
    <?php visita_after_main(); ?>
  </main> <!--main  -->

  <footer itemscope itemtype="http://schema.org/WPHeader" class="footer text-center medium-text-left">

    <nav id="nav-social">
			<a class="screen-reader-text" href="#nav" role="button" rel="nofollow" title="<?php esc_attr_e( 'Skip to navigation', 'visita' ) ?>"><?php esc_html_e( 'Skip to top navigation', 'visita' ) ?></a>
			<?php wp_nav_menu( array( 'theme_location' => 'social', 'menu_class' => 'menu menu-social align-center ', 'fallback_cb' => 'visita_default_social_menu' ) ); ?>
    </nav><!--#social-nav-->

		<nav class="footer-nav">
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_class' => 'menu menu-footer' ) ); ?>
		</nav><!--.top-nav-->

		<div class="site-credits">
      <?php printf( '<span class="powered">%1$s</span> <a href="%2$s" title="%3$s" target="_blank"> %3$s </a>',
				esc_attr(  __('visita.vegas by'), 'visita' ),
				esc_url( __( 'http://xparkmedia.com', 'visita' ) ),
				esc_attr( 'Xpark Media', 'visita' )
			); ?>
		</div>

	</footer><!--.footer-->

</div> <!-- .page -->
<?php wp_footer(); ?>
</body>
</html>
