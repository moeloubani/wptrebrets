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

$property_type = new CPT('wptrebs_property', array(
    'supports' => array('title', 'editor', 'thumbnail', 'comments', 'custom-fields')
));
$property_type->register_taxonomy(array(
    'type' => 'Property Type'
));

//Retrieve data for options
require 'inc/Options.php';

//Get PHRets library
require 'library/PHRets/phrets.php';

//Get feed
require 'inc/Feed.php';

//Checks if property already exists
require 'inc/CheckOld.php';

//Save feed as posts
require 'inc/Save.php';

//Updates property if already found
require 'inc/Update.php';

//Set shortcode to display on site
require 'inc/Shortcodes.php';

//Get styles and scripts
require 'inc/wp-functions.php';

//Instantiate plugin

//get feed info from feed class

function dd($variable) {
    var_dump($variable);
    die();
}

function wptrebretsLoad() {
	$mlsnums = \wptrebrets\inc\wptrebrets_get_option('rets_mls');
	$retsuser = \wptrebrets\inc\wptrebrets_get_option('rets_username');
	$retspass = \wptrebrets\inc\wptrebrets_get_option('rets_password');
    $feed = new \wptrebrets\inc\Feed("lp_dol, ml_num, addr, bath_tot, br, county, rltr, rms, s_r, status, zip, yr_built, area, timestamp_sql, pix_updt, idx_dt, legal_desc, ad_text", 15, $retsuser, $mlsnums, $retspass, "http://rets.torontomls.net:6103/rets-treb3pv/server/login");
    $save = new \wptrebrets\inc\Save($feed);
}

//add_action('init', 'wptrebretsLoad');