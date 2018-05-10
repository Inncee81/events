<?php

namespace Baqend\WordPress;

use Baqend\Component\Spider\Asset;
use Baqend\Component\Spider\Downloader\DownloaderException;
use Baqend\Component\Spider\Downloader\DownloaderInterface;

/**
 * Class Downloader created on 2018-03-14.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress
 */
class Downloader implements DownloaderInterface {

    /**
     * @var \WP_Http
     */
    private $client;

    /**
     * Downloader constructor.
     */
    public function __construct() {
        $this->client = \_wp_http_get_object();
    }

    /**
     * Downloads an asset by its URL.
     *
     * @param string $url The URL to download.
     *
     * @return Asset The downloaded asset.
     * @throws DownloaderException When the download did not succeed.
     */
    public function download( $url ) {
        $response = $this->client->get( $url );
        if ( is_wp_error( $response ) ) {
            /** @var \WP_Error $response */
            throw new DownloaderException( $url, new \Exception( $response->get_error_message(), $response->get_error_code() ) );
        }

        $contentType = $this->inferContentType( $response['headers'], $url );

        return new Asset( $url, $response['response']['code'], $contentType, $response['body'] );
    }

    /**
     * @param \Requests_Utility_CaseInsensitiveDictionary|array $headers
     * @param string $url
     *
     * @return null|string
     */
    private function inferContentType( $headers, $url ) {
        foreach ( $headers as $header => $value ) {
            if ( $header === 'content-type' ) {
                return strtolower( $value );
            }
        }

        $ext = substr( $url, strrpos( $url, '.' ) );
        if ( $ext === '.html' ) {
            return 'text/html';
        }

        if ( $ext === '.css' ) {
            return 'text/css';
        }

        if ( $ext === '.js' ) {
            return 'text/javascript';
        }

        return null;
    }
}
