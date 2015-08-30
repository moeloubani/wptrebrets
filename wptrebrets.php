<?php
/*
Plugin Name: TREB RETS Feed Plugin
Plugin URI: http://www.moeloubani.com/treb-plugin
Description: A plugin made to create a shortcode that pulls in a feed from TREB, now uses PHRETS 2.
Version: 2.0
Author: moeloubani
Author URI: http://www.moeloubani.com
License: GPL
*/


if (version_compare(phpversion(), '5.4', '<')) {
    wp_die('PHP 5.4 or higher required!');
}

//Handy function for testing, will remove in later versions
function dd($variable) {
    var_dump($variable);
    die();
}

//Autoload files
require_once('vendor/autoload.php');

function checkForPostType() {
    if (!post_type_exists('property') ) {

        $property_type = new CPT('wptrebs_property', array(
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'custom-fields')
        ));

        $property_type->register_taxonomy(array(
            'type' => 'Property Type'
        ));

    }
}

// Get it started
$wptrebrets_settings = new wptrebrets\inc\Options();
$wptrebrets_settings->hooks();

function wptrebrets_get_option( $key = '' ) {
    return cmb_get_option( wptrebrets\inc\Options::key(), $key );
}

//Instantiate plugin
function wptrebretsLoad() {
	$mlsnums = wptrebrets_get_option('rets_mls');
	$retsuser = wptrebrets_get_option('rets_username');
	$retspass = wptrebrets_get_option('rets_password');
    $feed = new \wptrebrets\inc\Feed("*", 25, $retsuser, $mlsnums, $retspass, "http://rets.torontomls.net:6103/rets-treb3pv/server/login");
    $save = new \wptrebrets\inc\Save($feed);
}

//add_action('init', 'wptrebretsLoad');
