<?php
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: Adds the Tealium tag and creates a data object from post data.
Version: 1.2
Author: Ian Hampton - Tealium EMEA
Author URI: http://tealium.com
*/

function activate_tealium() {
	add_option( 'tealiumTag', '' );
	add_option( 'tealiumTagCode', '' );
	add_option( 'tealiumExclusions', '' );
}

function deactive_tealium() {
	delete_option( 'tealiumTagCode' );
	delete_option( 'tealiumExclusions' );
	delete_option( 'tealiumTag' );
}

function admin_init_tealium() {
	register_setting( 'tealiumTag', 'tealiumTagCode' );
	register_setting( 'tealiumTag', 'tealiumExclusions' );
}

function admin_menu_tealium() {
	add_options_page( 'Tealium Tag Settings', 'Tealium Tag Settings', 'manage_options' , 'tealium', 'options_page_tealium' );
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
		$html .= 'Please enter your Tealium tag code <a href="admin.php?page=tealium">over here</a>.';
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

function outputFilter( $template ) {
	ob_start();
	return $template;
}

function tealiumTag() {
	$tealiumTagCode = get_option( 'tealiumTagCode' );
	if ( !empty( $tealiumTagCode ) ) {
		$content = ob_get_clean();

		// Insert Tealium tag after body tag (sadly there is no wp_body hook)
		$content = preg_replace( '#<body([^>]*)>#i', "<body$1>\n\n\t{$tealiumTagCode}", $content );
		echo $content;
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

if ( is_admin() ) {
	register_activation_hook( __FILE__, 'activate_tealium' );
	register_deactivation_hook( __FILE__, 'deactive_tealium' );
	add_action( 'admin_init', 'admin_init_tealium' );
	add_action( 'admin_menu', 'admin_menu_tealium' );
	add_action( 'admin_notices', 'admin_notices_tealium' );
}

add_action( 'wp_head', 'dataObject' );
add_filter( 'template_include', 'outputFilter', 1 );
add_filter( 'shutdown', 'tealiumTag', 0 );

?>