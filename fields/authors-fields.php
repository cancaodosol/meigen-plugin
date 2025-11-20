<?php
function qc_author_fields() {
    add_meta_box(
        'qc_author_info',
        '著者プロフィール',
        'qc_author_info_html',
        'authors'
    );
}
add_action('add_meta_boxes', 'qc_author_fields');

function qc_author_info_html($post) {
    $bio = get_post_meta($post->ID, '_qc_bio', true);
?>
    <label>略歴</label><br>
    <textarea name="qc_bio" style="width:100%;height:120px;"><?php echo esc_textarea($bio); ?></textarea>
<?php
}
function qc_save_author($post_id){
    if(isset($_POST['qc_bio'])){
        update_post_meta($post_id, '_qc_bio', sanitize_textarea_field($_POST['qc_bio']));
    }
}
add_action('save_post', 'qc_save_author');
