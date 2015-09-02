<?php
/*
Plugin Name: TREB RETS Feed Plugin
Plugin URI: http://www.moeloubani.com/treb-plugin
Description: A plugin made to create a shortcode that pulls in a feed from TREB, now uses PHRETS 2.
Version: 0.4
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
    $property_type = new CPT('property', array(
        'supports' => array('title', 'editor', 'thumbnail', 'comments', 'custom-fields')
    ));

    if (!post_type_exists('property') ) {
        $property_type->register_taxonomy(array(
            'type' => 'Property Type'
        ));
    }
}

checkForPostType();

// Get it started
$install = new \wptrebrets\inc\Install();
$wptrebrets_settings = new wptrebrets\inc\Options();
$wptrebrets_settings->hooks();

function wptrebrets_get_option( $key = '' ) {
    return cmb_get_option( wptrebrets\inc\Options::key(), $key );
}

function wptreb_startUp() {
    $start = new \wptrebrets\inc\Commands();
    $start->getInitial();
}

if (isset($_GET['wptreb_import']) && $_GET['wptreb_import'] === 'treb') {
    add_action('init', 'wptreb_startUp');
}