<?php

defined('ABSPATH') or exit;

// Register Schema Settings Page in Zynith SEO Menu
function zynith_seo_add_schema_menu() {
    add_submenu_page(
        'zynith_seo_dashboard', // Parent slug for Zynith SEO Dashboard
        'Schema', // Page title
        'Schema', // Menu title
        'manage_options', // Capability required to access this page
        'zynith_seo_schema_editor', // Menu slug
        'zynith_seo_schema_editor_page' // Callback function to display the page content
    );
}
add_action('admin_menu', 'zynith_seo_add_schema_menu');

// Display the Schema Settings Page content
function zynith_seo_schema_editor_page() {
    echo '<div class="wrap">';
    echo '<h1>Zynith SEO Automatic Schema Editor</h1>';
    settings_errors();
    echo '<form action="options.php" method="post">';
    settings_fields('zynith_seo_schema'); // Security fields for schema settings
    do_settings_sections('zynith_seo_schema'); // Display schema settings sections
    submit_button(); // Output the "Save Changes" button
    echo '</form>';
    echo '</div>';
}

// Register Schema Settings, Fields, and Custom Business Details
function zynith_seo_register_schema_settings() {
        
    // Schema toggle settings, each with a 'label' and an optional 'note' describing required fields
    $schema_options = [
        'home_website_schema' => [
            'label' => 'Enable Home Page Schema (sets the Site Name)',
            'note'  => 'Requires the <strong><a href="#zynith_seo_company_name">Company / Business Name</a></strong> field to be set.'
        ],
        'post_schema' => [
            'label' => 'Enable Post Schema',
            'note'  => '' // no special fields required beyond post data
        ],
        'page_schema' => [
            'label' => 'Enable Page Schema',
            'note'  => '' 
        ],
        'author_schema' => [
            'label' => 'Enable Author Schema',
            'note'  => 'Ensure the author has a valid display name and any desired social links.'
        ],
        'date_schema' => [
            'label' => 'Enable Date Schema',
            'note'  => '' 
        ],
        'about_schema' => [
            'label' => 'Enable About Schema',
            'note'  => 'Requires basic business details (e.g., <strong><a href="#zynith_seo_company_name">Company / Business Name</a></strong>, <strong><a href="#zynith_seo_business_address">Address</a></strong>).'
        ],
        'contact_schema' => [
            'label' => 'Enable Contact Schema',
            'note'  => 'Requires <strong><a href="#zynith_seo_business_phone">Phone Number</a></strong>, <strong><a href="#zynith_seo_business_email">Email</a></strong>, and <strong><a href="#zynith_seo_business_address">Address</a></strong>.'
        ],
        'faq_schema' => [
            'label' => 'Enable FAQ Schema',
            'note'  => '' 
        ],
        'local_schema' => [
            'label' => 'Enable Local Schema',
            'note'  => 'Requires: <strong><a href="#zynith_seo_company_name">Company / Business Name</a></strong>, <strong><a href="#zynith_seo_business_type">Business Type</a></strong>, <strong><a href="#zynith_seo_business_phone">Phone</a></strong>, <strong><a href="#zynith_seo_business_address">Address</a></strong>, <strong><a href="#zynith_seo_aggregate_rating">Aggregate Rating</a></strong>, <strong><a href="#zynith_seo_total_ratings">Total Ratings</a></strong>.'
        ]
    ];
    
    // Register each schema toggle as a setting and add the field
    foreach ($schema_options as $option_key => $data) {
        $label = $data['label'];
        $note  = isset($data['note']) ? $data['note'] : '';

        register_setting(
            'zynith_seo_schema',
            "zynith_seo_enable_{$option_key}",
            [
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean'
            ]
        );

        add_settings_field(
            "zynith_seo_enable_{$option_key}",  // Field ID
            $label,                             // Field label
            'zynith_seo_schema_toggle_field_callback', // Callback for the checkbox + note
            'zynith_seo_schema',               // Page
            'zynith_seo_schema_section',        // Section
            [
                'label_for' => "zynith_seo_enable_{$option_key}",
                'note'      => $note // pass the note to our callback
            ]
        );
    }
    
    // Register custom business information fields with labels
    $custom_fields = [
        'company_name'          => 'Company / Business Name',
        'business_type'         => 'Business Type',
        'business_address'      => 'Address',
        'business_email'        => 'Email',
        'business_phone'        => 'Phone Number',
        'business_locality'     => 'Locality',
        'business_region'       => 'Region',
        'business_postal_code'  => 'Postal Code', 
        'country'               => 'Country',
        'aggregate_rating'      => 'Aggregate Rating',
        'total_ratings'         => 'Total Ratings',
        'business_logo_url'     => 'Logo URL',
        'latitude'              => 'Latitude', 
        'longitude'             => 'Longitude',
        'geo_radius'            => 'Geo Radius', 
        'facebook_url'          => 'Facebook URL',
        'linkedin_url'          => 'LinkedIn URL',
        'youtube_url'           => 'YouTube URL',
        'instagram_url'         => 'Instagram URL',
        'twitter_url'           => 'Twitter URL'
    ];

    // Register each custom business field
    foreach ($custom_fields as $field => $label) {
        // Decide whether to use a text field or upload field callback
        $callback = ( $field === 'business_logo_url' )
            ? 'zynith_seo_logo_upload_field_callback'
            : 'zynith_seo_text_field_callback';

        register_setting(
            'zynith_seo_schema',
            "zynith_seo_{$field}",
            [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ]
        );

        add_settings_field(
            "zynith_seo_{$field}",
            $label,
            $callback,
            'zynith_seo_schema',
            'zynith_seo_schema_section',
            ['label_for' => "zynith_seo_{$field}"]
        );
    }

    // Register a section for schema settings with a description
    add_settings_section(
        'zynith_seo_schema_section',
        'Schema Markup Settings',
        'zynith_seo_schema_section_callback',
        'zynith_seo_schema'
    );
}
add_action('admin_init', 'zynith_seo_register_schema_settings');

