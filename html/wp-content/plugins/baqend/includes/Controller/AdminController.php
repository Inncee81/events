<?php

namespace Baqend\WordPress\Controller;

use Baqend\SDK\Model\AssetFilter;
use Baqend\WordPress\Admin\View;
use Baqend\WordPress\Loader;
use Baqend\WordPress\Plugin;
use Psr\Log\LoggerInterface;

/**
 * Class AdminController created on 16.06.17.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Controller
 */
class AdminController extends Controller {

    /**
     * @var View
     */
    private $view;

    /**
     * @var string
     */
    private $settings_group;

    /**
     * Admin constructor.
     *
     * @param Plugin $plugin
     */
    public function __construct( Plugin $plugin, LoggerInterface $logger ) {
        parent::__construct( $plugin, $logger );
        $this->view           = new View();
        $this->settings_group = $plugin->slug . '_group';
    }

    public function register(Loader $loader) {
        $loader->add_action('admin_enqueue_scripts', [$this, 'assets']);
        $loader->add_action('admin_init', [$this, 'register_settings']);
        $loader->add_action('admin_menu', [$this, 'add_menus']);
        $loader->add_action('admin_notices', [$this, 'add_notices']);
        $loader->add_filter('plugin_action_links_'.$this->plugin->get_basename(), [$this, 'add_settings_link']);
    }

    public function assets() {
        wp_enqueue_style($this->plugin->slug, $this->plugin->css_url('baqend-admin.css'), [], $this->plugin->version);
        wp_enqueue_script('baqend-vendor', $this->plugin->js_url('baqend-vendor.js'), ['jquery'], $this->plugin->version, true);
        wp_enqueue_script('baqend-admin', $this->plugin->js_url('baqend-admin.js'), ['jquery'], $this->plugin->version, true);
    }

    public function register_settings() {
        if ( ! $this->plugin->app_name && $this->is_on_baqend_admin( [ 'baqend', 'baqend_help' ] ) ) {
            exit( wp_redirect( baqend_admin_url( 'baqend' ) ) );
        }

        register_setting($this->settings_group, $this->plugin->slug, [$this, 'save_settings']);
    }

    /**
     * Adds links to the plugin page.
     *
     * @param string[] $links
     *
     * @return string[]
     */
    public function add_settings_link(array $links) {
        $settings_link = '<a href="' . baqend_admin_url( 'baqend' ) . '">'.__('Settings', 'baqend').'</a>';
        array_push($links, $settings_link);

        return $links;
    }

