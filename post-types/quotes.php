<?php
function qc_register_quotes() {
    register_post_type('quotes', [
        'label' => '名言',
        'public' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title', 'editor'],
        'has_archive' => true,
    ]);
}
add_action('init', 'qc_register_quotes');
