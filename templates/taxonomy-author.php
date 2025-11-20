<?php
if ( ! defined('ABSPATH') ) exit;
get_header();

$term = get_queried_object();
?>

<div class="meigen-author container">
    <h1><?php echo esc_html($term->name); ?></h1>
    <?php if ( ! empty($term->description) ) : ?>
        <div class="meigen-author-desc"><?php echo wp_kses_post(wpautop($term->description)); ?></div>
    <?php endif; ?>

    <h2>この著者の名言</h2>
    <?php
    $args = array(
        'post_type' => 'quote',
        'tax_query' => array(
            array(
                'taxonomy' => 'author',
                'field' => 'term_id',
                'terms' => $term->term_id
            )
        ),
        'posts_per_page' => 10,
    );
    $q = new WP_Query($args);
    if ( $q->have_posts() ) :
        while ( $q->have_posts() ) : $q->the_post();
            ?>
            <article class="meigen-item">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div><?php the_excerpt(); ?></div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>この著者の名言はまだ登録されていません。</p>';
    endif;
    ?>
</div>

<?php get_footer(); ?>
