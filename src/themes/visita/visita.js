(( $, doc ) => {

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

})(jQuery, document)
