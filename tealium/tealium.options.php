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

// Create a friendly alias from UDO parameters
function formatAsName( $key ) {
	// '_product_photo' becomes 'Product Photo'
	$key = ucwords( trim( str_replace( '_', ' ', $key ) ) );

	// Handle camelCase
	$key = join( preg_split( '/(^[^A-Z]+|[A-Z][^A-Z]+)/', $key, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ), ' ');

	// Remove multiple spaces etc.
	$key = preg_replace( '/(\s\s+|\t|\n)/', ' ', $key );
	
	return $key;
}

// Create an exhaustive list of possible data sources
function generateBulkDataSourceList() {
	$output = '';

	$UDOString = 'UDO Variable';
	$bulkString = "Imported from Wordpress";

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

	// Get all meta keys from WP DB
	if ( "1" !== get_option( 'tealiumExcludeMetaData' ) ) {
		global $wpdb;
		$metaKeys = $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} ORDER BY meta_key ASC" );

		$metaLayer = array();

		if ( $metaKeys ) {
			foreach ( $metaKeys as $metaKey ) {
				// Exclude meta keys with invalid characters
				if ( !preg_match( '/[^a-zA-Z0-9_$.]/', $metaKey->meta_key ) ) {
					$metaLayer[$metaKey->meta_key] = $bulkString;
				}
			}
		}

		$dataLayer = array_merge( $basicLayer, $metaLayer );
	}
	else {
		$dataLayer = $basicLayer;
	}

	// Remove excluded keys
	$dataLayer = apply_filters( 'tealium_removeExclusions', $dataLayer );

	if ( $dataLayer ) {
		foreach ( $dataLayer as $key => $value ) {
			$output .= $key . ', "'. $UDOString .'", "'. $value .'", "'. formatAsName( $key ) .'"&#13;&#10;';
		}
	}

	return $output;
}
?>