// Callback for the 'Logo URL' field that allows media library selection.
function zynith_seo_logo_upload_field_callback($args) {
    $option_name = $args['label_for']; // 'zynith_seo_business_logo_url'
    $value       = get_option($option_name, '');
    ?>
    <div style="display: flex; gap: 3px;"><input 
            type="text" 
            id="<?php echo esc_attr($option_name); ?>" 
            name="<?php echo esc_attr($option_name); ?>" 
            value="<?php echo esc_attr($value); ?>" 
            style="width: 300px;" 
        /><input 
            type="button" 
            class="button zynith-seo-upload-button" 
            data-target="#<?php echo esc_attr($option_name); ?>" 
            value="Select Image"
        /></div>
    <?php
    if (!empty($value)) echo '<div style="margin: 4px 0 0 1px;"><img src="' . esc_url($value) . '" alt="" style="max-width: 150px; padding: 1px; border: 1px solid #ccc; border-radius: 4px;" /></div>';
}

// Section description callback
function zynith_seo_schema_section_callback() {
    echo '<p>Toggle schema types on or off and enter custom details for enhanced schema specificity.</p>';
}

// Toggle field callback for schema settings
function zynith_seo_schema_toggle_field_callback($args) {
    $option_name = $args['label_for'];
    $checked     = get_option($option_name) ? 'checked' : '';

    // Render checkbox
    echo "<label class='zynith-toggle-switch' style='display:inline-block;margin-bottom:5px;'>
            <input type='checkbox' id='{$option_name}' name='{$option_name}' value='1' {$checked} />
            <span class='zynith-slider'></span>
          </label>";

    // If a 'note' was passed in the arguments, display it under the toggle
    if (!empty($args['note'])) echo "<span class='description'>" . wp_kses_post($args['note']) . "</span>";
}

// Text field callback for custom fields
function zynith_seo_text_field_callback($args) {
    $option_name = $args['label_for'];
    $value = get_option($option_name, '');
    echo "<input type='text' id='{$option_name}' name='{$option_name}' value='" . esc_attr($value) . "' />";
}

// Helper function to retrieve custom fields
function zynith_seo_get_custom_field($field) {
    return get_option("zynith_seo_{$field}", '');
}

