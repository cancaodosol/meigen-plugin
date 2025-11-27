<?php
/*
Plugin Name: Quotes Collection
Description: 名言・著者・書籍を管理するカスタム投稿プラグイン
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

define('QC_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('QC_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once QC_PLUGIN_PATH . 'post-types/authors.php';
require_once QC_PLUGIN_PATH . 'post-types/books.php';
require_once QC_PLUGIN_PATH . 'post-types/quotes.php';

require_once QC_PLUGIN_PATH . 'fields/authors-fields.php';
require_once QC_PLUGIN_PATH . 'fields/books-fields.php';
require_once QC_PLUGIN_PATH . 'fields/quotes-fields.php';

require_once QC_PLUGIN_PATH . 'api/rest-api.php';
require_once QC_PLUGIN_PATH . 'shortcodes/quotes-list.php';

/**
 * テンプレート読み込み
 */
function qc_include_templates($template)
{
    if (is_post_type_archive('quotes')) {
        return QC_PLUGIN_PATH . 'templates/archive-quotes.php';
    }
    if (is_singular('quotes')) {
        return QC_PLUGIN_PATH . 'templates/single-quote.php';
    }
    if (is_singular('books')) {
        return QC_PLUGIN_PATH . 'templates/single-book.php';
    }
    if (is_singular('authors')) {
        return QC_PLUGIN_PATH . 'templates/single-author.php';
    }
    return $template;
}
add_filter('template_include', 'qc_include_templates');
