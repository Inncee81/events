<?php /*

Generated from Guide – Do not change manually!

*/ ?><div class="wrap baqend-help"><h1><?php _e('Baqend &rsaquo; Help', 'baqend'); ?></h1>
<?php include 'tabs.php'; ?>








<h2 id="prepare-your-baqend-app"><?php _e('Prepare Your Baqend App', 'baqend'); ?></h2>
<p><?php _e('If you have not already, <a href="https://dashboard.baqend.com/register">register at Baqend</a> and create your app with a name of your choice.
When your app is ready, go to “Settings” in the navigation and find the “Custom Domain” settings in the “Hosting” section.
Add your domain and follow the DNS setup instructions.', 'baqend'); ?></p>
<h2 id="configure-your-wordpress-plugin"><?php _e('Configure Your WordPress Plugin', 'baqend'); ?></h2>
<p><?php _e('Once your Baqend app is prepared, go to the admin page of your WordPress blog and head over to the Baqend settings (located under <em>http://your-wordpress.blog/wp-admin/admin.php?page=baqend_settings</em>).
There, enter the credentials you use to login with Baqend.', 'baqend'); ?></p>
<p><img alt="<?php _e( 'Select an app', 'baqend' ); ?>" src="<?php echo plugin_dir_url( __DIR__ ); ?>img/select-app.png"/></p>
<p><?php _e('You can now choose the app you wish to use for the plugin. 
Click on “Select App” and your plugin will be configured for Baqend.', 'baqend'); ?></p>
<p><?php _e('If you want to change the Baqend account, head over to the <em>Account</em> tab and click on “Log Out”.', 'baqend'); ?></p>
<p><?php _e('Now, you can choose <a href="#speed-kit">Speed Kit</a> to use for your WordPress blog.
Use Speed Kit if you want to accelerate your existing WordPress blog and handle scalability on your own.
This is easy to use and you will be set-up and done in seconds!', 'baqend'); ?></p>
<h2 id="speed-kit"><?php _e('Speed Kit', 'baqend'); ?></h2>
<p><?php _e('The WordPress plugin makes using <a href="https://www.baqend.com/guide/topics/speed-kit/">Baqend Speed Kit</a> a breeze.
Simply head over to the <em>Speed Kit</em> tab and check <em>enabled</em> next to <em>Speed Kit Integration</em>.', 'baqend'); ?></p>
<p><img alt="<?php _e( 'Enable Speed Kit', 'baqend' ); ?>" src="<?php echo plugin_dir_url( __DIR__ ); ?>img/speed-kit-enable.png"/></p>
<p><?php _e('Now, you have several options to configure Speed Kit for you:', 'baqend'); ?></p>
<ul>
<li><strong><?php _e('Whitelist URLs', 'baqend'); ?></strong><ul>
<li><?php _e('Only the websites given in this list will be handled by Speed Kit.', 'baqend'); ?></li>
</ul>
</li>
<li><strong><?php _e('Blacklist URLs', 'baqend'); ?></strong><ul>
<li><?php _e('All websites given in this list will be ignored by Speed Kit.', 'baqend'); ?></li>
</ul>
</li>
<li><strong><?php _e('Bypass cache on cookie', 'baqend'); ?></strong><ul>
<li><?php _e('If a page contains one of the cookies given in this list, Speed Kit will ignore the given page. The cookies are given as prefixes.', 'baqend'); ?></li>
</ul>
</li>
<li><strong><?php _e('Allowed content types', 'baqend'); ?></strong><ul>
<li><?php _e('Only the given content types will be handled by Speed Kit.', 'baqend'); ?></li>
</ul>
</li>
<li><strong><?php _e('Automatic update interval', 'baqend'); ?></strong><ul>
<li><?php _e('Automatically triggers a revalidation of Speed Kit.', 'baqend'); ?></li>
</ul>
</li>
</ul>
<p><?php _e('Click “Save Settings” once you are ready.', 'baqend'); ?></p>

<?php if ( $hosting_enabled ): ?>
  <h2 id="hosting"><?php _e('Hosting', 'baqend'); ?></h2>
  <p><?php _e('The WordPress plugin makes it easy using <a href="https://www.baqend.com/guide/topics/hosting/">Baqend Hosting</a>, too.
  Enable Hosting by going into the Account tab and checking “Show Hosting settings”.', 'baqend'); ?></p>
  <p><?php _e('Now, when you go to the Hosting tab, you have several options to configure Speed Kit for you:', 'baqend'); ?></p>
  <ul>
  <li><strong><?php _e('Additional URLs to process', 'baqend'); ?></strong><ul>
  <li><?php _e('Here you can add additional URLs separated by new lines which will also be checked when collecting your blog contents.', 'baqend'); ?></li>
  </ul>
  </li>
  <li><strong><?php _e('URLs which should be excluded', 'baqend'); ?></strong><ul>
  <li><?php _e('When these URLs occur during the content collecting, they will not be uploaded to Baqend.', 'baqend'); ?></li>
  </ul>
  </li>
  <li><strong><?php _e('URL type to use on Baqend', 'baqend'); ?></strong><ul>
  <li><?php _e('Choose between relative or absolute URLs to be used in your hosted copy. ', 'baqend'); ?></li>
  </ul>
  </li>
  <li><strong><?php _e('Destination scheme', 'baqend'); ?></strong><ul>
  <li><?php _e('The HTTP scheme being used by your hosted copy. This is either HTTPS or HTTP.', 'baqend'); ?></li>
  </ul>
  </li>
  <li><strong><?php _e('Destination host', 'baqend'); ?></strong><ul>
  <li><?php _e('The HTTP host where your hosted copy will be deployed at. This is normally your domain name.', 'baqend'); ?></li>
  </ul>
  </li>
  <li><strong><?php _e('Working directory', 'baqend'); ?></strong><ul>
  <li><?php _e('This is the working directory in which all files are collected temporarily.', 'baqend'); ?></li>
  </ul>
  </li>
  </ul>
  <p><?php _e('Click “Save Settings” once you are ready.', 'baqend'); ?></p>
<?php endif; ?>
</div>
