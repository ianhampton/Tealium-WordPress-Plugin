=== Plugin Name ===
Contributors: tealium
Tags: analytics, tealium, data object, data layer
Donate link: http://tealium.com
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 2.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds the Tealium tag and creates a data layer/data object for your Wordpress site.

== Description ==

= Features =

Allows users to easily add the Tealium tag without editing any template files. 

A data object is added to your Wordpress website containing:

* Site name
* Site description
* Post date
* Post categories
* Post tags
* All post meta data, including custom fields
* Search terms
* Number of search results


= About Tealium =


Tealium is the leader in enterprise tag management, serving some of the most demanding customers in the world. Tealium's web-based services make it easy for digital marketers to deploy and manage their third-party vendor tags, and then correlate the data those tags generate into an actionable source. Using Tealium, organizations can streamline their digital marketing operations, improve web site performance, cut costs, and power their big data initiatives. The company differentiates itself through ease of use, scale and performance, and fanatical customer support. Select clients include Petco, A+E Networks, Fox Networks Group, Urban Outfitters, and many more! More than ever, today's marketers are turning to digital solutions to improve efficiency and results.

Tags – commonly known as tracking pixels – are becoming the data collection mechanism of choice, leaving marketers reliant on them to gather the right data about visitor activity and take action to improve their experience. But the process of deploying and managing vendor tags can be a nightmare for marketers, due to the technical nature associated with tagging, heavy costs, impact on site performance, and the dependence of IT resources to make changes. Tealium puts digital marketers in control of their online solutions, making it easy to add, edit, or remove any vendor without requesting assistance from IT. In addition, Tealium provides a rich source of all of an organization's digital data to help marketers improve the effectiveness of their customer acquisition campaigns.

== Installation ==

To install:

* Install from the Wordpress plugin repository, or copy to your wp-content/plugins folder.
* Enable through the plugins section within Wordpress.
* Enter your account/profile/environment settings into 'Tealium Settings' under Settings in the admin menu.
* That's it!

Optional steps:

* The position of the Tealium tag can be selected.
* If there are items you wish to exclude from your data object, add the keys as a comma separated list.
* A cache buster can be enabled for content editors.
* Use the data source bulk export to quickly import your Wordpress custom fields into Tealium.
* Choose between sync/async deployments.

== Frequently Asked Questions ==

= What data is currently included in the data object? =

* Site name
* Site description
* Post date
* Post categories
* Post tags
* All post meta data, including custom fields
* Search terms
* Number of search results

= What are the Tealium tag location options? =

* After opening body tag (recommended)
* Header - Before closing head tag
* Footer - Before closing body tag
* Immediately after the opening head tag

= Is WooCommerce supported? =

WooCommerce stores product information as meta data, so by default your data object will be rich with data on product pages.

Version 1.3 adds basic support for cart contents information in the data object.

= Can I add to the data layer dynamically using PHP code? =

This can be achieved calling the 'tealium_addToDataObject' action from your themes functions.php file or a separate plugin.

For more information see the [action examples on GitHub](https://github.com/ianhampton/Tealium-Wordpress-Plugin#action-examples).

= Can I automatically switch between Tealium environments on my dev/live website? =

This can be achieved by conditionally modifying the Tealium tag code by calling the 'tealium_tagCode' action from your themes functions.php file or a separate plugin.

For more information see the [action examples on GitHub](https://github.com/ianhampton/Tealium-Wordpress-Plugin#action-examples).

== Screenshots ==

1. The Tealium plugin allows your Tealium tag to be added straight to your site from your Wordpress dashboard.
2. A data object is added to your site containing basic post or page data.
3. The plugin also takes categories and tags...
4. ...and rolls them into the data object.
5. Custom fields and other meta data is also supported.
6. Many existing Wordpress plugins make use of meta data, so your data object should be rich with potential data sources.
7. Advanced settings allow a number of options to be configured and features enabled.
8. The bulk export feature allows you to quickly import your Wordpress data sources into Tealium.

== Changelog ==

= 2.1.2 =
* Change name parameter location to match TiQ update.

= 2.1.1 =
* Added pretty printing of JSON object for older versions of PHP.
* Add name parameter on bulk data source export.

= 2.1 =
* Added DNS Prefetching option

= 2.0 =
* UI makeover.
* Quickly deploy Tealium by providing account/profile/environment details.
* Data source export.
* Cache buster for content editors.
* Support for utag.sync.js file.
* Support for sync/async deployments.

= 1.5.1 =
* Fixed opening head tag bug.

= 1.5 =
* Added search result count to data object.
* Added actions to allow the data object and Tealium tag to be modified by theme/plugin developers.
* Added top of opening head tag location setting.
* Configurable option for Camel Case or underscore data layer style.

= 1.4 =
* Exclusions bug fix.

= 1.3 =
* Tag location is now configurable.
* Basic support for WooCommerce cart data.
* Plugin refactored to support translations.

= 1.2 =
* Added the ability to exclude keys from the data object.
* The JSON object will now be pretty-printed where PHP support allows.

= 1.1 =
* Migrate from capabilities to roles.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 2.1.2 =
Change name parameter location to match TiQ update.

= 2.1.1 =
Added pretty printing of JSON object for older versions of PHP.
Add name parameter on bulk data source export.

= 2.1 =
Added DNS prefetching option.

= 2.0 =
Updated UI, many new features. Now only requires account/profile/environment details - manually pasting in code from the code center is still supported.

= 1.5.1 =
Upgrade to add search result count to data layer.
Hooks to allow the data object and Tealium tag to be modified by theme/plugin developers.
New tag location setting (after opening head tag).
Camel Case and underscore data layer style options.

= 1.5 =
Upgrade to add search result count to data layer.
Hooks to allow the data object and Tealium tag to be modified by theme/plugin developers.
New tag location setting (after opening head tag).
Camel Case and underscore data layer style options.

** Important ** - If you wish to continue using the Camel Case data style please check that your configuration option corresponds to this choice.

= 1.4 =
Exclusions bug fix, upgrade advised.

= 1.3 =
New features include configurable tag location and WooCommerce support.

= 1.2 =
Upgrade to enable data object exclusions.

= 1.1 =
Migrate from capabilities to roles. Upgrade recommended.

= 1.0 =
Initial release.