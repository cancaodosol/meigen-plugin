<?php
if ( ! defined('ABSPATH') ) exit;

function meigen_register_quote_cpt() {
    register_post_type('quote', array(
        'label' => '名言',
        'public' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => array('title', 'editor', 'excerpt'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'quotes'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'meigen_register_quote_cpt');
