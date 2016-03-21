<?php
    use TGP\Utils;

    $feat_img_url = Utils\get_feat_img('large');

    $form_shortcode = null;
    $content = Utils\extract_preg('/\[contact-form-7 [^\]]*\]/', get_the_content(), $form_shortcode);

    $two_col = $content && $form_shortcode;

    $subtitle = get_post_meta($post->ID, 'subtitle', true);

    function actual_content($content, $subtitle) { ?>
        <?php if ($subtitle) { ?>
            <h3><?= $subtitle ?></h3>
        <?php } ?>
        <div class="post-content-inner">
            <?= apply_filters('the_content', $content) ?>
        </div>
    <?php }

?>

<?php if ($feat_img_url) { ?>
    <img class="page-bg-image" src="<?= $feat_img_url ?>">
<?php } ?>

<div class="row">
    <?php if ($two_col) { ?>
        <div class="col-ms-9 col-sm-5 col-ms-center post-content-wrapper">
            <div class="post-content">
                <?php actual_content($content, $subtitle) ?>
            </div>
        </div>
    <?php } ?>

    <div class="<?= $two_col ? 'col-ms-12 col-sm-7 col-ms-center' : 'col-sm-9 col-lg-7 col-center' ?> post-content-wrapper">
        <div class="post-content">
            <?php if ($form_shortcode) { ?>
                <div class="post-content-inner">
                    <?= do_shortcode($form_shortcode) ?>
                </div>
            <?php } else { ?>
                <?php actual_content($content, $subtitle) ?>
            <?php } ?>
        </div>
    </div>
</div>

<?php
if (is_page('about')) {
    $partners_query = new WP_Query([
        'post_type' => 'tgp_partner',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    ?>
        <div class="page-header partners">
            <h1>Partners</h1>
        </div>

        <div class="row">
            <div class="col-sm-9 col-lg-7 col-center post-content-wrapper">
                <div class="post-content">
                    <div class="post-content-inner">
                        <?php while ($partners_query->have_posts()) : $partners_query->the_post(); ?>
                            <a class="partner-image" href="<?= get_the_title() ?>" target="_blank">
                                <img src="<?= Utils\get_feat_img('thumbnail') ?>">
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
} ?>
