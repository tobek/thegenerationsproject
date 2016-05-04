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
        'posts_per_page' => 100,
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

// Takes in unix timestamp, adds # of months, returns unix timestamp
// Adapted from http://stackoverflow.com/a/24014541/458614
function add_months($timestamp, $months) {
    $date = new \DateTime("@$timestamp");

    $next = new \DateTime($date->format('Y-m-d'));
    if ($months >= 0) {
        $next->modify('last day of +'.$months.' month');
    }
    else {
        $next->modify('last day of '.$months.' month');
    }

    if($date->format('d') > $next->format('d')) {
        $interval = $date->diff($next);
    } else {
        $interval = new \DateInterval('P' . abs($months) . 'M');

        if ($months < 0) {
            $interval->invert = 1;
        }
    }

    $newDate = $date->add($interval);

    return $newDate->getTimestamp();
}

// Removes the matching pattern from subject, and returns matching pattern or first captured group if any
function extract_preg($pattern, $subject, &$result) {
    preg_match($pattern, $subject, $matches);

    if (isset($matches) && isset($matches[0])) {
        if (isset($matches[1])) {
            $result = $matches[1];
        }
        else {
            $result = $matches[0];
        }
        return trim(str_replace($matches[0], '', $subject));
    }
    else {
        $result = null;
        return $subject;
    }
}

// Removes and formats gallery shortcode from $subject and places it in $result, and returns $subject with shortcode removed
function extract_gallery_shortcode($subject, &$result, $ratio='3:2', $carousel=true, $autoplay=false) {
    $gallery_ids = null;

    $carousel = $carousel ? "carousel='fx=carousel'" : '';
    $autoplay = $autoplay ? '&pause-on-hover=false&paused=false&timeout=5000&speed=1000' : '';

    $subject = extract_preg('/\[gallery [^\]]*(ids="[^"]*")[^\]]*\]/', $subject, $gallery_ids);

    $cycle_options = "auto-height=$ratio&speed=250&swipe=true$autoplay";

    if ($gallery_ids) {
        $result= "[gss $gallery_ids $carousel options='$cycle_options']";
    }

    return $subject;
}
