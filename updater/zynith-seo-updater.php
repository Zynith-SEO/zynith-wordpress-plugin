<?php
/**
 * Module Name: Zynith SEO - Plugin Updater
 * Description: Handles plugin updates and related functionality.
 * Version:     1.0.6
 * Author:      Zynith SEO
 */
defined('ABSPATH') or exit;

// Define constants for cache
define('ZYNITH_SEO_UPDATER_CACHE_KEY', 'zynith_custom_updater');
define('ZYNITH_SEO_UPDATER_CACHE_DURATION', DAY_IN_SECONDS);

/**
 * Initialize the updater functionality.
 */
function zynith_seo_updater_init() {
    if (!is_admin()) return;
    
    global $pagenow;
    
    // Only run these filters on certain admin pages
    $allowed_pages = ['plugins.php', 'update-core.php', 'plugin-install.php', 'admin-ajax.php'];
    $is_cron = defined('DOING_CRON') && DOING_CRON;
    $is_ajax = defined('DOING_AJAX') && DOING_AJAX;
    if (!in_array($pagenow, $allowed_pages, true) && !$is_cron && !$is_ajax) return;
        
    // Purge cache if `force-check` is triggered
    $force_check_updates = filter_input(INPUT_GET, 'force-check');
    if (!empty($force_check_updates)) {
        zynith_seo_purge_cache();
        if (function_exists('wp_update_plugins')) wp_update_plugins();
    }

    // Attach updater functionality to WordPress hooks
    add_filter('plugins_api', 'zynith_seo_get_plugin_info', 20, 3);
    add_filter('site_transient_update_plugins', 'zynith_seo_check_for_updates');
    add_action('upgrader_process_complete', 'zynith_seo_purge_cache_after_update', 10, 2);
    add_action('in_plugin_update_message-' . plugin_basename(ZYNITH_SEO_FILE), 'zynith_seo_add_custom_update_message', 20, 2);
}
add_action('init', 'zynith_seo_updater_init');

/**
 * Fetch plugin information for the WordPress Plugins API.
 */
function zynith_seo_get_plugin_info($res, $action, $args) {
    if ('plugin_information' !== $action || ZYNITH_SEO_SLUG !== $args->slug) return $res;
    
    $remote = zynith_seo_fetch_remote_data();
    if (!$remote) return $res;
    
    $res = new stdClass();
    $res->name = $remote->name;
    $res->slug = $remote->slug;
    $res->version = $remote->version;
    $res->tested = $remote->tested;
    $res->requires = $remote->requires;
    $res->author = $remote->author;
    $res->author_profile = $remote->author_profile;
    $res->download_link = $remote->download_url;
    $res->trunk         = $remote->download_url;
    $res->requires_php = $remote->requires_php;
    $res->last_updated = $remote->last_updated;
    $res->rating = $remote->rating;
    $res->num_ratings = $remote->num_ratings;

    $res->sections = [
        'description' => $remote->sections->description ?? '',
        'installation' => $remote->sections->installation ?? '',
        'faq' => $remote->sections->faq ?? '',
        'changelog' => $remote->sections->changelog ?? '',
    ];

    if (!empty($remote->banners)) {
        $res->banners = [
            'low' => $remote->banners->low ?? '',
            'high' => $remote->banners->high ?? '',
        ];
    }

    return $res;
}

/**
 * Check for plugin updates.
 */
function zynith_seo_check_for_updates($transient) {
    if (empty($transient->checked)) return $transient;
    
    $remote = zynith_seo_fetch_remote_data();
    if (
        $remote &&
        version_compare(ZYNITH_SEO_VERSION, $remote->version, '<') &&
        version_compare($remote->requires, get_bloginfo('version'), '<=') &&
        version_compare($remote->requires_php, PHP_VERSION, '<=')
    ) {
        $res = new stdClass();
        $res->slug = ZYNITH_SEO_SLUG;
        $res->plugin = plugin_basename(ZYNITH_SEO_FILE);
        $res->new_version = $remote->version;
        $res->tested = $remote->tested;
        $res->package = $remote->download_url;

        $transient->response[$res->plugin] = $res;
    }

    return $transient;
}

