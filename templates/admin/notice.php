<?php
/**
 * Notice template.
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
		'message' => '',
		'type'    => 'success',
	]
);
?>
<div class="notice notice-<?php echo esc_attr( $props['type'] ); ?> is-dismissible">
	<p><?php echo wp_kses_post( $props['message'] ); ?></p>
</div>