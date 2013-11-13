<?php
/*
Plugin Name: Tealium
Plugin URI: http://tealium.com
Description: Adds the Tealium tag and creates a data layer from post data.
Version: 0.1
Author: Ian Hampton
Author URI: http://ianhampton.net
*/

function activate_tealium() {
	add_option('tealiumTag', '');
	add_option('tealiumTagCode', '');
}

function deactive_tealium() {
	delete_option('tealiumTagCode');
	delete_option('tealiumTag');
}

function admin_init_tealium() {
	register_setting('tealiumTag', 'tealiumTagCode');
}

function admin_menu_tealium() {
	add_options_page('Tealium Tag Settings', 'Tealium Tag Settings', 8, 'tealium', 'options_page_tealium');
}

function options_page_tealium() {
	include(plugin_dir_path(__FILE__).'tealium.options.php');	 
}

function insert_tealium($tealiumTagCode) {
	echo $tealiumTagCode;
} 

function dataLayer() { 
	$utagdata = array();
	
	// Blog info
	$utagdata['siteName'] = get_bloginfo('name');
	$utagdata['siteDescription'] = get_bloginfo('description');
	
	if ((is_single()) || is_page()) {
		// Get categories
		$categories = get_the_category();
		$catout = array();
		if($categories) {
			foreach($categories as $category) {
				$catout[] = $category->slug;
			}
			$utagdata['postCategory'] = $catout;
		}
		
		// Get tags
		$tags = get_the_tags();
		$tagout = array();
		if($tags) {
			foreach($tags as $tag) {
				$tagout[] = $tag->slug;
			}
			$utagdata['postTags'] = $tagout;
		}
		
		// Get word count
		$utagdata['wordCount'] = wordCount();
		
		// Misc post/page data
		$utagdata['pageType'] = get_post_type();
		$utagdata['postTitle'] = get_the_title();
		$utagdata['postAuthor'] = get_the_author();
		$utagdata['postDate'] = get_the_time('Y/m/d');
		
		// Get and merge post meta data
		$meta = get_post_meta( get_the_ID() );
		if ($meta) {
			$utagdata = array_merge($utagdata, $meta);
		}
	}
	else if (is_archive()) {
		$utagdata['pageType'] = "archive";
	}
	else if ((is_home()) || (is_front_page())) {
		$utagdata['pageType'] = "homepage";
	}
	else if (is_search()) {
		$utagdata['pageType'] = "search";
		$utagdata['searchQuery'] = get_search_query();
	}
	
	$jsondata = json_encode($utagdata);
	
	// Output data layer
	if (json_decode($jsondata) !== null) {
		echo "<script type=\"text/javascript\"> 
				var utag_data = {$jsondata}; 
				</script>";
	}
}

function tealiumTag() {
	$tealiumTagCode = get_option('tealiumTagCode');
	if (!empty($tealiumTagCode)) {
		insert_tealium($tealiumTagCode);
	}
			
}

function wordCount() {
	ob_start();
	the_content();
	$content = ob_get_clean();
	return sizeof(explode(" ", $content));
}	 

if (is_admin()) {
	register_activation_hook(__FILE__, 'activate_tealium');
	register_deactivation_hook(__FILE__, 'deactive_tealium');
	add_action('admin_init', 'admin_init_tealium');
	add_action('admin_menu', 'admin_menu_tealium');
}

add_action('wp_head', 'dataLayer');
add_action('wp_footer', 'tealiumTag');
?>
