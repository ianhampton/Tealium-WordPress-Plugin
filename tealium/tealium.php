<?php
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: Adds the Tealium tag and creates a data layer for your Wordpress site.
Version: 2.1
Author: Ian Hampton - Tealium EMEA
Author URI: http://tealium.com
Text Domain: tealium
*/

function activate_tealium() {

	// Only set data style to underscore for fresh installations
	if ( !get_option( 'tealiumTag' ) ) {
		update_option( 'tealiumDataStyle', '1' );
	}
	else {
		add_option( 'tealiumDataStyle', '' );
	}

	add_option( 'tealiumTag', '' );
	add_option( 'tealiumTagCode', '' );
	add_option( 'tealiumTagLocation', '' );
	add_option( 'tealiumExclusions', '' );
	add_option( 'tealiumAccount', '' );
	add_option( 'tealiumProfile', '' );
	add_option( 'tealiumEnvironment', '' );
	add_option( 'tealiumTagType', '' );
	add_option( 'tealiumCacheBuster', '' );
	add_option( 'tealiumUtagSync', '' );
	add_option( 'tealiumDNSPrefetch', '1' );
}

function deactive_tealium() {
	delete_option( 'tealiumExclusions' );
	delete_option( 'tealiumDataStyle', '' );
	delete_option( 'tealiumTagLocation' );
	delete_option( 'tealiumTagCode' );
	delete_option( 'tealiumTag' );
	delete_option( 'tealiumAccount' );
	delete_option( 'tealiumProfile' );
	delete_option( 'tealiumEnvironment' );
	delete_option( 'tealiumTagType' );
	delete_option( 'tealiumCacheBuster' );
	delete_option( 'tealiumUtagSync' );
	delete_option( 'tealiumDNSPrefetch' );
}

function admin_init_tealium() {
	register_setting( 'tealiumTagBasic', 'tealiumAccount' );
	register_setting( 'tealiumTagBasic', 'tealiumProfile' );
	register_setting( 'tealiumTagBasic', 'tealiumEnvironment' );
	register_setting( 'tealiumTagAdvanced', 'tealiumTagCode' );
	register_setting( 'tealiumTagAdvanced', 'tealiumTagLocation' );
	register_setting( 'tealiumTagAdvanced', 'tealiumDataStyle' );
	register_setting( 'tealiumTagAdvanced', 'tealiumExclusions' );
	register_setting( 'tealiumTagAdvanced', 'tealiumTagType' );
	register_setting( 'tealiumTagAdvanced', 'tealiumCacheBuster' );
	register_setting( 'tealiumTagAdvanced', 'tealiumUtagSync' );
	register_setting( 'tealiumTagAdvanced', 'tealiumDNSPrefetch' );

	wp_register_style( 'tealium-stylesheet', plugins_url('tealium.css', __FILE__) );
}

function admin_menu_tealium() {
	$page = add_options_page( __( 'Tealium Tag Settings', 'tealium' ), __( 'Tealium Settings', 'tealium' ), 'manage_options' , 'tealium', 'options_page_tealium' );
	add_action( 'admin_print_styles-' . $page, 'admin_styles_tealium' );
}

function options_page_tealium() {
	include plugin_dir_path( __FILE__ ).'tealium.options.php';
}

function admin_styles_tealium() {
	wp_enqueue_style( 'tealium-stylesheet' );
}


/*
 * Admin messages
 */
function admin_notices_tealium() {
	global $pagenow;
	$currentScreen = get_current_screen();
	$tealiumTagCode = get_option( 'tealiumTagCode' );
	$tealiumAccount = get_option( 'tealiumAccount' );
	$tealiumProfile = get_option( 'tealiumProfile' );
	$tealiumEnvironment = get_option( 'tealiumEnvironment' );

	// Add an admin message when looking at the plugins page if the Tealium tag is not found
	if ( $pagenow == 'plugins.php' ) {
		if ( empty( $tealiumTagCode ) && ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) ) {
			$html = '<div class="updated">';
			$html .= '<p>';
			$html .= sprintf( __( 'Please enter your Tealium account details or tag code <a href="%s">over here &raquo;</a>', 'tealium' ), esc_url( 'options-general.php?page=tealium' ) );
			$html .= '</p>';
			$html .= '</div>';
			echo $html;
		}
	}

	// Add an error message if utag.sync is enabled but no account is specified
	if ( $currentScreen->base == 'settings_page_tealium' ) {
		$utagSync = get_option( 'tealiumUtagSync' );
		if ( "1" == $utagSync ) {
			if ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) {
				$html = '<div class="error">';
				$html .= '<p>';
				$html .= 'You must provide account/profile/environment details to use utag.sync.js.';
				$html .= '</p>';
				$html .= '</div>';
				echo $html;
			}
		}
	}

	// Add an error message if the cache buster is enabled but no account is specified
	if ( $currentScreen->base == 'settings_page_tealium' ) {
		$tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
		if ( "1" == $tealiumCacheBuster ) {
			if ( empty( $tealiumAccount ) || empty( $tealiumProfile ) || empty( $tealiumEnvironment ) ) {
				$html = '<div class="error">';
				$html .= '<p>';
				$html .= 'You must provide account/profile/environment details to use a cache buster.';
				$html .= '</p>';
				$html .= '</div>';
				echo $html;
			}
		}
	}
}

