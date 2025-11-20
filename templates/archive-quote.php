<?php
if ( ! defined('ABSPATH') ) exit;
get_header();
?>

<div class="meigen-archive container">
    <h1>名言集</h1>

    <?php if ( have_posts() ) : ?>
        <div class="meigen-list">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php
                    $book_id = get_post_meta(get_the_ID(), 'book_id', true);
                    $book_title = $book_id ? get_the_title($book_id) : '';
                    $author_terms = get_the_terms(get_the_ID(), 'author');
                    $author_name = ($author_terms && !is_wp_error($author_terms)) ? esc_html($author_terms[0]->name) : '';
                ?>
                <article id="post-<?php the_ID(); ?>" class="meigen-item">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="meigen-excerpt"><?php the_excerpt(); ?></div>
                    <div class="meigen-meta">
                        <?php if ($author_name): ?><span class="meigen-author">著者：<?php echo $author_name; ?></span><?php endif; ?>
                        <?php if ($book_title): ?><span class="meigen-book"> 本： <?php echo esc_html($book_title); ?></span><?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="meigen-pagination">
            <?php
            the_posts_pagination(array(
                'mid_size' => 1,
                'prev_text' => '‹',
                'next_text' => '›'
            ));
            ?>
        </div>

    <?php else : ?>
        <p>名言が見つかりません。</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
