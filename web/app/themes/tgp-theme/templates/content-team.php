<?php
    $team_query = new WP_Query([
        'post_type' => 'tgp_person',
        'tgp_role' => 'team',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    $board_query = new WP_Query([
        'post_type' => 'tgp_person',
        'tgp_role' => 'board',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
?>
<div class="container">
    <?php // the_content(); ?>

    <div class="row row-eq-height">
        <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
            <div class="col-xs-center col-sm-4 col-ms-6 col-xs-10">
                <div class="card">
                    <div class="image-wrapper">
                        <?= get_the_post_thumbnail(null, 'medium', ['alt' => get_the_title()]) ?>
                        <h3 class="name"><?php the_title() ?></h3>
                    </div>

                    <h3 class="title"><?= get_post_meta($post->ID, 'position', true); ?></h3>

                    <div class="text"><?php the_content() ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="board">
        <h1>Board of Directors</h1>

        <div class="row row-eq-height">
            <?php while ($board_query->have_posts()) : $board_query->the_post(); ?>
                <div class="col-xs-center col-sm-3 col-ms-6 col-xs-8">
                    <div class="card">
                        <h3><?php the_title() ?></h3>
                        <div class="text"><?php the_content() ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>