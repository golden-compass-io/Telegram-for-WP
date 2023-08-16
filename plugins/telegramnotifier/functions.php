<?php
function enqueue_custom_admin_scripts() {
    wp_enqueue_script('checkbox-script', plugin_dir_url(__FILE__) . 'assets/js/checkbox.js', array(), '1.0', true);
    wp_enqueue_style('main-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');
 