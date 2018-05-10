<div id="note-update-plugin" class="update-nag" style="display: none">
  <p data-tmpl="<?php echo __('Your Baqend WordPress Plugin version %our% is outdated. Update to version %remote% now!', 'baqend') ?>"></p>
  <a class="button button-primary" href="https://www.baqend.com/wordpress-plugin/latest/baqend.zip" download><?php _e('Download Latest Plugin', 'baqend') ?></a>
  <br class="clear">
</div>

<h2 id="baqend-tabs" class="nav-tab-wrapper">
  <?php $this->tab( 'baqend', 'getting-started-tab', __( 'Getting Started', 'baqend' ) ); ?>
  <?php if ( ! isset( $logged_in ) || $logged_in !== false ): ?>
    <?php $this->tab( 'baqend_speed_kit', 'speed-kit-tab', __( 'Speed Kit', 'baqend' ) ); ?>
    <?php if ( $hosting_enabled ): ?>
      <?php $this->tab( 'baqend_hosting', 'hosting-tab', __( 'Hosting', 'baqend' ) ); ?>
    <?php endif; ?>
    <?php $this->tab( 'baqend_account', 'account-tab', __( 'Account', 'baqend' ) ); ?>
  <?php endif; ?>
  <?php $this->tab( 'baqend_help', 'help-tab', __( 'Help', 'baqend' ) ); ?>
</h2>
