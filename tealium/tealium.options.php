<div class="wrap">
	<h2>Tealium Tag Settings</h2>

	<p>Paste your Tealium tag code below to add it to your site:</p>

	<form method="post" action="options.php">
		<?php wp_nonce_field( 'update-options' ); ?>
		<?php settings_fields( 'tealiumTag' ); ?>

		<textarea name="tealiumTagCode" rows="10" cols="100"><?php echo get_option( 'tealiumTagCode' ); ?></textarea>

		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /></p>

	</form>
</div>
