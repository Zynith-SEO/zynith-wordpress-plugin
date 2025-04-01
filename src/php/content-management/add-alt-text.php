<?php
defined('ABSPATH') or exit;

function zynith_seo_add_alt_text($post_ID) {
    
    // Only proceed if this is indeed an image.
    if (wp_attachment_is_image($post_ID)) {

        // Get the current ALT text (if any).
        $current_alt = get_post_meta($post_ID, '_wp_attachment_image_alt', true);

        // If there's no ALT text yet, set it based on the (sanitized) title.
        if (empty($current_alt)) {
            
            // Get the attachment post data (the "title" is auto-derived from filename by WordPress).
            $attachment_title = get_the_title($post_ID);
            
            // Replace underscores/hyphens with spaces
            $processed = preg_replace('/[_-]+/', ' ', $attachment_title);

            // Insert a space between a lower/digit and an uppercase letter (e.g., "minecraftSteve" => "minecraft Steve")
            $processed = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $processed);

            // Convert multiple spaces to a single space
            $processed = preg_replace('/\s+/', ' ', $processed);

            // Trim leading/trailing spaces
            $processed = trim($processed);

            // Split into parts (words)
            $parts = explode(' ', $processed);

            // Remove any part that is purely numeric (e.g. "1", "2023", etc.)
            $parts = array_filter($parts, function($part) {
                return ! preg_match('/^\d+$/', $part);
            });

            // Rejoin and convert to Title Case
            $nice_alt = ucwords(implode(' ', $parts));

            // Finally, update the ALT text metadata.
            update_post_meta($post_ID, '_wp_attachment_image_alt', $nice_alt);
        }
    }
}
add_action('add_attachment', 'zynith_seo_add_alt_text');