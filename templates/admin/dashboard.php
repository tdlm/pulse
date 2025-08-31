<?php
/**
 * Dashboard template.
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
	[]
);
?>
<div class="wrap">
	<h1><span class="wp-pulse-logo"></span><?php esc_html_e( 'Pulse', 'pulse' ); ?></h1>
	<div id="pulse-dashboard-container"></div>
</div>
