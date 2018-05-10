<?php

namespace Baqend\WordPress\Controller;

use Baqend\SDK\Model\AssetFilter;
use Baqend\WordPress\Loader;
use WP_Post as Post;
use WP_Theme as Theme;
use WP_Widget as Widget;

/**
 * Class TriggerController created on 19.07.17.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Controller
 */
class TriggerController extends Controller {

    const COMMENT_APPROVED = 1;
    const COMMENT_DECLINED = 0;

    public function register( Loader $loader ) {
        $loader->add_action( 'save_post', [ $this, 'handle_post_save' ], 10, 3 );
        $loader->add_action( 'delete_post', [ $this, 'handle_post_delete' ], 10, 1 );
        $loader->add_action( 'comment_post', [ $this, 'handle_comment_post' ], 10, 3 );
        $loader->add_action( 'edit_comment', [ $this, 'handle_comment_edit' ], 10, 2 );
        $loader->add_action( 'delete_comment', [ $this, 'handle_comment_delete' ], 10, 2 );
        $loader->add_action( 'switch_theme', [ $this, 'handle_switch_theme' ], 10, 3 );
        $loader->add_action( 'widget_update_callback', [ $this, 'handle_widget_update_callback' ], 10, 4 );
        $loader->add_action( 'delete_widget', [ $this, 'handle_delete_widget' ], 10, 3 );
        $loader->add_action( 'transition_comment_status', [ $this, 'handle_transition_comment_status' ], 10, 3 );
        $loader->add_action( 'upgrader_process_complete', [ $this, 'upgrader_process_complete' ] );
        $loader->add_action( 'wp_dashboard_setup', [ $this, 'add_revalidation_widget' ] );
    }

    /**
     * @param int $post_id
     * @param Post $post
     * @param boolean $is_update
     */
    public function handle_post_save( $post_id, Post $post, $is_update ) {
        // For updated posts, send a revalidation request to the server
        if ( $is_update ) {
            $this->revalidate_post( $post );
        }
    }

    /**
     * @param int $post_id
     */
    public function handle_post_delete( $post_id ) {
        $post = get_post( $post_id );
        $this->revalidate_post( $post );
    }

    /**
     * @param int $comment_id
     * @param int $comment_approval 1, if comment is approved, 0 otherwise
     * @param array $comment_data
     */
    public function handle_comment_post( $comment_id, $comment_approval, array $comment_data ) {
        $post_id = $comment_data['comment_post_ID'];
        $post    = get_post( $post_id );

        $this->revalidate_post( $post );
    }

    /**
     * @param int $comment_id
     * @param array $comment_data
     */
    public function handle_comment_edit( $comment_id, array $comment_data ) {
        $post_id = $comment_data['comment_post_ID'];
        $post    = get_post( $post_id );

        $this->revalidate_post( $post );
    }

    /**
     * @param int $comment_id
     */
    public function handle_comment_delete( $comment_id ) {
        $comment = get_comment( $comment_id );
        $post_id = $comment->comment_post_ID;
        $post    = get_post( $post_id );

        $this->revalidate_post( $post );
    }

    /**
     * @param int|string $new_status
     * @param int|string $old_status
     * @param object $comment
     */
    public function handle_transition_comment_status( $new_status, $old_status, $comment ) {
        $post_id = $comment->{'comment_post_ID'};
        $post    = get_post( $post_id );

        $this->revalidate_post( $post );
    }

    /**
     * @param string $comment_id
     * @param Theme $new_theme
     * @param Theme $old_theme
     */
    public function handle_switch_theme( $new_name, $new_theme, $old_theme ) {
        $this->revalidate_site( site_url(), 'switch theme' );
    }

    /**
     * @param array $instance
     * @param array $new_instance
     * @param array $old_instance
     * @param Widget $widget
     */
    public function handle_widget_update_callback( $instance, $new_instance, $old_instance, $widget ) {
        $this->revalidate_site( site_url(), 'update widget' );
        return $new_instance;
    }

