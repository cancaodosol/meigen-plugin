<?php
function qc_register_books() {
    register_post_type('books', [
        'label' => '本',
        'public' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-book',
        'supports' => ['title', 'editor', 'thumbnail'],
        'has_archive' => true,
    ]);
}
add_action('init', 'qc_register_books');
