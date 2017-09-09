(( $, doc ) => {

  /**
  *
  */
  let mobileLoaded = false;
  const mobileWidth =  1024;
  const style = doc.createElement( 'link' );

  style.rel ='stylesheet'
  style.type ='text/css'
  style.href = visita.tabletstyles

  $( window ).on( 'load resize orientationchange', () => {
    if ( ! mobileLoaded && $( doc ).width() >= mobileWidth ) {
      mobileLoaded = true
      $( 'body' ).append( style )
    }
  })

})( jQuery, document );

/**
* Enables menu toggle.
*/

(( $ ) => {

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


})( jQuery );
