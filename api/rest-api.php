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

                $data[] = [
                    'id' => $p->ID,
                    'title' => $p->post_title,
                    'content' => wpautop($p->post_content),
                    'book' => $book ? get_the_title($book) : null,
                    'author' => $author ? get_the_title($author) : null,
                ];
            }

            return $data;
        }
    ]);

});
