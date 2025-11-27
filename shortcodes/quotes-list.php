<?php
// 名言一覧をカード表示するショートコード

if (!defined('ABSPATH')) {
    exit;
}

function qc_register_meigen_assets()
{
    $version = file_exists(QC_PLUGIN_PATH . 'assets/meigen.css') ? filemtime(QC_PLUGIN_PATH . 'assets/meigen.css') : null;
    wp_register_style(
        'qc-meigen',
        QC_PLUGIN_URL . 'assets/meigen.css',
        [],
        $version
    );
}
add_action('wp_enqueue_scripts', 'qc_register_meigen_assets');

function qc_meigen_render_card($post_id)
{
    $book_id = get_post_meta($post_id, '_qc_quote_book', true);
    $author_id = $book_id ? get_post_meta($book_id, '_qc_book_author', true) : null;
    $page = get_post_meta($post_id, '_qc_quote_page', true);
    $raw_content = strip_shortcodes(get_post_field('post_content', $post_id));
    $quote_text = wp_trim_words(wp_strip_all_tags($raw_content), 80, '…');
    $title = get_the_title($post_id);
    $permalink = get_permalink($post_id);

    ob_start();
?>
    <article class="qc-meigen-card">
        <h3 class="qc-meigen-title"><?php echo esc_html($title); ?></h3>
        <div class="qc-meigen-quote"><?php echo esc_html($quote_text); ?></div>
        <div class="qc-meigen-meta">
            <?php if ($author_id): ?>
                <span class="qc-meigen-meta-item">
                    <a href="<?php echo esc_url(get_permalink($author_id)); ?>">
                        <?php echo esc_html(get_the_title($author_id)). " 著"; ?>
                    </a>
                </span>
            <?php endif; ?>
            <?php if ($book_id): ?>
                <span class="qc-meigen-meta-item">
                    「
                        <a href="<?php echo esc_url(get_permalink($book_id)); ?>">
                            <?php echo esc_html(get_the_title($book_id)); ?>
                        </a>
                    」
                </span>
            <?php endif; ?>
            <?php if ($page): ?>
                <span class="qc-meigen-meta-item">
                    <?php echo esc_html($page); ?>
                </span>
            <?php endif; ?>
        </div>
    </article>
<?php
    return ob_get_clean();
}

function qc_meigen_list_shortcode($atts = [])
{
    $atts = shortcode_atts([
        'count' => 12,
    ], $atts, 'meigen_list');

    $requested_count = intval($atts['count']);
    if ($requested_count < 0) {
        $requested_count = -1;
    }

    $query = new WP_Query([
        'post_type'      => 'quotes',
        'posts_per_page' => -1, // 並び替えのため全件取得し、後で件数を絞る
        'post_status'    => 'publish',
    ]);

    $cache = [];
    $sort_value = function ($post_id) use (&$cache) {
        if (isset($cache[$post_id])) {
            return $cache[$post_id];
        }
        $book_id = get_post_meta($post_id, '_qc_quote_book', true);
        $author_id = $book_id ? get_post_meta($book_id, '_qc_book_author', true) : null;
        $author = $author_id ? get_the_title($author_id) : '';
        $book = $book_id ? get_the_title($book_id) : '';

        $normalize = function ($text) {
            if (function_exists('mb_strtolower')) {
                return mb_strtolower(trim((string) $text), 'UTF-8');
            }
            return strtolower(trim((string) $text));
        };

        $cache[$post_id] = [
            'author' => $normalize($author),
            'book'   => $normalize($book),
            'book_id'   => $book_id,
            'author_id' => $author_id,
        ];
        return $cache[$post_id];
    };

    if ($query->have_posts()) {
        usort($query->posts, function ($a, $b) use ($sort_value) {
            $va = $sort_value($a->ID);
            $vb = $sort_value($b->ID);

            $author_cmp = strcmp($va['author'], $vb['author']);
            if ($author_cmp !== 0) {
                return $author_cmp;
            }

            return strcmp($va['book'], $vb['book']);
        });

        if ($requested_count > 0) {
            $query->posts = array_slice($query->posts, 0, $requested_count);
        }
        $query->post_count = count($query->posts);
        $query->rewind_posts();
    }

    if (!$query->have_posts()) {
        return '<p class="qc-meigen-empty">名言がまだ登録されていません。</p>';
    }

    wp_enqueue_style('qc-meigen');

    ob_start();
?>
    <div class="qc-meigen-shortcode">
        <div class="qc-meigen-grid">
            <?php
            while ($query->have_posts()):
                $query->the_post();
            ?>
                <?php echo qc_meigen_render_card(get_the_ID()); ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('meigen_list', 'qc_meigen_list_shortcode');

function qc_meigen_random_shortcode()
{
    $query = new WP_Query([
        'post_type'      => 'quotes',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'rand',
    ]);

    if (!$query->have_posts()) {
        return '<p class="qc-meigen-empty">名言がまだ登録されていません。</p>';
    }

    wp_enqueue_style('qc-meigen');

    ob_start();
?>
    <?php
    while ($query->have_posts()):
        $query->the_post();
    ?>
        <?php echo qc_meigen_render_card(get_the_ID()); ?>
    <?php endwhile; ?>
<?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('meigen_random', 'qc_meigen_random_shortcode');
