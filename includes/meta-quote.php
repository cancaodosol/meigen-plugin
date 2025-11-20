<?php
if ( ! defined('ABSPATH') ) exit;

// メタボックス追加
function meigen_add_quote_meta_box() {
    add_meta_box(
        'meigen_quote_meta',
        '名言の追加情報',
        'meigen_quote_meta_callback',
        'quote',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'meigen_add_quote_meta_box');

function meigen_quote_meta_callback($post) {
    wp_nonce_field('meigen_save_quote_meta', 'meigen_quote_meta_nonce');

    $purchase_link = get_post_meta($post->ID, 'purchase_link', true);
    $book_id = get_post_meta($post->ID, 'book_id', true);

    // 本一覧取得
    $books = get_posts(array(
        'post_type' => 'book',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    echo '<p><label><strong>本</strong></label></p>';
    echo '<select name="book_id" style="width:100%;">';
    echo '<option value="">— 選択してください —</option>';
    foreach ($books as $b) {
        $selected = $book_id == $b->ID ? 'selected' : '';
        echo "<option value='".esc_attr($b->ID)."' {$selected}>".esc_html($b->post_title)."</option>";
    }
    echo '</select>';

    echo '<p><label><strong>購入リンク</strong></label>';
    echo "<input type='url' name='purchase_link' value='" . esc_attr($purchase_link) . "' style='width:100%;' /></p>";
}

function meigen_save_quote_meta($post_id) {
    if ( ! isset($_POST['meigen_quote_meta_nonce']) ) return;
    if ( ! wp_verify_nonce($_POST['meigen_quote_meta_nonce'], 'meigen_save_quote_meta') ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( isset($_POST['post_type']) && 'quote' == $_POST['post_type'] ) {
        if ( ! current_user_can('edit_post', $post_id) ) return;
    }

    if ( isset($_POST['purchase_link']) ) {
        update_post_meta($post_id, 'purchase_link', esc_url_raw($_POST['purchase_link']));
    } else {
        delete_post_meta($post_id, 'purchase_link');
    }

    if ( isset($_POST['book_id']) && $_POST['book_id'] !== '' ) {
        update_post_meta($post_id, 'book_id', intval($_POST['book_id']));
    } else {
        delete_post_meta($post_id, 'book_id');
    }
}
add_action('save_post_quote', 'meigen_save_quote_meta');
