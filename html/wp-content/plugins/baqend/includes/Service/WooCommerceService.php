<?php

namespace Baqend\WordPress\Service;

use Baqend\Component\Spider\UrlHelper;

/**
 * Class WooCommerceService created on 10.10.17.
 *
 * @author Konstantin Simon Maria Möllers
 * @package Baqend\WordPress\Service
 */
class WooCommerceService {

    /**
     * Checks whether the WooCommerce shop is active.
     *
     * @return bool True, if WooCommerce happens to be active.
     */
    public function is_shop_active() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Checks whether a given page is user-specific in WooCommerce.
     *
     * @param \WP_Post $post The page to check.
     *
     * @return bool True, if the page is user-specific.
     */
    public function is_user_specific_page( \WP_Post $post ) {
        $ignored_pages = $this->get_user_specific_post_IDs();

        $page_id = $post->ID;

        return in_array( $page_id, $ignored_pages, true );
    }

    /**
     * Returns an array of user-specific pathnames used by WooCommerce.
     *
     * @return string[] An array of pathname strings.
     */
    public function get_user_specific_pathnames() {
        return array_map(
            function (\WP_Post $post) {
                $url = get_page_link( $post );

                return UrlHelper::extractPath( $url );
            },
            $this->get_user_specific_posts()
        );
    }

    /**
     * Returns an array of user-specific WordPress posts.
     *
     * @return \WP_Post[] An array of WordPress posts.
     */
    public function get_user_specific_posts() {
        return array_filter(
            array_map(
                function ( $ID ) {
                    return get_post( $ID );
                },
                $this->get_user_specific_post_IDs()
            ),
            function ( \WP_Post $post = null ) {
                return $post !== null;
            }
        );
    }

    /**
     * @return int[]
     */
    private function get_user_specific_post_IDs() {
        $pages = [
            (int) get_option( 'woocommerce_cart_page_id', - 1 ),
            (int) get_option( 'woocommerce_checkout_page_id', - 1 ),
            (int) get_option( 'woocommerce_pay_page_id', - 1 ),
            (int) get_option( 'woocommerce_thanks_page_id', - 1 ),
            (int) get_option( 'woocommerce_myaccount_page_id', - 1 ),
            (int) get_option( 'woocommerce_edit_address_page_id', - 1 ),
            (int) get_option( 'woocommerce_view_order_page_id', - 1 ),
        ];

        // Check if wish list is active
        if ( class_exists('\\WC_Wishlists_Pages') ) {
            $pages[] = (int) \WC_Wishlists_Pages::get_page_id( 'create-a-list' );
            $pages[] = (int) \WC_Wishlists_Pages::get_page_id( 'view-a-list' );
            $pages[] = (int) \WC_Wishlists_Pages::get_page_id( 'find-a-list' );
            $pages[] = (int) \WC_Wishlists_Pages::get_page_id( 'edit-my-list' );
        }

        return array_values(array_filter( $pages, function ($it) {
            return $it !== -1;
        }));
    }
}
