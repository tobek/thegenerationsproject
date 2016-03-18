<?php
    use TGP\Utils;
    
    $feat_img_url = Utils\get_feat_img('large');

    $content = get_the_content();

    preg_match('/\[contact-form-7 [^\]]*\]/', $content, $matches);
    if (isset($matches) && isset($matches[0])) {
        $form_shortcode = $matches[0];
        $content = trim(str_replace($form_shortcode, '', $content));
    }

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
