<?php
if ( ! defined('ABSPATH') ) exit;

// テンプレート読み込み：テーマにテンプレがなければプラグイン内テンプレにフォールバック
function meigen_template_loader($template) {
    // archive-quote
    if ( is_post_type_archive('quote') ) {
        $theme_template = locate_template('archive-quote.php');
        if ( $theme_template ) return $theme_template;
        return MEIGEN_PLUGIN_DIR . 'templates/archive-quote.php';
    }

    if ( is_singular('quote') ) {
        $theme_template = locate_template('single-quote.php');
        if ( $theme_template ) return $theme_template;
        return MEIGEN_PLUGIN_DIR . 'templates/single-quote.php';
    }

    if ( is_tax('author') ) {
        $theme_template = locate_template('taxonomy-author.php');
        if ( $theme_template ) return $theme_template;
        return MEIGEN_PLUGIN_DIR . 'templates/taxonomy-author.php';
    }

    return $template;
}
add_filter('template_include', 'meigen_template_loader', 99);
