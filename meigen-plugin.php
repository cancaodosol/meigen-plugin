<?php
/*
Plugin Name: 名言集プラグイン (Meigen Plugin)
Description: 名言・本・著者の管理プラグイン（一覧テンプレ・書影・著者ページ・REST API付き）
Version: 1.1
Author: ChatGPT
*/

if ( ! defined( 'ABSPATH' ) ) exit;
define('MEIGEN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MEIGEN_PLUGIN_URL', plugin_dir_url(__FILE__));

// 必須ファイル読み込み
require_once MEIGEN_PLUGIN_DIR . 'includes/cpt-quote.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/cpt-book.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/taxonomy-author.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/meta-quote.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/meta-book.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/templates.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/rest-api.php';
require_once MEIGEN_PLUGIN_DIR . 'includes/enqueue.php';

// アクティベーション時にリライトルールを再生成
function meigen_plugin_activate() {
    meigen_register_quote_cpt();
    meigen_register_book_cpt();
    meigen_register_author_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'meigen_plugin_activate');

function meigen_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'meigen_plugin_deactivate');
