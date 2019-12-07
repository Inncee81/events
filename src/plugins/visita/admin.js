(($) => {

  if ( typeof acf !== 'undefined' ) {

    const remove_button_panel = (args) => {
      args.stepMinute = 10
      args.showSecond = false,
      args.showButtonPanel = false
      return args
    }

    acf.add_filter( 'date_picker_args', remove_button_panel )
    acf.add_filter( 'time_picker_args', remove_button_panel )

  }
})(jQuery);
