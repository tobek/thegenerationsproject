<div class="container">
<?php
    if ($post->post_name === 'storytellers') {
        $sub_pages_query = new WP_Query([
            'post_type' => 'tgp_person',
            'tgp_role' => 'storyteller',
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);

        global $dont_link_posts, $show_full_content;
        $dont_link_posts = true;
        $show_full_content = true;
    }
    else {
        $sub_pages_query = new WP_Query([
            'post_type' => 'page',
            'post_parent' => $post->ID,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);
    }

    $index = 0;

    if (! $sub_pages_query->have_posts()) {
        get_template_part('templates/content-page-child');
    }
    else { ?>
        <?php while ( $sub_pages_query->have_posts() ) : $sub_pages_query->the_post(); ?>
            <?php get_template_part('templates/content', 'alternating-rows'); ?>
        <?php endwhile; ?>
    <?php }

?>
</div>