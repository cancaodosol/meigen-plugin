<?php
function qc_quote_fields() {
    add_meta_box(
        'qc_quote_details',
        '名言の詳細',
        'qc_quote_details_html',
        'quotes'
    );
}
add_action('add_meta_boxes', 'qc_quote_fields');

function qc_quote_details_html($post) {
    $book = get_post_meta($post->ID, '_qc_quote_book', true);
    $new_book_title = '';
    $new_book_link = '';
    $new_book_author = '';

    wp_nonce_field('qc_save_quote', 'qc_quote_nonce');
?>
    <label>出典（本）</label><br>
    <select name="qc_quote_book" style="width:100%;">
        <option value="">選択してください</option>
        <?php
        $books = get_posts(['post_type' => 'books', 'numberposts' => -1]);
        foreach($books as $b):
        ?>
            <option value="<?= $b->ID ?>" <?= selected($book, $b->ID, false) ?>>
                <?= esc_html($b->post_title) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <hr>
    <p><strong>新規の本を同時登録する場合はこちらから入力してください</strong></p>
    <p style="margin: 0 0 8px 0;">タイトルは必須。それ以外は任意です。保存時に本が作成され、自動で出典に選択されます。</p>
    <label>タイトル（必須）</label><br>
    <input type="text" name="qc_new_book_title" value="<?php echo esc_attr($new_book_title); ?>" style="width:100%;" placeholder="例：七つの習慣">

    <br><br>

    <label>購入リンク</label><br>
    <input type="text" name="qc_new_book_link" value="<?php echo esc_attr($new_book_link); ?>" style="width:100%;" placeholder="https://...">

    <br><br>

    <label>著者</label><br>
    <select name="qc_new_book_author" style="width:100%;">
        <option value="">選択してください</option>
        <?php
        $authors = get_posts(['post_type' => 'authors', 'numberposts' => -1]);
        foreach($authors as $a):
        ?>
            <option value="<?= $a->ID ?>" <?= selected($new_book_author, $a->ID, false) ?>>
                <?= esc_html($a->post_title) ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php
}

function qc_save_quote($post_id){
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 入れ子のsave_post（本の自動作成時など）でループしないよう、実際の投稿タイプを確認
    if (get_post_type($post_id) !== 'quotes') {
        return;
    }

    if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'quotes') {
        return;
    }

    if (!isset($_POST['qc_quote_nonce']) || !wp_verify_nonce($_POST['qc_quote_nonce'], 'qc_save_quote')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $book_to_assign = null;

    // 新規書籍登録（タイトルが入っている場合のみ）
    if (!empty($_POST['qc_new_book_title'])) {
        $new_book_id = wp_insert_post([
            'post_type' => 'books',
            'post_status' => 'publish',
            'post_title' => sanitize_text_field($_POST['qc_new_book_title']),
        ]);

        if (!is_wp_error($new_book_id)) {
            if (!empty($_POST['qc_new_book_link'])) {
                update_post_meta($new_book_id, '_qc_purchase_link', esc_url_raw($_POST['qc_new_book_link']));
            }
            if (!empty($_POST['qc_new_book_author'])) {
                update_post_meta($new_book_id, '_qc_book_author', intval($_POST['qc_new_book_author']));
            }
            $book_to_assign = $new_book_id;
        }
    }

    // 既存選択（新規作成がなかった場合）
    if (!$book_to_assign && isset($_POST['qc_quote_book']) && intval($_POST['qc_quote_book']) > 0) {
        $book_to_assign = intval($_POST['qc_quote_book']);
    }

    if ($book_to_assign) {
        update_post_meta($post_id, '_qc_quote_book', $book_to_assign);
    } else {
        delete_post_meta($post_id, '_qc_quote_book');
    }
}
add_action('save_post', 'qc_save_quote');
