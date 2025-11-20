<?php
function qc_register_authors() {
    register_post_type('authors', [
        'label' => '著者',
        'public' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => ['title', 'editor', 'thumbnail'],
        'has_archive' => true,
    ]);
}
add_action('init', 'qc_register_authors');
