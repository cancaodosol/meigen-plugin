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

function qc_quotes_columns($columns) {
    $date = $columns['date'] ?? null;
    $stats_key = null;
    foreach (['stats', 'jetpack_stats'] as $key) {
        if (isset($columns[$key])) {
            $stats_key = $key;
            break;
        }
    }
    $stats = $stats_key ? $columns[$stats_key] : null;

    unset($columns['date']);
    if ($stats_key) {
        unset($columns[$stats_key]);
    }

    // 並び順: チェックボックス → タイトル → 出典（本） → 著者 → その他 → 統計 → 日付
    $new = [];

    if (isset($columns['cb'])) {
        $new['cb'] = $columns['cb'];
        unset($columns['cb']);
    }

    if (isset($columns['title'])) {
        $new['title'] = $columns['title'];
        unset($columns['title']);
    }

    $new['qc_quote_book'] = '出典（本）';
    $new['qc_quote_author'] = '著者';

    foreach ($columns as $key => $label) {
        $new[$key] = $label;
    }

    if ($stats_key && $stats) {
        $new[$stats_key] = $stats;
    }
    if ($date) {
        $new['date'] = $date;
    }

    return $new;
}
add_filter('manage_quotes_posts_columns', 'qc_quotes_columns');

function qc_quotes_custom_column($column, $post_id) {
    if ($column === 'qc_quote_book') {
        $book_id = get_post_meta($post_id, '_qc_quote_book', true);
        if ($book_id) {
            $title = get_the_title($book_id);
            $link = get_edit_post_link($book_id);
            echo $title ? '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>' : '-';
        } else {
            echo '-';
        }
    }

    if ($column === 'qc_quote_author') {
        $book_id = get_post_meta($post_id, '_qc_quote_book', true);
        $author_id = $book_id ? get_post_meta($book_id, '_qc_book_author', true) : null;

        if ($author_id) {
            $title = get_the_title($author_id);
            $link = get_edit_post_link($author_id);
            echo $title ? '<a href="' . esc_url($link) . '">' . esc_html($title) . '</a>' : '-';
        } else {
            echo '-';
        }
    }
}
add_action('manage_quotes_posts_custom_column', 'qc_quotes_custom_column', 10, 2);
