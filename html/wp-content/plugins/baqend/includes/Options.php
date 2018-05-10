<?php

namespace Baqend\WordPress;

use Baqend\SDK\Model\AssetFilter;
use Baqend\SDK\Value\Version;

/**
 * A class representing options of the plugin.
 *
 * Created on 2017-06-20.
 *
 * @author Konstantin Simon Maria Möllers
 */
class Options {

    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * @var array
     */
    private $options;

    /**
     * Options constructor.
     *
     * @param Plugin $plugin
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin  = $plugin;
        $this->options = $this->load_options();
        $this->init();
        $this->upgrade();
    }

    public function init() {
        $host   = $this->plugin->env->host();
        $scheme = $this->plugin->env->scheme();

        $this->set_default( 'additional_files', '' )
             ->set_default( 'additional_urls', includes_url( 'js/wp-emoji-release.min.js' ) )
             ->set_default( 'app_name', null )
             ->set_default( 'archive_end_time', null )
             ->set_default( 'archive_name', null )
             ->set_default( 'archive_start_time', null )
             ->set_default( 'archive_status_messages', [] )
             ->set_default( 'debugging_mode', '0' )
             ->set_default( 'delete_temp_files', '1' )
             ->set_default( 'delivery_method', 'zip' )
             ->set_default( 'destination_host', $host )
             ->set_default( 'destination_scheme', $scheme )
             ->set_default( 'destination_url_type', 'relative' )
             ->set_default( 'hosting_enabled', false )
             ->set_default( 'http_basic_auth_digest', null )
             ->set_default( 'local_dir', '' )
             ->set_default( 'password', null )
             ->set_default( 'relative_path', '' )
             ->set_default( 'speed_kit_enabled', false )
             ->set_default( 'speed_kit_whitelist', [
                 $host,
                 'fonts.googleapis.com',
                 'fonts.gstatic.com',
                 'maxcdn.bootstrapcdn.com',
             ] )
             ->set_default( 'speed_kit_blacklist', [
                 $host . '/wp-login',
                 $host . '/wp-admin',
                 $host . '/wp-content/plugins/baqend',
             ] )
             ->set_default( 'speed_kit_cookies', [
                 'wordpress_logged_in',
                 'twostep_auth',
                 'comment_',
                 'woocommerce_',
             ] )
             ->set_default( 'speed_kit_content_type', [
                 AssetFilter::DOCUMENT,
                 AssetFilter::STYLE,
                 AssetFilter::IMAGE,
                 AssetFilter::SCRIPT,
                 AssetFilter::TRACK,
                 AssetFilter::FONT,
             ] )
             ->set_default( 'fetch_origin_interval', - 2 )
             ->set_default( 'speed_kit_update_interval', 'weekly' )
             ->set_default( 'speed_kit_max_staleness', 60000 )
             ->set_default( 'speed_kit_app_domain', '' )
             ->set_default( 'temp_files_dir', trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'static-files' ) )
             ->set_default( 'urls_to_exclude', [] )
             ->set_default( 'username', null )
             ->set_default( 'version', $this->plugin->version )
             ->save();
    }

    /**
     * Upgrades the plugin's options to the newer version.
     */
    public function upgrade() {
        $version        = Version::parse( $this->get( 'version' ) );
        $plugin_version = Version::parse( $this->plugin->version );

        // Is the installed version up to date? Do not upgrade
        if ( $version->greaterThanOrEqualTo( $plugin_version ) ) {
            return;
        }

        // Upgrade to 1.6.0
        if ( $version->lessThan( Version::fromValues( 1, 6 ) ) ) {
            $this->upgrade_to_1_6_0();
        }

        $this->set( 'version', $plugin_version->__toString() )->save();
        $this->plugin->logger->info( 'Upgraded to ' . $plugin_version->__toString() . ' successfully.' );
    }

    /**
     * Returns whether the value exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has( $name ) {
        return array_key_exists( $name, $this->options ) && null !== $this->options[ $name ];
    }

    /**
     * Returns an option value.
     *
     * @param string $name Name of the option.
     *
     * @return mixed
     */
    public function get( $name ) {
        return $this->options[ $name ];
    }

    /**
     * Returns an option value.
     *
     * @param string $name Name of the option.
     * @param mixed $value The new option value.
     *
     * @return Options
     */
    public function set( $name, $value ) {
        $this->options[ $name ] = $value;

        return $this;
    }

    /**
     * Sets the default option value.
     *
     * @param string $name Name of the option.
     * @param mixed $value The default option value.
     *
     * @return Options
     */
    public function set_default( $name, $value ) {
        if ( ! $this->has( $name ) ) {
            $this->options[ $name ] = $value;
        }

        return $this;
    }

    /**
     * Removes a value.
     *
     * @param string $name
     *
     * @return Options
     */
    public function remove( $name ) {
        if ( array_key_exists( $name, $this->options ) ) {
            unset( $this->options[ $name ] );
        }

        return $this;
    }

    /**
     * Saves options.
     *
     * @return bool
     */
    public function save() {
        return $this->update_options();
    }

    /**
     * @return bool
     */
    public function is_empty() {
        return empty( $this->options );
    }

    /**
     * @return array
     */
    public function all() {
        return $this->options;
    }

    /**
     * Resets the options.
     *
     * @return $this
     */
    public function reset() {
        $this->options = [];

        return $this;
    }

    /**
     * Sets all given values.
     *
     * @param array $options
     */
    public function set_all( array $options ) {
        foreach ( $options as $k => $v ) {
            $this->set( $k, $v );
        }
    }

    /**
     * Get the current path to the temp static archive directory
     * @return string The path to the temp static archive directory
     */
    public function get_archive_dir() {
        return $this->get( 'temp_files_dir' ) . $this->get( 'archive_name' );
    }

    /**
     * Get the destination URL (scheme + host)
     * @return string The destination URL
     */
    public function get_destination_origin() {
        return $this->get( 'destination_scheme' ) . '://' . $this->get( 'destination_host' );
    }

    /**
     * @return mixed
     */
    private function load_options() {
        return get_option( $this->plugin->slug, [] );
    }

    /**
     * @return bool
     */
    private function update_options() {
        return update_option( $this->plugin->slug, $this->options );
    }

    /**
     * Upgrade to 1.6.0 where cookies will be replaced and the whitelist will be extended.
     */
    private function upgrade_to_1_6_0() {
        // Extend the cookies and delete wp-
        $cookies = $this->get( 'speed_kit_cookies' );

        $cleaned_cookies = array_diff( $cookies, [ 'wp-' ] );
        $added_cookies   = $this->add_to_array( $cleaned_cookies, [
            'wordpress_logged_in',
            'twostep_auth',
        ] );

        $this->set( 'speed_kit_cookies', $added_cookies );

        // Extend the whitelist
        $whitelist = $this->get( 'speed_kit_whitelist' );

        $extended_whitelist = $this->add_to_array( $whitelist, [
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'maxcdn.bootstrapcdn.com',
        ] );

        $this->set( 'speed_kit_whitelist', $extended_whitelist );
    }

    /**
     * Add elements to an array if they are not included yet.
     *
     * @param string[] $array The array to add elements to.
     * @param string[] $to_add The elements to add.
     *
     * @return string[] The manipulated array.
     */
    private function add_to_array( array $array, array $to_add ) {
        foreach ( $to_add as $item ) {
            if ( ! in_array( $item, $array, true ) ) {
                $array[] = $item;
            }
        }

        return $array;
    }
}