// Output schema based on settings
function zynith_seo_output_schema() {
    if (get_option('zynith_seo_enable_home_website_schema') && (is_front_page() || is_page('home'))) {
        zynith_seo_output_website_schema();
        zynith_seo_output_local_schema();
    }
    if (get_option('zynith_seo_enable_post_schema') && is_single()) zynith_seo_output_post_schema();
    if (get_option('zynith_seo_enable_page_schema') && is_page()) zynith_seo_output_page_schema();
    if (get_option('zynith_seo_enable_author_schema') && is_author()) zynith_seo_output_author_schema();
    if (get_option('zynith_seo_enable_date_schema') && is_date()) zynith_seo_output_date_schema();
    if (get_option('zynith_seo_enable_about_schema') && is_page(['about', 'about-us'])) zynith_seo_output_about_schema();
    if (get_option('zynith_seo_enable_contact_schema') && is_page(['contact', 'contact-us'])) zynith_seo_output_contact_schema();
    if (get_option('zynith_seo_enable_faq_schema') && is_page(['faq', 'frequently-asked-questions'])) zynith_seo_output_faq_schema();
}
add_action('wp_head', 'zynith_seo_output_schema');

// Output WebSite Schema Markup for Home Page
function zynith_seo_output_website_schema() {
    // Build the WebSite schema
    $website_schema = [
        "@context" => "https://schema.org",
        "@type"    => "WebSite",
        "name"     => get_bloginfo('name'), // or get_option('blogname')
        "url"      => home_url()
    ];

    // Output the JSON-LD
    echo "\n<!-- WebSite Schema (Home) -->\n";
    echo '<script type="application/ld+json">' . json_encode( $website_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . '</script>';
    echo "\n<!-- End WebSite Schema -->\n";
}

// Output Local Schema Markup for Home Page
function zynith_seo_output_local_schema() {
    // Check if the Local Schema toggle is enabled and if we are on the home page
    if (get_option('zynith_seo_enable_local_schema') && is_front_page()) {
        
        // Retrieve dynamic data from custom fields
        $business_name = zynith_seo_get_custom_field('company_name');
        $business_type = zynith_seo_get_custom_field('business_type');
        $business_image = zynith_seo_get_custom_field('business_logo_url');
        $business_phone = zynith_seo_get_custom_field('business_phone');
        $business_address = zynith_seo_get_custom_field('business_address');
        $business_locality = zynith_seo_get_custom_field('business_locality');
        $business_region = zynith_seo_get_custom_field('business_region');
        $business_postal_code = zynith_seo_get_custom_field('business_postal_code');
        $country = zynith_seo_get_custom_field('country') ?: 'US'; // Default to 'US' if empty
        $aggregate_rating = zynith_seo_get_custom_field('aggregate_rating');
        $total_ratings = zynith_seo_get_custom_field('total_ratings');
        $latitude = zynith_seo_get_custom_field('latitude');
        $longitude = zynith_seo_get_custom_field('longitude');
        $geo_radius = zynith_seo_get_custom_field('geo_radius') ?: '56327'; // Default radius if empty
        $service_url = home_url();
        
        $social_links = array_values(array_filter([
            esc_url(zynith_seo_get_custom_field('facebook_url')),
            esc_url(zynith_seo_get_custom_field('linkedin_url')),
            esc_url(zynith_seo_get_custom_field('twitter_url')),
            esc_url(zynith_seo_get_custom_field('instagram_url')),
            esc_url(zynith_seo_get_custom_field('youtube_url'))
        ]));

        // Build the JSON-LD schema array with dynamic values
        $local_schema = [
            "@context" => "https://schema.org/",
            "@type" => "Service",
            "serviceType" => esc_html($business_type),  // Dynamically set service type from 'business_type'
            "provider" => [
                "@type" => "LocalBusiness",
                "name" => esc_html($business_name),
                "image" => esc_url($business_image),
                "telephone" => esc_html($business_phone),
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => esc_html($business_address),
                    "addressLocality" => esc_html($business_locality),
                    "addressRegion" => esc_html($business_region),
                    "postalCode" => esc_html($business_postal_code),
                    "addressCountry" => esc_html($country)
                ],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => esc_html($aggregate_rating),
                    "reviewCount" => esc_html($total_ratings)
                ],
                "sameAs" => $service_url
            ],
            "areaServed" => [
                "@type" => "GeoCircle",
                "geoMidpoint" => [
                    "@type" => "GeoCoordinates",
                    "latitude" => esc_html($latitude),
                    "longitude" => esc_html($longitude)
                ],
                "geoRadius" => esc_html($geo_radius)
            ],
            "sameAs" => $social_links  // Include dynamic social media links
        ];

        // Output JSON-LD schema
        echo "\n<!-- Local Schema for Home Page -->\n";
        echo '<script type="application/ld+json">' . json_encode($local_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
        echo "\n<!-- End Local Schema -->\n";
    }
}

