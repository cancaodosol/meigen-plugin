<?php
if ( ! defined('ABSPATH') ) exit;

// 本用メタボックス（購入リンクなど）
function meigen_add_book_meta_box() {
    add_meta_box(
        'meigen_book_meta',
        '本の追加情報',
        'meigen_book_meta_callback',
        'book',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'meigen_add_book_meta_box');

function meigen_book_meta_callback($post) {
    wp_nonce_field('meigen_save_book_meta', 'meigen_book_meta_nonce');

    $purchase_link = get_post_meta($post->ID, 'book_purchase_link', true);
    $publisher = get_post_meta($post->ID, 'book_publisher', true);
    $isbn = get_post_meta($post->ID, 'book_isbn', true);

    echo '<p><label>購入リンク（URL）</label>';
    echo "<input type='url' name='book_purchase_link' value='" . esc_attr($purchase_link) . "' style='width:100%;' /></p>";

    echo '<p><label>出版社</label>';
    echo "<input type='text' name='book_publisher' value='" . esc_attr($publisher) . "' style='width:100%;' /></p>";

    echo '<p><label>ISBN</label>';
    echo "<input type='text' name='book_isbn' value='" . esc_attr($isbn) . "' style='width:100%;' /></p>";

    echo '<p>表紙画像は「アイキャッチ画像」から設定してください。</p>';
}

function meigen_save_book_meta($post_id) {
    if ( ! isset($_POST['meigen_book_meta_nonce']) ) return;
    if ( ! wp_verify_nonce($_POST['meigen_book_meta_nonce'], 'meigen_save_book_meta') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( isset($_POST['post_type']) && 'book' == $_POST['post_type'] ) {
        if ( ! current_user_can('edit_post', $post_id) ) return;
    }

    if ( isset($_POST['book_purchase_link']) ) {
        update_post_meta($post_id, 'book_purchase_link', esc_url_raw($_POST['book_purchase_link']));
    } else {
        delete_post_meta($post_id, 'book_purchase_link');
    }

    if ( isset($_POST['book_publisher']) ) {
        update_post_meta($post_id, 'book_publisher', sanitize_text_field($_POST['book_publisher']));
    } else {
        delete_post_meta($post_id, 'book_publisher');
    }

    if ( isset($_POST['book_isbn']) ) {
        update_post_meta($post_id, 'book_isbn', sanitize_text_field($_POST['book_isbn']));
    } else {
        delete_post_meta($post_id, 'book_isbn');
    }
}
add_action('save_post_book', 'meigen_save_book_meta');
