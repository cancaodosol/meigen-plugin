<?php
if ( ! defined('ABSPATH') ) exit;

function meigen_register_book_cpt() {
    register_post_type('book', array(
        'label' => '本',
        'public' => true,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'thumbnail', 'editor'),
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'books'),
    ));
}
add_action('init', 'meigen_register_book_cpt');

function meigen_enable_post_thumbnails_for_book() {
    if ( function_exists('add_post_type_support') ) {
        add_post_type_support('book', 'thumbnail');
    }
}
add_action('init', 'meigen_enable_post_thumbnails_for_book', 20);