// Output Enhanced Post Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_post_schema() {
    global $post;
    
    // Retrieve organization and author details from the plugin settings
    $organization_name  = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $organization_logo  = zynith_seo_get_custom_field('business_logo_url') ?: get_site_icon_url();
    $aggregate_rating   = zynith_seo_get_custom_field('aggregate_rating');
    $total_ratings      = zynith_seo_get_custom_field('total_ratings');

    $post_schema = [
        "@context" => "https://schema.org",
        "@type" => "BlogPosting",
        "headline" => get_the_title(),
        "description" => has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_shortcodes($post->post_content), 30, '...'), // Use excerpt or auto-generate
        "datePublished" => get_the_date(DATE_ISO8601),
        "dateModified" => get_the_modified_date(DATE_ISO8601),
        "url" => get_permalink(), // URL of the post
        "inLanguage" => get_bloginfo('language'), // Language setting of the post
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => get_permalink()
        ],
        "author" => [
            "@type" => "Person",
            "name" => get_the_author_meta('display_name', $post->post_author),
            "url" => get_author_posts_url($post->post_author)
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => $organization_name,
            "url" => home_url(),
            "logo" => [
                "@type" => "ImageObject",
                "url" => esc_url($organization_logo)
            ]
        ]
    ];

    // Add featured image details if available
    if (has_post_thumbnail()) {
        $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $post_schema['image'] = [
            "@type" => "ImageObject",
            "url" => $featured_image[0],
            "width" => $featured_image[1],
            "height" => $featured_image[2]
        ];
    }

    // Add keywords from post tags if available
    $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);
    if (!empty($tags)) $post_schema['keywords'] = implode(", ", $tags); // Comma-separated list of tags
    
    // Add primary category if available (fallback to general categories if not)
    $categories = get_the_category($post->ID);
    if (!empty($categories)) {
        $primary_category = $categories[0]->name; // Get the first category
        $post_schema['articleSection'] = $primary_category;
    }

    // Add AggregateRating if ratings data is stored
    if (!empty($aggregate_rating) && !empty($total_ratings)) {
        $post_schema['aggregateRating'] = [
            "@type" => "AggregateRating",
            "ratingValue" => esc_html($aggregate_rating),
            "reviewCount" => esc_html($total_ratings)
        ];
    }

    echo "\n<!-- Post Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($post_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End Post Schema -->\n";
}

