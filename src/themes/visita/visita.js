import LazyLoad from 'vanilla-lazyload';

const mobileWidth =  640;
const lazyLoad = new LazyLoad();

( ( $, doc ) => {

  $.get(visita.weather, (data) => {
    $('.site-logo .weather')
    .attr('title', visita.weather_text)
    .text(Math.round(data.current[`temp_${visita.weather_unit}`]) + `\u00b0${visita.weather_unit.toUpperCase()}`)
  })

  let mobileLoaded = false;
  const stylesheet = {
    type: 'text/css',
    rel: 'stylesheet',
  }

  // document ready
  $( function( ) {
    $( '<link/>', Object.assign( stylesheet, { href: visita.styles } ) ).appendTo( 'head' );
    $( '<link/>', Object.assign( stylesheet, { href: visita.fonts } ) ).appendTo( 'head' );
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
      const link = $( this ).parent( ).find( 'a.url' );

      if ( e.ctrlKey || e.metaKey ) {
        link.attr( { target: '_blank' } )
      }

      $( this ).parent( ).find( 'a.url' )[0].click();
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

( ( $, doc ) => {

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

  let count = 0, time= 300, timer;

  $( '.menu-main .menu-item-has-children > a' ).on( 'click touchend', ( e ) => {

    if ( e.type == 'click' && $( doc ).width() > mobileWidth ) {
      return;
    }

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

} )( jQuery, document );
