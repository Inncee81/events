<div class="wrap">
  <?php wp_nonce_field( 'baqend_deploy' ) ?>

  <h1><?php _e('Baqend &rsaquo; Hosting', 'baqend') ?></h1>

  <?php include 'tabs.php'; ?>

  <?php if ( $error ): ?>
    <div id="message" class="updated error is-dismissible">
      <p><?php echo $error->get_error_message() ?></p>
      <button type="button" class="notice-dismiss">
        <span class="screen-reader-text"><?php _e( 'dismiss' ) ?></span>
      </button>
    </div>
  <?php endif; ?>

  <div id="deploy-message" class="updated notice is-dismissible" style="display: none">
    <p><?php echo __( '<b>Successfully</b> deployed %d files to Baqend.', 'baqend' ) ?></p>
    <p><?php printf( __( 'Test your WordPress Blog on Baqend: <a href="https://%s.app.baqend.com">https://%1$s.app.baqend.com</a>', 'baqend' ), $app_name ) ?></p>
    <ul></ul>
    <button type="button" class="notice-dismiss">
      <span class="screen-reader-text"><?php _e( 'dismiss' ) ?></span>
    </button>
  </div>

  <p><?php echo sprintf( __( 'Not sure what to do? Get more information about <strong>Hosting</strong> in the <a href="%s">Hosting Help!</a>', 'baqend' ), baqend_admin_url( 'baqend_help#hosting' ) ); ?></p>

  <p><?php printf( __( '<strong>Host this WordPress website</strong> to your app “%s” on Baqend.', 'baqend' ), $app_name ) ?></p>
  <p><?php printf( __( '<a href="https://%s.app.baqend.com" class="button button-primary button-hero">Open my Blog on Baqend</a>', 'baqend' ), $app_name ) ?></p>

  <form id="deploy" style="display: none">
    <h2 class="title"><?php _e('Upload the Blog', 'baqend') ?></h2>
    <p><?php _e('Click on the following button to upload your blog to Baqend.', 'baqend') ?></p>
    <div class="submit-wrap">
      <p class="submit">
        <?php submit_button(__('Start Deployment', 'baqend')); ?>
      </p>
      <div class="media-item">
        <div class="progress">
          <div class="bar" style="width: 0%" id="progress-bar"></div>
        </div>
      </div>
      <div class="spinner"></div>
      <div id="progress-states">
        <p class="progress-state" id="progress-setup"><?php _e('Setting up upload ...', 'baqend') ?></p>
        <p class="progress-state" id="progress-fetch-urls"><?php _e('Fetching the contents of your blog ...', 'baqend') ?></p>
        <p class="progress-state" id="progress-find-files"><?php _e('Comparing files with Baqend ...', 'baqend') ?></p>
        <p class="progress-state" id="progress-upload"><?php _e('Uploading to Baqend ...', 'baqend') ?></p>
        <p class="progress-state" id="progress-clean"><?php _e('Cleaning up ...', 'baqend') ?></p>
        <p class="progress-state" id="progress-done"><?php _e('Done!', 'baqend') ?></p>
      </div>
    </div>
    <br class="clear">
    <ul id="uploaded-files"></ul>
  </form>

  <form action="options.php" method="post">
    <h2 class="title"><?php _e('Hosting Settings', 'baqend') ?></h2>
    <?php settings_fields($settings_group); ?>
    <?php echo $fields ?>
  </form>

  <br class="clear">

  <form id="clean-bucket" style="display: none">
    <h2 class="title"><?php _e('Remove the Upload', 'baqend') ?></h2>
    <p><?php _e('Click on the following button to remove all files that were uploaded to Baqend.', 'baqend') ?></p>
    <div class="submit-wrap">
      <p class="submit">
        <?php submit_button(__('Clean Uploaded Files', 'baqend')); ?>
        <div class="spinner"></div>
      </p>
    </div>
  </form>

  <br class="clear">
</div>
