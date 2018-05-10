<?php

namespace Baqend\WordPress\Controller;

use Baqend\SDK\Exception\ResponseException;
use Baqend\SDK\Model\AssetFilter;
use Baqend\SDK\Value\Version;
use Baqend\WordPress\Loader;

/**
 * Class AjaxController created on 17.07.17.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Controller
 */
class AjaxController extends Controller {

    public function register( Loader $loader ) {
        // Register AJAX calls
        $loader->add_ajax( 'setup', [ $this, 'setup' ] );
        $loader->add_ajax( 'fetch_urls', [ $this, 'fetch_urls' ] );
        $loader->add_ajax( 'find_files_to_upload', [ $this, 'find_files_to_upload' ] );
        $loader->add_ajax( 'upload_archive', [ $this, 'upload_archive' ] );
        $loader->add_ajax( 'clean', [ $this, 'clean' ] );
        $loader->add_ajax( 'clean_bucket', [ $this, 'clean_bucket' ] );
        $loader->add_ajax( 'update_speed_kit', [ $this, 'update_speed_kit' ] );
        $loader->add_ajax( 'trigger_speed_kit', [ $this, 'trigger_speed_kit' ] );
        $loader->add_ajax( 'check_plugin_update', [ $this, 'check_plugin_update' ] );
        $loader->add_ajax( 'clear_cron_error', [ $this, 'clear_cron_error' ] );
    }

    /**
     * Sets up the environment for generating static output.
     */
    public function setup() {
        $error = $this->plugin->generator->setup();
        if ( is_wp_error( $error ) ) {
            $this->logger->error( 'setup failed with ' . get_class( $error ) . ': ' . $error->get_error_message(), [ 'exception' => $error ] );
            $this->send_json_response( [
                'setup' => false,
                'error' => $error->get_error_message(),
            ], 500 );

            return;
        }

        $this->send_json_response( [ 'setup' => true ] );
    }

    /**
     * Fetches URLs from the blog to generate a static HTML version of it.
     */
    public function fetch_urls() {
        // Generate the static files
        $error = $this->plugin->generator->process_urls();
        if ( is_wp_error( $error ) ) {
            $this->logger->error( 'fetch_urls failed with ' . get_class( $error ) . ': ' . $error->get_error_message(), [ 'exception' => $error ] );
            $this->send_json_response( [
                'fetch_urls' => false,
                'error'      => $error->get_error_message(),
            ], 500 );

            return;
        }

        $archive_dir = $this->get_archive_dir();

        $this->send_json_response( [
            'fetch_urls'  => true,
            'archive_dir' => $archive_dir,
            'response'    => $error,
        ] );
    }

