import LazyLoad from 'vanilla-lazyload';

const lazyLoad = new LazyLoad();

( ( $, doc ) => {

  let mobileLoaded = false;
  const mobileWidth =  768;
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

  //make headers clickable
  $( '.entry-header.float, .visita-widget .entry-header' ).on( 'click', function( e ) {
    if ( e.target.className !== 'post-edit-link' ) {
      e.preventDefault();
      $( this ).parent( ).find( '.image.url' )[0].click();
    }
  });

  // open external link on new window
  $( 'a[rel="external"]' ).each( function( e ) {
    var $href =  $(this).attr( 'href' );

    if ( $href!== '#' &&  $href!== '') {
      $( this ).attr( { target: '_blank' } );
    } else {
      $( this ).attr( { 'rel': 'bookmark'} )
    }
	});

  //don't allow iframes to redirect parent page
  if (window.top !== window.self) {
    delete window.top.onbeforeunload;
  }
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

  let count = 0, time= 400, timer;

  $( '.menu-main .menu-item-has-children > a' ).on( 'touchend', ( e ) => {

    e.preventDefault();

    count++

    if ( count > 1 ) {
      clearTimeout( timer );

      if ( e.target.href ) {
        document.location.href = e.target.href;
      }

    } else  {

      timer = setTimeout(() => count = 0, time);

      $( e.target  ).parent()
      .toggleClass( 'show' )
      .siblings()
      .removeClass( 'show' )
    }
  } );

} )( jQuery );
