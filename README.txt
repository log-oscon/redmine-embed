=== Plugin Name ===
Contributors: goblindegook, log_oscon
Tags: redmine, issue tracking, embed, issues, project management
Requires at least: 4.2
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed Redmine issues in your posts and pages.

== Description ==

Embed Redmine issue data in your posts and pages simply by pasting the issue's web page address into the editor field.

You will need to obtain a Redmine REST API key for the plugin.

== Installation ==

= Using Composer =

1. Require the plugin using Composer from your project's root:

`composer require logoscon/redmine-embed`

= Using the WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'redmine embed'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `redmine-embed.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `redmine-embed.zip`
2. Extract the `redmine-embed` directory to your computer
3. Upload the `redmine-embed` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= Where can I look up my Redmine REST API key? =

Find the Redmine REST API on your account page (`http://<my redmine install>/my/account`), on the right-hand pane of the default layout.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Initial release.
