<?php
function wptrebs_styles() {
    wp_enqueue_style( 'wptrebs_feed', plugins_url('..\assets\css\style.css', __FILE__) );
}

add_action('wp_enqueue_scripts', 'wptrebs_styles');

add_image_size('wptrebs_feed_img', 169, 115, true);

add_image_size('wptrebs_gallery_img', 125, 75, true);

//On plugin activation schedule our daily database backup
register_activation_hook( __FILE__, 'wi_create_daily_property_schedule' );
function wi_create_daily_property_schedule(){
	//Use wp_next_scheduled to check if the event is already scheduled
	$timestamp = wp_next_scheduled( 'wi_create_daily_property' );

	//If $timestamp == false schedule daily backups since it hasn't been done previously
	if( $timestamp == false ){
		//Schedule the event for right now, then to repeat daily using the hook 'wi_create_daily_backup'
		wp_schedule_event( time(), 'daily', 'wi_create_daily_property' );
	}
}

//Hook our function , wi_create_backup(), into the action wi_create_daily_backup
add_action( 'wi_create_daily_property', 'wi_create_property' );

function wi_create_property(){
	add_action('init', 'wptrebretsLoad');
}