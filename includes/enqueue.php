<?php
if ( ! defined('ABSPATH') ) exit;

function meigen_enqueue_assets() {
    wp_enqueue_style('meigen-plugin-style', MEIGEN_PLUGIN_URL . 'assets/meigen.css');
}
add_action('wp_enqueue_scripts', 'meigen_enqueue_assets');
