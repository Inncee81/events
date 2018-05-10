<?php

namespace Baqend\WordPress\StaticSite;

use Baqend\Component\Spider\Processor\CssProcessor;
use Baqend\Component\Spider\Processor\HtmlProcessor;
use Baqend\Component\Spider\Processor\Processor;
use Baqend\Component\Spider\Processor\ProcessorInterface;
use Baqend\Component\Spider\Processor\ReplaceProcessor;
use Baqend\Component\Spider\Processor\StoreProcessor;
use Baqend\Component\Spider\Processor\UrlRewriteProcessor;
use Baqend\Component\Spider\Queue\BreadthQueue;
use Baqend\Component\Spider\Spider;
use Baqend\Component\Spider\UrlHandler\BlacklistUrlHandler;
use Baqend\Component\Spider\UrlHandler\OriginUrlHandler;
use Baqend\Component\Spider\UrlHandler\UrlHandlerInterface;
use Baqend\WordPress\Downloader;
use Baqend\WordPress\Plugin;

/**
 * Class Generator created on 2018-03-14.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\StaticSite
 */
class Generator implements GeneratorInterface {

    const DEFAULT_BATCH_SIZE = 10;

    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * NewGenerator constructor.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
    }

    /**
     * Sets up the environment for the generation process.
     *
     * @return null|\WP_Error
     */
    public function setup() {
        global $blog_id;
        $archive_name = join( '-', [ $this->plugin->slug, $blog_id, time() ] );

        $this->plugin->options
            ->set( 'archive_status_messages', [] )
            ->set( 'archive_name', $archive_name )
            ->set( 'archive_start_time', $this->plugin->env->getFormattedDateTime() )
            ->set( 'archive_end_time', null )
            ->save();

        $archive_dir = $this->plugin->options->get_archive_dir();

        // Create temp archive directory
        if ( ! is_dir( $archive_dir ) ) {
            $this->plugin->logger->debug( 'Creating archive directory: ' . $archive_dir );
            $create_dir = wp_mkdir_p( $archive_dir );
            if ( $create_dir === false ) {
                return new \WP_Error( 'cannot_create_archive_dir' );
            }
        }

        return null;
    }

    /**
     * Processes URLs for the generation process.
     *
     * @param int|null $batch_size
     *
     * @return null|\WP_Error
     */
    public function process_urls( $batch_size = null ) {
        $handler   = $this->create_url_handler();
        $processor = $this->create_processor();

        $spider = new Spider( new BreadthQueue(), new Downloader(), $handler, $processor );

        // Add all URLs to the queue
        $this->queue_urls( $spider );

        $spider->crawl();

        return null;
    }

    /**
     * Cleans up temporary files needed during execution.
     *
     * @return boolean
     */
    public function clean() {
        if ( $this->plugin->options->get( 'delete_temp_files' ) === '1' ) {
            $this->plugin->logger->debug( 'Deleting temporary files' );

            return $this->delete_temp_static_files();
        }

        $this->plugin->logger->debug( 'Keeping temporary files' );

        return true;
    }

    /**
     * Queues all the URLs which should be crawled.
     *
     * @param Spider $spider The spider which provides the queue.
     */
    private function queue_urls( Spider $spider ) {
        $origin = $this->plugin->env->origin();

        // Add homepage
        $homepage_url = trailingslashit( get_home_url() );
        $this->plugin->logger->debug( 'Adding origin URL to queue: ' . $homepage_url );
        $spider->queue( $homepage_url );

        // Add additional URLs
        $additional_urls = $this->plugin->options->get( 'additional_urls' );
        $urls            = array_unique( self::string_to_array( $additional_urls ) );
        foreach ( $urls as $url ) {
            if ( $url[0] === '/' ) {
                $spider->queue( $origin . $url );
            }

            $this->plugin->logger->debug( 'Adding additional URL to queue: ' . $url );
        }
    }

    /**
     * Delete temporary, generated static files.
     *
     * @return boolean True, if deletion was successful.
     */
    public function delete_temp_static_files() {
        // Check archive exists
        $archive_dir = $this->plugin->options->get_archive_dir();
        if ( ! is_dir( $archive_dir ) ) {
            return false;
        }

        $directory_iterator = new \RecursiveDirectoryIterator( $archive_dir, \FilesystemIterator::SKIP_DOTS );
        $recursive_iterator = new \RecursiveIteratorIterator( $directory_iterator, \RecursiveIteratorIterator::CHILD_FIRST );

        // recurse through the entire directory and delete all files / subdirectories
        foreach ( $recursive_iterator as $item ) {
            $success = $item->isDir() ? rmdir( $item ) : unlink( $item );
            if ( ! $success ) {
                return false;
            }
        }

        // must make sure to delete the original directory at the end
        return rmdir( $archive_dir );
    }

    /**
     * Converts a textarea value an array of lines.
     *
     * @param  string $textarea Textarea to convert
     *
     * @return string[] Lines of that textarea
     */
    public static function string_to_array( $textarea ) {
        $lines = preg_split( "/\r\n|[\r\n]/", $textarea );
        array_walk( $lines, 'trim' );
        $lines = array_filter( $lines );

        return $lines;
    }

    /**
     * @return UrlHandlerInterface
     */
    private function create_url_handler() {
        $origin  = $this->plugin->env->origin();
        $handler = null;

        $blacklist   = (array) $this->plugin->options->get( 'urls_to_exclude' );
        $blacklist[] = $origin . '/*.php';
        $blacklist[] = $origin . '/**.php';
        $handler     = new BlacklistUrlHandler( $blacklist, $handler );

        $handler = new OriginUrlHandler( $origin, $handler );

        return $handler;
    }

    /**
     * @return ProcessorInterface
     */
    private function create_processor() {
        $sourceOrigin = $this->plugin->env->origin();
        $destOrigin   = $this->plugin->options->get_destination_origin();
        $workingDir   = $this->plugin->options->get_archive_dir();

        $processor = new Processor();
        $processor->addProcessor( new UrlRewriteProcessor( $sourceOrigin, $destOrigin ) );
        $processor->addProcessor( $cssProcessor = new CssProcessor() );
        $processor->addProcessor( new HtmlProcessor( $cssProcessor ) );
        $processor->addProcessor( new ReplaceProcessor( $sourceOrigin, $destOrigin ) );
        $processor->addProcessor( new StoreProcessor( $destOrigin, $workingDir ) );

        return $processor;
    }
}
