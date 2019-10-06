<?php
/**
* Visita - Ads file
*
* @file ads.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2020 Xpark Media
* @version release: 1.0.3
* @filesource  wp-content/themes/includes/_inc/ads.php
* @since available since 0.1.0
*/

/**
* Remove "Private: " from title
* _visita_top
*/
function visita_before_ad( ) {
  echo '
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <ins class="adsbygoogle top"
   style="display:block"
   data-ad-client="ca-pub-1000685253996582"
   data-full-width-responsive="false"
   data-ad-slot="4059481352"
   data-ad-format="auto"></ins>
  <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
}
// add_action( 'visita_before_loop', 'visita_before_ad', 5 );

/**
* _visita_bottom
*/
function visita_after_ad( ) {
  echo '
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <ins class="adsbygoogle bottom"
   style="display:block"
   data-ad-client="ca-pub-1000685253996582"
   data-ad-slot="5396613757"
   data-ad-format="auto"></ins>
   <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
}
// add_action( 'visita_after_loop', 'visita_after_ad', 5 );

/**
*
*/
function visita_gtm_tag( ) {
  echo '<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N4P3GK"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
  new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
  \'//www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,\'script\',\'dataLayer\',\'GTM-N4P3GK\');</script>';
}
// add_action( 'visita_before_page', 'visita_gtm_tag', 5 );
