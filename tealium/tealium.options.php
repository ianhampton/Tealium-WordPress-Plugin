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
?>

<div class="wrap">
	<h2><?php _e( 'Tealium Settings', 'tealium' ) ?></h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field( 'update-options' ); ?>
		<?php settings_fields( 'tealiumTag' ); ?>

		<p>
			<?php _e( 'Paste your Tealium tag code below to add it to your site:', 'tealium' ) ?>
			<br />
			<textarea name="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ) ?></textarea>
		</p>

		<h3><?php _e( 'Advanced Settings', 'tealium' ) ?></h3>
		<p>
			<?php _e( 'Tealium tag location:', 'tealium' ) ?>
			<br />
			<?php
			$options = [ __( 'After opening body tag (recommended)', 'tealium' ), __( 'Header - Before closing head tag', 'tealium' ), __( 'Footer - Before closing body tag', 'tealium' ) ];
			echo select( 'tealiumTagLocation', $options );
			?>
		</p>
		<p>
			<?php _e( 'Keys to exclude from data object:', 'tealium' ) ?>
			<br />
			<input name='tealiumExclusions' size='50' type='text' value='<?php echo get_option( 'tealiumExclusions' ) ?>' />
			<br />
			<small><?php _e( 'Comma separated list - <i>postDate, custom_field_1</i>', 'tealium' ) ?></small>
		</p>

		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tealium' ) ?>" /></p>

	</form>
</div>