// Output Enhanced Page Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_page_schema() {
    global $post;
    
    // Retrieve organization details from the plugin settings
    $organization_name  = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $organization_logo  = zynith_seo_get_custom_field('business_logo_url') ?: get_site_icon_url();
    $aggregate_rating   = zynith_seo_get_custom_field('aggregate_rating');
    $total_ratings      = zynith_seo_get_custom_field('total_ratings');
    $business_type      = zynith_seo_get_custom_field('business_type') ?: 'Organization';

    $page_schema = [
        "@context"  => "https://schema.org",
        "@type"     => "WebPage",
        "name"      => get_the_title(),
        "url"       => get_permalink(),
        "datePublished" => get_the_date(DATE_ISO8601),
        "dateModified"  => get_the_modified_date(DATE_ISO8601),
        "description"   => has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_shortcodes($post->post_content), 30, '...'),
        "inLanguage"    => get_bloginfo('language'),
        "mainEntityOfPage"  => [
            "@type" => "WebPage",
            "@id" => get_permalink()
        ],
        "author" => [
            "@type" => "Person",
            "name"  => get_the_author_meta('display_name', $post->post_author),
            "url"   => get_author_posts_url($post->post_author)
        ],
        "publisher" => [
            "@type" => $business_type,
            "name"  => $organization_name,
            "url"   => home_url(),
            "logo"  => [
                "@type" => "ImageObject",
                "url"   => esc_url($organization_logo)
            ]
        ]
    ];

    // Add featured image details if available
    if (has_post_thumbnail()) {
        $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        $page_schema['image'] = [
            "@type" => "ImageObject",
            "url" => $featured_image[0],
            "width" => $featured_image[1],
            "height" => $featured_image[2]
        ];
    }

    // Add keywords from post tags, if available
    $tags = wp_get_post_tags($post->ID, ['fields' => 'names']);
    if (!empty($tags)) $page_schema['keywords'] = implode(", ", $tags);
    
    // Add AggregateRating if ratings data is stored
    if (!empty($aggregate_rating) && !empty($total_ratings)) {
        $page_schema['aggregateRating'] = [
            "@type" => "AggregateRating",
            "ratingValue" => esc_html($aggregate_rating),
            "reviewCount" => esc_html($total_ratings)
        ];
    }

    echo "\n<!-- Page Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($page_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End Page Schema -->\n";
}

// Output Enhanced Author Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_author_schema() {
    $author = get_queried_object();
    
    // Get author's profile picture if available
    $author_image = get_avatar_url($author->ID);
    
    // Get author's bio if available
    $author_bio = get_the_author_meta('description', $author->ID);

    // Get social media links from plugin settings if set, or fallback to author meta fields
    $social_links = [];
    $social_fields = ['facebook_url', 'linkedin_url', 'twitter_url', 'instagram_url'];
    foreach ($social_fields as $field) {
        // Check for social links in plugin settings first, then author profile
        $social_url = zynith_seo_get_custom_field($field) ?: get_the_author_meta(str_replace('_url', '', $field), $author->ID);
        if ($social_url) $social_links[] = esc_url($social_url);
    }

    // Retrieve organization details from plugin settings
    $organization_name  = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $organization_logo  = zynith_seo_get_custom_field('business_logo_url') ?: get_site_icon_url();
    $aggregate_rating   = zynith_seo_get_custom_field('aggregate_rating');
    $total_ratings      = zynith_seo_get_custom_field('total_ratings');
    $business_type      = zynith_seo_get_custom_field('business_type') ?: 'Organization';
    $publishing_principles_url  = home_url('/publishing-principles'); // Default to site URL for publishing principles

    // Get total number of posts by the author
    $author_post_count = count_user_posts($author->ID);

    // Build the author schema with additional fields
    $author_schema = [
        "@context" => "https://schema.org",
        "@type" => "Person",
        "name" => $author->display_name,
        "url" => get_author_posts_url($author->ID),
        "description" => $author_bio ?: '', // Adds bio if available
        "image" => $author_image, // Profile picture
        "sameAs" => $social_links, // Social media links from plugin settings or author profile
        "knowsAbout" => "Articles", // Contextual relevance; could also pull categories or tags if consistent with author expertise
        "mainEntityOfPage" => [
            "@type" => "ProfilePage",
            "@id" => get_author_posts_url($author->ID)
        ],
        "worksFor" => [
            "@type" => $business_type,
            "name" => $organization_name,
            "url" => home_url(),
            "logo" => [
                "@type" => "ImageObject",
                "url" => esc_url($organization_logo)
            ]
        ],
        "publishingPrinciples" => esc_url($publishing_principles_url), // Link to content policy if applicable
        "totalPosts" => $author_post_count // Total posts count for the author
    ];

    // Add AggregateRating if ratings data is stored
    if (!empty($aggregate_rating) && !empty($total_ratings)) {
        $author_schema['worksFor']['aggregateRating'] = [
            "@type" => "AggregateRating",
            "ratingValue" => esc_html($aggregate_rating),
            "reviewCount" => esc_html($total_ratings)
        ];
    }
    
    echo "\n<!-- Author Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($author_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End Author Schema -->\n";
}

