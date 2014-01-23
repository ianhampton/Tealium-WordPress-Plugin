=== Plugin Name ===
Contributors: tealium
Tags: analytics, tealium, data layer
Donate link: http://tealium.com
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds the Tealium tag and creates a data object from post data.

== Description ==

= Features =

Allows users to easily add the Tealium tag without editing any template files. 

A data object is created containing:

* Site name
* Site description
* Post date
* Post categories
* Post tags
* All post meta data, including custom fields
* Search terms


= About Tealium =


Tealium is the leader in enterprise tag management, serving some of the most demanding customers in the world. Tealium's web-based services make it easy for digital marketers to deploy and manage their third-party vendor tags, and then correlate the data those tags generate into an actionable source. Using Tealium, organizations can streamline their digital marketing operations, improve web site performance, cut costs, and power their big data initiatives. The company differentiates itself through ease of use, scale and performance, and fanatical customer support. Select clients include Petco, A+E Networks, Fox Networks Group, Urban Outfitters, and many more! More than ever, today's marketers are turning to digital solutions to improve efficiency and results.

Tags – commonly known as tracking pixels – are becoming the data collection mechanism of choice, leaving marketers reliant on them to gather the right data about visitor activity and take action to improve their experience. But the process of deploying and managing vendor tags can be a nightmare for marketers, due to the technical nature associated with tagging, heavy costs, impact on site performance, and the dependence of IT resources to make changes. Tealium puts digital marketers in control of their online solutions, making it easy to add, edit, or remove any vendor without requesting assistance from IT. In addition, Tealium provides a rich source of all of an organization's digital data to help marketers improve the effectiveness of their customer acquisition campaigns.

== Installation ==

To install:

* Copy to your wp-content/plugins folder.
* Enable through the plugins section within Wordpress.
* Paste your Tealium code into 'Tealium Tag Settings' under Settings in Wordpress.
* Optional - If there are items you wish to exclude from your data layer, add the keys as a comma separated list.

== Frequently Asked Questions ==

= What data is currently included in the data layer? =

* Site name
* Site description
* Post date
* Post categories
* Post tags
* All post meta data, including custom fields
* Search terms


== Screenshots ==

1. Tag settings.

== Changelog ==

= 1.2 =
* Added a ability to exclude keys from data layer.
* The JSON object will now be pretty-printed where PHP support allows.

= 1.1 =
* Migrate from capabilities to roles.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.2 =
Upgrade to enable data layer exclusions.

= 1.1 =
Migrate from capabilities to roles. Upgrade recommended.

= 1.0 =
Initial release.