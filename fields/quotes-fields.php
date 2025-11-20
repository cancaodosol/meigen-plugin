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
<?php
}

function qc_save_quote($post_id){
    if(isset($_POST['qc_quote_book'])){
        update_post_meta($post_id, '_qc_quote_book', intval($_POST['qc_quote_book']));
    }
}
add_action('save_post', 'qc_save_quote');
