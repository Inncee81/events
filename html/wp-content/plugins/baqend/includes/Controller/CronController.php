<?php

namespace Baqend\WordPress\Controller;

use Baqend\SDK\Model\AssetFilter;
use Baqend\WordPress\Loader;

/**
 * Cron Controller created on 10.08.2017.
 *
 * This controller contains all actions which are triggered by cron
 * jobs from WordPress.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Controller
 */
class CronController extends Controller {

    public function register( Loader $loader ) {
        $loader->add_action( 'cron_revalidate_html', [ $this, 'revalidate_html' ] );
        $loader->add_action( 'cron_update_speed_kit', [ $this, 'update_speed_kit' ] );
    }

    /**
     * This action is triggered by the plugin to revalidate HTML.
     */
    public function revalidate_html() {
        // Revalidate HTML
        $filter = new AssetFilter();
        $filter->addContentType( AssetFilter::DOCUMENT );

        $this->logger->info( 'Revalidating HTML as scheduled', [ 'filter' => $filter ] );

        try {
            $this->plugin->baqend->asset()->revalidate( $filter );
            $this->logger->info( 'Revalidation successful' );
        } catch ( \Exception $e ) {
            $this->logger->error( 'Revalidation failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
        }
        $this->logger->info( 'Next revalidation: ' . date( 'Y-m-d H:i:s', wp_next_scheduled( 'cron_revalidate_html' ) ) );
    }

    /**
     * This action is triggered by the plugin to check the Speed Kit
     * version and update it automatically.
     */
    public function update_speed_kit() {
        $this->logger->info( 'Checking for Speed Kit updates' );

        try {
            $speed_kit  = $this->plugin->baqend->getSpeedKit();
            $io_service = $this->plugin->io_service;

            $sw_path      = $this->plugin->sw_path();
            $snippet_path = $this->plugin->snippet_path();
            $this->logger->debug( 'Found Speed Kit files', [
                'sw'      => $sw_path,
                'snippet' => $snippet_path,
            ] );

            $info = $speed_kit->createInfo( $sw_path, $snippet_path, '1.X.X' );
            if ( $info->isLatest() ) {
                $this->logger->info( 'Speed Kit ' . $info->getLocalVersion() . ' is already the latest version' );

                return;
            }

            $this->logger->debug( 'Updating Speed Kit ' . $info->getLocalVersion() . ' to ' . $info->getRemoteVersion() );

            // Put updates into the directory
            $io_service->write_file_contents( $snippet_path, $info->getSnippetContent() );
            $io_service->write_file_contents( $sw_path, $info->getSwContent() );

            $this->logger->info( 'Successfully updated Speed Kit to ' . $info->getRemoteVersion() );
        } catch ( \Exception $e ) {
            /* translators: %s: Original error message */
            $message = __( 'Could not update Speed Kit: %s', 'baqend' );

            $this->plugin->options->set( 'cron_error', sprintf( $message, $e->getMessage() ) )->save();
            $this->logger->error( 'Update failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
        }
    }
}
