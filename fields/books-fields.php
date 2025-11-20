<?php
function qc_book_fields() {
    add_meta_box(
        'qc_book_details',
        '書籍情報',
        'qc_book_details_html',
        'books'
    );
}
add_action('add_meta_boxes', 'qc_book_fields');

function qc_book_details_html($post) {
    $link = get_post_meta($post->ID, '_qc_purchase_link', true);
    $author = get_post_meta($post->ID, '_qc_book_author', true);
?>
    <label>購入リンク</label><br>
    <input type="text" name="qc_purchase_link" value="<?php echo esc_attr($link); ?>" style="width:100%;">

    <br><br>

    <label>著者</label><br>
    <select name="qc_book_author" style="width:100%;">
        <option value="">選択してください</option>
        <?php
        $authors = get_posts(['post_type' => 'authors', 'numberposts' => -1]);
        foreach($authors as $a):
        ?>
            <option value="<?= $a->ID ?>" <?= selected($author, $a->ID, false) ?>>
                <?= esc_html($a->post_title) ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php
}

function qc_save_book($post_id){
    if(isset($_POST['qc_purchase_link'])){
        update_post_meta($post_id, '_qc_purchase_link', esc_url_raw($_POST['qc_purchase_link']));
    }
    if(isset($_POST['qc_book_author'])){
        update_post_meta($post_id, '_qc_book_author', intval($_POST['qc_book_author']));
    }
}
add_action('save_post', 'qc_save_book');
