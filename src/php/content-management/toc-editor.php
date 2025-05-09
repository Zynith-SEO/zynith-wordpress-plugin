<?php
defined('ABSPATH') or exit;

// Add settings page under the Zynith SEO menu
function zynith_seo_toc_add_settings_page() {
    add_submenu_page(
        'zynith_seo_dashboard',       // Parent menu slug
        'Zynith ToC Settings',        // Page title
        'ToC Settings',               // Menu title
        'manage_options',             // Capability
        'zynith-seo-toc',             // Menu slug
        'zynith_seo_toc_render_settings_page' // Callback function
    );
}
add_action('admin_menu', 'zynith_seo_toc_add_settings_page');

// Render the settings page
function zynith_seo_toc_render_settings_page() {
    
    // Handle form submission
    if ($_POST) {
        
        // Save settings
        update_option('zynith_toc_title', sanitize_text_field($_POST['zynith_toc_title']));
        update_option('zynith_toc_enable_numbering', isset($_POST['zynith_toc_enable_numbering']) ? 1 : 0);
        update_option('zynith_toc_heading_levels', $_POST['zynith_toc_heading_levels']);
        update_option('zynith_toc_list_delimiter', sanitize_text_field($_POST['zynith_toc_list_delimiter']));
        update_option('zynith_toc_custom_css_class', sanitize_text_field($_POST['zynith_toc_custom_css_class']));
        update_option('zynith_toc_sticky', isset($_POST['zynith_toc_sticky']) ? 1 : 0);
        update_option('zynith_toc_default_state', sanitize_text_field($_POST['zynith_toc_default_state']));

        // Add settings saved message using add_settings_error
        add_settings_error('zynith_toc_messages', 'zynith_toc_message', 'Settings saved', 'updated');
    }

    // Display settings errors or success messages
    settings_errors('zynith_toc_messages');

    // Get settings
    $toc_title = get_option('zynith_toc_title', 'Table of Contents');
    $enable_numbering = get_option('zynith_toc_enable_numbering', 0);
    $heading_levels = get_option('zynith_toc_heading_levels', ['h2', 'h3']);
    $list_delimiter = get_option('zynith_toc_list_delimiter', 'none');
    $custom_css_class = get_option('zynith_toc_custom_css_class', '');
    $sticky_toc = get_option('zynith_toc_sticky', 0);
    $default_state = get_option('zynith_toc_default_state', 'collapsed');

    ?>
    <div class="wrap">
        <h1>TOC Settings</h1>
        <p>To add a Table of Contents to any page or post, simply place the shortcode <code>[zynith-toc]</code> in your content where you want it to appear.</p>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="zynith_toc_title">TOC Title</label></th>
                    <td><input type="text" id="zynith_toc_title" name="zynith_toc_title" value="<?php echo esc_attr($toc_title); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Enable Numbering</th>
                    <td>
                        <label class="zynith-toggle-switch">
                            <input type="checkbox" name="zynith_toc_enable_numbering" <?php checked($enable_numbering, 1); ?> />
                            <span class="zynith-slider"></span>
                        </label>
                        Enable numbering for TOC items
                    </td>
                </tr>
                <tr>
                    <th scope="row">Sticky TOC</th>
                    <td>
                        <label class="zynith-toggle-switch">
                            <input type="checkbox" name="zynith_toc_sticky" <?php checked($sticky_toc, 1); ?> />
                            <span class="zynith-slider"></span>
                        </label>
                        Enable sticky (floating) TOC
                    </td>
                </tr>
                <tr>
                    <th scope="row">Heading Levels</th>
                    <td>
                        <?php foreach (range(1, 6) as $level): ?>
                            <label>
                                <input type="checkbox" name="zynith_toc_heading_levels[]" value="h<?php echo $level; ?>" <?php checked(in_array("h{$level}", $heading_levels)); ?> />
                                H<?php echo $level; ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="zynith_toc_list_delimiter">List Item Delimiter</label></th>
                    <td>
                        <select id="zynith_toc_list_delimiter" name="zynith_toc_list_delimiter">
                            <option value="none" <?php selected($list_delimiter, 'none'); ?>>No Delimiter</option>
                            <option value="bullet" <?php selected($list_delimiter, 'bullet'); ?>>● Bullet</option>
                            <option value="dash" <?php selected($list_delimiter, 'dash'); ?>>- Dash</option>
                            <option value="arrow" <?php selected($list_delimiter, 'arrow'); ?>>→ Arrow</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="zynith_toc_custom_css_class">Custom CSS Class</label></th>
                    <td><input type="text" id="zynith_toc_custom_css_class" name="zynith_toc_custom_css_class" value="<?php echo esc_attr($custom_css_class); ?>" placeholder="Optional CSS class" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="zynith_toc_default_state">Default State</label></th>
                    <td>
                        <select id="zynith_toc_default_state" name="zynith_toc_default_state">
                            <option value="collapsed" <?php selected($default_state, 'collapsed'); ?>>Collapsed</option>
                            <option value="expanded" <?php selected($default_state, 'expanded'); ?>>Expanded</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// Filter to add IDs to headings only if TOC shortcode is present
function zynith_seo_toc_maybe_add_ids($content) {
    if (has_shortcode($content, 'zynith-toc')) {
        $content = zynith_seo_toc_add_ids_to_headings($content);
    }
    return $content;
}
add_filter('the_content', 'zynith_seo_toc_maybe_add_ids');

// Add IDs to headings in the content
function zynith_seo_toc_add_ids_to_headings($content) {
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $unique_ids = [];
    foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
        $headings = $dom->getElementsByTagName($tag);
        foreach ($headings as $heading) {
            $sanitized_text = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $heading->textContent), '-'));
            $id = $sanitized_text;
            $counter = 1;
            while (in_array($id, $unique_ids)) {
                $id = $sanitized_text . '-' . $counter++;
            }
            $unique_ids[] = $id;
            $heading->setAttribute('id', $id);
        }
    }

    return $dom->saveHTML();
}

