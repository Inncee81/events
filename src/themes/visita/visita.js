import LazyLoad from 'vanilla-lazyload';

const lazyLoad = new LazyLoad();

( ( $, doc ) => {

  let mobileLoaded = false;
  const mobileWidth =  767;
  const stylesheet = {
    type: 'text/css',
    rel: 'stylesheet',
  }

  // document ready
  $( function( ) {
    $( '<link/>', Object.assign( stylesheet, { href: visita.fonts } ) ).appendTo( 'head' );
    $( '<link/>', Object.assign( stylesheet, { href: visita.styles } ) ).appendTo( 'head' );
  } );

  //check  window size for loading
  $( window ).on( 'load resize orientationchange', () => {
    if ( ! mobileLoaded && $( doc ).width() >= mobileWidth ) {
      $( '<link/>', Object.assign( stylesheet, { href: visita.tablet } ) ).appendTo( 'head' );
      mobileLoaded = true;
    }
  } )

} )( jQuery, document );


/**
* Enables menu toggle.
*/

( ( $ ) => {

  let nav = $( '#nav' );
  if ( ! nav[0] )
    return;

  let button = nav.find( '.menu-toggle' );
  if ( ! button[0] )
    return;

  $( '.menu-toggle' ).on( 'click', ( e ) => {
    e.preventDefault();
    nav.toggleClass( 'show-menu' );
  } );

  $( '.menu-main .menu-item-has-children > a' ).on( 'touchend', ( e ) => {
    e.preventDefault();
     $( e.target  )
     .parent( )
     .toggleClass( 'show' )
     .siblings()
     .removeClass( 'show' )
  } )

} )( jQuery );
