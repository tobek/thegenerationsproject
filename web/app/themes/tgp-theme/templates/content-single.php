<?php
    use TGP\Utils;
?>

<?php while (have_posts()) : the_post(); ?>
    <?php
        $feat_img_url = Utils\get_feat_img('large');

        $gallery_shortcode = null;
        $content = Utils\extract_preg('/\[gallery [^\]]*\]/', get_the_content(), $gallery_shortcode);

        $content = apply_filters('the_content', $content);
    ?>

    <article <?php post_class(); ?>>
        <?php get_template_part('templates/page', 'header'); ?>

        <div class="container">
            <div class="row">
                <?php if ($gallery_shortcode) { ?>
                    <div class="col-xs-12 col-sm-9 col-md-5 post-content-wrapper">
                        <div class="post-content">
                            <div class="post-content-inner">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-ms-12 col-sm-12 col-md-7">
                        <?php //do_shortcode($gallery_shortcode) ?>
                        <?php if (function_exists('slideshow')) {
                            slideshow(true, null, $post->ID, [
                                'layout' => 'responsive',
                                'resizeimages' => true,
                                'auto' => false,
                                'autospeed' => 20,
                                'fadespeed' => 5,
                                'infospeed' => 5,
                                'showthumbs' => true,
                            ]);
                        } ?>
                    </div>
                <?php } else if ($feat_img_url) { ?>
                    <div class="col-xs-12 col-ms-9 col-sm-6 post-content-wrapper">
                        <div class="post-content">
                            <div class="post-content-inner">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-ms-9 col-sm-6">
                        <img class="post-single-image" src="<?= $feat_img_url ?>">
                    </div>
                <?php } else { ?>
                    <div class="col-sm-9 col-lg-7 post-content-wrapper">
                        <div class="post-content">
                            <div class="post-content-inner">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php // comments_template('/templates/comments.php'); ?>
    </article>
<?php endwhile; ?>
