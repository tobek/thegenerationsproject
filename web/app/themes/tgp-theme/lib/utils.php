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
    $autoplay = $autoplay ? '&pause-on-hover=true&paused=false&timeout=5000&speed=1000' : '';

    $subject = extract_preg('/\[gallery [^\]]*(ids="[^"]*")[^\]]*\]/', $subject, $gallery_ids);

    if ($gallery_ids) {
        $result= "[gss $gallery_ids $carousel options='auto-height=$ratio&speed=250&swipe=true$autoplay']";
    }

    return $subject;
}
