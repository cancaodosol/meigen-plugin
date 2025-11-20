<?php get_header(); ?>

<h1><?php the_title(); ?></h1>

<div class="content">
    <?php the_content(); ?>

    <?php
    $bio = get_post_meta(get_the_ID(), '_qc_bio', true);
    ?>
    <h3>略歴</h3>
    <p><?php echo nl2br(esc_html($bio)); ?></p>
</div>

<?php get_footer(); ?>
