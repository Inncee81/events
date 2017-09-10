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

  <footer class="footer text-center medium-text-left">

		<nav class="footer-nav">
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_class' => 'footer-menu' ) ); ?>
		</nav><!--.top-nav-->

		<div class="site-credits">
      <?php printf(  __( '<span class="powered"> %1$s </span> <a href="%2$s" title="%3$s" target="_blank"> %3$s </a>', 'visita' ),
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
