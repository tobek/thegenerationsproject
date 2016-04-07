<?php
    $event_date = get_post_meta($post->ID, 'event_date', true);
    if ($event_date) {
        $event_date = date('M d, Y', $date);
    }

    global $dont_link_posts, $show_full_content;
    $link_posts = ! isset($dont_link_posts) || ! $dont_link_posts;
    $show_full_content = isset($show_full_content) && $show_full_content;

    $title = get_the_title();

    $row_classes = '';

    if ($show_full_content) {
        $row_classes .= 'full-content';
    }
    if (strlen($title) > 50) {
        $row_classes .= ' long-title';
    }
?>

<article class="alternating-rows tgp-post js-post <?= $row_classes ?>">
    <header>
        <a class="title" <?php if ($link_posts) { ?>href="<?= get_the_permalink() ?>"<?php } ?>>
            <h3><?= $title ?></h3>
        </a>
        <?php if (get_post_meta($post->ID, 'event_date', true)) { ?>
            <?php get_template_part('templates/entry-meta'); ?>
        <?php } ?>
    </header>

    <a class="image" <?php if ($link_posts) { ?>href="<?= get_the_permalink() ?>"<?php } ?>>
        <?= get_the_post_thumbnail(null, 'thumbnail') ?>
    </a>

    <?php if ($show_full_content) { ?>
        <div class="body">
            <?php the_content() ?>
        </div>
    <?php } else { ?>
        <?php $excerpt = get_the_excerpt(); ?>
        <div class="body excerpt <?= strlen($excerpt) < 200 ? 'is-short' : '' ?>">
            <?= $excerpt ?>
        </div>
    <?php } ?>
</article>
