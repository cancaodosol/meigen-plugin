<?php
if ( ! defined('ABSPATH') ) exit;

function meigen_register_author_taxonomy() {
    register_taxonomy(
        'author',
        'quote',
        array(
            'label' => '著者',
            'hierarchical' => false,
            'show_admin_column' => true,
            'rewrite' => array('slug' => 'author'),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'meigen_register_author_taxonomy');