// Output Enhanced Date Archive Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_date_schema() {
    // Get the year and month from the query
    $year = get_query_var('year');
    $month = get_query_var('monthnum');
    
    // Generate start and end dates for the month in ISO8601 format
    $start_date = date("Y-m-01", strtotime("$year-$month-01"));
    $end_date = date("Y-m-t", strtotime("$year-$month-01"));
    
    // Count posts in this archive month
    $archive_query = new WP_Query([
        'year' => $year,
        'monthnum' => $month,
        'posts_per_page' => -1,
    ]);
    $post_count = $archive_query->found_posts;

    // Retrieve organization details from the plugin settings
    $organization_name = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $organization_logo = zynith_seo_get_custom_field('business_logo_url') ?: get_site_icon_url();
    $aggregate_rating = zynith_seo_get_custom_field('aggregate_rating');
    $total_ratings = zynith_seo_get_custom_field('total_ratings');
    $business_type = zynith_seo_get_custom_field('business_type') ?: 'Organization';

    // Build the schema
    $date_schema = [
        "@context" => "https://schema.org",
        "@type" => "CollectionPage",
        "name" => single_month_title(' ', false) . " Archives",
        "url" => get_month_link($year, $month),
        "datePublished" => $start_date,
        "dateModified" => $end_date,
        "inLanguage" => get_bloginfo('language'), // Language of the archive page
        "about" => "Archive of posts published in " . single_month_title(' ', false), // Summary
        "publisher" => [
            "@type" => $business_type,
            "name" => $organization_name,
            "logo" => [
                "@type" => "ImageObject",
                "url" => esc_url($organization_logo)
            ]
        ],
        "mainEntity" => [
            "@type" => "ItemList",
            "numberOfItems" => $post_count,
            "itemListOrder" => "Descending",
            "itemListElement" => []
        ]
    ];

    // Add aggregate rating if available
    if (!empty($aggregate_rating) && !empty($total_ratings)) {
        $date_schema['publisher']['aggregateRating'] = [
            "@type" => "AggregateRating",
            "ratingValue" => esc_html($aggregate_rating),
            "reviewCount" => esc_html($total_ratings)
        ];
    }

    // Add individual post details as part of the main entity item list
    if ($post_count > 0) {
        foreach ($archive_query->posts as $index => $post) {
            $date_schema['mainEntity']['itemListElement'][] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "url" => get_permalink($post->ID),
                "name" => get_the_title($post->ID),
                "datePublished" => get_the_date(DATE_ISO8601, $post->ID),
                "author" => [
                    "@type" => "Person",
                    "name" => get_the_author_meta('display_name', $post->post_author)
                ]
            ];
        }
    }

    echo "\n<!-- Date Archive Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($date_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End Date Archive Schema -->\n";
}

// Output Enhanced About Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_about_schema() {
    // Retrieve organization details from the plugin's custom fields
    $organization_name  = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $business_type      = zynith_seo_get_custom_field('business_type');
    $business_address   = zynith_seo_get_custom_field('business_address');
    $business_email     = zynith_seo_get_custom_field('business_email') ?: get_option('admin_email');
    $business_phone     = zynith_seo_get_custom_field('business_phone');
    $business_locality  = zynith_seo_get_custom_field('business_locality');
    $business_region    = zynith_seo_get_custom_field('business_region');
    $business_logo      = zynith_seo_get_custom_field('business_logo_url') ?: get_site_icon_url();

    // Collect social media links from plugin settings
    $social_links = [
        zynith_seo_get_custom_field('facebook_url'),
        zynith_seo_get_custom_field('youtube_url'),
        zynith_seo_get_custom_field('instagram_url'),
        zynith_seo_get_custom_field('twitter_url')
    ];
    $social_links = array_filter($social_links); // Remove empty links

    // Build the About schema with dynamic fields from plugin settings
    $about_schema = [
        "@context"  => "https://schema.org",
        "@type"     => "AboutPage",
        "name"      => get_the_title(), // Pulls the About page title
        "url"       => get_permalink(), // URL of the About page
        "description"   => has_excerpt() ? get_the_excerpt() : wp_trim_words(strip_shortcodes(get_the_content()), 40, '...'), // About page excerpt or auto-generated summary
        "mainEntity"    => [
            "@type" => "Organization",
            "name"  => $organization_name,
            "logo"  => [
                "@type" => "ImageObject",
                "url"   => esc_url($business_logo)
            ],
            "contactPoint"  => [
                "@type" => "ContactPoint",
                "contactType"   => "Customer Service",
                "telephone"     => esc_html($business_phone),
                "email"         => esc_html($business_email),
                "availableLanguage" => get_bloginfo('language')
            ],
            "address"   => [
                "@type" => "PostalAddress",
                "streetAddress" => esc_html($business_address),
                "addressLocality"   => esc_html($business_locality),
                "addressRegion"     => esc_html($business_region)
            ],
            "sameAs"    => array_map('esc_url', $social_links) // Add social media profiles dynamically
        ]
    ];

    if (!empty($business_type)) $about_schema['mainEntity']['@type'] = esc_html($business_type);
    
    echo "\n<!-- About Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($about_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End About Schema -->\n";
}

