Tealium Wordpress Plugin
========================

To install:

* Copy to your wp-content/plugins folder.
* Enable through the plugins section within Wordpress.
* Paste your Tealium code into 'Tealium Tag Settings' under Settings in Wordpress.

Action examples
---------------

There are a couple of actions that can be used to manipulate the Tealium tag or UDO in your themes functions.php file. For example:

```php
/*
 * Add data to Tealium data object programatically
 */
function addToDataObject() {
	global $utagdata;
	$utagdata['php_version'] = phpversion();
	$utagdata['timestamp'] = time();
}
add_action( 'tealium_addToDataObject', 'addToDataObject' );

/*
 * Switch Tealium environment based on URL
 */
function switchEnvironment() {
	global $tealiumtag;
	if ( get_site_url() == 'http://dev.mywebsite.com' ) {
		$tealiumtag = str_replace( '/prod/', '/dev/', $tealiumtag );
	}
}
add_action( 'tealium_tagCode', 'switchEnvironment' );
```

