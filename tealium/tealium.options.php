<div class="wrap">
	<h2>Tealium Tag Settings</h2>

	<form method="post" action="options.php">
		<?php wp_nonce_field( 'update-options' ); ?>
		<?php settings_fields( 'tealiumTag' ); ?>
		
		<p>
			Paste your Tealium tag code below to add it to your site:
			<br />
			<textarea name="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>
		</p>
		<p>
			Keys to exclude from data object:
			<br />
			<input name='tealiumExclusions' size='50' type='text' value='<?php echo get_option( 'tealiumExclusions' ); ?>' />
			<br />
			<small>Comma separated list - <i>postDate, custom_field_1</i></small>
		</p>
		
		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /></p>

	</form>
</div>