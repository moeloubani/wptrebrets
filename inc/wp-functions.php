<?php
function wptrebs_styles() {
    wp_enqueue_style( 'wptrebs_feed', plugins_url('..\assets\css\style.css', __FILE__) );
}

add_action('wp_enqueue_scripts', 'wptrebs_styles');

add_image_size('wptrebs_feed_img', 200, 180, true);