<?php

if (!defined('ABSPATH')) exit;

// Move the Plugin File Editor to the last position in the Plugins menu
function zynith_seo_move_plugin_editor_menu() {
    remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
    add_submenu_page( 'plugins.php', 'Plugin Editor', 'Plugin Editor', 'edit_plugins', 'plugin-editor.php', '', 100 );
}
add_action('admin_menu', 'zynith_seo_move_plugin_editor_menu', 999);