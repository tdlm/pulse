<?php
/**
 * Post functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Post
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Post;

/**
 * Get the post type label.
 *
 * @since 1.0.0
 *
 * @param string $post_type The post type.
 *
 * @return string The post type label.
 */
function get_post_type_label( $post_type ) {
	if ( true === post_type_exists( $post_type ) ) {
		$post_type_object = get_post_type_object( $post_type );
		return $post_type_object->labels->singular_name;
	}

	return __( 'Post', 'pulse' );
}
