<?php
/**
 * Template Name: Home Page Template
 */
    
    use TGP\Utils;

    $feat_pages_query = new WP_Query([
        'post_type' => 'page',
        'meta_key' => 'featured',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    $feat_posts_query = new WP_Query([
        'post_type' => 'post',
    ]);
?>

<div class="slideshow">
    <?= do_shortcode('[sangar-slider id=124]'); ?>
</div>

<div class="container">
    <div class="row">
        <div class="donate-cta col-sm-6 col-ms-6">
            <h4>Help support The Generations Project!</h4>
            <a href="/donate/" class='btn btn-primary'>Donate</a>
        </div>

        <div class="news-widget col-sm-6 col-ms-6">
            <h3>News</h3>
            <ul>
                <?php while ($feat_posts_query->have_posts()) : $feat_posts_query->the_post(); ?>
                    <li>
                        <a href="<?= get_the_permalink() ?>"><?= get_the_title() ?></a>
                        <time class="updated" datetime="<?= get_post_time('c', true); ?>"><?= get_the_date(); ?></time>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <div class="sections row">
        <?php while ($feat_pages_query->have_posts()) : $feat_pages_query->the_post(); ?>
            <div class="section col-md-3 col-sm-6 col-ms-6">
                <a href="<?= get_the_permalink() ?>">
                    <h3><?= get_the_title() ?></h3>
                    <?= get_the_post_thumbnail(null, 'thumbnail') ?>
                </a>
                <div><?= get_the_excerpt() ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>