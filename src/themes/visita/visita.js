import 'foundation/js/foundation.core';
import 'foundation/js/foundation.reveal';
import 'foundation/js/foundation.util.box';
import 'foundation/js/foundation.util.motion';
import 'foundation/js/foundation.util.keyboard';
import 'foundation/js/foundation.util.triggers';
import 'foundation/js/foundation.util.mediaQuery';

import {langs} from './langs';
import LazyLoad from 'vanilla-lazyload';
import {TinyDatePicker, DateRangePicker} from 'tiny-date-picker/dist/date-range-picker'

const mobileWidth =  640;
const lazyLoad = new LazyLoad();


( ( $, doc ) => {

  $.get(visita.weather, (data) => {
    $('.site-logo .weather')
    .attr('aria-hidden', 'false')
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

  //check window size for loading
  const load_tablet_up  = (function() {
    if ( ! mobileLoaded && $( doc ).width() >= mobileWidth ) {
      $( '<link/>', Object.assign( stylesheet, { href: visita.tablet } ) ).appendTo( 'head' );
      mobileLoaded = true;
    }
  })();

  $( window ).on( 'resize orientationchange', load_tablet_up)

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
  $( 'a[rel~="external"], .gallery-icon > a' ).each( function( e ) {
    var $href =  $(this).attr( 'href' );

    if ( $href!== '#' &&  $href!== '') {
      $( this ).attr( { target: '_blank' } );
    } else {
      $( this ).attr( { 'rel': 'bookmark'} )
    }
	});

  // Don't allow iframes to redirect parent page
  if (window.top !== window.self) {
    delete window.top.onbeforeunload;
  }

  // setup ajax calls
  $.ajaxSetup({  headers: {
    'Authorization': 'Basic c2VhcmNoOk9NcGowUXVlRippUSpwQnI5WGIwQndURw=='
  } })

  // Add reviews
  var $modal = $('#reveal');
  $('[data-reviews]').click(function(e) {
    e.preventDefault();
    $.ajax(this.href)
      .done(function(resp) {
        $modal
        .find('.reveal-content')
        .html(resp)
        $modal.foundation('open');
    });
  })

  // Show reviews
  if (location.hash.search(/comment-/) == 1) {
    const $link = $('[itemprop=aggregateRating]').attr('href')
    if ($link) {
      $.ajax($link)
        .done(function(resp) {
          $modal
          .find('.reveal-content')
          .html(resp)
          $modal.foundation('open');
      });
    }
  }

  //
  $(() => $(document).foundation());

} )( jQuery, document );

/**
* Enable menu toggle.
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


/**
* Search Functionality
*/
( ( $, doc ) => {

  // start search functionality
  const lang = $('html').attr('lang');
  const search = $('#search-advanced');
  const end = search.find('[name=_ends]');
  const start = search.find('[name=_starts]');

  // change date time to unix time
  const unixtime = function(date) {
    return Math.round(date.getTime() / 1000)
  }

  // autocomplete
  $('.search-field').autocomplete({
    minLength: 2,
    source: (query, suggest) => {
      query.lang = lang
      $.get( '/wp-json/vv/v1/s', query, ( res ) => {
        suggest(res);
      })
    },
    select: ( event, ui )  => {
      window.location.href = ui.item.link;
    }
  })

  // focus on modal open
  $(document).on('open.zf.reveal', search, function() {
    $('[name=s]').focus()
  });

  // show calendar only for events
  search.on('change', 'select', function() {
    $('.eventos-dates:visible').hide();
    if ( $(this).val() == 'event' ) {
      $('.eventos-dates:hidden').show();
      search.find('[type=hidden]').attr('disabled', false);
    } else search.find('[type=hidden]').attr('disabled', true);
  });

  // set calendar widget
  DateRangePicker('.eventos-dates', {
    startOpts: { min: new Date(), lang: langs[lang] }
  }).on('statechange', (_, rp) => {
    start.val( rp.state.start ? unixtime(rp.state.start) : '' );
    end.val( rp.state.end ? unixtime(rp.state.end): '' );
  });

} )( jQuery, document );
