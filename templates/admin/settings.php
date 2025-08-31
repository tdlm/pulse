<?php
/**
 * Settings template.
 *
 * @package WP_Pulse
 * @subpackage Admin
 * @since 1.0.0
 *
 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

/**
 * Properties.
 *
 * @var array
 */
$props = wp_parse_args(
	$args ?? [],
	[
		'current_tab' => '',
		'tabs'        => [],
	]
);

if ( true === isset( $_GET['settings-updated'] ) ) {
	add_settings_error( 'wp-pulse-messages', 'wp-pulse-message', __( 'Settings Saved', 'wp-pulse' ), 'updated' );
}

settings_errors( 'wp-pulse-messages' );
?>
<div class="wrap">
	<h1><span class="wp-pulse-logo"></span><?php esc_html_e( 'Pulse Settings', 'pulse' ); ?></h1>
	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $props['tabs'] as $tab_key => $tab ) :
			$url = add_query_arg(
				[
					'page' => 'wp-pulse-settings',
					'tab'  => $tab_key,
				],
				admin_url( 'admin.php' )
			);
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="nav-tab <?php echo esc_attr( $props['current_tab'] === $tab_key ? 'nav-tab-active' : '' ); ?>">
				<?php echo esc_html( $tab['title'] ); ?>
			</a>
		<?php endforeach; ?>
	</h2>
	<div id="pulse-settings-container">
		<form action="options.php" method="post">
			<?php
			settings_fields( 'pulse_settings_group' );
			do_settings_sections( "pulse_settings_{$props['current_tab']}" );
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
</div>