<?php
/*
Plugin Name:       Zynith SEO
Plugin URI:        https://zynith.app/wordpress-plugin-zynith-seo-readme/
Description:       A powerful yet lightweight SEO plugin designed for maximum efficiency and to streamline SEO management for optimal search engine results.
Version:           10.5.1
Author:            Zynith SEO
Author URI:        https://zynith.app/
Text Domain:       zynith-seo
Contributors:      Schieler Mew, Kellie Watson
License:           GPL-3.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
Tested up to:      6.7.2
Requires at least: 5.0
Requires PHP:      7.4
Donate link:       https://www.paypal.com/donate/?hosted_button_id=XVXQ3RX7N4SQN
Tags:              SEO, XML sitemap, Schema Markup, Meta Tags, Robots.txt, SEO Signals, WordPress SEO, Breadcrumbs, TOC
Support:           https://www.facebook.com/groups/761871078859984
*/
defined('ABSPATH') or exit;

define('ZYNITH_SEO_VERSION', '10.5.1');

define('ZYNITH_SEO_TEXT_DOMAIN', 'zynith-seo');
define('ZYNITH_SEO_FILE', __FILE__);
define('ZYNITH_SEO_SLUG', plugin_basename(__FILE__));
define('ZYNITH_SEO_DIR', plugin_dir_path(__FILE__));
define('ZYNITH_SEO_URL', plugin_dir_url(__FILE__));

// Load required files
require_once ZYNITH_SEO_DIR . 'updater/zynith-seo-updater.php';
require_once ZYNITH_SEO_DIR . 'dashboard/zynith-dashboard-information-widget.php';
require_once ZYNITH_SEO_DIR . 'dashboard/zynith-settings.php';