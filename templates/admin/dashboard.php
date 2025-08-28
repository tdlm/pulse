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
	<h1>Pulse</h1>
	<div id="pulse-dashboard-container"></div>
</div>
