<?php
    use Roots\Sage\Titles;
    use TGP\Utils;

    $show_timeline = false;
    $is_event = false;
    $this_event_year_month = null;
    if (is_page('events-timeline')) {
        $show_timeline = true;
    }
    else if (is_single() && $timestamp = get_post_meta($post->ID, 'event_date', true)) {
        $show_timeline = true;
        $is_event = true;
        $this_event_year_month = date('Y-m', $timestamp);
    }
?>

<div class="page-header container">
    <h1><?= Titles\title(); ?></h1>
    
    <?php if (is_single()) { ?>
        <?php get_template_part('templates/entry-meta'); ?>
    <?php } ?>

    <?php if ($show_timeline) {
        global $months_with_events;

        if (! isset($months_with_events)) {
            list($events, $months_with_events) = Utils\get_months_with_events();
        }
        ?>
    <div class="timeline">
        <?php for ($i=0; $i < 24; $i++) {
            $timestamp = strtotime("+$i month");
            $year_month = date('Y-m', $timestamp);
            $month = date('M', $timestamp);

            $classes = '';
            $link = null;
            if (isset($months_with_events[$year_month])) {
                $classes = 'js-timeline-node has-event';
                if (count($months_with_events[$year_month]) > 1) {
                    $classes .= ' has-multiple-events';
                }

                if ($is_event) {
                    $link = "/events-timeline/#$year_month";
                }
                else {
                    $link = get_permalink($months_with_events[$year_month][0]->ID);
                }
            }

            if ($this_event_year_month === $year_month) {
                // We're on a single Event page and this is the month it's on.
                $classes .= ' is-current-event';
                $link = null;
            }

            if ($i === 0 || $month === 'Jan') {
                ?><span class="year"><?= date('Y', $timestamp) ?></span><?php
            }

            if ($month === 'Dec') {
                $classes .= ' year-end';
            }

            ?><a class="month <?= $classes ?>" data-year-month="<?= $year_month ?>" <?= $link ? "href=\"$link\"" : '' ?>><span class="letter"><?= $month[0] ?></span></a><?php

        } ?>
    </div>
    <?php } ?>
</div>
