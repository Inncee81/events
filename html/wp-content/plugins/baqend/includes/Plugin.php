<?php

namespace Baqend\WordPress;

use Baqend\SDK\Baqend;
use Baqend\SDK\Client\ClientInterface;
use Baqend\SDK\Exception\ResponseException;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

/**
 * The main plugin class.
 *
 * @property string slug
 * @property string version
 * @property Loader loader
 * @property Logger logger
 * @property Options options
 * @property Baqend baqend
 * @property \Symfony\Component\Serializer\Serializer serializer
 * @property Service\UploadService upload_service
 * @property Service\IOService io_service
 * @property Service\WooCommerceService woo_commerce_service
 * @property StaticSite\GeneratorInterface generator
 * @property Controller\AdminController admin_controller
 * @property Controller\AjaxController ajax_controller
 * @property Controller\CronController cron_controller
 * @property Controller\FrontendController frontend_controller
 * @property Controller\TriggerController trigger_controller
 * @property Env $env
 */
class Plugin {

    const CONTROLLER_TAG = 'baqend.controller';

    /**
     * @var string|null
     */
    public $app_name = null;

    /**
     * @var string
     */
    private $css_dir_url;

    /**
     * @var string
     */
    private $js_dir_url;

    /**
     * @var string
     */
    private $css_dir_path;

    /**
     * @var string
     */
    private $js_dir_path;

    public function __construct() {
        $logger = new Logger( 'baqend', [ new ErrorLogHandler() ] );
        $this->logger = $logger;

        $env       = new Env();
        $this->env = $env;

        $this->slug = Info::SLUG;
        $this->version = Info::VERSION;

        $this->options = new Options($this);
        $this->loader = new Loader($this);
        $this->serializer = Baqend::createSerializer();
        $this->baqend = new Baqend(ClientInterface::WORD_PRESS_TRANSPORT, [], null, $this->serializer);
        $this->generator = new StaticSite\Generator($this);

        // Services
        $this->io_service = new Service\IOService();
        $this->upload_service = new Service\UploadService($this->baqend, $this->io_service);
        $this->woo_commerce_service = new Service\WooCommerceService();

        // Controllers
        $this->admin_controller = new Controller\AdminController($this, $logger);
        $this->loader->add_controller($this->admin_controller);
        $this->ajax_controller = new Controller\AjaxController($this, $logger);
        $this->loader->add_controller($this->ajax_controller);
        $this->frontend_controller = new Controller\FrontendController($this, $logger);
        $this->loader->add_controller($this->frontend_controller);
        $this->trigger_controller = new Controller\TriggerController($this, $logger);
        $this->loader->add_controller($this->trigger_controller);
        $this->cron_controller = new Controller\CronController($this, $logger);
        $this->loader->add_controller($this->cron_controller);

        $this->css_dir_url = plugin_dir_url(__DIR__).'css/';
        $this->js_dir_url  = plugin_dir_url(__DIR__).'js/';
        $this->css_dir_path = plugin_dir_path(__DIR__).'css/';
        $this->js_dir_path  = plugin_dir_path(__DIR__).'js/';

        // Load the text domain for i18n
        $this->loader->add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Register Baqend login and logout handlers
        $this->loader->add_action('plugins_loaded', [$this, 'login']);
        $this->loader->add_action('plugins_loaded', [$this, 'logout']);
        $this->loader->add_action('activated_plugin', [$this, 'activated_plugin'], 10, 1);
    }

    /**
     * Builds a CSS URL for a given file.
     *
     * @param string $css_file
     *
     * @return string
     */
    public function css_url($css_file) {
        return $this->css_dir_url.$css_file;
    }

    /**
     * Builds a JavaScript URL for a given file.
     *
     * @param string $js_file
     *
     * @return string
     */
    public function js_url($js_file) {
        return $this->js_dir_url.$js_file;
    }

    /**
     * Builds a CSS path for a given file.
     *
     * @param string $css_file
     *
     * @return string
     */
    public function css_path($css_file) {
        return $this->css_dir_path.$css_file;
    }

