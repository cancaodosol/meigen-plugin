<?php get_header(); ?>

<h1>名言一覧</h1>

<div class="quotes-list">
<?php
while (have_posts()):
    the_post();
    $book = get_post_meta(get_the_ID(), '_qc_quote_book', true);
    $author = $book ? get_post_meta($book, '_qc_book_author', true) : null;
?>
    <div class="quote-item">
        <h2><?php the_title(); ?></h2>
        <p><?php the_excerpt(); ?></p>

        <?php if ($book): ?>
            <p>出典：<a href="<?php echo get_permalink($book); ?>"><?php echo get_the_title($book); ?></a></p>
        <?php endif; ?>

        <?php if ($author): ?>
            <p>著者：<a href="<?php echo get_permalink($author); ?>"><?php echo get_the_title($author); ?></a></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>

<?php get_footer(); ?>