    /**
     * Processes options aver saving them.
     *
     * FIXME: This is also called when saving the options. What should we do?
     *
     * @param string[] $options Options to process.
     *
     * @return mixed[] The processed options.
     */
    public function save_settings( array $options = null ) {
        $fields  = $this->create_fields();
        $changed = [];

        foreach ( $fields as $field ) {
            $key = $field['slug'];
            if ( ! array_key_exists( $key, $options ) ) {
                continue;
            }

            $oldValue = $this->plugin->options->get( $key );
            $value    = $options[ $key ];

            if ( is_string( $value ) ) {
                $value = $this->normalize_setting( $field, $value );
            }

            if ( $oldValue !== $value ) {
                $this->plugin->options->set( $key, $value );
                $changed[ $key ] = $value;
            }
        }

        $this->update_cron_hooks($changed);

        return $this->plugin->options->all();
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     */
    public function add_menus() {
        // Add main menu item
        add_menu_page(
            __('Baqend', 'baqend'),
            __('Baqend', 'baqend'),
            'edit_posts',
            'baqend',
            [$this, 'render_getting_started'],
            'dashicons-upload'
        );

        // Add settings submenu item
        $this->add_submenu(__('Getting Started', 'baqend'), 'baqend', [$this, 'render_getting_started']);

        // Add deploy submenu item
        $this->add_submenu(__('Speed Kit', 'baqend'), 'baqend_speed_kit', [$this, 'render_speed_kit']);

        if ($this->plugin->options->get('hosting_enabled')) {
            // Add hosting submenu item
            $this->add_submenu( __( 'Hosting', 'baqend' ), 'baqend_hosting', [ $this, 'render_hosting' ] );
        }

        // Add account submenu item
        $this->add_submenu(__('Account', 'baqend'), 'baqend_account', [$this, 'render_account']);

        // Add help submenu item
        $this->add_submenu(__('Help', 'baqend'), 'baqend_help', [$this, 'render_help']);
    }

    /**
     * Displays notices in the admin if applicable.
     */
    public function add_notices() {
        if ( $this->is_login_info_broken() ) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <strong><?php _e( 'We updated the Baqend WordPress plugin. Please logout and login again to fix a login vulnerability.', 'baqend' ) ?></strong>
                </p>
                <p>
                    <a href="<?php echo baqend_admin_url( 'baqend_account' ); ?>"><?php _e( 'Click here to go to the logout', 'baqend' ) ?></a>
                </p>
            </div>
            <?php
        }

        if ( !is_ssl() ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong><?php _e( 'Baqend Speed Kit needs HTTPS to work correctly.', 'baqend' ) ?></strong></p>
                <p>
                    <?php _e( 'Please ensure your WordPress is running on HTTPS.', 'baqend' ) ?>
                    <?php _e( 'Therefore, install an SSL certificate on your Apache or Nginx web server, or ask your website provider to install it for you.', 'baqend' ) ?>
                    <?php _e( 'Then, go to the general settings and ensure your WordPress address is beginning with “https://”.', 'baqend' ) ?>
                </p>
                <p>
                    <a href="<?php echo admin_url( 'options-general.php' ); ?>"><?php _e( 'Click here to go to the general settings', 'baqend' ) ?></a>
                </p>
            </div>
            <?php
        }

        if ( $this->plugin->options->has('cron_error') ) {
            $dismiss_url = add_query_arg( [ 'baqend_cron_error_dismiss' => true ], admin_url() );

            $cron_error = $this->plugin->options->get( 'cron_error' );
            ?>
            <div class="notice notice-error is-dismissible" data-dismiss-ajax="clear_cron_error">
                <p>
                    <strong><?php echo $cron_error; ?></strong>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Render the getting started view.
     */
    public function render_getting_started() {
        $stats = null;
        if ( $this->plugin->app_name ) {
            $stats = $this->plugin->baqend->createAppStats( $this->plugin->app_name );
        }

        $this->view
            ->set_template( 'gettingStarted.php' )
            ->assign( 'logged_in', $this->plugin->app_name !== null )
            ->assign( 'app_name', $this->plugin->app_name )
            ->assign( 'username', $this->plugin->options->get('username') )
            ->assign( 'speed_kit', $this->plugin->options->get( 'speed_kit_enabled' ) )
            ->assign( 'hosting_time', $this->plugin->options->get( 'archive_end_time' ) )
            ->assign( 'stats', $stats )
            ->assign( 'hosting_enabled', $this->plugin->options->get('hosting_enabled') )
            ->assign( 'bbq_username', $this->plugin->options->get('bbq_username') )
            ->assign( 'bbq_password', $this->plugin->options->get('bbq_password') )
            ->render();
    }

    /**
     * Render the account view.
     */
    public function render_account() {
        $settings = $this->plugin->options->all();

        // Generate the settings fields
        $field_args = $this->create_fields( [
            'hosting_enabled',
        ] );
        $fields     = $this->custom_settings_fields( $field_args, $settings );
        $api_token = $this->plugin->options->get('api_token');

        $this->view
            ->set_template( 'account.php' )
            ->assign( 'app_name', $this->plugin->app_name )
            ->assign( 'api_token', $api_token )
            ->assign( 'fields', $fields )
            ->assign( 'settings', $settings )
            ->assign( 'settings_group', $this->settings_group )
            ->assign( 'hosting_enabled', $this->plugin->options->get('hosting_enabled') )
            ->assign( 'bbq_username', $this->plugin->options->get('bbq_username') )
            ->assign( 'bbq_password', $this->plugin->options->get('bbq_password') )
            ->render();
    }

    /**
     * Render the deployment view.
     */
    public function render_hosting() {
        $settings = $this->plugin->options->all();

        // Generate the settings fields
        $field_args = $this->create_fields( [
            'additional_urls',
            'urls_to_exclude',
            'destination_scheme',
            'destination_host',
            'temp_files_dir',
        ] );
        $fields     = $this->custom_settings_fields( $field_args, $settings );

        $this->view
            ->set_template( 'hosting.php' )
            ->assign( 'fields', $fields )
            ->assign( 'app_name', $this->plugin->app_name )
            ->assign( 'settings_group', $this->settings_group )
            ->assign( 'hosting_enabled', $this->plugin->options->get( 'hosting_enabled' ) )
            ->render();
    }

    /**
     * Render the Speed Kit view.
     */
    public function render_speed_kit() {
        // Speed Kit settings have changed, perform revalidation
        if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) {
            try {
                $filter = new AssetFilter();
                $filter->addContentType( AssetFilter::DOCUMENT );

                $this->plugin->baqend->asset()->revalidate( $filter );

                $this->logger->info( 'Revalidation by save settings succeeded', [ 'filter' => $filter->jsonSerialize() ] );
            } catch ( \Exception $e ) {
                $this->logger->error( 'Revalidation by save settings failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            }
        }

        $settings = $this->plugin->options->all();

        // Get updates on Speed Kit
        $sw_path      = $this->plugin->sw_path();
        $snippet_path = $this->plugin->snippet_path();
        $info         = $this->plugin->baqend->getSpeedKit()->createInfo( $sw_path, $snippet_path, '1.X.X' );

        // Generate the settings fields
        $field_args = $this->create_fields( [
            'speed_kit_enabled',
            'speed_kit_whitelist',
            'speed_kit_blacklist',
            'speed_kit_cookies',
            'speed_kit_content_type',
            'speed_kit_update_interval',
            'fetch_origin_interval',
            'speed_kit_max_staleness',
            'speed_kit_app_domain',
        ] );
        $fields     = $this->custom_settings_fields( $field_args, $settings );

        $this->view
            ->set_template( 'speedKit.php' )
            ->assign( 'fields', $fields )
            ->assign( 'settings', $settings )
            ->assign( 'settings_group', $this->settings_group )
            ->assign( 'speed_kit', $info )
            ->assign( 'hosting_enabled', $this->plugin->options->get('hosting_enabled') )
            ->render();
    }

    /**
     * Render the help view.
     */
    public function render_help() {
        // Generate the help fields
        $this->view
            ->set_template( 'help.php' )
            ->assign( 'logged_in', $this->plugin->app_name !== null )
            ->assign( 'hosting_enabled', $this->plugin->options->get( 'hosting_enabled' ) )
            ->render();
    }

    /**
     * Checks whether the user is on one of the given Baqend admin pages.
     *
     * @param string[] $pages The pages which should be checked.
     *
     * @return bool True, if user is on one of those pages.
     */
    private function is_on_baqend_admin( array $pages ) {
        global $pagenow;
        $page = $_GET['page'];

        return $pagenow === 'admin.php' && ! in_array( $page, $pages, true );
    }

    /**
     * Adds a submenu to the plugin.
     *
     * @param string $title
     * @param string $slug
     * @param callable $callback
     */
    private function add_submenu($title, $slug, callable $callback) {
        // Add help submenu item
        add_submenu_page(
            'baqend',
            $title,
            $title,
            'manage_options',
            $slug,
            $callback
        );
    }

    /**
     * Generate settings fields by passing an array of data (see the render method).
     *
     * @param array $field_args The array that helps build the settings fields
     * @param array $settings The settings array from the options table
     *
     * @return string The settings fields' HTML to be output in the view
     */
    private function custom_settings_fields($field_args, $settings) {
        $output = '<div class="metabox-holder">';

        foreach ($field_args as $field) {
            $slug    = $field['slug'];
            $setting = $this->plugin->slug.'['.$slug.']';
            $label   = esc_attr__($field['label'], 'baqend');
            $box_pre  ='<div class="postbox"><div class="inside">';
            $output  .= $box_pre.'<table class="form-table"><tr><th><label for="'.$setting.'">'.$label.'</label></th><td>';

            $val = $settings[ $slug ];
            if ($field['type'] === 'choice') {
                $multiple = isset($field['multiple']) ? $field['multiple'] : false;
                $options = implode(array_map(function ($key, $value) use ($val, $multiple) {
                    $isSelected = $multiple ? in_array($key, $val, true) : $val == $key;

                    return '<option value="'.$key.'"'.($isSelected ? ' selected' : '').'>'.$value.'</option>';
                }, array_keys($field['options']), array_values($field['options'])));
                $output  .= '<select id="'.$setting.'" name="'.$setting.($multiple ? '[]' : '').'" class="regular-text"'.($multiple ? ' multiple="multiple"' : '').'>'.$options.'</select>';
            } elseif ($field['type'] === 'textarea') {
                $output .= '<textarea id="'.$setting.'" name="'.$setting.'" rows="10" class="regular-text" style="white-space:nowrap; background:#f1f1f1;">'.$val.'</textarea>';
            } elseif ($field['type'] === 'list') {
                $val    = (array) $val;
                $output .= '<textarea id="'.$setting.'" name="'.$setting.'" rows="10" class="regular-text" wrap="soft" style="font-family:monospace;width:100%;background:#f1f1f1;">'.implode("\n", $val).'</textarea>';
            } elseif ($field['type'] === 'checkbox') {
                $output .= '<label><input name="'.$setting.'" type="hidden" value="false"><input id="'.$setting.'" name="'.$setting.'" type="checkbox" value="true"'.($val ? ' checked' : '').'>'.$field['checkbox_label'].'</label>';
            } elseif ($field['type'] === 'checkboxes') {
                $val    = (array) $val;
                $output .= '<fieldset>';
                foreach ($field['options'] as $key => $value) {
                    $isChecked = in_array($key, $val, true);
                    $output .= '<label><input id="' . $setting . '" name="' . $setting . '[]" type="checkbox" value="' . $key . '"' . ( $isChecked ? ' checked' : '' ) . '>' . $value . '</label><br>';
                }
                $output .= '</fieldset>';
            } else {
                $output .= '<input type="'.$field['type'].'" id="'.$setting.'" name="'.$setting.'" value="'.$val.'" class="regular-text">';
            }

            if (isset($field['help'])) {
                $output .= '<p class="description">'.$field['help'].'</p>';
            }

            $button_options = array( 'id' => $setting, 'style' => 'float: right;');
            $submit_button = get_submit_button( __( 'Save Settings', 'baqend' ), 'primary', 'submit', false, $button_options);
            $box_post = '<div class="submit">'.$submit_button.'</div></div></div>';
            $output .= '</td></tr></table>'.$box_post;
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * @param string[]|null $fields_to_filter Slugs of the fields to display.
     *
     * @return array The fields.
     */
    private function create_fields(array $fields_to_filter = null) {
        $url_example = __('Make sure that you use absolute URLs starting with your domain, like %s/wp-includes/js/wp-emoji-release.min.js', 'baqend');
        $comment_out = __('You can comment out single lines by using a semicolon (“;”)', 'baqend');

        $speed_kit_list_example = sprintf( $url_example, substr( home_url( '', 'https' ), 8 ) );

        $fields                 = [
            [
                'label' => __('Additional URLs to process', 'baqend'),
                'slug'  => 'additional_urls',
                'type'  => 'textarea',
                'help'  => __('Here you can add additional URLs separated by new lines which will also be checked when collecting your blog contents.', 'baqend').'<br/>'.sprintf($url_example, home_url()),
            ],
            [
                'label' => __('Additional files to process', 'baqend'),
                'slug'  => 'additional_files',
                'type'  => 'text',
            ],
            [
                'label' => __('URLs which should be excluded', 'baqend'),
                'slug'  => 'urls_to_exclude',
                'type'  => 'list',
                'help'  => __('When these URLs occur during the content collecting, they will not be uploaded to Baqend.', 'baqend').'<br/>'.sprintf($url_example, home_url()),
            ],
//            [
//                'label' => __('local_dir', 'baqend'),
//                'slug' => 'local_dir',
//                'type' => 'text',
//            ],
//            [
//                'label' => __('delete_temp_files', 'baqend'),
//                'slug' => 'delete_temp_files',
//                'type' => 'text',
//            ],
//            [
//                'label' => __('Relative path', 'baqend'),
//                'slug' => 'relative_path',
//                'type' => 'text',
//            ],

            [
                'label'   => __('URL type to use on Baqend', 'baqend'),
                'slug'    => 'destination_url_type',
                'type'    => 'choice',
                'options' => [
                    'relative' => __('Relative URLs', 'baqend'),
                    'absolute' => __('Absolute URLs', 'baqend'),
                ],
            ],
            [
                'label'   => __('Destination scheme', 'baqend'),
                'slug'    => 'destination_scheme',
                'type'    => 'choice',
                'options' => [
                    'https' => __('Secured (https://, recommended)', 'baqend'),
                    'http'  => __('Unsecured (http://)', 'baqend'),
                ],
            ],
            [
                'label' => __('Destination host', 'baqend'),
                'slug'  => 'destination_host',
                'type'  => 'text',
            ],

            [
                'label' => __('Working directory', 'baqend'),
                'slug'  => 'temp_files_dir',
                'type'  => 'text',
                'help'  => __('This is the working directory in which all files are collected temporarily.', 'baqend'),
            ],

            [
                'label'          => __('Hosting settings', 'baqend'),
                'slug'           => 'hosting_enabled',
                'type'           => 'checkbox',
                'checkbox_label' => __('show tab', 'baqend'),
                'help'           => __('When checked, the Hosting settings tab is shown.', 'baqend'),
            ],

            [
                'label'          => __('Speed Kit Integration', 'baqend'),
                'slug'           => 'speed_kit_enabled',
                'type'           => 'checkbox',
                'checkbox_label' => __('enable', 'baqend'),
                'help'           => __('When checked, the <a href="https://www.baqend.com/speedkit.html" target="_blank">Baqend Speed Kit</a> will be automatically integrated into your WordPress website.', 'baqend'),
            ],
            [
                'label' => __('Whitelist URLs', 'baqend'),
                'slug'  => 'speed_kit_whitelist',
                'type'  => 'list',
                'help'  => __('Only the websites given in this list will be handled by Speed Kit.', 'baqend').'<br/>' . $speed_kit_list_example . '<br/>' . $comment_out,
            ],
            [
                'label' => __('Blacklist URLs', 'baqend'),
                'slug'  => 'speed_kit_blacklist',
                'type'  => 'list',
                'help'  => __('All websites given in this list will be ignored by Speed Kit.', 'baqend').'<br/>' . $speed_kit_list_example . '<br/>' . $comment_out,
            ],
            [
                'label' => __('Bypass cache on cookie', 'baqend'),
                'slug'  => 'speed_kit_cookies',
                'type'  => 'list',
                'help'  => __('If a page contains one of the cookies given in this list, Speed Kit will ignore the given page. The cookies are given as prefixes.', 'baqend').'<br/>'.$comment_out,
            ],
            [
                'label'    => __( 'Allowed content types', 'baqend' ),
                'slug'     => 'speed_kit_content_type',
                'type'     => 'checkboxes',
                'multiple' => true,
                'help'     => __( 'Only the given content types will be handled by Speed Kit.', 'baqend' ),
                'options'  => [
                    AssetFilter::DOCUMENT => __( 'HTML documents <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::IMAGE    => __( 'Images <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::SCRIPT   => __( 'JavaScript files <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::STYLE    => __( 'Style sheets <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::FONT     => __( 'Fonts <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::TRACK    => __( 'Subtitle tracks <em>(recommended)</em>', 'baqend' ),
                    AssetFilter::FEED     => __( 'News feeds', 'baqend' ),
                    AssetFilter::AUDIO    => __( 'Audio files', 'baqend' ),
                    AssetFilter::VIDEO    => __( 'Videos', 'baqend' ),
                ],
            ],
            [
                'label'          => __('Fetch origin interval', 'baqend'),
                'slug'           => 'fetch_origin_interval',
                'type'           => 'choice',
                'help'           => __('An interval in which Speed Kit will call the original document, e.g., to receive its cookies.', 'baqend'),
                'options'  => [
                      -2 => __('automatically retrieve interval', 'baqend').' '.__('(recommended)', 'baqend'),
                      -1 => __('never fetch from origin', 'baqend'),
                       0 => __('always fetch from origin', 'baqend'),
                      10 => __('10 seconds', 'baqend'),
                      30 => __('half a minute', 'baqend'),
                      60 => __('one minute', 'baqend'),
                     300 => __('5 minutes', 'baqend'),
                     600 => __('10 minutes', 'baqend'),
                    1200 => __('20 minutes', 'baqend'),
                    1800 => __('half an hour', 'baqend'),
                    3600 => __('one hour', 'baqend'),
                ],
            ],
            [
                'label'    => __('Automatic update interval', 'baqend'),
                'slug'     => 'speed_kit_update_interval',
                'type'     => 'choice',
                'help'     => __('Automatically triggers a revalidation of Speed Kit. Use this setting to react on changes over time that you are aware of – e.g. recommendation calculations.', 'baqend'),
                'options'  => [
                    'off' => __('off', 'baqend'),
                    'hourly' => __('hourly', 'baqend'),
                    'twicedaily' => __('twice daily', 'baqend'),
                    'daily' => __('daily', 'baqend'),
                    'weekly' => __('weekly', 'baqend'),
                ],
            ],
            [
                'label'    => __('Max Staleness', 'baqend'),
                'slug'     => 'speed_kit_max_staleness',
                'type'     => 'choice',
                'help'     => __('(Advanced, handle with care) The time interval in which the Bloom filter may be stale.', 'baqend'),
                'options'  => [
                    1*15000 => __('quarter a minute', 'baqend'),
                    1*30000 => __('half a minute', 'baqend'),
                    1*60000 => __('one minute', 'baqend').' '.__('(recommended)', 'baqend'),
                    2*60000 => __('2 minutes', 'baqend'),
                    5*60000 => __('5 minutes', 'baqend'),
                    10*60000 => __('10 minutes', 'baqend'),
                    30*60000 => __('half an hour', 'baqend'),
                    60*60000 => __('one hour', 'baqend'),
                ],
            ],
            [
                'label' => __('App Domain', 'baqend'),
                'help'     => __('(Advanced, handle with care) The domain of the Baqend Speed Kit API.', 'baqend'),
                'slug' => 'speed_kit_app_domain',
                'type' => 'text',
            ],
        ];
        if (null === $fields_to_filter) {
            return $fields;
        }

        // Filters only the field to display
        return array_filter($fields, function ($field) use ($fields_to_filter) {
            return in_array($field['slug'], $fields_to_filter, true);
        });
    }

    public function update_cron_hooks(array $changed) {
        $time = time();
        if (isset($changed['speed_kit_update_interval'])) {
            wp_clear_scheduled_hook('cron_revalidate_html');

            $speed_kit_update_interval = $changed['speed_kit_update_interval'];
            if ($speed_kit_update_interval !== 'off') {
                wp_schedule_event($time, $speed_kit_update_interval, 'cron_revalidate_html');
            }
        }
    }

    /**
     * Normalizes a setting based on its field information.
     *
     * @param array $field The field information.
     * @param string $value The value to normalize.
     *
     * @return mixed
     */
    private function normalize_setting( array $field, $value ) {
        $type = $field['type'];

        if ( $type == 'list' ) {
            $value = preg_split( '/\r\n|[\r\n]/', $value );
            return array_filter( $value, function ( $item ) {
                return ! empty( $item );
            } );
        }

        if ( $type == 'checkbox' ) {
            return $value === 'true' ? true : ( $value === 'false' ? false : null );
        }

        return $value;
    }

    /**
     * Checks whether the login info is broken.
     *
     * @return bool
     */
    private function is_login_info_broken() {
        $options = $this->plugin->options;

        if ( ! $options->has( 'app_name' ) ) {
            return false;
        }

        if ( $options->has( 'api_token' ) ) {
            return false;
        }

        return ! $options->has( 'username' ) || ! $options->has( 'password' );
    }
}