    /**
     * Uses the archive directory to find changed files which need to be uploaded.
     */
    public function find_files_to_upload() {
        $archive_dir = $this->get_archive_dir();

        try {
            // Upload the files
            $upload_service = $this->plugin->upload_service;
            $updated_files  = $upload_service->find_files_to_upload_from_directory( $archive_dir, 'www' );
            $this->send_json_response( array_values( $updated_files ) );
        } catch ( \Error $e ) {
            $this->logger->error( 'find_files_to_upload failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'type'  => get_class( $e ),
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'code'  => $e->getCode(),
            ], 500 );
        } catch ( \Exception $e ) {
            $this->logger->error( 'find_files_to_upload failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'type'  => get_class( $e ),
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'code'  => $e->getCode(),
            ], 500 );
        }
    }

    /**
     * Uploads files to Baqend within an archive.
     */
    public function upload_archive() {
        try {
            $archive_dir = $this->get_archive_dir();

            $files = $_POST['parameter'];
            $this->plugin->upload_service->upload_files_to_bucket( $files, $archive_dir, 'www' );

            $this->send_json_response( [ 'upload_archive' => true ] );
        } catch ( \Error $e ) {
            $this->logger->error( ' failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'type'  => get_class( $e ),
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'code'  => $e->getCode(),
            ], 500 );
        } catch ( \Exception $e ) {
            $this->logger->error( 'upload_archive failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'type'  => get_class( $e ),
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'code'  => $e->getCode(),
            ], 500 );
        }
    }

    /**
     * Cleans up the archive directory and environment.
     */
    public function clean() {
        // Clean up local files
        $success = $this->plugin->generator->clean();

        $end_time = $this->plugin->env->getFormattedDateTime();
        $this->plugin->options->set( 'archive_end_time', $end_time )->save();

        $this->send_json_response( [ 'clean' => $success ] );
    }

    /**
     * Cleans up the remote bucket on Baqend.
     */
    public function clean_bucket() {
        $success = $this->plugin->upload_service->clean_bucket( 'www' );
        $this->send_json_response( [ 'clean_bucket' => $success ] );
    }

    /**
     * Updates to the latest version of Speed Kit.
     */
    public function update_speed_kit() {
        try {
            // Get updates on Speed Kit
            $sw_path      = $this->plugin->sw_path();
            $snippet_path = $this->plugin->snippet_path();
            $info         = $this->plugin->baqend->getSpeedKit()->createInfo( $sw_path, $snippet_path, '1.X.X' );

            if ( $info->isLatest() ) {
                $this->send_json_response( [
                    'update_speed_kit' => true,
                    'was_latest'       => true,
                    'version'          => $info->getRemoteVersion(),
                ] );

                return;
            }

            // Put updates into the directory
            $this->plugin->io_service->write_file_contents( $snippet_path, $info->getSnippetContent() );
            $this->plugin->io_service->write_file_contents( $sw_path, $info->getSwContent() );

            $this->send_json_response( [
                'update_speed_kit' => true,
                'was_latest'       => false,
                'version'          => $info->getRemoteVersion(),
            ] );
        } catch ( \Exception $e ) {
            $this->logger->error( 'update_speed_kit failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'update_speed_kit' => false,
                'error'            => $e->getMessage(),
            ], 500 );
        }
    }

    /**
     * Triggers a revalidation of Speed Kit.
     */
    public function trigger_speed_kit() {
        $filter = new AssetFilter();
        $filter->addContentType( AssetFilter::DOCUMENT );
        $error = null;
        try {
            $this->plugin->baqend->asset()->revalidate( $filter );
            $success = true;
            $this->logger->debug( 'Manual revalidation succeeded', [ 'filter' => $filter->jsonSerialize() ] );
        } catch ( ResponseException $e ) {
            $response = $e->getResponse()->getBody()->getContents();
            $this->logger->error( 'Manual revalidation failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [
                'exception' => $e,
                'response'  => $response,
            ] );
            $error   = json_decode( $response, true );
            $success = false;
        }
        $this->send_json_response( [ 'trigger_speed_kit' => $success, 'error' => $error ] );
    }

    /**
     * Checks for updates of the WordPress plugin.
     */
    public function check_plugin_update() {
        try {
            $ourVersion    = Version::parse( $this->plugin->version );
            $latestVersion = $this->plugin->baqend->getWordPressPlugin()->getLatestVersion();

            $this->send_json_response(
                [
                    'our'     => $ourVersion->__toString(),
                    'latest'  => $latestVersion->__toString(),
                    'compare' => $ourVersion->compare( $latestVersion ),
                ]
            );
        } catch ( \Exception $e ) {
            $this->logger->error( 'check_plugin_update failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
            $this->send_json_response( [
                'check_plugin_update' => false,
                'error'               => $e->getMessage(),
            ], 500 );
        }
    }

    /**
     * Clears the cron error message.
     */
    public function clear_cron_error() {
        $this->plugin->options->remove( 'cron_error' )->save();
        $this->send_json_response([ 'clear_cron_error' => true ]);
    }

    /**
     * Sends a JSON response to the User.
     *
     * @param array $data
     * @param int $status_code
     */
    private function send_json_response( array $data, $status_code = 200 ) {
        status_header( $status_code );
        header( 'Content-Type: application/json' );
        die( json_encode( $data, 15, 512 ) );
    }

    /**
     * Returns the archive directory location.
     *
     * @return string
     */
    private function get_archive_dir() {
        return trailingslashit( $this->plugin->options->get( 'temp_files_dir' ) . $this->plugin->options->get( 'archive_name' ) );
    }
}