/**
 * Fetch remote update data, with caching.
 */
function zynith_seo_fetch_remote_data() {
    static $cached_response = null; // Cache for the current request cycle
    if (!is_null($cached_response)) return $cached_response; // Return the same data on subsequent calls this page load
    //$cached_response = get_transient(ZYNITH_SEO_UPDATER_CACHE_KEY);
    //if ($cached_response !== false && is_string($cached_response)) return json_decode($cached_response);
    
    update_option('zynith_seo_admin_message', '');

    $license_key = get_option('zynith_license_key', '');
    $tbyb = get_option('zynith_seo_tbyb', '');
    $api_url = 'https://zynith.app/wp-json/zynith/v1/update-info/?cc=' . time();
    $response = wp_remote_get($api_url, [
        'timeout' => 10,
        'headers' => [
            'Accept' => 'application/json',
            'X-Zynith-License' => $license_key,
            'X-Zynith-TBYB' => $tbyb,
        ],
    ]);
    
    $http_code = wp_remote_retrieve_response_code($response);
    if (is_wp_error($response) || $http_code !== 200) return false;
    
    zynith_seo_access_log();
    $remote_body = wp_remote_retrieve_body($response);
    if (empty($remote_body) || !is_string($remote_body)) return false;
    
    set_transient(ZYNITH_SEO_UPDATER_CACHE_KEY, $remote_body, ZYNITH_SEO_UPDATER_CACHE_DURATION);
    
    $cached_response = json_decode($remote_body);
    return $cached_response;
}

function zynith_seo_access_log() {
    $license_key    = get_option('zynith_license_key', '');
    $post_response  = wp_remote_post('https://zynith.app/wp-admin/admin-ajax.php?action=zynith_seo_log', [
        'method'    => 'POST',
        'timeout'   => 15,
        'headers'   => [
            'Content-Type' => 'application/json'
        ],
        'body'      => json_encode([
            'zynith_seo_license_key'    => $license_key,
            'zynith_seo_domain'         => home_url(),
            'zynith_seo_version'        => ZYNITH_SEO_VERSION
        ])
    ]);
    if (!is_wp_error($post_response)) {
        $body = wp_remote_retrieve_body($post_response);

        // Strip BOM from the response body
        $body = preg_replace('/^\xEF\xBB\xBF/', '', $body);
        $data = json_decode($body, true);
        if (isset($data['success']) && $data['success'] === true) {
            $message    = isset($data['data']['message']) ? $data['data']['message'] : '';
            $result     = update_option('zynith_seo_admin_message', $message);
            if (isset($data['data']['status'])) {
                $status = $data['data']['status'];
                update_option('zynith_seo_tbyb', $status);
            }
        }
    }
}

/**
 * Purge cached update data.
 */
function zynith_seo_purge_cache() {
    delete_transient(ZYNITH_SEO_UPDATER_CACHE_KEY);
}

/**
 * Purge the cache after a plugin update.
 */
function zynith_seo_purge_cache_after_update($upgrader, $options) {
    if (isset($options['action']) && $options['action'] === 'update' && isset($options['type']) && $options['type'] === 'plugin') zynith_seo_purge_cache();
}

/**
 * Add a custom update message if the plugin update is unavailable.
 */
function zynith_seo_add_custom_update_message($plugin_data, $response) {
    if (empty($response->package)) {
        echo '<br>' . sprintf(
            __(
                "To enable updates, please enter a valid license key on the %sLicense%s page. If you don't have a license key, please %sobtain one here%s.",
                ZYNITH_SEO_TEXT_DOMAIN
            ),
            '<a href="' . admin_url('/admin.php?page=zynith_seo_dashboard') . '">',
            '</a>',
            '<a href="https://zynith.app/" target="_blank">',
            '</a>'
        );
    }
}