<?php
    $event_date = get_post_meta($post->ID, 'event_date', true);
    if ($event_date) {
        $event_date = date('M d, Y', $date);
    }

    global $dont_link_posts, $show_full_content;
    $link_posts = ! isset($dont_link_posts) || ! $dont_link_posts;
    $show_full_content = isset($show_full_content) && $show_full_content;
?>

<article class="alternating-rows tgp-post js-post">
    <a class="image" <?php if ($link_posts) { ?>href="<?= get_the_permalink() ?>"<?php } ?>>
        <?= get_the_post_thumbnail(null, 'thumbnail') ?>
    </a>


    <header>
        <a class="title" <?php if ($link_posts) { ?>href="<?= get_the_permalink() ?>"<?php } ?>>
            <h3><?= get_the_title() ?></h3>
        </a>
        <?php if (get_post_meta($post->ID, 'event_date', true)) { ?>
            <?php get_template_part('templates/entry-meta'); ?>
        <?php } ?>
    </header>

    <?php if ($show_full_content) { ?>
        <div class="body full-content">
            <?php the_content() ?>
        </div>
    <?php } else { ?>
        <?php $excerpt = get_the_excerpt(); ?>
        <div class="body excerpt <?= strlen($excerpt) < 200 ? 'is-short' : '' ?>">
            <?= $excerpt ?>
        </div>
    <?php } ?>
</article>