// Generate the TOC
function zynith_seo_toc_generate() {
    global $post;

    if (empty($post)) return '';
    
    // Ensure headings have IDs
    $content = zynith_seo_toc_add_ids_to_headings($post->post_content);

    // Gather all headings
    preg_match_all('/<h([1-6]).*?id="(.*?)".*?>(.*?)<\/h\1>/s', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) return '';
    
    $toc_title = get_option('zynith_toc_title', 'Table of Contents');
    $enable_numbering = get_option('zynith_toc_enable_numbering', 0);
    $list_delimiter = get_option('zynith_toc_list_delimiter', 'none');
    $custom_css_class = get_option('zynith_toc_custom_css_class', '');
    $sticky_toc = get_option('zynith_toc_sticky', 0);
    $default_state = get_option('zynith_toc_default_state', 'collapsed');
    $heading_levels     = get_option('zynith_toc_heading_levels', ['h2', 'h3']);
    
    // Filter out headings that are not in the allowed levels
    $matches = array_filter($matches, function($match) use ($heading_levels) {
        $level = (int) $match[1];
        return in_array('h' . $level, $heading_levels, true);
    });

    // Re-index array after filtering
    $matches = array_values($matches);
    
    // If no headings remain after filtering, return
    if (empty($matches)) return '';
    
    // Determine the minimum heading level we are actually using (e.g., 2 if we only show h2/h3)
    $levels_used        = array_map(fn($m) => (int)$m[1], $matches);
    $min_heading_level  = min($levels_used);
    
    // Set open/close state
    $default_state_attribute = $default_state === 'expanded' ? 'open' : '';

    // Determine list style
    $list_style = 'list-style: none;'; // Default to no list style
    if ($list_delimiter === 'bullet') {
        $list_style = 'list-style: disc;';
    }
    elseif ($list_delimiter === 'dash' || $list_delimiter === 'arrow') {
        $list_style = 'list-style: none;'; // Add custom arrow styles manually
    }

    // Start building TOC
    $output = '<details class="zynith-toc ' . esc_attr($custom_css_class) . ($sticky_toc ? ' sticky' : '') . '" ' . $default_state_attribute . '>';
    $output .= '<summary>' . esc_html($toc_title) . '</summary>';
    $output .= '<ul style="' . esc_attr($list_style) . ' padding: 0;">';

    // Keep track of nesting and hierarchical counters
    $current_level = 0;

    // Store up to 6 levels
    $counters = [0, 0, 0, 0, 0, 0];

    // Helper function to build hierarchical number like "1.2.1" from counters
    $build_number = function($counters, $level) {
        // Join all non-zero counters up to current level
        return implode('.', array_slice($counters, 0, $level));
    };

    foreach ($matches as $match) {
        $level = intval($match[1]);
        $id = $match[2];
        $title = wp_strip_all_tags($match[3]);

        // Convert actual heading level (e.g. 2,3) to 1-based index for counters array based on the min heading level we’re actually displaying.
        $adjusted_level = $level - $min_heading_level + 1;
        
        // Open additional <ul> tags if we're going deeper
        while ($current_level < $adjusted_level) {
            $output .= '<ul style="' . esc_attr($list_style) . '">';
            $current_level++;
        }
        
        // Close <ul> tags if we've moved back up
        while ($current_level > $adjusted_level) {
            $output .= '</ul>';
            $current_level--;
        }
        
        // Increment this level’s counter and reset deeper levels
        $counters[$adjusted_level - 1]++;
        for ($i = $adjusted_level; $i < 6; $i++) $counters[$i] = 0;
        
        // Build the numbering string if numbering is enabled
        $number_str = '';
        if ($enable_numbering) $number_str = $build_number($counters, $adjusted_level) . '. ';
        
        // Determine the delimiter symbol
        $delimiter = '';
        if ($list_delimiter === 'dash') {
            $delimiter = '- ';
        }
        elseif ($list_delimiter === 'arrow') {
            $delimiter = '→ ';
        }
        
        // List item
        $output .= '<li>' . $number_str . $delimiter . '<a href="#' . esc_attr($id) . '">' . esc_html($title) . '</a></li>';
    }

    // Close any remaining opened <ul> tags
    while ($current_level > 0) {
        $output .= '</ul>';
        $current_level--;
    }

    $output .= '</ul></details>';
    return $output;
}
add_shortcode('zynith-toc', 'zynith_seo_toc_generate');