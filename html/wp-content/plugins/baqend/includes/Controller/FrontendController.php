<?php

namespace Baqend\WordPress\Controller;

use Baqend\WordPress\Loader;

/**
 * Class Frontend created on 17.07.17.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Controller
 */
class FrontendController extends Controller {

    public function register( Loader $loader ) {
        $loader->add_action( 'wp_head', [ $this, 'head' ], 1 );
        $loader->add_action( 'admin_head', [ $this, 'head' ], 1 );
        $loader->add_action( 'send_headers', [ $this, 'send_headers' ] );
    }

    /**
     * Is executed when WordPress is enqueueing scripts for the website.
     */
    public function head() {
        $app_name = $this->plugin->options->get( 'app_name' );
        if ( empty( $app_name ) ) {
            return;
        }

        $sw_url = $this->get_service_worker_url();
        $config = [
            'appName'  => $app_name,
            'sw'       => $sw_url,
            'scope'    => $this->scope_of_service_worker( $sw_url ),
            'disabled' => is_user_logged_in() || ! $this->plugin->options->get( 'speed_kit_enabled' ),
        ];

        $whitelist_option = $this->plugin->options->get( 'speed_kit_whitelist' );
        if ( ! empty( $whitelist_option ) ) {
            if ( ! isset( $config['whitelist'] ) ) {
                $config['whitelist'] = [ [] ];
            }
            $config['whitelist'][0]['url'] = $this->strip_comments( $whitelist_option );
        }

        $content_types_option = $this->plugin->options->get( 'speed_kit_content_type' );
        if ( ! empty( $content_types_option ) ) {
            if ( ! isset( $config['whitelist'] ) ) {
                $config['whitelist'] = [ [] ];
            }
            $config['whitelist'][0]['contentType'] = $content_types_option;
        }

        $blacklist_option = $this->plugin->options->get( 'speed_kit_blacklist' );
        if ( ! empty( $blacklist_option ) ) {
            if ( ! isset( $config['blacklist'] ) ) {
                $config['blacklist'] = [];
            }

            $config['blacklist'][] = [ 'url' => $this->strip_comments( $blacklist_option ) ];
        }

        $woo_commerce_blacklist = $this->get_woo_commerce_blacklist();
        if ( ! empty( $woo_commerce_blacklist ) ) {
            if ( ! isset( $config['blacklist'] ) ) {
                $config['blacklist'] = [];
            }

            $config['blacklist'][] = $woo_commerce_blacklist;
        }

        $cookies_option = $this->plugin->options->get( 'speed_kit_cookies' );
        if ( ! empty( $cookies_option ) ) {
            if ( ! isset( $config['blacklist'] ) ) {
                $config['blacklist'] = [];
            }

            $config['blacklist'][] = [ 'cookie' => $this->strip_comments( $cookies_option ) ];
        }

        $max_staleness_option = $this->plugin->options->get( 'speed_kit_max_staleness' );
        if ( 60000 !== (int) $max_staleness_option ) {
            $config['maxStaleness'] = (int) $max_staleness_option;
        }

        $app_domain_option = $this->plugin->options->get( 'speed_kit_app_domain' );
        if ( ! empty( $app_domain_option ) ) {
            $config['appDomain'] = $app_domain_option;
        }

        $fetch_origin_interval_option = (int) $this->plugin->options->get( 'fetch_origin_interval' );
        if ( - 1 !== $fetch_origin_interval_option ) {
            // Retrieve setting from PHP settings
            if ( - 2 === $fetch_origin_interval_option ) {
                $session_max_lifetime = ini_get( 'session.gc_maxlifetime' );
                if ( is_numeric( $session_max_lifetime ) ) {
                    $fetch_origin_interval_option = (int) $session_max_lifetime;
                } else {
                    $fetch_origin_interval_option = 1200;
                }
            }

            $config['fetchOriginInterval'] = $fetch_origin_interval_option;
        }

        $config  = json_encode( $config );
        $snippet = file_get_contents( $this->plugin->snippet_path() );
        $snippet = str_replace( ',"undefined"!=typeof speedKit?speedKit:config)', ',' . $config . ')', $snippet );
        $snippet = str_replace( ', typeof speedKit !== \'undefined\' ? speedKit : config)', ',' . $config . ')', $snippet );
        $snippet = str_replace( ',\'undefined\'==typeof speedKit?config:speedKit)', ',' . $config . ')', $snippet );
        $snippet = str_replace( ',config)', ',' . $config . ')', $snippet );
        echo <<<HTML
<!-- Baqend Speed Kit -->
<script type="application/javascript">$snippet</script>

HTML;
    }

    /**
     * Is executed when WordPress wants to set HTTP response headers.
     */
    public function send_headers() {
        if ( headers_sent() || ! $this->plugin->baqend->isConnected() ) {
            return;
        }

        $swUrl = $this->get_service_worker_url();
        header( 'link: <' . $swUrl . '>; rel=preload; as=serviceworker; crossorigin', false );
        header( 'link: <' . $swUrl . '>; rel=serviceworker; scope="/"', false );
    }

    /**
     * @return string
     */
    private function get_service_worker_url() {
        return $this->plugin->sw_url();
    }

    /**
     * @param string $sw_url
     *
     * @return string
     */
    private function scope_of_service_worker( $sw_url ) {
        $url = parse_url( $sw_url );

        return trailingslashit( dirname( $url['path'] ) );
    }

    /**
     * @return array
     */
    private function get_woo_commerce_blacklist() {
        if ( ! $this->plugin->woo_commerce_service->is_shop_active() ) {
            return [];
        }

        $pathname = $this->plugin->woo_commerce_service->get_user_specific_pathnames();

        return [ 'pathname' => $pathname, 'contentType' => [ 'document' ] ];
    }

    /**
     * @param string[] $options
     *
     * @return string[]
     */
    private function strip_comments( array $options ) {
        return array_values( array_filter(
            array_map(
                function ( $option ) {
                    $index = strpos( $option, ';' );
                    if ( $index !== false ) {
                        $option = substr( $option, 0, $index );
                    }

                    return trim( $option );
                },
                $options
            ),
            function ( $option ) {
                return ! empty( $option );
            }
        ) );
    }
}
