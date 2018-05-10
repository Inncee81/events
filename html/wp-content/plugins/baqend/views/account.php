<div class="wrap">
  <h1><?php _e( 'Baqend &rsaquo; Account', 'baqend' ); ?></h1>

  <?php include 'tabs.php'; ?>

  <p><?php _e( 'Here you can change your Baqend account settings.', 'baqend' ); ?></p>

  <?php if ( $hosting_enabled ): ?>
    <p><?php printf( __( 'Test your WordPress Blog on Baqend: <a href="https://%s.app.baqend.com">https://%1$s.app.baqend.com</a>', 'baqend' ), $app_name ); ?></p>
  <?php endif; ?>

  <form method="post">
    <div class="metabox-holder"><div class="postbox"><div class="inside">
    <table class="form-table">
      <tr>
        <th><?php _e( 'App Name', 'baqend' ); ?></th>
        <td>
          <input type="text" value="<?php echo $app_name; ?>" readonly>
          <p class="description">
            <?php _e( 'The lower-cased name used in the <a href="https://dashboard.baqend.com/apps" target="_blank">Baqend Dashboard</a>.', 'baqend' ); ?>
          </p>
        </td>
      </tr>

      <?php if ( $api_token !== null ): ?>
        <tr>
          <th><?php _e( 'API Token', 'baqend' ); ?></th>
          <td>
            <input type="text" value="<?php echo $api_token; ?>" readonly>
            <p class="description">
              <?php _e( 'An API token set by another software to connect this WordPress instance.', 'baqend' ); ?>
            </p>
          </td>
        </tr>
      <?php endif; ?>

      <?php if ( $bbq_username !== null ): ?>
        <tr>
          <th><?php _e( 'Baqend Username', 'baqend' ); ?></th>
          <td>
            <input type="text" value="<?php echo $bbq_username; ?>" readonly>
            <p class="description">
              <?php echo sprintf( __( 'Your username to login to the <a href="%s">Baqend Dashboard</a>', 'baqend' ), 'https://dashboard.baqend.com/apps' ); ?>
            </p>
          </td>
        </tr>
      <?php endif; ?>

      <?php if ( $bbq_password !== null ): ?>
        <tr>
          <th><?php _e( 'Baqend Password', 'baqend' ); ?></th>
          <td>
            <input type="text" value="<?php echo $bbq_password; ?>" readonly>
            <p class="description">
              <?php echo sprintf( __( 'Your password to login to the <a href="%s">Baqend Dashboard</a>', 'baqend' ), 'https://dashboard.baqend.com/apps' ); ?>
            </p>
          </td>
        </tr>
      <?php endif; ?>
    </table>

    <div class="submit">
      <input type="hidden" name="logout" value="true">
      <?php submit_button( __( 'Log Out', 'baqend' ), 'primary', 'accountLogout', false, $api_token !== null ? [ 'disabled' => 'disabled', 'style' => 'float:right' ] : [ 'style' => 'float:right' ] ); ?>
      <div class="spinner"></div>
    </div>
    </div></div>
  </form>

  <form action="options.php" method="post" style="clear: left;">
    <?php settings_fields( $settings_group ); ?>
    <?php echo $fields ?>
  </form>

  <br class="clear">
</div>
