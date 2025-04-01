<?php
defined('ABSPATH') or exit;

// Disable REST API for unauthenticated users
function zynith_seo_disable_rest_api( $access ) {
    // Allow REST API access only for logged-in users
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_not_logged_in', __( 'You must be logged in to access the REST API.', 'zynith-seo' ), array( 'status' => 401 ) );
    }
    return $access;
}
add_filter( 'rest_authentication_errors', 'zynith_seo_disable_rest_api' );