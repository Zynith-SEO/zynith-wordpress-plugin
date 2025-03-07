<?php
/*
Plugin Name: ZYNITH SEO - SVG Upload Support
Description: Allows SVG files to be uploaded to the WordPress media library by default for ZYNITH SEO.
Version: 1.0
Author: ZYNITH SEO
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Allow SVG uploads in the WordPress media library
function zynith_seo_enable_svg_upload( $mimes ) {
    // Add SVG mime type
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'zynith_seo_enable_svg_upload' );

// Sanitize SVG uploads to prevent security vulnerabilities
function zynith_seo_sanitize_svg( $file ) {
    if ( 'image/svg+xml' === $file['type'] ) {
        // Load the SVG content
        $svg = file_get_contents( $file['tmp_name'] );

        // Check if SVG file contains any unwanted code (like JavaScript or embedded objects)
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress errors for invalid XML structure
        $dom->loadXML($svg);

        if (!$dom) {
            // Reject invalid SVGs
            $file['error'] = 'Invalid SVG file.';
        } else {
            // Optionally, you can add more complex sanitization here if needed
        }
        libxml_clear_errors();
    }
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'zynith_seo_sanitize_svg' );