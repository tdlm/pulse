<?php
/**
 * Pulse detail template.
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
	<h1><?php esc_html_e( 'Pulse Detail', 'pulse' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wp-pulse' ) ); ?>" class="button button-link">
		← <?php esc_html_e( 'Back to Dashboard', 'pulse' ); ?>
	</a>
	<div id="pulse-detail-container"></div>
</div>