/*
 * Removes exclusions listed in admin setting
 */
function removeExclusions( $utagdata ) {
	$exclusions = get_option( 'tealiumExclusions' );
	if ( !empty( $exclusions ) ) {

		// Convert list to array and trim whitespace
		$exclusions = array_map( 'trim', explode( ',', $exclusions ) );

		foreach ( $exclusions as $exclusion ) {
			if ( array_key_exists( $exclusion, $utagdata ) ) {
				// Remove from utag data array
				unset( $utagdata[$exclusion] );
			}
		}
	}
	return $utagdata;
}
add_filter( 'tealium_removeExclusions', 'removeExclusions' );


/*
 * Convert camel case to underscores
 */
function convertCamelCase( $utagdata, $arrayHolder = array() ) {
	$underscoreArray = !empty( $arrayHolder ) ? $arrayHolder : array();
	foreach ( $utagdata as $key => $val ) {
		$newKey = preg_replace( '/[A-Z]/', '_$0', $key );
		$newKey = strtolower( $newKey );
		$newKey = ltrim( $newKey, '_' );
		if ( !is_array( $val ) ) {
			$underscoreArray[$newKey] = $val;
		} else {
			$underscoreArray[$newKey] = convertCamelCase( $val, $underscoreArray[$newKey] );
		}
	}
	return $underscoreArray;
}
add_filter( 'tealium_convertCamelCase', 'convertCamelCase' );


/*
 * Adds WooCommerce data to data layer
 */
function wooCommerceData( $utagdata ) {
	global $woocommerce;

	// Get cart details
	$woocart = (array) $woocommerce->cart;
	$productData = array();

	if ( !empty( $woocart['cart_contents'] ) ) {

		// Get cart product IDs, SKUs, Titles etc.
		foreach ( $woocart['cart_contents'] as $cartItem ) {
			$productMeta = new WC_Product( $cartItem['product_id'] );

			$productData['product_id'][] = $cartItem['product_id'];
			$productData['product_sku'][] = $productMeta->post->sku;
			$productData['product_name'][] = $productMeta->post->post->post_title;
			$productData['product_quantity'][] = $cartItem['quantity'];
			$productData['product_regular_price'][] = get_post_meta( $cartItem['product_id'], '_regular_price', true );
			$productData['product_sale_price'][] = get_post_meta( $cartItem['product_id'], '_sale_price', true );
			$productData['product_type'][] = $productMeta->post->product_type;
		}
	}

	// Remove the extensive individual product details
	unset( $woocart['cart_contents'] );
	unset( $woocart['cart_session_data'] );
	unset( $woocart['tax'] );

	// Get currency in use
	$woocart['site_currency'] = get_woocommerce_currency();

	// Merge shop and cart details into utagdata
	$utagdata = array_merge( $utagdata, $woocart );
	$utagdata = array_merge( $utagdata, $productData );

	return $utagdata;
}
add_filter( 'tealium_wooCommerceData', 'wooCommerceData' );

/*
 * Creates the data object as an array
 */
