<?php
// Create a dropdown field
function selectList( $id, $options, $multiple = false ) {
	$opt    = get_option( $id );
	$output = '<select class="select" name="' . $id . '" id="' . $id . '">';
	foreach ( $options as $val => $name ) {
		$sel = '';
		if ( $opt == $val )
			$sel = ' selected="selected"';
		if ( $name == '' )
			$name = $val;
		$output .= '<option value="' . $val . '"' . $sel . '>' . $name . '</option>';
	}
	$output .= '</select>';
	return $output;
}

function generateBulkDataSourceList() {
	$output = '';

	$UDOString = 'UDO Variable';

	// Array of basic data sources
	$basicLayer = array(
		"siteName" => "Contains the site's name",
		"siteDescription" => "Contains the site's description",
		"postCategory" => "Contains the post's category, e.g. 'technology'",
		"postTags" => "Contains the post tags, e.g. 'tag management'",
		"pageType" => "Contains the page type, e.g. 'archive', 'homepage', or 'search'",
		"postTitle" => "Contains the post's title",
		"postAuthor" => "Contains the post author",
		"postDate" => "Contains the post date",
		"searchQuery" => "Contains the search query conducted by user",
		"searchResults" => "Contains the number of search results returned"
	);

	if ( get_option( 'tealiumDataStyle' ) == '1' ) {
		// Convert camel case to underscore
		$basicLayer = apply_filters( 'tealium_convertCamelCase', $basicLayer );
	}

	// Remove excluded keys
	$basicLayer = apply_filters( 'tealium_removeExclusions', $basicLayer );

	if ( $basicLayer ) {
		foreach ( $basicLayer as $key => $value ) {
			$output .= $key . ', "'. $UDOString .'", "'. $value .'"&#13;&#10;';
		}
	}

	global $wpdb;
	$metaKeys = $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} ORDER BY meta_id DESC" );

	$bulkString = ', "'. $UDOString .'", "Imported from Wordpress"&#13;&#10;';
	
	if ( $metaKeys ) {
		foreach ( $metaKeys as $metaKey ) {

			// Exclude meta keys with invalid characters
			if ( !preg_match( '/[^a-zA-Z0-9_$.]/', $metaKey->meta_key ) ) {
				$output .= $metaKey->meta_key . $bulkString;
			}
		}	
	}

	return $output;
}

?>

<div class="wrap">
	<div class="tealium-icon">
		<h2><?php _e( 'Tealium Settings', 'tealium' ); ?></h2>
	</div>

	<?php 
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic_settings'; 
	?>

	<h2 class="nav-tab-wrapper">
    	<a href="?page=tealium&tab=basic_settings" class="nav-tab <?php echo $active_tab == 'basic_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Basic Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=advanced_settings" class="nav-tab <?php echo $active_tab == 'advanced_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Advanced Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=data_export" class="nav-tab <?php echo $active_tab == 'data_export' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Data Source Bulk Export', 'tealium' ); ?></a>
	</h2>

	<?php
	if( $active_tab == 'basic_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagBasic' ); ?>
			
			<p>
				<p>
					<?php _e( 'Account:', 'tealium' ); ?>
					<br />
					<input name='tealiumAccount' size='30' type='text' value='<?php echo get_option( 'tealiumAccount' ); ?>' />
					<br />
					<small><?php _e( 'For example: <i>companyname</i>', 'tealium' ); ?></small>
				</p>
				<p>
					<?php _e( 'Profile:', 'tealium' ); ?>
					<br />
					<input name='tealiumProfile' size='30' type='text' value='<?php echo get_option( 'tealiumProfile' ); ?>' />
					<br />
					<small><?php _e( 'For example: <i>main</i>', 'tealium' ); ?></small>
				</p>
				<p>
					<?php _e( 'Environment:', 'tealium' ); ?>
					<br />
					<input name='tealiumEnvironment' size='30' type='text' value='<?php echo get_option( 'tealiumEnvironment' ); ?>' />
					<br />
					<small><?php _e( 'For example: <i>prod</i>', 'tealium' ); ?></small>
				</p>
			</p>
			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
		<?php
	}
	else if( $active_tab == 'advanced_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagAdvanced' ); ?>
			<p>
				<?php _e( 'Tag location:', 'tealium' ); ?>
				<br />
				<?php
				$options = array();
				$options[] = __( 'After opening body tag (recommended)', 'tealium' );
				$options[] = __( 'Header - Before closing head tag', 'tealium' );
				$options[] = __( 'Footer - Before closing body tag', 'tealium' );
				$options[] = __( 'Immediately after opening head tag', 'tealium' );
				echo selectList( 'tealiumTagLocation', $options );
				?>
			</p>
			<p>
				<?php _e( 'Tag type:', 'tealium' ); ?>
				<br />
				<?php
				$options = array();
				$options[] = __( 'Asynchronous (recommended)', 'tealium' );
				$options[] = __( 'Synchronous', 'tealium' );
				echo selectList( 'tealiumTagType', $options );
				?>
			</p>
			<p>
				<?php _e( 'Data layer style:', 'tealium' ); ?>
				<br />
				<?php
				$options = array();
				$options[] = __( 'CamelCase (legacy)', 'tealium' );
				$options[] = __( 'Underscore (recommended)', 'tealium' );
				echo selectList( 'tealiumDataStyle', $options );
				?>
				<br />
				<small><?php _e( 'For example CamelCase = <i>postDate, siteName</i>. Underscore = <i>post_date, site_name</i>.', 'tealium' ); ?></small>
			</p>
			<p>
				<?php _e( 'Keys to exclude from data object:', 'tealium' ); ?>
				<br />
				<input name='tealiumExclusions' size='50' type='text' value='<?php echo get_option( 'tealiumExclusions' ); ?>' />
				<br />
				<small><?php _e( 'Comma separated list - <i>postDate, custom_field_1</i>.', 'tealium' ); ?></small>
			</p>
			<p>
				<label for="tealiumUtagSync">
					<input type="checkbox" name="tealiumUtagSync" id="tealiumUtagSync" value="1"<?php checked( 1 == get_option( 'tealiumUtagSync' ) ); ?> />
					<?php _e( 'Add utag.sync.js tag', 'tealium' ); ?>
				</label>
			</p>
			<p>
				<label for="tealiumCacheBuster">
					<input type="checkbox" name="tealiumCacheBuster" id="tealiumCacheBuster" value="1"<?php checked( 1 == get_option( 'tealiumCacheBuster' ) ); ?> />
					<?php _e( 'Add cache buster for admin users', 'tealium' ); ?>
				</label>
			</p>
			<p>
				<br />
				<?php _e( 'Advanced tag code:', 'tealium' ); ?>
				<br />
				<textarea name="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>
				<br />
				<small><?php _e( 'Optional: Tealium tag code pasted above will be used instead of any account/profile/environment values entered under Basic Settings.', 'tealium' ); ?></small>
			</p>
			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
	<?php
	}
	else {
		?>
		<p>
			<?php _e( 'Bulk export of basic data sources and all valid custom fields. Copy and paste into the \'Bulk Import from CSV\' option under Data Layer in Tealium IQ.', 'tealium' ); ?>
			<p>
				<textarea readonly="readonly" name="csvExport" rows="20" cols="90"><?php echo generateBulkDataSourceList() ?></textarea>
			</p>
		</p>
		<?php
	}
	?>
</div>