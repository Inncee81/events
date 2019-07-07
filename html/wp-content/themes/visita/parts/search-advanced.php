
<form action="<?php echo home_url() ?>" method="get" class="reveal" id="search-advanced" data-reveal aria-hidden="true">
  <button class="button small float-right" data-close aria-label="<?php esc_html_e( 'Close Modal', 'visita' ) ?>" type="button">&times;</button>

  <h4><?php esc_html_e( 'Advaced Search', 'visita' ) ?></h4>
  <input type="search" name="s" />

  <label>
    <span class="screen-reader-text">
      <?php esc_html_e( 'Types: ', 'visita' ) ?>
    </span>
    <select name="post_type">
      <option value="event">Evento</option>
      <option value="show">Show</option>
      <option value="hotel">Hotel</option>
      <option value="attraction"> Atracción</option>
      <option value="club">Nightclubs</option>
    </select>
  </label>

  <input name="_ends" type="hidden" />
  <input name="_starts" type="hidden" />

  <div class="eventos-dates"></div>
  <div><button type="submit" class="button expanded"><?php esc_html_e( 'Search', 'visita' ) ?></button></div>

</form>
