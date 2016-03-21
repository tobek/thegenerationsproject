<?php
    $date = get_post_meta($post->ID, 'event_date', true);

    if (! $date) {
        $date = get_post_time();
    }
?>

<time datetime="<?= date('c', $date); ?>" data-year-month="<?= date('Y-m', $date) ?>"><?= date('F jS, Y', $date) ?></time>