// Output Enhanced Contact Schema Markup using Zynith SEO Plugin Settings
function zynith_seo_output_contact_schema() {
    // Retrieve contact information from the plugin's settings
    $business_name = zynith_seo_get_custom_field('company_name') ?: get_bloginfo('name');
    $business_phone = zynith_seo_get_custom_field('business_phone');
    $business_email = zynith_seo_get_custom_field('business_email') ?: get_option('admin_email');
    $business_address = zynith_seo_get_custom_field('business_address');
    $business_locality = zynith_seo_get_custom_field('business_locality');
    $business_region = zynith_seo_get_custom_field('business_region');

    // Collect social media links from plugin settings
    $social_links = [
        zynith_seo_get_custom_field('facebook_url'),
        zynith_seo_get_custom_field('linkedin_url'),
        zynith_seo_get_custom_field('youtube_url'),
        zynith_seo_get_custom_field('instagram_url'),
        zynith_seo_get_custom_field('twitter_url')
    ];
    $social_links = array_filter($social_links); // Remove any empty links

    // Build the Contact schema
    $contact_schema = [
        "@context" => "https://schema.org",
        "@type" => "ContactPage",
        "name" => get_the_title(), // Pulls the Contact page title
        "url" => get_permalink(),
        "mainEntity" => [
            "@type" => "Organization",
            "name" => $business_name,
            "contactPoint" => [
                "@type" => "ContactPoint",
                "contactType" => "Customer Service",
                "telephone" => esc_html($business_phone),
                "email" => esc_html($business_email),
                "availableLanguage" => get_bloginfo('language')
            ],
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => esc_html($business_address),
                "addressLocality" => esc_html($business_locality),
                "addressRegion" => esc_html($business_region)
            ],
            "sameAs" => array_map('esc_url', $social_links) // Add social media profiles dynamically
        ]
    ];

    echo "\n<!-- Contact Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($contact_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End Contact Schema -->\n";
}

// Output FAQ Schema Markup
function zynith_seo_output_faq_schema() {
    $faq_schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "name" => "Frequently Asked Questions",
        "url" => get_permalink()
    ];
    echo "\n<!-- FAQ Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    echo "\n<!-- End FAQ Schema -->\n";
}

function zynith_seo_enqueue_schema_editor_assets() {
    // Enqueue WordPress media scripts (required for media library)
    wp_enqueue_media();

    $script_file_path = plugin_dir_path(dirname(__FILE__)) . 'assets/js/automatic-schema-editor.js';
    $script_url       = plugin_dir_url(dirname(__FILE__))  . 'assets/js/automatic-schema-editor.js';

    // Use filemtime() for cache-busting if the file exists
    $version = file_exists($script_file_path) ? filemtime($script_file_path) : ZYNITH_SEO_VERSION;

    wp_enqueue_script('zynith-seo-admin-js', $script_url, ['jquery'], $version, true);
}

function zynith_seo_load_schema_editor_page() {
    add_action('admin_enqueue_scripts', 'zynith_seo_enqueue_schema_editor_assets');
}
add_action('load-zynith-seo_page_zynith_seo_schema_editor', 'zynith_seo_load_schema_editor_page');