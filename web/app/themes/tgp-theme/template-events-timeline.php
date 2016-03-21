<?php
/**
 * Template Name: Events Timeline Template
 */

    use TGP\Utils;

    $events = get_posts([
        'post_status' => 'publish',
        'post_type' => 'post',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ]);

    // Utils\dump($events);

    $months_with_events = [];
    foreach ($events as $event) {
        $timestamp = get_post_meta($event->ID, 'event_date', true);

        $year_month = date('Y-m', $timestamp);
        if (! isset($months_with_events[$year_month])) {
            $months_with_events[$year_month] = [];
        }
        $months_with_events[$year_month][] = $event;
    }

    // Need to fake The Loop. We need to call get_posts so that we can calculate and pass $months_with_events to timeline generation, so no need to duplicate work and also call WP_Query. But get_the_excerpt requires The Loop so we'll fake it as we loop through $events.
    global $post, $pages, $page;
    $page = 1;
?>

<?php get_template_part('templates/page', 'header'); ?>

<div class="events-container js-events-container">
    <div class="container events js-events">
        <?php foreach ($events as $event) {
            $post = $event;
            $pages = [$event->post_content];
            get_template_part('templates/content', 'alternating-rows');
        } ?>
    </div>
</div>