function dataObject() {
	global $utagdata;
	$utagdata = array();

	// Blog info
	$utagdata['siteName'] = get_bloginfo( 'name' );
	$utagdata['siteDescription'] = get_bloginfo( 'description' );

	if ( ( is_single() ) || is_page() ) {
		global $post;

		// Get categories
		$categories = get_the_category();
		$catout = array();

		if ( $categories ) {
			foreach ( $categories as $category ) {
				$catout[] = $category->slug;
			}
			$utagdata['postCategory'] = $catout;
		}

		// Get tags
		$tags = get_the_tags();
		$tagout = array();
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				$tagout[] = $tag->slug;
			}
			$utagdata['postTags'] = $tagout;
		}

		// Misc post/page data
		$utagdata['pageType'] = get_post_type();
		$utagdata['postTitle'] = get_the_title();
		$utagdata['postAuthor'] = get_userdata( $post->post_author )->display_name;
		$utagdata['postDate'] = get_the_time( 'Y/m/d' );

		// Get and merge post meta data
		$meta = get_post_meta( get_the_ID() );
		if ( $meta ) {
			$utagdata = array_merge( $utagdata, $meta );
		}
	}
	else if ( is_archive() ) {
			$utagdata['pageType'] = "archive";
		}
	else if ( ( is_home() ) || ( is_front_page() ) ) {
			$utagdata['pageType'] = "homepage";
		}
	else if ( is_search() ) {
			// Collect search and result data
			$searchQuery = get_search_query();
			$searchResults = &new WP_Query( 's='.str_replace( ' ', '+', $searchQuery.'&showposts=-1' ) );
			$searchCount = $searchResults->post_count;
			wp_reset_query();

			// Add to udo
			$utagdata['pageType'] = "search";
			$utagdata['searchQuery'] = $searchQuery;
			$utagdata['searchResults'] = $searchCount;
		}

	// Add shop data if WooCommerce is installed
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$utagdata = apply_filters( 'tealium_wooCommerceData', $utagdata );
	}

	// Include data layer additions from action if set
	if ( has_action( 'tealium_addToDataObject' ) ) {
		do_action( 'tealium_addToDataObject' );
	}

	if ( get_option( 'tealiumDataStyle' ) == '1' ) {
		// Convert camel case to underscore
		$utagdata = apply_filters( 'tealium_convertCamelCase', $utagdata );
	}

	// Remove excluded keys
	$utagdata = apply_filters( 'tealium_removeExclusions', $utagdata );

	return $utagdata;
}

/*
 * Encodes the data object array as JSON, outputs script tag
 */
function encodedDataObject( $return = false ) {
	$utagdata = dataObject();

	// Encode data object
	if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
		// Pretty print JSON if PHP version supports it
		$jsondata = json_encode( $utagdata, JSON_PRETTY_PRINT );
	}
	else {
		$jsondata = json_encode( $utagdata );
	}

	// Output data object
	if ( json_decode( $jsondata ) !== null ) {
		$utag_data = "<script type=\"text/javascript\">\nvar utag_data = {$jsondata};\n</script>\n";
		if ( !$return ) {
			echo $utag_data;
		}
		else {
			return $utag_data;
		}
	}
}

/*
 * Get the Tealium tag code, applying filters if necessary
 */
function getTealiumTagCode() {
	global $tealiumtag;
	$tealiumAdvanced = get_option( 'tealiumTagCode' );
	$tealiumAccount = get_option( 'tealiumAccount' );
	$tealiumProfile = get_option( 'tealiumProfile' );
	$tealiumEnvironment = get_option( 'tealiumEnvironment' );
	$tealiumTagType = get_option( 'tealiumTagType' );
	$tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
	$cacheBuster = "";

	if ( ( current_user_can( 'edit_posts' ) ) && ( "1" == $tealiumCacheBuster ) ) {
		$cacheBuster = "?_cb=".time();
	}

	// Use the free text 'advanced' config if it appears to contain a tag
	if ( ( !empty( $tealiumAdvanced ) ) && ( strpos( $tealiumAdvanced, 'utag.js' ) !== false ) ) {
		$tealiumtag = $tealiumAdvanced;
	}
	else {
		if ( ( !empty( $tealiumAccount ) ) && ( !empty( $tealiumProfile ) ) && ( !empty( $tealiumEnvironment ) ) ) {
			if ( $tealiumTagType != '1' ) {
				$tealiumtag = "<!-- Loading script asynchronously -->\n";
				$tealiumtag .= "<script type=\"text/javascript\">\n";
				$tealiumtag .= " (function(a,b,c,d){\n";
				$tealiumtag .= " a='//tags.tiqcdn.com/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.js{$cacheBuster}';\n";
				$tealiumtag .= " b=document;c='script';d=b.createElement(c);d.src=a;d.type='text/java'+c;d.async=true;\n";
				$tealiumtag .= " a=b.getElementsByTagName(c)[0];a.parentNode.insertBefore(d,a);\n";
				$tealiumtag .= " })();\n";
				$tealiumtag .= "</script>\n";
				$tealiumtag .= "<!-- END: T-WP -->\n";
			}
			else {
				$tealiumtag = "<!-- Loading script synchronously -->\n";
				$tealiumtag .= "<script type=\"text/javascript\" src=\"//tags.tiqcdn.com/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.js{$cacheBuster}\"></script>\n";
				$tealiumtag .= "<!-- END: T-WP -->\n";
			}
		}
	}

	// Include tag action if set
	if ( has_action( 'tealium_tagCode' ) ) {
		do_action( 'tealium_tagCode' );
	}

	return $tealiumtag;
}

