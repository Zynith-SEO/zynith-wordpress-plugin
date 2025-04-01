<?php
defined('ABSPATH') or exit;

// Function to set the autosave interval based on the user option
function zynith_seo_set_autosave_interval() {
    $autosave_interval = get_option( 'zynith_seo_autosave_interval', 60 ); // Default to 60 seconds
    
    if ( $autosave_interval == 0 ) {
        // Disable autosave by deregistering the autosave script
        add_action( 'admin_enqueue_scripts', 'zynith_seo_disable_autosave' );
    } elseif ( $autosave_interval >= 10 && $autosave_interval <= 300 ) { // Ensure interval is between 10 and 300 seconds
        add_filter( 'autosave_interval', function() use ( $autosave_interval ) {
            return $autosave_interval;
        });
    }
}

// Function to deregister autosave script when interval is set to 0
function zynith_seo_disable_autosave() {
    wp_deregister_script( 'autosave' );
}

add_action( 'init', 'zynith_seo_set_autosave_interval' );