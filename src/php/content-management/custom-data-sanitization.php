<?php
defined('ABSPATH') or exit;

/**
 * Custom sanitization function that preserves placeholders and allows a wide range of symbols.
 *
 * @param string $input The input string to sanitize.
 * @return string The sanitized string with placeholders preserved.
 */
function zynith_seo_custom_text_placeholder_sanitize($input) {
    // Find all placeholders (e.g., %%home_url%%)
    preg_match_all('/%%[a-zA-Z0-9_]+%%/', $input, $matches);
    $placeholders = $matches[0];
    
    // Replace placeholders with unique temporary tokens
    $temp_tokens = [];
    foreach ($placeholders as $index => $placeholder) {
        $token = '__PLACEHOLDER_' . $index . '__';
        $temp_tokens[$token] = $placeholder;
        $input = str_replace($placeholder, $token, $input);
    }
    
    // Sanitize the input using sanitize_text_field()
    $sanitized = sanitize_text_field($input);
    
    // Restore the placeholders
    foreach ($temp_tokens as $token => $placeholder) $sanitized = str_replace($token, $placeholder, $sanitized);
    
    return $sanitized;
}