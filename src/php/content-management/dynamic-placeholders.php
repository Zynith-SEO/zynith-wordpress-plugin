<?php

defined('ABSPATH') or exit;

/**
 * Replace placeholders in a given string with dynamic metadata values.
 *
 * @param string $content The string containing placeholders.
 * @param string $type    The type of object (e.g., 'post', 'taxonomy', 'author', 'home').
 * @param object $object  The WordPress object (e.g., WP_Post, WP_Term, WP_User).
 * @return string Modified string with placeholders replaced.
 */
function zynithseo_replace_placeholders_in_content($content, $type, $object) {
    // Get global post object (or however you like to fetch post data).
    $post = get_post();
    if (!$post) return $content;

    $timezone = get_option('timezone_string') ? get_option('timezone_string') : 'UTC';
    
    $metadata = [
        'sitetitle'     => get_bloginfo('name'),
        'tagline'       => get_bloginfo('description'),
        'home_url'      => esc_url(home_url() ?? ''),
        'currentyear'   => date('Y'),
        'object_id'     => '',
        'title'         => '',
        'url'           => '',
        'post_date'     => '',
        'author_name'   => '',
        'modified_date' => '',
	];
    
    switch ($type) {
        case 'taxonomy':
            $metadata['object_id']      = isset($object->term_id) ? $object->term_id : '';
            $metadata['title']          = $object->name ?: '';
            $metadata['url']            = rawurldecode(get_term_link($metadata['object_id'], $object->taxonomy));
            $metadata['post_date']      = current_time('Y-m-d');
            $metadata['modified_date']  = current_time('Y-m-d');	
            $metadata['author_name']    = get_the_author_meta('display_name', get_current_user_id());
            break;
        case 'author':
            $metadata['object_id']      = isset($object->ID) ? $object->ID : '';
            $metadata['title']          = sanitize_text_field($object->display_name);
            $metadata['url']            = rawurldecode(get_author_posts_url($metadata['object_id'], $object->user_nicename));
            $metadata['author_name']    = sanitize_text_field($object->display_name);
            $registered                 = new DateTime($object->user_registered);
            $metadata['post_date']      = $registered->format('Y-m-d');
            $metadata['modified_date']  = current_time('Y-m-d');
            break;
        case 'post':
            $metadata['object_id']      = isset($object->ID) ? $object->ID : '';
            $metadata['title']          = sanitize_text_field(get_the_title($metadata['object_id']));
            $metadata['url']            = rawurldecode(get_permalink($object->ID));
            $metadata['post_date']      = get_the_date('Y-m-d\TH:i:sP', $post);
            $metadata['author_name']    = sanitize_text_field(get_the_author_meta('display_name', $object->post_author));
            $metadata['modified_date']  = get_post_modified_time('Y-m-d\TH:i:sP', false, $post, false);
            break;
        case 'home':
            $metadata['url'] = rawurldecode(home_url());
            break;
        default:
            break;
    }

    // The final transformation is what gets replaced in the content.
    $placeholders = [
        '%%sitetitle%%'     => $metadata['sitetitle'],
        '%%tagline%%'       => $metadata['tagline'],
        '%%home_url%%'      => $metadata['home_url'],
        '%%currentyear%%'   => $metadata['currentyear'],
        '%%title%%'         => $metadata['title'],
        '%%post_url%%'      => $metadata['url'],
        '%%date%%'          => isset($metadata['post_date']) ? esc_html((new DateTime($metadata['post_date'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($timezone))->format('Y-m-d\TH:i:sP')) : '',
        '%%modified%%'      => isset($metadata['modified_date']) ? esc_html((new DateTime($metadata['modified_date'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($timezone))->format('Y-m-d\TH:i:sP')) : '',
        '%%author%%'        => $metadata['author_name'],
    ];
    
    // Perform simple string replacements throughout the content.
    $updated_content = str_replace(
        array_keys($placeholders),
        array_values($placeholders),
        $content
    );
    return $updated_content;
}