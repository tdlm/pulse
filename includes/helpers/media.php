<?php
/**
 * Media functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Media
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Media;

/**
 * Enqueue script.
 *
 * @since 1.0.0
 *
 * @param string $handle       Script handle.
 * @param string $src          Script source.
 * @param array  $dependencies Script dependencies.
 * @param string $version      Script version.
 * @param bool   $in_footer    Whether to enqueue in footer.
 */
function enqueue_script(
	string $handle,
	string $src = '',
	array $dependencies = [],
	$version = false,
	bool $in_footer = true
) {
		$localizes = [];

	switch ( $handle ) {
		case 'pulse/runtime':
			$localizes[] = [
				'object_name' => 'Pulse',
				'value'       => [],
			];
			break;
	}

	wp_enqueue_script( $handle, $src, $dependencies, $version, $in_footer );

	if ( 0 < count( $localizes ) ) {
		foreach ( $localizes as $localize ) {
			$object_name  = $localize['object_name'] ?? '';
			$local_params = true === isset( $localize['value'] ) && true === is_array( $localize['value'] ) ?
				$localize['value'] :
				[];

			wp_localize_script(
				$handle,
				$object_name,
				$local_params
			);
		}
	}
}

/**
 * Enqueue style.
 *
 * @since 1.1.0
 *
 * @param string           $handle       Style handle.
 * @param string           $src          Style source.
 * @param string[]         $dependencies Style dependencies.
 * @param string|bool|null $version      Style version.
 * @param string           $media        Style media.
 *
 * @return void
 */
function enqueue_style(
	string $handle,
	string $src = '',
	array $dependencies = [],
	$version = false,
	string $media = 'all'
) {
	wp_enqueue_style( $handle, $src, $dependencies, $version, $media );
}
