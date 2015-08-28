wptrebrets
==========

Add data from TREB to WordPress

This plugin will download listings from the TREB server. It will download the properties and add them as a post type as well as the images and store them on your server. It will set up a cron job through WordPress to download and update properties each night.

To install it from the files in this repo you will have to have Composer installed and be able to run composer update to get the dependencies. If this isn't possible for you you can also visit this link to download a copy that includes everything and is ready to upload to the WordPress: http://www.moeloubani.com/new-version-of-treb-wordpress-plugin/

No support is offered with this plugin but if you have any questions please email me at moe@loubani.com, I'm also available for integration / customization of this plugin or consulting on any type of web development project.

Usage: use the post types as you would any other post - it will be a post type called property. The custom fields are visible if you check the custom fields box at the top above help.

Use shortcode [wptrebs_feed number="4"] to get a feed of properties, you can pass a number to retrieve or leave it out to get the default 4.

Version 0.2

- Added Composer to manage some dependencies
- Moved to PHRets 2.2 from 1.X
- Moved up minimum requirement to PHP 5.4

Version 0.1:

- First release
- Imports properties and images to WordPress
- Adds images to featured and links others to post for gallery or slider plugins


To do:

- Everything updates well but photos still not updating all the way, working on it.
- Want to add a search widget as well
- Adding a simpler way to link between post type
- Google Maps integration


Moe Loubani
http://www.moeloubani.com
moe@loubani.com

Edit: I've decided to expand on this plugin in the near future, keep checking back here for updates and please submit any issues you're having if you use it so that I can make sure it's fixed on the next pass.
