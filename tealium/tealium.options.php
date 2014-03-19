<?php
// Create a dropdown field
function select( $id, $options, $multiple = false ) {
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
	global $wpdb;
	$metaKeys = $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM {$wpdb->postmeta} ORDER BY meta_id DESC" );
	
	$string = ', "Data Layer", "Imported from Wordpress"&#13;&#10;';
	if ( $metaKeys ) {
		$output = '';
		foreach ( $metaKeys as $metaKey ) {
			$output .= $metaKey->meta_key . $string;
		}	
	}
	
	$output .= 'siteName'. $string;
	$output .= 'siteDescription'. $string;
	$output .= 'pageType'. $string;
	return $output;
}

?>

<div class="wrap">
	<h2><?php _e( 'Tealium Settings', 'tealium' ); ?></h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field( 'update-options' ); ?>
		<?php settings_fields( 'tealiumTag' ); ?>

		<p>
			<?php _e( 'Paste your Tealium tag code below to add it to your site:', 'tealium' ); ?>
			<br />
			<textarea name="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>
		</p>

		<h3><?php _e( 'Advanced Settings', 'tealium' ); ?></h3>
		<p>
			<?php _e( 'Tealium tag location:', 'tealium' ); ?>
			<br />
			<?php
			$options = array();
			$options[] = __( 'After opening body tag (recommended)', 'tealium' );
			$options[] = __( 'Header - Before closing head tag', 'tealium' );
			$options[] = __( 'Footer - Before closing body tag', 'tealium' );
			$options[] = __( 'Immediately after opening head tag', 'tealium' );
			echo select( 'tealiumTagLocation', $options );
			?>
		</p>
		<p>
			<?php _e( 'Data layer style:', 'tealium' ); ?>
			<br />
			<?php
			$options = array();
			$options[] = __( 'CamelCase (legacy)', 'tealium' );
			$options[] = __( 'Underscore (recommended)', 'tealium' );
			echo select( 'tealiumDataStyle', $options );
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

		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ); ?>" /></p>

	</form>
	
	<!-- Coming soon! -->
	<!--<h2><?php _e( 'Data Source Bulk Export', 'tealium' ); ?></h2>
	<textarea name="csvExport" rows="10" cols="100"><?php echo generateBulkDataSourceList() ?></textarea>-->
</div>