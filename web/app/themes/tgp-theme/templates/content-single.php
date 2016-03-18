<?php
    use TGP\Utils;

    $feat_img_url = Utils\get_feat_img('large');

    $two_col = false;
    if ($feat_img_url) {
        $two_col = true;
    }

?>

<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
        <header class="page-header container">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php get_template_part('templates/entry-meta'); ?>
        </header>

        <div class="container">
            <div class="row">
                <div class="<?= $two_col ? 'col-xs-12 col-ms-9 col-sm-6' : 'col-sm-9 col-lg-7' ?> post-content-wrapper">
                    <div class="post-content">
                        <div class="post-content-inner">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <?php if ($feat_img_url) { ?>
                    <div class="col-xs-12 col-ms-9 col-sm-6">
                        <img class="post-single-image" src="<?= $feat_img_url ?>">
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php // comments_template('/templates/comments.php'); ?>
    </article>
<?php endwhile; ?>
