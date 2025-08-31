<?php
/**
 * Dashboard screen controls template.
 *
 * @package WP_Pulse
 * @subpackage Admin
 * @since 1.0.0
 */

/**
 * Properties.
 *
 * @var array
 */
$props = wp_parse_args(
	$args ?? [],
	[
		'nonce'     => '',
		'user_id'   => 0,
		'heartbeat' => 120,
		'option'    => 'on',
	]
);
?>
<fieldset>
	<h5><?php esc_html_e( 'Live updates', 'pulse' ); ?></h5>

	<div>
		<input type="hidden" name="pulse_enable_live_update_nonce" id="pulse_enable_live_update_nonce"
			value="<?php echo esc_attr( $props['nonce'] ); ?>" />
	</div>
	<div>
		<input type="hidden" name="pulse_enable_live_update_user" id="pulse_enable_live_update_user"
			value="<?php echo absint( $props['user_id'] ); ?>" />
	</div>
	<div class="metabox-prefs pulse-live-update-checkbox">
		<label for="pulse_enable_live_update">
			<input type="checkbox" value="on" name="pulse_enable_live_update" id="pulse_enable_live_update"
				data-heartbeat="<?php echo esc_attr( $props['heartbeat'] ); ?>" <?php checked( $props['option'], 'on' ); ?> />
			<?php esc_html_e( 'Enabled', 'pulse' ); ?>
			<span class="spinner"></span>
		</label>
	</div>
</fieldset>
