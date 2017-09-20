<amp-sidebar id="sidebar" layout="nodisplay" side="right">
  <div class="amp-sidebar-header">
    <a href="#" on="tap:sidebar.toggle">✕</a>
  </div>
  <?php if ($menu = wp_get_nav_menu_items(
    (get_locale() == 'es_MX') ? 5 : 71
  )): ?>
    <ul >
      <?php foreach ($menu as $item) {
        if($item->post_title != 'Language switcher' && !$item->menu_item_parent) {
          echo '<li><a class="' . esc_attr(implode(' ', $item->classes)) . '" title="' . esc_attr($item->title) . '"
            href="' . esc_url($item->url) . '">' . esc_html($item->title) .
          '</a></li>' . "\n";
        }
      } ?>
    </ul>
  <?php endif; ?>
</amp-sidebar>
