<?php

if (!defined('ABSPATH')) exit;

// Add the Deferment Manager submenu
function zynith_seo_deferment_manager_menu() {
    add_submenu_page(
        'zynith_seo_dashboard',
        'Deferment Manager',
        'Deferment Manager',
        'manage_options',
        'zynith-seo-deferment-manager',
        'zynith_seo_render_deferment_manager_page'
    );
}
add_action('admin_menu', 'zynith_seo_deferment_manager_menu');

// Render the Deferment Manager admin page
function zynith_seo_render_deferment_manager_page() {
    global $wp_scripts;

    // Load current settings
    $defer_settings = get_option('zynith_deferment_settings', []);
    $detected_scripts = get_option('zynith_detected_scripts', []);
    $global_settings = get_option('zynith_global_settings', [
        'lazy_loading' => 'disabled',
        'critical_preloading' => [],
        'media_lcp_optimization' => 'disabled',
    ]);

    // Save settings if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('zynith_save_settings', 'zynith_nonce');

        // Save global settings
        $global_settings['lazy_loading'] = isset($_POST['global_lazy_loading']) ? 'enabled' : 'disabled';
        $global_settings['critical_preloading'] = array_map('sanitize_text_field', explode("\n", $_POST['global_critical_preloading'] ?? ''));
        $global_settings['media_lcp_optimization'] = isset($_POST['global_media_lcp_optimization']) ? 'enabled' : 'disabled';
        update_option('zynith_global_settings', $global_settings);

        // Save deferment settings
        $defer_settings = array_map('sanitize_text_field', $_POST['zynith_deferment_settings'] ?? []);
        update_option('zynith_deferment_settings', $defer_settings);
    }

    echo '<div class="wrap">';
    echo '<h1>' . __('Zynith SEO JS Deferment Manager', 'zynith-seo') . '</h1>';
    echo '<form method="post">';
    wp_nonce_field('zynith_save_settings', 'zynith_nonce');

    // Global Settings Section
    echo '<h2>' . __('Global Settings', 'zynith-seo') . '</h2>';
    echo '<table class="form-table">';

    // Lazy Loading Toggle
    echo '<tr><th>' . __('Lazy Loading', 'zynith-seo') . '</th><td>';
    echo '<label class="zynith-toggle-switch">';
    echo '<input type="checkbox" name="global_lazy_loading" ' . checked($global_settings['lazy_loading'], 'enabled', false) . ' />';
    echo '<span class="zynith-slider"></span>';
    echo '</label>';
    echo __('Enable Lazy Loading', 'zynith-seo');
    echo '</td></tr>';

    // Critical Script Preloading
    echo '<tr><th>' . __('Critical Script Preloading', 'zynith-seo') . '</th><td>';
    echo '<textarea name="global_critical_preloading" rows="3" cols="50" placeholder="Add script URLs, one per line">' . implode("\n", $global_settings['critical_preloading']) . '</textarea>';
    echo '<p class="description">' . __('List script URLs to preload for critical functionality.', 'zynith-seo') . '</p>';
    echo '</td></tr>';

    // Media LCP Optimization Toggle
    echo '<tr><th>' . __('Media LCP Optimization', 'zynith-seo') . '</th><td>';
    echo '<label class="zynith-toggle-switch">';
    echo '<input type="checkbox" name="global_media_lcp_optimization" ' . checked($global_settings['media_lcp_optimization'], 'enabled', false) . ' />';
    echo '<span class="zynith-slider"></span>';
    echo '</label>';
    echo __('Enable Media LCP Optimization', 'zynith-seo');
    echo '</td></tr>';
    echo '</table>';

    // WordPress-Enqueued Scripts Section
    echo '<h2>' . __('WordPress-Enqueued Scripts', 'zynith-seo') . '</h2>';
    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead><tr><th>' . __('Script Handle', 'zynith-seo') . '</th><th>' . __('Current Setting', 'zynith-seo') . '</th><th>' . __('Change Setting', 'zynith-seo') . '</th></tr></thead>';
    echo '<tbody>';

    foreach ($wp_scripts->queue as $handle) {
        $src = $wp_scripts->registered[$handle]->src ?? $handle;
        $setting = $defer_settings[$src] ?? 'none';
        echo '<tr>';
        echo '<td>' . esc_html($handle) . '</td>';
        echo '<td>' . esc_html(ucfirst($setting)) . '</td>';
        echo '<td>';
        echo '<select name="zynith_deferment_settings[' . esc_attr($src) . ']">';
        echo '<option value="none"' . selected($setting, 'none', false) . '>None</option>';
        echo '<option value="defer"' . selected($setting, 'defer', false) . '>Defer</option>';
        echo '<option value="async"' . selected($setting, 'async', false) . '>Async</option>';
        echo '</select>';
        echo '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // HTML-Detected Scripts Section
    echo '<h2>' . __('HTML-Detected Scripts', 'zynith-seo') . '</h2>';
    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead><tr><th>' . __('Script Source', 'zynith-seo') . '</th><th>' . __('Current Setting', 'zynith-seo') . '</th><th>' . __('Change Setting', 'zynith-seo') . '</th></tr></thead>';
    echo '<tbody>';

    foreach ($detected_scripts as $script_src) {
        $setting = $defer_settings[$script_src] ?? 'none';
        echo '<tr>';
        echo '<td>' . esc_html($script_src) . '</td>';
        echo '<td>' . esc_html(ucfirst($setting)) . '</td>';
        echo '<td>';
        echo '<select name="zynith_deferment_settings[' . esc_attr($script_src) . ']">';
        echo '<option value="none"' . selected($setting, 'none', false) . '>None</option>';
        echo '<option value="defer"' . selected($setting, 'defer', false) . '>Defer</option>';
        echo '<option value="async"' . selected($setting, 'async', false) . '>Async</option>';
        echo '</select>';
        echo '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';

    echo '<p><input type="submit" class="button-primary" value="' . __('Save Changes', 'zynith-seo') . '"></p>';
    echo '</form>';
    echo '</div>';
}

// Lazy Loading Implementation
function zynith_seo_lazy_loading($tag, $handle, $src) {
    
    // Only apply lazy loading on the frontend, not in the admin
    if (is_admin()) return $tag;

    // Provide defaults for all expected keys
    $defaults = [
        'lazy_loading'           => 'disabled',
        'critical_preloading'    => [],
        'media_lcp_optimization' => 'disabled'
    ];
    
    // Merge DB settings with your defaults
    $global_settings = wp_parse_args(get_option('zynith_global_settings', []), $defaults);
        
    if ($global_settings['lazy_loading'] === 'enabled') return str_replace('<script', '<script loading="lazy"', $tag);
    return $tag;
}
add_filter('script_loader_tag', 'zynith_seo_lazy_loading', 10, 3);

// Critical Script Preloading
function zynith_seo_preload_critical_scripts() {
    $global_settings = get_option('zynith_global_settings', []);
    if (!empty($global_settings['critical_preloading'])) {
        foreach ($global_settings['critical_preloading'] as $script_url) {
            echo '<link rel="preload" href="' . esc_url($script_url) . '" as="script">';
        }
    }
}
add_action('wp_head', 'zynith_seo_preload_critical_scripts');

// Media LCP Optimization with Broader Logic and Dynamic Detection
function zynith_seo_media_lcp_optimization() {
    $global_settings = get_option('zynith_global_settings', []);
    $custom_selector = $global_settings['custom_media_selector'] ?? ''; // Custom selector from settings

    if ($global_settings['media_lcp_optimization'] === 'enabled') {
        // PHP-based detection for media elements (images, videos, and backgrounds)
        ob_start(function ($output) use ($custom_selector) {
            // Regex to match media elements based on class, ID, inline styles, or video tags
            $regex = '/
                <(?:img|div|section|header|video)[^>]*            # Match <img>, <div>, <section>, <header>, <video>
                (?:class=["\'].*?(hero|main-image|banner|cover|featured|background|back-ground|bckgrnd).*?["\']| # Match class attribute
                id=["\'].*?(hero|main-image|featured-image|banner).*?["\']|                                     # Match ID attribute
                style=["\'][^"\']*background-image:\s*url\((["\']?)([^"\')]+)\1\)["\'])                        # Match inline background-image styles
                [^>]*>/ix';

            preg_match($regex, $output, $matches);

            // Match custom selector from settings
            $custom_regex = !empty($custom_selector) 
                ? '/<.*?' . preg_quote($custom_selector, '/') . '.*?>/i' 
                : null;

            // Determine the media URL
            $media_url = !empty($matches[4]) ? $matches[4] : (!empty($matches[2]) ? $matches[2] : null);

            if ($custom_regex) {
                preg_match($custom_regex, $output, $custom_matches);
                if (!empty($custom_matches[0])) {
                    $media_url = $custom_matches[0];
                }
            }

            if ($media_url) {
                echo '<link rel="preload" href="' . esc_url($media_url) . '" as="image">';
            }

            // Handle video elements
            if (preg_match('/<video[^>]*src=["\']([^"\']+)["\']/', $output, $video_match)) {
                $video_url = $video_match[1];
                echo '<link rel="preload" href="' . esc_url($video_url) . '" as="video">';
            }

            return $output;
        });

        // JavaScript fallback for dynamic LCP detection
        echo '<script>
            document.addEventListener("DOMContentLoaded", () => {
                if ("PerformanceObserver" in window) {
                    try {
                        const observer = new PerformanceObserver((list) => {
                            const entries = list.getEntries();
                            const lcpEntry = entries.find(entry => {
                                if (!entry.element) return false;

                                // Detect based on tag, class, ID, or background-image
                                const isMediaElement = (
                                    entry.element.tagName === "IMG" || 
                                    entry.element.tagName === "VIDEO" ||
                                    /hero|main-image|banner|featured|background|back-ground|bckgrnd/.test(entry.element.className) || 
                                    /hero|main-image|banner|featured|background|back-ground|bckgrnd/.test(entry.element.id)
                                );

                                if (isMediaElement) {
                                    return true;
                                }

                                // Check for background images
                                const computedStyle = window.getComputedStyle(entry.element);
                                const backgroundImage = computedStyle.backgroundImage;
                                return backgroundImage && backgroundImage.includes("url(");
                            });

                            if (lcpEntry) {
                                const mediaElement = lcpEntry.element;

                                // Handle standard <img> elements
                                if (mediaElement.tagName === "IMG") {
                                    const imageUrl = mediaElement.getAttribute("src");
                                    if (imageUrl) {
                                        const link = document.createElement("link");
                                        link.rel = "preload";
                                        link.as = "image";
                                        link.href = imageUrl;
                                        document.head.appendChild(link);
                                    }
                                }

                                // Handle <video> elements
                                if (mediaElement.tagName === "VIDEO") {
                                    const videoUrl = mediaElement.getAttribute("src");
                                    if (videoUrl) {
                                        const link = document.createElement("link");
                                        link.rel = "preload";
                                        link.as = "video";
                                        link.href = videoUrl;
                                        document.head.appendChild(link);
                                    }
                                }

                                // Handle background images
                                const computedStyle = window.getComputedStyle(mediaElement);
                                const backgroundImage = computedStyle.backgroundImage;
                                const bgMatch = backgroundImage.match(/url\\(["\']?(.*?)["\']?\\)/);
                                if (bgMatch && bgMatch[1]) {
                                    const link = document.createElement("link");
                                    link.rel = "preload";
                                    link.as = "image";
                                    link.href = bgMatch[1];
                                    document.head.appendChild(link);
                                }
                            }
                        });

                        observer.observe({ type: "largest-contentful-paint", buffered: true });
                    } catch (error) {
                        console.error("LCP Observer Error:", error);
                    }
                }
            });
        </script>';
    }
}
add_action('wp_head', 'zynith_seo_media_lcp_optimization');

// Detect HTML scripts
add_action('template_redirect', function () {
    // Start output buffering to capture page output
    ob_start(function ($output) {
        // Define the regex to match script tags with a 'src' attribute
        $regex = '/<script[^>]*src=["\']([^"\']+)["\']/i';
        
        // Match all script tags in the output
        preg_match_all($regex, $output, $matches);
        
        // If matches are found, process them
        if (!empty($matches[1])) {
            // Fetch the existing detected scripts
            $detected_scripts = get_option('zynith_detected_scripts', []);
            
            // Merge new scripts with existing ones, ensuring no duplicates
            $new_scripts = array_unique(array_merge($detected_scripts, $matches[1]));
            
            // Save the updated scripts list to the database
            update_option('zynith_detected_scripts', $new_scripts);
            
            // Optional: Log for debugging purposes
            error_log('Detected scripts updated: ' . print_r($new_scripts, true));
        }
        
        // Return the output unchanged
        return $output;
    });
});