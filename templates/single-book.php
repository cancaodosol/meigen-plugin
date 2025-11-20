<?php get_header(); ?>

<h1><?php the_title(); ?></h1>

<div class="content">
    <?php the_content(); ?>

    <?php
    $author = get_post_meta(get_the_ID(), '_qc_book_author', true);
    $link = get_post_meta(get_the_ID(), '_qc_purchase_link', true);
    ?>

    <?php if ($author): ?>
        <p>著者：<a href="<?php echo get_permalink($author); ?>"><?php echo get_the_title($author); ?></a></p>
    <?php endif; ?>

    <?php if ($link): ?>
        <p><a href="<?php echo esc_url($link); ?>" target="_blank">購入リンク</a></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