<div class="wrap">
	<div class="tealium-icon">
		<h2><?php _e( 'Tealium Settings', 'tealium' ); ?></h2>
	</div>

	<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic_settings'; ?>

	<h2 class="nav-tab-wrapper">
    	<a href="?page=tealium&tab=basic_settings" class="nav-tab <?php echo $active_tab == 'basic_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Basic Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=advanced_settings" class="nav-tab <?php echo $active_tab == 'advanced_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Advanced Settings', 'tealium' ); ?></a>
    	<a href="?page=tealium&tab=data_export" class="nav-tab <?php echo $active_tab == 'data_export' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Data Source Bulk Export', 'tealium' ); ?></a>
	</h2>

	<?php
	if ( $active_tab == 'basic_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagBasic' ); ?>

			<table class="form-table basic">
				<tr>
					<th scope="row"><label for="tealiumAccount"><?php _e( 'Account', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumAccount' id='tealiumAccount' size='30' type='text' value='<?php echo get_option( 'tealiumAccount' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>companyname</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumProfile"><?php _e( 'Profile', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumProfile' id='tealiumProfile' size='30' type='text' value='<?php echo get_option( 'tealiumProfile' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>main</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumEnvironment"><?php _e( 'Environment', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumEnvironment' id='tealiumEnvironment' size='30' type='text' value='<?php echo get_option( 'tealiumEnvironment' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'For example: <code>prod</code>', 'tealium' ); ?></p>
					</td>
				</tr>
			</table>

			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
		<?php
	}
	else if ( $active_tab == 'advanced_settings' ) {
		?>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<?php settings_fields( 'tealiumTagAdvanced' ); ?>

			<table class="form-table advanced">
				<tr>
					<th scope="row"><label for="tealiumTagLocation"><?php _e( 'Tag location', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'After opening body tag (recommended)', 'tealium' );
						$options[] = __( 'Header - Before closing head tag', 'tealium' );
						$options[] = __( 'Footer - Before closing body tag', 'tealium' );
						$options[] = __( 'Immediately after opening head tag', 'tealium' );
						echo selectList( 'tealiumTagLocation', $options );
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumTagType"><?php _e( 'Tag type', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'Asynchronous (recommended)', 'tealium' );
						$options[] = __( 'Synchronous', 'tealium' );
						echo selectList( 'tealiumTagType', $options );
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumDataStyle"><?php _e( 'Data layer style', 'tealium' ); ?></label></th>
					<td>
						<?php
						$options = array();
						$options[] = __( 'CamelCase (legacy)', 'tealium' );
						$options[] = __( 'Underscore (recommended)', 'tealium' );
						echo selectList( 'tealiumDataStyle', $options );
						?>
						<p class="description"><?php _e( 'CamelCase = <code>postDate, siteName</code> Underscore = <code>post_date, site_name</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tealiumExclusions"><?php _e( 'Data layer exclusions', 'tealium' ); ?></label></th>
					<td>
						<input name='tealiumExclusions' id='tealiumExclusions' size='50' type='text' value='<?php echo get_option( 'tealiumExclusions' ); ?>' class='regular-text' />
						<p class="description"><?php _e( 'Comma separated list, e.g. <code>postDate, custom_field_1</code>', 'tealium' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude meta data', 'tealium' ); ?></th>
					<td>
						<label for="tealiumExcludeMetaData">
							<input type="checkbox" name="tealiumExcludeMetaData" id="tealiumExcludeMetaData" value="1"<?php checked( 1 == get_option( 'tealiumExcludeMetaData' ) ); ?> />
							<?php _e( 'Remove ALL Wordpress meta data from data layer', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Synchronous file', 'tealium' ); ?></th>
					<td>
						<label for="tealiumUtagSync">
							<input type="checkbox" name="tealiumUtagSync" id="tealiumUtagSync" value="1"<?php checked( 1 == get_option( 'tealiumUtagSync' ) ); ?> />
							<?php _e( 'This profile uses a utag.sync.js file', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Cache buster', 'tealium' ); ?></th>
					<td>
						<label for="tealiumCacheBuster">
							<input type="checkbox" name="tealiumCacheBuster" id="tealiumCacheBuster" value="1"<?php checked( 1 == get_option( 'tealiumCacheBuster' ) ); ?> />
							<?php _e( 'Add a cache buster for content editors', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'DNS prefetching', 'tealium' ); ?></th>
					<td>
						<label for="tealiumDNSPrefetch">
							<input type="checkbox" name="tealiumDNSPrefetch" id="tealiumDNSPrefetch" value="1"<?php checked( 1 == get_option( 'tealiumDNSPrefetch' ) ); ?> />
							<?php _e( 'Enable DNS Prefetching', 'tealium' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'EU only', 'tealium' ); ?></th>
					<td>
						<label for="tealiumEUOnly">
							<input type="checkbox" name="tealiumEUOnly" id="tealiumEUOnly" value="1"<?php checked( 1 == get_option( 'tealiumEUOnly' ) ); ?> />
							<?php _e( 'Only use EU based CDN nodes', 'tealium' ); ?>
						</label>
					</td>
				</tr>
			</table>

			<h3 class="advanced"><label for="tealiumTagCode"><?php _e( 'Advanced Tag Code', 'tealium' ); ?></label></h3>
			<p class="description"><?php _e( 'Optional: Tealium tag code pasted below will be used instead of any account/profile/environment values entered under Basic Settings.', 'tealium' ); ?></p>
			<textarea name="tealiumTagCode" id="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>

			<input type="hidden" name="action" value="update" />

			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>
		</form>
		<?php
		}
	else {
		?>
		<p>
			<p class="description"><?php _e( 'Bulk export of basic data sources and all valid custom fields. Copy and paste into the \'Bulk Import from CSV\' option under Data Layer in Tealium IQ.', 'tealium' ); ?></p>
			<p><textarea readonly="readonly" name="csvExport" rows="20" cols="90"><?php echo generateBulkDataSourceList() ?></textarea></p>
		</p>
		<?php
	}
	?>
</div>