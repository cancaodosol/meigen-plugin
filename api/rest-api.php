<?php
function qc_prepare_quote_response($post) {
    $book = get_post_meta($post->ID, '_qc_quote_book', true);
    $author = $book ? get_post_meta($book, '_qc_book_author', true) : null;
    $purchase_link = $book ? get_post_meta($book, '_qc_purchase_link', true) : null;
    $page = get_post_meta($post->ID, '_qc_quote_page', true);

    $book_data = null;
    if ($book) {
        $author_data = null;
        if ($author) {
            $author_data = [
                'id' => $author,
                'title' => get_the_title($author),
                'permalink' => get_permalink($author),
            ];
        }

        $book_data = [
            'id' => $book,
            'title' => get_the_title($book),
            'permalink' => get_permalink($book),
            'purchase_link' => $purchase_link ? esc_url_raw($purchase_link) : null,
            'author' => $author_data,
        ];
    }

    return [
        'id' => $post->ID,
        'title' => $post->post_title,
        'content' => wpautop($post->post_content),
        'short_content' => wp_trim_words(wp_strip_all_tags(wpautop($post->post_content)), 80, '…'),
        'permalink' => get_permalink($post->ID),
        'page' => $page !== '' ? $page : null,
        'book' => $book_data,
    ];
}

add_action('rest_api_init', function () {

    register_rest_route('quotes/v1', '/list', [
        'methods' => 'GET',
        'callback' => function () {
            $posts = get_posts([
                'post_type' => 'quotes',
                'numberposts' => -1
            ]);

            return array_map('qc_prepare_quote_response', $posts);
        }
    ]);

    register_rest_route('quotes/v1', '/random', [
        'methods' => 'GET',
        'callback' => function () {
            $posts = get_posts([
                'post_type' => 'quotes',
                'numberposts' => 1,
                'orderby' => 'rand',
            ]);

            if (empty($posts)) {
                return [];
            }

            return qc_prepare_quote_response($posts[0]);
        }
    ]);

});

/**
 * Chrome拡張（chrome-extension://）からのCORSを許可
 */
function qc_allow_chrome_extension_cors($value) {
    $origin = get_http_origin();
    if ($origin && strpos($origin, 'chrome-extension://') === 0) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
    }
    return $value;
}
add_filter('rest_pre_serve_request', 'qc_allow_chrome_extension_cors', 15);
