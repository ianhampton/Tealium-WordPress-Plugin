<?php
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: Adds the Tealium tag and creates a data layer for your Wordpress site.
Version: 1.4
Author: Ian Hampton - Tealium EMEA
Author URI: http://tealium.com
Text Domain: tealium
*/

function activate_tealium() {
	add_option( 'tealiumTag', '' );
	add_option( 'tealiumTagCode', '' );
	add_option( 'tealiumTagLocation', '' );
	add_option( 'tealiumExclusions', '' );
}

function deactive_tealium() {
	delete_option( 'tealiumExclusions' );
	delete_option( 'tealiumTagLocation' );
	delete_option( 'tealiumTagCode' );
	delete_option( 'tealiumTag' );
}

function admin_init_tealium() {
	register_setting( 'tealiumTag', 'tealiumTagCode' );
	register_setting( 'tealiumTag', 'tealiumTagLocation' );
	register_setting( 'tealiumTag', 'tealiumExclusions' );
}

function admin_menu_tealium() {
	add_options_page( __( 'Tealium Tag Settings', 'tealium' ), __( 'Tealium Settings', 'tealium' ), 'manage_options' , 'tealium', 'options_page_tealium' );
}

function options_page_tealium() {
	include plugin_dir_path( __FILE__ ).'tealium.options.php';
}

function admin_notices_tealium() {
	global $pagenow;
	$tealiumTagCode = get_option( 'tealiumTagCode' );

	if ( ( $pagenow == 'plugins.php' ) && ( empty( $tealiumTagCode ) ) ) {
		$html = '<div class="updated">';
		$html .= '<p>';
		$html .= sprintf( __( 'Please enter your Tealium tag code <a href="%s">over here</a>.', 'tealium' ), esc_url( 'options-general.php?page=tealium' ) );
		$html .= '</p>';
		$html .= '</div>';
		echo $html;
	}
}

function dataObject() {
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
			$utagdata['pageType'] = "search";
			$utagdata['searchQuery'] = get_search_query();
		}

	// Remove excluded keys
	$utagdata = removeExclusions( $utagdata );

	// Check if WooCommerce is installed
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
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
		unset( $woocart['tax'] );

		// Get currency in use
		$woocart['site_currency'] = get_woocommerce_currency();

		// Merge shop and cart details into utagdata
		$utagdata = array_merge( $utagdata, $woocart );
		$utagdata = array_merge( $utagdata, $productData );
	}

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
		echo "<script type=\"text/javascript\">\nvar utag_data = {$jsondata};\n</script>\n";
	}
}

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

function getTealiumTagCode() {
	echo get_option( 'tealiumTagCode' );
}

function outputFilter( $template ) {
	ob_start();
	return $template;
}

function tealiumTag( $tealiumTagCode ) {
	$content = ob_get_clean();
	$tealiumTagCode = get_option( 'tealiumTagCode' );

	// Insert Tealium tag after body tag (sadly there is no wp_body hook)
	$content = preg_replace( '#<body([^>]*)>#i', "<body$1>\n\n\t{$tealiumTagCode}", $content );
	echo $content;
}


$tealiumTagLocation = get_option( 'tealiumTagLocation' );
$tealiumTagCode = get_option( 'tealiumTagCode' );

if ( !empty( $tealiumTagCode ) ) {
	switch ( $tealiumTagLocation ) {
	case '1':
		// Location - Header
		add_action( 'wp_head', 'getTealiumTagCode', 10000 );
		break;
	case '2':
		// Location - Footer
		add_action( 'wp_footer', 'getTealiumTagCode', 10000 );
		break;
	case '0':
	default:
		// Location - After opening body tag
		// Start content filter
		add_filter( 'template_include', 'outputFilter', 1 );
		// Inject Tealium tag, output page contents
		add_filter( 'shutdown', 'tealiumTag', 0 );
		break;
	}
}

if ( is_admin() ) {
	register_activation_hook( __FILE__, 'activate_tealium' );
	register_deactivation_hook( __FILE__, 'deactive_tealium' );
	add_action( 'admin_init', 'admin_init_tealium' );
	add_action( 'admin_menu', 'admin_menu_tealium' );
	add_action( 'admin_notices', 'admin_notices_tealium' );
}

// Insert the data object
add_action( 'wp_head', 'dataObject', 0 );

?>