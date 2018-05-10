<div class="wrap">
  <h1><?php _e('Baqend &rsaquo; Getting Started', 'baqend') ?></h1>

  <?php include 'tabs.php'; ?>

  <?php if ($app_name === null): ?>
    <?php include 'loginForm.php'; ?>
  <?php else: ?>
    <div class="getting-started">
      <?php if ( $bbq_password ): ?>
        <div class="getting-started-info">
          <h2><?php _e('Welcome to the Baqend WordPress Plugin!', 'baqend'); ?></h2>
          <p><?php _e('We automatically created a Baqend Speed Kit App for you which is connected to your WordPress.', 'baqend'); ?></p>
          <p>
            <span><?php echo sprintf( __( 'Your password to login to the <a href="%s">Baqend Dashboard</a>', 'baqend' ), 'https://dashboard.baqend.com/apps' ); ?>:</span>
            <code><?php echo $bbq_password; ?></code>
            <span><?php _e('with username', 'baqend'); ?></span>
            <code><?php echo $bbq_username; ?></code>
          </p>
        </div>
      <?php endif; ?>

      <ul class="getting-started-actions">
        <li>
          <a href="<?php echo baqend_admin_url('baqend_speed_kit'); ?>">
            <p class="getting-started-heading">
              <img src="<?php echo plugin_dir_url(__DIR__) ?>img/rocket-outline.png">
              <?php _e('Speed Kit', 'baqend') ?>
            </p>
            <p><?php _e('With Speed Kit, you can improve the performance of your currently hosted WordPress blog within seconds!', 'baqend') ?></p>
            <p>
              <?php _e('Speed Kit is currently', 'baqend') ?>
              <?php if ($speed_kit): ?>
                <span class="enabled"><?php _e('enabled', 'baqend') ?></span>
              <?php else: ?>
                <span class="disabled"><?php _e('disabled', 'baqend') ?></span>
              <?php endif; ?>
            </p>
          </a>
        </li>
        <?php if ($hosting_enabled): ?>
        <li>
          <a href="<?php echo baqend_admin_url('baqend_hosting'); ?>">
            <p class="getting-started-heading">
              <img src="<?php echo plugin_dir_url(__DIR__) ?>img/icon_code.png">
              <?php _e('Hosting', 'baqend') ?>
            </p>
            <p><?php _e('With Hosting, you can serve a scalable and high-performing copy of your WordPress blog!', 'baqend') ?></p>
            <p>
              <?php if ($hosting_enabled): ?>
                <?php _e('Last hosting:', 'baqend') ?>
                <?php if ($hosting_time !== null): ?>
                  <span><?php echo date_i18n(get_option('date_format'), strtotime($hosting_time)) ?></span>
                <?php else: ?>
                  <span class="disabled"><?php _e('never', 'baqend') ?></span>
                <?php endif; ?>
              <?php else: ?>
                <span><?php _e('<span class="disabled">Hosting is disabled.</span><br>You can enable it in Account settings.', 'baqend') ?></span>
              <?php endif; ?>
            </p>
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <?php if ($stats): ?>
        <ul class="statistics">
          <li>
            <span class="statistics-number"><?php echo size_format($stats->getDownload()); ?></span>
            <span class="statistics-caption"><? _e('Outgoing Data', 'baqend') ?></span>
          </li>
          <li>
            <span class="statistics-number"><?php echo $stats->getRequests(); ?></span>
            <span class="statistics-caption"><? _e('Requests', 'baqend') ?></span>
          </li>
        </ul>
      <?php endif; ?>
      <form id="form-trigger-speed-kit">
        <div class="metabox-holder">
          <div class="postbox">
            <div class="inside" style="padding-bottom:0;text-align:center;">
              <p><?php echo __( 'Click on the following button to manually revalidate your website with Speed Kit.', 'baqend' ); ?></p>
              <div class="submit-wrap">
                <?php submit_button( __( 'Revalidate Website', 'baqend' ), 'primary', 'revalidate', false); ?>
                <div class="spinner" style="margin-top:0;float:none; position:absolute;"></div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <br class="clear">
</div>
