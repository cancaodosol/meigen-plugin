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
    // 既存の列を控えておき、末尾に日付/統計などを回す
    $date = $columns['date'] ?? null;
    $stats_key = null;
    foreach (['stats', 'jetpack_stats'] as $key) {
        if (isset($columns[$key])) {
            $stats_key = $key;
            break;
        }
    }

    $stats = $stats_key ? $columns[$stats_key] : null;

    // 末尾にしたい列を一度外す
    unset($columns['date']);
    if ($stats_key) {
        unset($columns[$stats_key]);
    }

    // 固定位置で追加したい列
    $columns['qc_book_author'] = '著者';
    $columns['qc_purchase_link'] = '購入リンク';

    // 日付/統計列を末尾へ
    if ($stats_key && $stats) {
        $columns[$stats_key] = $stats;
    }
    if ($date) {
        $columns['date'] = $date;
    }

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
