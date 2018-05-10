<?php

namespace Baqend\WordPress\StaticSite;

/**
 * Class StaticSiteGenerator wraps logic to generate static HTML files out of the WordPress blog.
 *
 * @package Baqend\WordPress
 */
interface GeneratorInterface {

    /**
     * Sets up the environment for the generation process.
     *
     * @return null|\WP_Error
     */
    public function setup();

    /**
     * Processes URLs for the generation process.
     *
     * @param int|null $batch_size
     *
     * @return null|\WP_Error
     */
    public function process_urls($batch_size = null);

    /**
     * Cleans up temporary files needed during execution.
     *
     * @return boolean
     */
    public function clean();
}
