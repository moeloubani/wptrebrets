wptrebrets
==========

Add data from TREB to WordPress

If you don't want to bother with Composer or are just trying to download this to run on your site or a client's site, download it from the link below: 

http://www.moeloubani.com/new-version-of-treb-wordpress-plugin/

Use this plugin to integrate a RETS feed from TREB into your WordPress site.

Add your username/password in the settings after activating the plugin and then navigate to:

http://www.yoursite.com/wp-admin/index.php?wptreb_import=treb

And wait as the initial import finishes.

This plugin requires PHP 5.4+

Once a day it will run a check to see if it needs to update to the latest version of the listing or add - SOLD to the title if it is no unavailable. It will also download the 25 latest properties each day.

To install it just upload the plugin into your plugins folder and add your login info to your settings. It will automatically be run in a day.

No support is offered with this plugin but if you have any questions please email me at moe@loubani.com, I'm also available for full real estate site solutions.

Usage: use the post types as you would any other post. The custom fields are visible if you check the custom fields box at the top.

Use shortcode [wptrebs_feed number="4"] to get a feed of properties, you can pass a number to retrieve or leave it out to get the default 4.

Version 0.4:
Documented more, made sure updating works for nightly checks, downloads 25 new properties that were listed the day before each day

Version 0.3:
Made a bunch of changes and documented a lot, updated to PHRets 2.0 and changed the way properties are imported.

Version 0.2:
Cleaned up updating

Version 0.1:
First release, use at your own risk.


To do:

Everything updates well but photos still not updating all the way, working on it.


Moe Loubani

http://www.moeloubani.com

moe@loubani.com

Edit: I've decided to expand on this plugin in the near future, keep checking back here for updates and please submit any issues you're having if you use it so that I can make sure it's fixed on the next pass.