function outputTealiumTagCode() {
	echo getTealiumTagCode();
}

/*
 * Generate utag.sync.js tag
 */
function outputUtagSync() {
	$tealiumAccount = get_option( 'tealiumAccount' );
	$tealiumProfile = get_option( 'tealiumProfile' );
	$tealiumEnvironment = get_option( 'tealiumEnvironment' );
	$tealiumCacheBuster = get_option( 'tealiumCacheBuster' );
	$cacheBuster = "";
	$utagSync = "";

	if ( ( current_user_can( 'edit_posts' ) ) && ( "1" == $tealiumCacheBuster ) ) {
		$cacheBuster = "?_cb=".time();
	}

	if ( ( !empty( $tealiumAccount ) ) && ( !empty( $tealiumProfile ) ) && ( !empty( $tealiumEnvironment ) ) ) {
		$utagSync = "<script src=\"//tags.tiqcdn.com/utag/{$tealiumAccount}/{$tealiumProfile}/{$tealiumEnvironment}/utag.sync.js{$cacheBuster}\"></script>\n";
	}

	echo $utagSync;
}


/*
 * Generate DNS Prefetch
 */
function outputDNSPrefetch() {
	$dnsPrefetch = "<link rel=\"dns-prefetch\" href=\"//tags.tiqcdn.com\">\n";
	echo $dnsPrefetch;
}


/*
 * Enable output buffer
 */
function outputFilter( $template ) {
	ob_start();
	return $template;
}

/*
 * Used in combination with outputFilter() to add Tealium tag after <body>
 */
function tealiumTagBody( $tealiumTagCode ) {
	$content = ob_get_clean();
	$tealiumTagCode = getTealiumTagCode();

	// Insert Tealium tag after body tag (sadly there is no wp_body hook)
	$content = preg_replace( '#<body([^>]*)>#i', "<body$1>\n\n{$tealiumTagCode}", $content, 1 );
	echo $content;
}

/*
 * Used in combination with outputFilter() to add Tealium tag after <head>
 */
function tealiumTagHead( $tealiumTagCode ) {
	$content = ob_get_clean();
	$tealiumTagCode = getTealiumTagCode();
	$tealiumDataObject = encodedDataObject( true );

	// Insert Tealium tag immediately after head tag
	$content = preg_replace( '#<head([^>]*)>#i', "<head$1>\n{$tealiumDataObject}\n{$tealiumTagCode}", $content, 1 );
	echo $content;
}

/*
 * Determine where the Tealium tag should be located and insert it
 */
function insertTealiumTag() {
	$tealiumTagLocation = get_option( 'tealiumTagLocation' );
	$tealiumTagCode = getTealiumTagCode();

	if ( !empty( $tealiumTagCode ) ) {
		switch ( $tealiumTagLocation ) {
		case '1':
			// Location - Header
			add_action( 'wp_head', 'outputTealiumTagCode', 10000 );
			break;
		case '2':
			// Location - Footer
			add_action( 'wp_footer', 'outputTealiumTagCode', 10000 );
			break;
		case '3':
			// Location - Header (Top)
			// Start content buffer
			add_filter( 'template_include', 'outputFilter', 1 );
			// Inject Tealium tag, output page contents
			add_filter( 'shutdown', 'tealiumTagHead', 0 );
			break;
		case '0':
		default:
			// Location - After opening body tag
			// Start content buffer
			add_filter( 'template_include', 'outputFilter', 1 );
			// Inject Tealium tag, output page contents
			add_filter( 'shutdown', 'tealiumTagBody', 0 );
			break;
		}
	}

	// Add utag.sync.js if required
	$utagSync = get_option( 'tealiumUtagSync' );
	if ( "1" == $utagSync ) {
		add_action( 'wp_head', 'outputUtagSync', 1 );
	}

	// Add utag.sync.js if required
	$dnsPrefetch = get_option( 'tealiumDNSPrefetch' );
	if ( "1" == $dnsPrefetch ) {
		add_action( 'wp_head', 'outputDNSPrefetch', 0 );
	}
}

if ( is_admin() ) {
	register_activation_hook( __FILE__, 'activate_tealium' );
	register_deactivation_hook( __FILE__, 'deactive_tealium' );
	add_action( 'admin_init', 'admin_init_tealium' );
	add_action( 'admin_menu', 'admin_menu_tealium' );
	add_action( 'admin_notices', 'admin_notices_tealium' );
}

// Insert the Tealium tag
add_action( 'init', 'insertTealiumTag' );

// Insert the data object
if ( get_option( 'tealiumTagLocation' ) != '3' ) {
	add_action( 'wp_head', 'encodedDataObject', 2 );
}

?>