    /**
     * Builds a JavaScript path for a given file.
     *
     * @param string $js_file
     *
     * @return string
     */
    public function js_path($js_file) {
        return $this->js_dir_path.$js_file;
    }

    /**
     * @return string
     */
    public function get_basename() {
        return plugin_basename(dirname(__DIR__) . '/baqend.php');
    }

    /**
     * @return string
     */
    public function get_description() {
        return __('This plugin connects your WordPress website to Baqend and makes it super-fast.', 'baqend');
    }

    /**
     * Runs the plugin.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Sets the default options in the options table on activation.
     */
    public function activate() {
        // Reset the options if they do not exist
        if ($this->options->is_empty()) {
            $this->options->reset();
        }

        // Install the ServiceWorker
        $sw_path = $this->sw_path();
        if (!file_exists($sw_path)) {
            file_put_contents($sw_path, $this->baqend->getSpeedKit()->getServiceWorker());
        }

        // Install the Snippet
        $snippet_path = $this->snippet_path();
        if (!file_exists($snippet_path)) {
            file_put_contents($snippet_path, $this->baqend->getSpeedKit()->getSnippet());
        }

        // Set hook to update Speed Kit
        $recurrence = 'hourly';
        $this->logger->debug( 'Set the update hook', [ 'recurrence' => $recurrence ] );
        wp_schedule_event( time(), $recurrence, 'cron_update_speed_kit' );

        // Try to login with admin email
        $this->register_wp_admin();
    }

    /**
     * Deactivates the plugin.
     */
    public function deactivate() {
        // Clear hook to update Speed Kit
        wp_clear_scheduled_hook('cron_update_speed_kit');
    }

    /**
     * Executed once a plugin is activated.
     *
     * @param string $plugin The plugin which was activated.
     */
    public function activated_plugin( $plugin ) {
        if ($plugin === $this->get_basename()) {
            // Redirect to settings
            exit( wp_redirect( baqend_admin_url( 'baqend#activated' ) ) );
        }
    }

    /**
     * Tries to register the WordPress admin as a Baqend user.
     *
     * @return bool True, if registration was successful.
     */
    public function register_wp_admin() {
        $admin_email = get_option( 'admin_email' );
        if ( $admin_email === false ) {
            return false;
        }

        $this->logger->debug( 'Try to register the WordPress admin', [ 'adminEmail' => $admin_email ] );
        $db = $this->baqend;
        $db->connect( 'bbq' );
        $db->getRestClient()->setAuthorizationToken(null);
        try {
            $result = $db->code()->post( 'wordpressSignup', [ 'adminEmail' => $admin_email ] );
            $this->logger->debug( 'Received message from BBQ', [ 'result' => $result ] );
            if ( $result['success'] !== true ) {
                return false;
            }
        } catch ( ResponseException $e ) {
            return false;
        }

        $db->disconnect();
        $this->options->set( 'bbq_username', $result['bbqUsername'] )->save();
        $this->options->set( 'bbq_password', $result['bbqPassword'] )->save();
        $app_name = $this->login_user( $result['bbqAppName'], $result['wordpressUsername'], $result['wordpressPassword'] );

        return $app_name !== null;
    }

