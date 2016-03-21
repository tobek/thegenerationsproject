<?php use Roots\Sage\Titles; ?>

<div class="page-header container">
    <h1><?= Titles\title(); ?></h1>

    <?php if (is_page('events-timeline')) { global $months_with_events; ?>
    <div class="timeline">
        <?php for ($i=0; $i < 24; $i++) {
            $timestamp = strtotime("+$i month");
            $year_month = date('Y-m', $timestamp);
            $month = date('M', $timestamp);

            $classes = '';
            $link = null;
            if (isset($months_with_events[$year_month])) {
                $classes = 'js-timeline-node has-event';
                $link = get_permalink($months_with_events[$year_month][0]->ID);
                if (count($months_with_events[$year_month]) > 1) {
                    $classes .= ' has-multiple-events';
                }
            }

            if ($i === 0 || $month === 'Jan') {
                ?><span class="year"><?= date('Y', $timestamp) ?></span><?php
            }

            ?><a class="month <?= $classes ?>" data-year-month="<?= $year_month ?>" <?= $link ? "href=\"$link\"" : '' ?>><span class="letter"><?= $month[0] ?></span></a><?php
            
        } ?>
    </div>
    <?php } ?>
</div>
