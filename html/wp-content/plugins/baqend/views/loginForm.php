<div id="login-error" class="updated error is-dismissible" style="display: none">
  <p><?php _e('Your credentials were not correct. Please try again.', 'baqend') ?></p>
  <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('dismiss') ?></span></button>
</div>

<div id="registration-error" class="updated error is-dismissible" style="display: none">
  <p><?php _e('The passwords did not match. Please try again.', 'baqend') ?></p>
  <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('dismiss') ?></span></button>
</div>

<form id="form-registration" style="display: none">
  <h2 class="title"><?php _e('Register at Baqend', 'baqend') ?></h2>
  <p><?php _e('First, create an account for the Baqend Dashboard. You need it to allocate your personal webspace.', 'baqend') ?></p>

  <table class="form-table">
    <tr>
      <th><label for="registration-username"><?php _e('E-Mail', 'baqend') ?></label></th>
      <td>
        <input type="text" name="username" id="registration-username" class="regular-text" value="<?php echo $username; ?>" disabled>
        <p class="description"><?php _e('Enter an e-mail you will use to login to Baqend.', 'baqend') ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="registration-password-first"><?php _e('Password', 'baqend') ?></label></th>
      <td>
        <input type="password" name="password" id="registration-password-first" class="regular-text" disabled>
        <p class="description"><?php _e('Please choose a secure password.', 'baqend') ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="registration-password-second"><?php _e('Repeat password', 'baqend') ?></label></th>
      <td>
        <input type="password" name="password" id="registration-password-second" class="regular-text" disabled>
      </td>
    </tr>
  </table>

  <div class="submit-wrap">
    <?php submit_button(__('Create an Account', 'baqend'), 'primary', 'baqendRegister'); ?>
    <div class="spinner"></div>
  </div>
</form>

<br class="clear">

<form id="form-login" style="display: none">
  <h2 class="title"><?php _e('Log in to Baqend', 'baqend') ?></h2>
  <p><?php _e('Alternatively, log in to Baqend with the credentials of an existing Baqend account.', 'baqend') ?></p>

  <table class="form-table">
    <tr>
      <th><label for="form-username"><?php _e('E-Mail', 'baqend') ?></label></th>
      <td>
        <input type="text" name="username" id="form-username" class="regular-text" value="<?php echo $username; ?>" disabled>
        <p class="description"><?php _e('The e-mail you use to login to Baqend.', 'baqend') ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="form-password"><?php _e('Password', 'baqend') ?></label></th>
      <td>
        <input type="password" name="password" id="form-password" class="regular-text" disabled>
        <p class="description"><?php _e('The password you use to login to Baqend.', 'baqend') ?></p>
      </td>
    </tr>
  </table>

  <div class="submit-wrap">
    <?php submit_button(__('Log In', 'baqend'), 'primary', 'baqendLogin'); ?>
    <div class="spinner"></div>
  </div>
</form>

<br class="clear">

<form id="select-app" style="display: none">
  <h2 class="title"><?php _e('Choose an app', 'baqend') ?></h2>
  <p><?php _e('Please select one of your apps below. Your WordPress blog will then be hosted on the selected app.', 'baqend') ?></p>

  <table class="form-table">
    <tr>
      <th><label for="form-app-name"><?php _e('App Name', 'baqend') ?></label></th>
      <td>
        <select name="app-name" id="form-app-name" disabled>
        </select>
      </td>
    </tr>
  </table>

  <div class="submit-wrap">
    <?php submit_button(__('Select App', 'baqend'), 'primary', 'baqendSelectApp'); ?>
    <?php submit_button(__('Not %s? Log Out', 'baqend'), '', 'logout'); ?>
    <div class="spinner"></div>
  </div>
</form>

<form method="post" id="form-submit-bat" style="display: none">
  <input type="hidden" name="bq-username" id="form-bq-username">
  <input type="hidden" name="bq-password" id="form-bq-password">
  <input type="hidden" name="bq-app-name" id="form-bq-app-name">
</form>
