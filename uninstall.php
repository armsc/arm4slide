<?php
//if uninstall not called from WordPress exit
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
// multisite handling
if (is_multisite()) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
    if ($blogs) {
        foreach($blogs as $blog) {
            switch_to_blog($blog['blog_id']);
            delete_option('arm4slide');
        }
        restore_current_blog();
    }
} else {
    delete_option('my_plugin_option');
}
?>
