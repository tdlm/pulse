<?php
/**
 * Keeps a finger on the pulse of post-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Posts
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;

/**
 * Posts class.
 */
class Posts extends Pulse {

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'deleted_post',
	];

	/**
	 * Deleted post callback.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function callback_deleted_post( $post_id ) {
		$post_details = $this->get_post_details( $post_id );

		// If we don't have post details, we don't have anything to log.
		if ( true === empty( $post_details ) ) {
			return;
		}

		// If the post type is excluded, we don't care about it.
		if ( true === in_array( $post_details['post_type'], $this->get_excluded_post_types(), true ) ) {
			return;
		}

		// We don't care about no stinking auto-drafts.
		if ( 'auto-draft' === $post_details['post_status'] ) {
			return;
		}

		Log::log(
			'deleted',
			sprintf(
				/* translators: %1$s: Post type label. %2$s: Post title. */
				__( '%1$s "%2$s" deleted from the trash.', 'pulse' ),
				$post_details['post_type_label'],
				$post_details['post_title']
			),
			'posts',
			$post_details['post_type'],
			null,
			$post_id,
			$post_details
		);
	}

	/**
	 * Get excluded post types.
	 *
	 * @return array Post types to exclude.
	 */
	public function get_excluded_post_types() {
		return apply_filters(
			'wp_pulse_posts_excluded_post_types',
			[
				'attachment',
				'nav_menu_item',
				'revision',
			]
		);
	}

	/**
	 * Get post details.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Optional. Post object.
	 * @return array Post details.
	 */
	private function get_post_details( $post_id, $post = null ) {
		if ( null === $post ) {
			$post = get_post( $post_id );
		}

		if ( false === $post instanceof \WP_Post ) {
			return [];
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$post_type_label  = true === $post_type_object instanceof \WP_Post_Type ? $post_type_object->labels->singular_name : ucfirst( $post->post_type );

		return [
			'post_id'         => $post->ID,
			'post_title'      => $post->post_title ? $post->post_title : __( '(no title)', 'pulse' ),
			'post_type'       => $post->post_type,
			'post_type_label' => $post_type_label,
			'post_status'     => $post->post_status,
			'post_author'     => $post->post_author,
			'post_date'       => $post->post_date,
			'post_modified'   => $post->post_modified,
		];
	}
}
