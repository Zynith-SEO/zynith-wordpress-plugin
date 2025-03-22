<?php

if (!defined('ABSPATH')) exit;

// Add settings page under Tools
function zynith_seo_breadcrumbs_add_settings_page() {
    add_submenu_page(
        'zynith_seo_dashboard',       // Parent slug, ensures it appears under Zynith SEO
        'Breadcrumb Editor',         // Page title
        'Breadcrumb Editor',         // Menu title
        'manage_options',            // Capability required
        'zynith-seo-breadcrumbs',     // Menu slug
        'zynith_seo_breadcrumbs_settings_page' // Callback function
    );
}
add_action('admin_menu', 'zynith_seo_breadcrumbs_add_settings_page');

// Display settings page
function zynith_seo_breadcrumbs_settings_page() {
    // Handle form submission
    if (isset($_POST['zynith_seo_breadcrumbs_save_settings'])) {
        update_option('zynith_seo_breadcrumbs_delimiter', sanitize_text_field($_POST['zynith_seo_breadcrumbs_delimiter']));
        update_option('zynith_seo_breadcrumbs_home_label', sanitize_text_field($_POST['zynith_seo_breadcrumbs_home_label']));
        update_option('zynith_seo_breadcrumbs_disable_json_ld', isset($_POST['zynith_seo_breadcrumbs_disable_json_ld']) ? 'yes' : 'no');
        update_option('zynith_seo_breadcrumbs_disable_urls', isset($_POST['zynith_seo_breadcrumbs_disable_urls']) ? 'yes' : 'no');
        update_option('zynith_seo_breadcrumbs_style_as', sanitize_text_field($_POST['zynith_seo_breadcrumbs_style_as']));
        update_option('zynith_seo_breadcrumbs_style_bold', isset($_POST['zynith_seo_breadcrumbs_style_bold']) ? 'yes' : 'no');
        update_option('zynith_seo_breadcrumbs_style_italic', isset($_POST['zynith_seo_breadcrumbs_style_italic']) ? 'yes' : 'no');
        update_option('zynith_seo_breadcrumbs_style_underline', isset($_POST['zynith_seo_breadcrumbs_style_underline']) ? 'yes' : 'no');
        update_option('zynith_seo_breadcrumbs_class', sanitize_text_field($_POST['zynith_seo_breadcrumbs_class']));
       
        // Add settings saved message using add_settings_error
        add_settings_error('zynith_seo_breadcrumbs', 'settings_updated', 'Settings saved', 'updated');
    }
    
    // Display any settings errors or success messages
    settings_errors('zynith_seo_breadcrumbs');

    $delimiter = get_option('zynith_seo_breadcrumbs_delimiter', '>');
    $home_label = get_option('zynith_seo_breadcrumbs_home_label', get_bloginfo('name'));
    $disable_json_ld = get_option('zynith_seo_breadcrumbs_disable_json_ld', 'no');
    $disable_urls = get_option('zynith_seo_breadcrumbs_disable_urls', 'no');
    $style_as = get_option('zynith_seo_breadcrumbs_style_as', 'p');
    $style_bold = get_option('zynith_seo_breadcrumbs_style_bold', 'no');
    $style_italic = get_option('zynith_seo_breadcrumbs_style_italic', 'no');
    $style_underline = get_option('zynith_seo_breadcrumbs_style_underline', 'no');
    $custom_class = get_option('zynith_seo_breadcrumbs_class', '');
    
    ?>
    <div class="wrap">
        <h1>Zynith SEO Breadcrumbs Settings</h1>
        <p style="margin-top: 10px; font-size: 14px;">To display the Zynith SEO breadcrumbs on your site, simply add the <code>[zynith-breadcrumbs]</code> shortcode to your desired page or post.</p>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Delimiter</th>
                    <td>
                        <select name="zynith_seo_breadcrumbs_delimiter">
                            <option value="|" <?php selected($delimiter, '|'); ?>> | (Pipe)</option>
                            <option value="&gt;" <?php selected($delimiter, '>'); ?>> > (Greater Than)</option>
                            <option value="&raquo;" <?php selected($delimiter, '»'); ?>> » (Double Arrow)</option>
                            <option value="&bull;" <?php selected($delimiter, '•'); ?>> • (Bullet)</option>
                            <option value="-" <?php selected($delimiter, '-'); ?>> - (Dash)</option>
                            <option value="/" <?php selected($delimiter, '/'); ?>> / (Slash)</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Home Label</th>
                    <td><input type="text" name="zynith_seo_breadcrumbs_home_label" value="<?php echo esc_attr($home_label); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Disable JSON LD</th>
                    <td>
                        <label class="zynith-toggle-switch">
                            <input type="checkbox" name="zynith_seo_breadcrumbs_disable_json_ld" <?php checked($disable_json_ld, 'yes'); ?> />
                            <span class="zynith-slider"></span>
                        </label>
                        Disable JSON LD
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Disable Breadcrumb URLs</th>
                    <td>
                        <label class="zynith-toggle-switch">
                            <input type="checkbox" name="zynith_seo_breadcrumbs_disable_urls" <?php checked($disable_urls, 'yes'); ?> />
                            <span class="zynith-slider"></span>
                        </label>
                        Disable URLs
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Specify Class</th>
                    <td><input type="text" name="zynith_seo_breadcrumbs_class" value="<?php echo esc_attr($custom_class); ?>" placeholder="Optional CSS class" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Style As</th>
                    <td>
                        <select name="zynith_seo_breadcrumbs_style_as">
                            <option value="p" <?php selected($style_as, 'p'); ?>>Style as p</option>
                            <option value="h1" <?php selected($style_as, 'h1'); ?>>Style as h1</option>
                            <option value="h2" <?php selected($style_as, 'h2'); ?>>Style as h2</option>
                            <option value="h3" <?php selected($style_as, 'h3'); ?>>Style as h3</option>
                            <option value="h4" <?php selected($style_as, 'h4'); ?>>Style as h4</option>
                            <option value="h5" <?php selected($style_as, 'h5'); ?>>Style as h5</option>
                            <option value="h6" <?php selected($style_as, 'h6'); ?>>Style as h6</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Additional Styles</th>
                    <td>
                        <div class="zynith-item-wrapper">
                            <label class="zynith-toggle-switch">
                                <input type="checkbox" name="zynith_seo_breadcrumbs_style_bold" <?php checked($style_bold, 'yes'); ?> />
                                <span class="zynith-slider"></span>
                            </label>
                            <span>Bold</span>
                        </div>
                        <div class="zynith-item-wrapper">
                            <label class="zynith-toggle-switch">
                                <input type="checkbox" name="zynith_seo_breadcrumbs_style_italic" <?php checked($style_italic, 'yes'); ?> />
                                <span class="zynith-slider"></span>
                            </label>
                            <span>Italicized</span>
                        </div>
                        <div class="zynith-item-wrapper">
                            <label class="zynith-toggle-switch">
                                <input type="checkbox" name="zynith_seo_breadcrumbs_style_underline" <?php checked($style_underline, 'yes'); ?> />
                                <span class="zynith-slider"></span>
                            </label>
                            <span>Underlined</span>
                        </div>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'zynith_seo_breadcrumbs_save_settings'); ?>
        </form>
    </div>
    <?php
}

