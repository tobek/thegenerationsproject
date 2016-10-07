<?php

/**
 * @NOTE - this file is unused here in tgp-theme. This is for hackily overriding the plugin file `web/app/plugins/gallery-slideshow/gss_html.php`. Only putting it here for the purposes of source control (and also, in any build automation, this could be automatically copied into the appropriate place). Of course, this is not a very elegant method and could break with any updates to that plugin, but it's necessary at the moment.
 *
 *     cp tgp-theme/templates/gss_html-override.php web/app/plugins/gallery-slideshow/gss_html.php
 */

function gss_html_output($ids,$name,$style,$options,$carousel) {
    $ids = explode( ',', $ids );
    $slide_count = count($ids);
    $options = html_entity_decode($options);
    parse_str( $options, $opts );
    if( $style != '' ){
        $style = ' style="' . $style . '"';
    }
    $longest_cap = array( 'length' => 0, 'text' => '' );
    $slides = '';
    $carousel_slides = '';
    $pager = "\n\t\t\t<div id=\"" . $name . '_pager" class="gss-pager"></div>';
    foreach( $ids as $image_id ){
        $attachment = get_post( $image_id, 'ARRAY_A' );
        $src = wp_get_attachment_image_src( $image_id, 'large' ); // @TOBY changed 'full' to 'large'
        $excerpt = htmlspecialchars($attachment['post_excerpt']);
        // @TOBY added following 5 lines
        $post_title = $attachment['post_title'] === 'none' ? '' : htmlspecialchars($attachment['post_title']);
        $link_to = htmlspecialchars($attachment['post_content'], ENT_QUOTES); // janking description field to use as link
        $caption_classes = '';
        if (! $post_title) $caption_classes .= 'no-caption-title ';
        if (! $excerpt) $caption_classes .= 'no-caption-body';
        // @TOBY changed following line
        $slides .= "\n\t\t\t<img src=\"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7\" style=\"background-image:url('$src[0]')\" alt=\"$excerpt\" data-post-title=\"$post_title\"  data-link-to=\"$link_to\" data-caption-classes=\"$caption_classes\"/>\n";
        if( !empty($carousel) ){
            $carousel_slides .= "\t\t\t<div><img src=\"$src[0]\" title=\"$excerpt\" /></div>\n";
        }
        if( strlen( $attachment['post_excerpt'] ) > $longest_cap['length'] ){
            $longest_cap['length'] = strlen( $attachment['post_excerpt'] );
            $longest_cap['text'] = $attachment['post_excerpt'];
        }
    }
    // do options
    $default_opts = array(
        'timeout' => '0',
        'prev' => '#' . $name . '_prev',
        'next' => '#' . $name . '_next',
        'pager' => '#' . $name . '_pager',
        'pager-template' => '<a href=#>&nbsp;</a>',
        'speed' => '750',
        'center-horz' => 'true'
    );
    $has_captions = !empty( $longest_cap['text'] ) ? true : false;
    if( $has_captions ){
        $default_opts['caption'] = '#' . $name . '_captions';
        // @TOBY changed following line to change caption-template @TODO this could maybe be done with `caption-template` in options passed to shortcode, but i was having entity encoding issues
        // $default_opts['caption-template'] = '{{postTitle}} {{alt}}';
        $default_opts['caption-template'] = htmlspecialchars('<div class="{{captionClasses}}"><h3 class="image-title {{ 1 === 1 ? "foo" : "bar" }}>{{postTitle}}</h3><div class="image-caption">{{alt}}</div></div>');
        $no_captions_class = '';
    }
    else{
        $no_captions_class = ' no-captions';
    }
    foreach( $default_opts as $k => $v ){
        if( !array_key_exists( $k, $opts ) ){
            $opts[$k] = $v;
        }
    }
    $options_string = '';
    foreach( $opts as $k => $v ){
        $options_string .= 'data-cycle-' . $k . '="' . $v . '"' . "\n\t\t";
    }
    // begin gss html assembly
    $html = "\n\n" . '<div id="' . $name . '" class="gss-container' . $no_captions_class . '"' . $style . '>' . "\n\t";
    $html .=    '<div class="cycle-slideshow" 
        '. $options_string . 
    '>';
    if( $has_captions ){
        $html .= $pager;
    }
    $html .= $slides . "\t</div>\n\t";
    $html .=    '<div class="gss-info">' . "\n\t\t";
    
    if( !empty($carousel) ){
        $thumbs_to_show = $slide_count < 8 ? $slide_count : 8;
        $default_carousel_opts = array(
            'timeout' => '0',
            'carousel-visible' => $thumbs_to_show,
            'slides'=> '> div',
            'carousel-fluid' => 'true',
            'allow-wrap' => 'false'
        );
        $carousel = html_entity_decode($carousel);
        parse_str( $carousel, $carousel_opts );

        $carousel_vis_set = array_key_exists('carousel-visible', $carousel_opts) ? 'data-car-vis-set="true"' : '';
        
        foreach( $default_carousel_opts as $k => $v ){
            if( !array_key_exists( $k, $carousel_opts ) ){
                $carousel_opts[$k] = $v;
            }
        }
        $carousel_opts_string = '';
        foreach( $carousel_opts as $k => $v){
            $carousel_opts_string .= 'data-cycle-' . $k . '="' . $v . '"' . "\n\t\t";
        }
        $html .= '<div class="cycle-slideshow carousel-pager"
        ' . $carousel_opts_string . $carousel_vis_set . ">\n" . $carousel_slides . "\t\t</div>\n\t\t";
    }
    
    $html .=        '<div class="gss-nav"><div id="' . $name . '_prev" class="gss-prev">&lt;</div><div id="' . $name . '_next" class="gss-next">&gt;</div></div>';
    if( !$has_captions ){
        $html .= $pager;
    }
    if( $has_captions ){
        $html .=        '<div class="gss-long-cap">' . $longest_cap['text'] . "</div>";
        $html .=        '<div id="' . $name . '_captions" class="gss-captions">' . "</div>";
    }
    $html .= "\n\t</div>\n</div>\n\n";
    return $html;
}

?>