    /**
     * @param string $widget_id
     * @param string $sidebar_id
     * @param string $id_base
     */
    public function handle_delete_widget( $widget_id, $sidebar_id, $id_base ) {
        $this->revalidate_site( site_url(), 'delete widget' );
    }

    /**
     * Called when WordPress updated this plugin.
     */
    public function upgrader_process_complete() {
        if ( ! $this->plugin->baqend->isConnected() ) {
            return;
        }

        try {
            // Revalidate HTML
            $filter = new AssetFilter();
            $filter->addContentType( AssetFilter::DOCUMENT );

            $this->plugin->baqend->asset()->revalidate( $filter );
            $this->logger->debug( 'Revalidation by plugin update succeeded', [ 'filter' => $filter->jsonSerialize() ] );
        } catch ( \Exception $e ) {
            $this->logger->error( 'Revalidation by plugin update failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
        }
    }

    public function add_revalidation_widget() {
        add_meta_box( 'revalidation_widget', 'Speed Kit', function () {
            $speedKit        = $this->plugin->options->get( 'speed_kit_enabled' );
            $status_content  = $speedKit ? '<span class="enabled">' . __( 'enabled', 'baqend' ) . '</span>' :
                '<span class="disabled">' . __( 'disabled', 'baqend' ) . '</span>';
            $revalidate_info = __( 'Click on the following button to manually revalidate your website with Speed Kit.', 'baqend' );

            echo '<p><strong>Status:</strong> ' . __( 'Speed Kit is currently', 'baqend' ) . ' ' . $status_content .
                 '.</p><form id="form-trigger-speed-kit">' . $revalidate_info . '<div class="submit-wrap" style="text-align:right;">' .
                 '<div class="spinner" style="margin-top:0;float:none;"></div>' .
                 get_submit_button( __( 'Revalidate Website', 'baqend' ), 'primary', 'revalidate', false ) .
                 '</div></form>';
        }, 'dashboard', 'normal', 'high' );
    }

    private function revalidate_site( $site_url, $operation ) {
        // Do not revalidate if not logged in with Baqend
        if ( ! $this->plugin->baqend->isConnected() ) {
            return;
        }

        $filter = new AssetFilter();
        $filter->addPrefix( $site_url );

        try {
            $this->plugin->baqend->asset()->revalidate( $filter );
            $this->logger->debug( 'Revalidation by ' . $operation . ' succeeded', [ 'filter' => $filter->jsonSerialize() ] );
        } catch ( \Exception $e ) {
            $this->logger->error( 'Revalidation by ' . $operation . ' failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
        }
    }

    /**
     * @param Post $post
     */
    private function revalidate_post( Post $post ) {
        // Do not revalidate if not logged in with Baqend
        if ( ! $this->plugin->baqend->isConnected() ) {
            return;
        }

        $filter = new AssetFilter();
        // Invalidate the home URL
        $filter->addPrefix( home_url() );
        // Invalidate the post's permalink
        $filter->addPrefix( get_permalink( $post ) );
        // Invalidate the feed
        $filter->addPrefix( get_feed_link() );
        // Invalidate all archives
        $filter->addPrefixes( $this->get_all_archive_urls() );

        try {
            $this->plugin->baqend->asset()->revalidate( $filter );
            $this->logger->debug( 'Revalidation by post succeeded', [ 'filter' => $filter->jsonSerialize() ] );
        } catch ( \Exception $e ) {
            $this->logger->error( 'Revalidation by post failed with ' . get_class( $e ) . ': ' . $e->getMessage(), [ 'exception' => $e ] );
        }
    }

    /**
     * @return string[]
     */
    private function get_all_archive_urls() {
        $archives_html = wp_get_archives( [ 'echo' => false, 'format' => 'link' ] );
        if ( ! preg_match_all( '#href=\'([^\']+)\'#', $archives_html, $matches ) ) {
            return [];
        }

        list( , $urls ) = $matches;

        return $urls;
    }
}
