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
        <div class="container"><div class="row">
        <?php while ( $sub_pages_query->have_posts() ) : $sub_pages_query->the_post(); ?>
            <div class="sub-page row">
                <div class="text col-ms-8 col-sm-8">
                    <a href="<?= get_the_permalink() ?>">
                        <h3><?= get_the_title() ?></h3>
                    </a>
                    <div><?= get_the_excerpt() ?></div>
                </div>
                <div class="image col-ms-4 col-sm-4">
                    <a href="<?= get_the_permalink() ?>">
                        <?= get_the_post_thumbnail(null, 'thumbnail') ?>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
        </div></div>
    <?php }

?>
