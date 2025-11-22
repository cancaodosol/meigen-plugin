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

function qc_books_columns($columns) {
    $columns['qc_book_author'] = '著者';
    $columns['qc_purchase_link'] = '購入リンク';
    return $columns;
}
add_filter('manage_books_posts_columns', 'qc_books_columns');

function qc_books_custom_column($column, $post_id) {
    if ($column === 'qc_book_author') {
        $author_id = get_post_meta($post_id, '_qc_book_author', true);
        if ($author_id) {
            $title = get_the_title($author_id);
            $link = get_permalink($author_id);
            echo $title ? '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>' : '-';
        } else {
            echo '-';
        }
    }

    if ($column === 'qc_purchase_link') {
        $link = get_post_meta($post_id, '_qc_purchase_link', true);
        if ($link) {
            echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_url($link) . '</a>';
        } else {
            echo '-';
        }
    }
}
add_action('manage_books_posts_custom_column', 'qc_books_custom_column', 10, 2);
