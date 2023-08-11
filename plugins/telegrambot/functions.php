<?php
function enqueue_custom_admin_scripts() {
    wp_enqueue_script('checkbox-script', plugin_dir_url(__FILE__) . 'js/checkbox.js', array(), '1.0', true);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');
