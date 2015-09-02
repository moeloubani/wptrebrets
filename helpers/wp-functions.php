<?php

//Adds CSS and Javascript
function wptrebs_styles_scripts() {
    wp_enqueue_style( 'wptrebs_feed', plugins_url('..\assets\css\style.css', __FILE__) );
}

add_action('wp_enqueue_scripts', 'wptrebs_styles_scripts');


// Image sizes for feed and gallery/carousel
add_image_size('wptrebs_feed_img', 169, 115, true);
add_image_size('wptrebs_gallery_img', 125, 75, true);
