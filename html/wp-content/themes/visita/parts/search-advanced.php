
<form action="<?php echo home_url() ?>" method="get" class="reveal" id="search-advaced" data-reveal aria-hidden="true">
  <button class="button float-right" data-close aria-label="Close Modal" type="button">&times;</button>
  <h3><?php esc_html_e( 'Advaced Search', 'visita' ) ?></h3>
  <input type="search" name="s" />

  <label>Tipo:
    <select name="post_type">
      <option value="event">Evento</option>
      <option value="show">Show</option>
      <option value="hotel">Hotel</option>
      <option value="attraction"> Atracción</option>
      <option value="club">Nightclubs</option>
    </select>
  </label>

  <label>Fechas
    <input name="_starts" type="hidden" id="event-starts"/>
    <input name="_ends" type="hidden" id="event-ends" />
  </label>

  <div class="eventos-dates"></div>

  <div><button type="submit" class="button expanded"><?php esc_html_e( 'Search', 'visita' ) ?></button></div>

</form>
