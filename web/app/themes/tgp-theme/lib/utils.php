<?php

namespace TGP\Utils;

function dump($thing) {
    echo '<pre>';
    print_r($thing);
    die;
}

// Use within The Loop
function get_feat_img($size) {
    $feat_img_id = get_post_thumbnail_id();
    if ($feat_img_id) {
        $feat_img_url_array = wp_get_attachment_image_src($feat_img_id, 'large', true);
        return $feat_img_url_array[0];
    }
    else {
        return null;
    }
}

function get_months_with_events() {
    $events = get_posts([
        'post_status' => 'publish',
        'post_type' => 'post',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ]);

    $months_with_events = [];
    foreach ($events as $event) {
        $timestamp = get_post_meta($event->ID, 'event_date', true);

        $year_month = date('Y-m', $timestamp);
        if (! isset($months_with_events[$year_month])) {
            $months_with_events[$year_month] = [];
        }
        $months_with_events[$year_month][] = $event;
    }

    return array($events, $months_with_events);
}

function extract_preg($pattern, $subject, &$result) {
    preg_match($pattern, $subject, $matches);

    if (isset($matches) && isset($matches[0])) {
        $result = $matches[0];
        return trim(str_replace($result, '', $subject));
    }
    else {
        $result = null;
        return $subject;
    }
}
