<?php
/**
 * Template Name: Events Timeline Template
 */

    use TGP\Utils;

    $events_query = new WP_Query([
        'post_status' => 'publish',
        'post_type' => 'post',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ]);
?>

<?php get_template_part('templates/page', 'header'); ?>

<div class="container">
    <?php while ( $events_query->have_posts() ) : $events_query->the_post(); ?>
        <?php get_template_part('templates/content', 'alternating-rows'); ?>
    <?php endwhile; ?>
</div>
