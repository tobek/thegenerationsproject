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
        <div class="col-sm-6 col-ms-6">
            <div class="donate-cta">
                <h3>Help support The Generations Project!</h3>
                <a href="/donate/"><button class='btn btn-default'>Donate</button></a>
            </div>
        </div>

        <div class="col-sm-6 col-ms-6">
            <div class="news-widget">
                <h3>News</h3>
                <ul class="posts">
                    <?php while ($feat_posts_query->have_posts()) : $feat_posts_query->the_post(); ?>
                        <li class="post">
                            <a href="<?= get_the_permalink() ?>"><?= get_the_title() ?></a>
                            <time class="updated" datetime="<?= get_post_time('c', true); ?>"><?= get_the_date(); ?></time>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="featured-sections row">
        <?php while ($feat_pages_query->have_posts()) : $feat_pages_query->the_post(); ?>
            <div class="col-md-3 col-sm-6 col-ms-6">
                <div class="featured-section">
                    <a href="<?= get_the_permalink() ?>">
                        <h3><?= get_the_title() ?></h3>
                        <div class="img-holder">
                            <?= get_the_post_thumbnail(null, 'medium') ?>
                        </div>
                    </a>
                    <div class="excerpt"><?= get_the_excerpt() ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
