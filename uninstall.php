<?php
// Make sure uninstallation is triggered
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

/**
 * Remove capabilities
 */
function dpmm_remove_capabilities()
{
    global $wpdb;
    $wp_roles = get_option($wpdb->prefix . 'user_roles');

    if ($wp_roles && is_array($wp_roles)) {
        foreach ($wp_roles as $role => $role_details) {
            $get_role = get_role($role);
            $get_role->remove_cap('dpmm_view_site');
            $get_role->remove_cap('dpmm_control');
        }
    }
}

/**
 * Uninstall - clean up database removing plugin options
*/
function dpmm_delete_plugin()
{
    delete_option('dpmm-content-default');
    delete_option('dpmm-content');
    delete_option('dpmm-enabled');
    delete_option('dpmm-site-title');
    delete_option('dpmm-roles');
    delete_option('dpmm-mode');
    delete_option('dpmm_code_snippet');

    // remove capabilities
    dpmm_remove_capabilities();
}

dpmm_delete_plugin();