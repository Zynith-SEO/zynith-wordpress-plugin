<?php
/*
Plugin Name: ZYNITH SEO Heartbeat Optimizer
Description: Optimize the WordPress Heartbeat API frequency.
Version: 1.0
Author: ZYNITH SEO
*/

// Modify the Heartbeat API frequency
function zynith_seo_optimize_heartbeat( $settings ) {
    $frequency = get_option( 'zynith_seo_heartbeat_frequency', 15 ); // Default 15 seconds
    $settings['interval'] = max( intval( $frequency ), 5 ); // Ensure minimum of 5 seconds
    return $settings;
}
add_filter( 'heartbeat_settings', 'zynith_seo_optimize_heartbeat' );