// Generate breadcrumbs with schema
function zynith_seo_breadcrumbs_display() {
    $delimiter = get_option('zynith_seo_breadcrumbs_delimiter', '>');
    $home_label = get_option('zynith_seo_breadcrumbs_home_label', get_bloginfo('name'));
    $disable_json_ld = get_option('zynith_seo_breadcrumbs_disable_json_ld', 'no');
    $disable_urls = get_option('zynith_seo_breadcrumbs_disable_urls', 'no');
    $style_as = get_option('zynith_seo_breadcrumbs_style_as', 'p');
    $style_bold = get_option('zynith_seo_breadcrumbs_style_bold', 'no') === 'yes';
    $style_italic = get_option('zynith_seo_breadcrumbs_style_italic', 'no') === 'yes';
    $style_underline = get_option('zynith_seo_breadcrumbs_style_underline', 'no') === 'yes';
    $custom_class = get_option('zynith_seo_breadcrumbs_class', '');

    $style_tags_open = '';
    $style_tags_close = '';
    if ($style_bold) { $style_tags_open .= '<strong>'; $style_tags_close = '</strong>' . $style_tags_close; }
    if ($style_italic) { $style_tags_open .= '<em>'; $style_tags_close = '</em>' . $style_tags_close; }
    if ($style_underline) { $style_tags_open .= '<u>'; $style_tags_close = '</u>' . $style_tags_close; }

    $class_attribute = $custom_class ? ' class="zynith-seo-breadcrumbs ' . esc_attr($custom_class) . '"' : ' class="zynith-seo-breadcrumbs"';

    $breadcrumbs = "<{$style_as}{$class_attribute}>";
    if (!is_front_page()) {
        if ($disable_urls === 'yes') {
            $breadcrumbs .= $style_tags_open . esc_html($home_label) . $style_tags_close . ' ' . esc_html($delimiter) . ' ';
        }
        else {
            $breadcrumbs .= $style_tags_open . '<a href="' . home_url() . '">' . esc_html($home_label) . '</a>' . ' ' . esc_html($delimiter) . ' ' . $style_tags_close;
        }
        
        if (is_category() || is_single()) {
            $categories = get_the_category();
            if ($categories) {
                if ($disable_urls === 'yes') {
                    $breadcrumbs .= $style_tags_open . esc_html($categories[0]->name) . $style_tags_close . ' ' . esc_html($delimiter) . ' ';
                } else {
                    $breadcrumbs .= $style_tags_open . '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a>' . ' ' . esc_html($delimiter) . ' ' . $style_tags_close;
                }
            }
            if (is_single()) $breadcrumbs .= $style_tags_open . '<span>' . get_the_title() . '</span>' . $style_tags_close;
        }
        elseif (is_page()) {
            global $post;
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                $crumbs = [];
                while ($parent_id) {
                    $page = get_page($parent_id);
                    if ($disable_urls === 'yes') {
                        $crumbs[] = $style_tags_open . esc_html(get_the_title($page->ID)) . $style_tags_close;
                    } else {
                        $crumbs[] = $style_tags_open . '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>' . $style_tags_close;
                    }
                    $parent_id = $page->post_parent;
                }
                $crumbs = array_reverse($crumbs);
                foreach ($crumbs as $crumb) $breadcrumbs .= $crumb . ' ' . esc_html($delimiter) . ' ';
            }
            $breadcrumbs .= $style_tags_open . '<span>' . get_the_title() . '</span>' . $style_tags_close;
        }
    }
    $breadcrumbs .= "</{$style_as}>";

    if ($disable_json_ld !== 'yes') {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [],
        ];
        $position = 1;
        $schema['itemListElement'][] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => esc_html($home_label),
            "item" => home_url(),
        ];
        if (is_category() || is_single()) {
            $categories = get_the_category();
            if ($categories) {
                $schema['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $position++,
                    "name" => esc_html($categories[0]->name),
                    "item" => esc_url(get_category_link($categories[0]->term_id)),
                ];
            }
            if (is_single()) {
                $schema['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $position,
                    "name" => get_the_title(),
                    "item" => get_permalink(),
                ];
            }
        }
        elseif (is_page()) {
            global $post;
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $schema['itemListElement'][] = [
                        "@type" => "ListItem",
                        "position" => $position++,
                        "name" => get_the_title($page->ID),
                        "item" => get_permalink($page->ID),
                    ];
                    $parent_id = $page->post_parent;
                }
            }
            $schema['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => $position,
                "name" => get_the_title(),
                "item" => get_permalink(),
            ];
        }  
        $breadcrumbs .= '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
    return $breadcrumbs;
}
add_shortcode('zynith-breadcrumbs', 'zynith_seo_breadcrumbs_display');