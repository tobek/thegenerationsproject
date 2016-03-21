<?php
    $event_date = get_post_meta($post->ID, 'event_date', true);
    if ($event_date) {
        $event_date = date('M d, Y', $date);
    }

?>

<article class="alternating-rows">
    <a class="image" href="<?= get_the_permalink() ?>">
        <?= get_the_post_thumbnail(null, 'thumbnail') ?>
    </a>


    <header>
        <a class="title" href="<?= get_the_permalink() ?>">
            <h3><?= get_the_title() ?></h3>
        </a>
        <?php if (get_post_meta($post->ID, 'event_date', true)) { ?>
            <?php get_template_part('templates/entry-meta'); ?>
        <?php } ?>
    </header>

    <div class="excerpt"><?= get_the_excerpt() ?></div>
</article>
