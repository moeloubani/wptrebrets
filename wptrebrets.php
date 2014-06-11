<?php

/*
Plugin Name: TREB RETS Feed Plugin
Plugin URI: http://www.moeloubani.com/treb-plugin
Description: A plugin made to create a shortcode that pulls in a feed from TREB.
Version: 1.0
Author: moeloubani
Author URI: http://www.moeloubani.com
License: A "Slug" license name e.g. GPL2
*/

//Get options framework
require 'library/CMB/init.php';

//Get custom post type library and create post type
require 'library/CPT/CPT.php';

$property_type = new CPT('wptrebs_property');
$property_type->register_taxonomy(array(
    'type' => 'Property Type'
));

//Retrieve data for options
require 'inc/Options.php';

//Get PHRets library
require 'library/PHRets/phrets.php';

//Get feed
require 'inc/Feed.php';

//Save feed as posts
require 'inc/Save.php';

//Set shortcode to display on site
require 'inc/Shortcodes.php';

//Instantiate plugin

//get feed info from feed class



function wptrebretsLoad() {
    $thing = new \wptrebrets\inc\Feed("lp_dol, ml_num, addr, bath_tot, br, county, rltr, rms, s_r, status, zip, yr_built, area, timestamp_sql, pix_updt, idx_dt, legal_desc, ad_text", 3, "D14hcd", "W2925698,C2902118", "Kf$7439", "http://rets.torontomls.net:6103/rets-treb3pv/server/login");
    $thing->start();
    $thing->connect();
    $thing->search();

//print_r($thing->show());
    $save = new \wptrebrets\inc\Save($thing->mls);
//$save->photos($thing->photos());

    $save->posts($thing->show());


    global $user_ID, $wpdb;

    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . '
        WHERE post_title = %s
        AND post_type = \'stuff\'',
        $postTitle
    );
    $wpdb->query( $query );

    if ( $wpdb->num_rows ) {
        $post_id = $wpdb->get_var( $query );
        $meta = get_post_meta( $post_id, 'times', TRUE );
        $meta++;
        update_post_meta( $post_id, 'times', $meta );
    } else {
        $new_post = array(
            'post_title' => $postTitle,
            'post_content' => '',
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => '',
            'post_type' => 'stuff',
            'post_category' => array(0)
        );

        $post_id = wp_insert_post($new_post);
        add_post_meta($post_id, 'times', '1');
    }
}

//add_action('init', 'wptrebretsLoad');


//$listings = $thing->show();
