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
    <h1>
        <?php if ($is_event) { ?>
            <span class="double-circles-outer"><span class="double-circles-inner"></span></span>
        <?php } ?>
        <?= Titles\title(); ?>
    </h1>
    
    <?php if (is_single()) { ?>
        <?php get_template_part('templates/entry-meta'); ?>
    <?php } ?>

    <?php if ($show_timeline) {
        global $months_with_events;

        if (! isset($months_with_events)) {
            list($events, $months_with_events) = Utils\get_months_with_events();
        }

        // PHP associative arrays are ordered so this works:
        $oldest_timestamp = strtotime(array_keys($months_with_events)[0]);

        ?>
    <div class="timeline js-timeline">
        <?php for ($i=-1; $i < 23; $i++) {
            $timestamp = Utils\add_months($oldest_timestamp, $i);
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

            ?><a class="month <?= $classes ?>" data-year-month="<?= $year_month ?>" <?= $link ? "href=\"$link\"" : '' ?>><span class="letter"><?= $month ?></span></a><?php

        } ?>
    </div>

    <div id="timeline-svg-container">
        <svg id="timeline-svg" width="0" height="0" >
           <path
                id="timeline-path"
                d="M0 0"             
                stroke-width="0.3em" />
        </svg>
    </div>

    <?php } ?>
</div>
