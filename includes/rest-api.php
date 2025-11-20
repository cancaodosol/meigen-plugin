<?php
if ( ! defined('ABSPATH') ) exit;

add_action('rest_api_init', function () {
    // 名言一覧
    register_rest_route('meigen/v1', '/quotes', array(
        'methods' => 'GET',
        'callback' => 'meigen_rest_get_quotes',
        'args' => array(
            'page' => array('validate_callback' => 'is_numeric'),
            'per_page' => array('validate_callback' => 'is_numeric'),
            'author' => array(),
            'book' => array(),
        ),
        'permission_callback' => '__return_true',
    ));

    // 本一覧
    register_rest_route('meigen/v1', '/books', array(
        'methods' => 'GET',
        'callback' => 'meigen_rest_get_books',
        'permission_callback' => '__return_true',
    ));

    // 著者一覧
    register_rest_route('meigen/v1', '/authors', array(
        'methods' => 'GET',
        'callback' => 'meigen_rest_get_authors',
        'permission_callback' => '__return_true',
    ));
});

function meigen_rest_get_quotes($request) {
    $page = max(1, intval($request->get_param('page') ?: 1));
    $per_page = intval($request->get_param('per_page') ?: 10);

    $args = array(
        'post_type' => 'quote',
        'posts_per_page' => $per_page,
        'paged' => $page,
    );

    if ( $author = $request->get_param('author') ) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'author',
            'field' => 'slug',
            'terms' => $author,
        ));
    }

    if ( $book = $request->get_param('book') ) {
        $args['meta_query'][] = array(
            'key' => 'book_id',
            'value' => intval($book),
            'compare' => '=',
        );
    }

    $q = new WP_Query($args);
    $data = array();
    foreach ( $q->posts as $p ) {
        $book_id = get_post_meta($p->ID, 'book_id', true);
        $book = null;
        if ( $book_id ) {
            $book = array(
                'id' => intval($book_id),
                'title' => get_the_title($book_id),
                'permalink' => get_permalink($book_id),
                'cover' => get_the_post_thumbnail_url($book_id, 'medium'),
                'purchase_link' => get_post_meta($book_id, 'book_purchase_link', true),
            );
        }

        $author_terms = get_the_terms($p->ID, 'author');
        $author = null;
        if ( $author_terms && ! is_wp_error($author_terms) ) {
            $t = $author_terms[0];
            $author = array('id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug, 'link' => get_term_link($t));
        }

        $data[] = array(
            'id' => $p->ID,
            'title' => get_the_title($p),
            'content' => apply_filters('the_content', $p->post_content),
            'excerpt' => get_the_excerpt($p),
            'permalink' => get_permalink($p),
            'book' => $book,
            'author' => $author,
            'meta' => array(
                'purchase_link' => get_post_meta($p->ID, 'purchase_link', true),
            )
        );
    }

    $total = $q->found_posts;
    $headers = array(
        'X-WP-Total' => intval($total),
        'X-WP-TotalPages' => intval( ceil($total / $per_page) ),
    );

    return new WP_REST_Response($data, 200, $headers);
}

function meigen_rest_get_books($request) {
    $books = get_posts(array('post_type' => 'book', 'posts_per_page' => -1));
    $data = array();
    foreach ($books as $b) {
        $data[] = array(
            'id' => $b->ID,
            'title' => $b->post_title,
            'permalink' => get_permalink($b),
            'cover' => get_the_post_thumbnail_url($b->ID, 'medium'),
            'purchase_link' => get_post_meta($b->ID, 'book_purchase_link', true),
            'publisher' => get_post_meta($b->ID, 'book_publisher', true),
            'isbn' => get_post_meta($b->ID, 'book_isbn', true),
        );
    }
    return rest_ensure_response($data);
}

function meigen_rest_get_authors($request) {
    $terms = get_terms(array('taxonomy' => 'author', 'hide_empty' => false));
    $data = array();
    foreach ($terms as $t) {
        $data[] = array(
            'id' => $t->term_id,
            'name' => $t->name,
            'slug' => $t->slug,
            'description' => $t->description,
            'link' => get_term_link($t),
        );
    }
    return rest_ensure_response($data);
}
