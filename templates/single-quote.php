<?php
if ( ! defined('ABSPATH') ) exit;
get_header();
?>

<div class="meigen-single container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" class="meigen-quote">
            <h1><?php the_title(); ?></h1>
            <div class="meigen-content"><?php the_content(); ?></div>

            <?php
                $book_id = get_post_meta(get_the_ID(), 'book_id', true);
                $purchase_link = get_post_meta(get_the_ID(), 'purchase_link', true);
            ?>

            <?php if ( $book_id ) :
                $book_url = get_permalink($book_id);
                $book_title = get_the_title($book_id);
                $book_thumb = get_the_post_thumbnail_url($book_id, 'medium');
                $book_purchase_link = get_post_meta($book_id, 'book_purchase_link', true);
            ?>
                <div class="meigen-bookbox">
                    <?php if ( $book_thumb ): ?>
                        <div class="meigen-book-thumb"><a href="<?php echo esc_url($book_url); ?>"><img src="<?php echo esc_url($book_thumb); ?>" alt="<?php echo esc_attr($book_title); ?>" /></a></div>
                    <?php endif; ?>
                    <div class="meigen-book-info">
                        <h3>収録本：<a href="<?php echo esc_url($book_url); ?>"><?php echo esc_html($book_title); ?></a></h3>
                        <?php if ( $book_purchase_link ): ?>
                            <p><a href="<?php echo esc_url($book_purchase_link); ?>" target="_blank" rel="noopener">購入リンク（本ページ）</a></p>
                        <?php endif; ?>
                        <?php if ( $purchase_link ): ?>
                            <p><a href="<?php echo esc_url($purchase_link); ?>" target="_blank" rel="noopener">この名言の購入リンク</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="meigen-meta">
                <p>著者：
                    <?php
                        $author_terms = get_the_terms(get_the_ID(), 'author');
                        if ( $author_terms && !is_wp_error($author_terms) ) {
                            $term = $author_terms[0];
                            echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                        } else {
                            echo '不明';
                        }
                    ?>
                </p>
            </div>

        </article>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
