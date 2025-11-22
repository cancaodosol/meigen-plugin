<?php
add_action('rest_api_init', function () {

    register_rest_route('quotes/v1', '/list', [
        'methods' => 'GET',
        'callback' => function () {
            $posts = get_posts([
                'post_type' => 'quotes',
                'numberposts' => -1
            ]);

            $data = [];
            foreach ($posts as $p) {
                $book = get_post_meta($p->ID, '_qc_quote_book', true);
                $author = $book ? get_post_meta($book, '_qc_book_author', true) : null;
                $purchase_link = $book ? get_post_meta($book, '_qc_purchase_link', true) : null;

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

                $data[] = [
                    'id' => $p->ID,
                    'title' => $p->post_title,
                    'content' => wpautop($p->post_content),
                    'permalink' => get_permalink($p->ID),
                    'book' => $book_data,
                ];
            }

            return $data;
        }
    ]);

});
