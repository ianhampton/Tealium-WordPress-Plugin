<?php
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: Adds the Tealium tag and creates a data object from post data.
Version: 0.2
Author: Ian Hampton
Author URI: http://ianhampton.net
*/

function activate_tealium() {
	add_option( 'tealiumTag', '' );
	add_option( 'tealiumTagCode', '' );
}

function deactive_tealium() {
	delete_option( 'tealiumTagCode' );
	delete_option( 'tealiumTag' );
}

function admin_init_tealium() {
	register_setting( 'tealiumTag', 'tealiumTagCode' );
}

function admin_menu_tealium() {
	add_options_page( 'Tealium Tag Settings', 'Tealium Tag Settings', 8, 'tealium', 'options_page_tealium' );
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

	// Encode data object
	$jsondata = json_encode( $utagdata );

	// Output data object
	if ( json_decode( $jsondata ) !== null ) {
		echo "<script type=\"text/javascript\">
				var utag_data = {$jsondata};
			  </script>";
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
