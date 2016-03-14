<div class="container">
<?php
    $sub_pages_query = new WP_Query([
        'post_type' => 'page',
        'post_parent' => $post->ID,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    $index = 0;

    if (! $sub_pages_query->have_posts()) {
        the_content();
    }
    else { ?>
        <?php while ( $sub_pages_query->have_posts() ) : $sub_pages_query->the_post(); ?>
            <div class="sub-page">
                <a class="image" href="<?= get_the_permalink() ?>">
                    <?= get_the_post_thumbnail(null, 'thumbnail') ?>
                </a>

                <a class="title" href="<?= get_the_permalink() ?>">
                    <h3><?= get_the_title() ?></h3>
                </a>
                
                <div class="excerpt"><?= get_the_excerpt() ?></div>
            </div>
        <?php endwhile; ?>
    <?php }

?>
</div>