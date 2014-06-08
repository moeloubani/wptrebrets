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

//Set options page
require 'inc/admin/Options.php';

//Retrieve data for options
require 'inc/Options.php';

//Get feed
require 'inc/Feed.php';

//Save feed as posts
require 'inc/Save.php';

//Set shortcode to display on site
require 'inc/Shortcodes.php';

//Instantiate plugin