    /**
     * Logs WordPress in to Baqend using a POST request or values from the settings.
     *
     * This method will be executed when the plugin is loaded.
     * It performs a request against Baqend to either login or validate the saved user token.
     */
    public function login() {
        $db       = $this->baqend;
        $app_name = null;

        if ( ! $this->options->has('username') ) {
            $username = get_option( 'admin_email' );
            $this->options->set( 'username', $username );
            $this->options->save();
        }

        if ( array_key_exists( 'bq-app-name', $_POST ) ) {
            // Login using post data
            $app_name = $this->login_user( $_POST['bq-app-name'], $_POST['bq-username'], $_POST['bq-password'] );
        } elseif ( $this->options->has( 'app_name' ) && $this->options->has( 'authorization' ) ) {
            // Login from options setting
            $app_name      = $this->options->get( 'app_name' );
            $authorization = $this->options->get( 'authorization' );

            $db->connect( $app_name );
            $db->getRestClient()->setAuthorizationToken( $authorization );
        } elseif ( $this->options->has( 'app_name' ) && $this->options->has( 'api_token' ) ) {
            // Login from API token setting
            $app_name      = $this->options->get( 'app_name' );
            $api_token = $this->options->get( 'api_token' );

            $db->connect( $app_name );
            $db->getRestClient()->setAuthorizationToken( $api_token );
        }

        // Set invalid token handler
        if ( $db->isConnected() && $db->getRestClient()->isAuthorized() ) {
            $this->app_name = $app_name;
            if ( $this->options->has( 'username' ) && $this->options->has( 'password' ) ) {
                $db->getRestClient()->setInvalidTokenHandler( function () use ( $db ) {
                    // Login user again
                    $username = $this->options->get( 'username' );
                    $password = $this->options->get( 'password' );
                    $db->getRestClient()->setAuthorizationToken( null );
                    $db->user()->login( $username, $password );

                    // Update token in settings
                    $this->options->set( 'authorization', $db->getRestClient()->getAuthorizationToken() );
                    $this->options->save();
                } );
            } elseif ( $this->options->has( 'api_token' ) ) {
                $db->getRestClient()->setInvalidTokenHandler( function () use ( $db ) {
                    // Set token again
                    $api_token = $this->options->get( 'api_token' );
                    $db->getRestClient()->setAuthorizationToken( $api_token );

                    // Update token in settings
                    $this->options->set( 'authorization', $db->getRestClient()->getAuthorizationToken() );
                    $this->options->save();
                } );
            }
        }
    }

    /**
     * Logs a user into his app.
     *
     * @param string $app_name The app name to login to.
     * @param string $username The username to use on that app.
     * @param string $password The user's password on that app.
     *
     * @return null|string The logged in app's name or null, if login failed.
     */
    public function login_user( $app_name, $username, $password ) {
        $this->baqend->connect( $app_name );
        try {
            $this->baqend->user()->login( $username, $password );
        } catch ( ResponseException $e ) {
            return null;
        }

        // Save app to options
        if ( $token = $this->baqend->getRestClient()->getAuthorizationToken() ) {
            $this->options->set( 'app_name', $app_name );
            $this->options->set( 'username', $username );
            $this->options->set( 'password', $password );
            $this->options->set( 'destination_scheme', 'https' );
            $this->options->set( 'destination_host', $app_name . '.app.baqend.com' );
            $this->options->set( 'authorization', $token );
            $this->options->set( 'speed_kit_enabled', true );
            $this->options->save();

            return $app_name;
        }

        return null;
    }

    /**
     * Logs WordPress out from Baqend using a POST request.
     *
     * This method will be executed when the plugin is loaded.
     */
    public function logout() {
        $db = $this->baqend;
        if ($db->isConnected() && array_key_exists('logout', $_POST) && $_POST['logout'] === 'true') {
            // Execute logout request
            $this->did_logout();
            $this->app_name = null;
            $this->options->remove( 'app_name' );
            $this->options->remove( 'authorization' );
            $this->options->remove( 'bbq_password' );
            $this->options->save();
        }
    }

    /**
     * Loads the plugin language files.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            Info::SLUG,
            false,
            dirname(dirname(plugin_basename(__FILE__))).'/languages/'
        );
    }

    /**
     * Returns the Service Worker's absolute pathname.
     *
     * @return string
     */
    public function sw_path() {
        return trailingslashit( ABSPATH ) . 'sw.js';
    }

    /**
     * Returns the Service Worker's absolute URL.
     *
     * @return string
     */
    public function sw_url() {
        return site_url( 'sw.js' );
    }

    /**
     * Returns the snippet's absolute pathname.
     *
     * @return string
     */
    public function snippet_path() {
        return $this->js_path( 'snippet.js' );
    }

    /**
     * Returns the snippet's absolute URL.
     *
     * @return string
     */
    public function snippet_url() {
        return $this->js_url( 'snippet.js' );
    }

    /**
     * @return bool
     */
    private function did_logout() {
        try {
            return $this->baqend->user()->logout();
        } catch ( \Exception $e ) {
            return true;
        }
    }

}
