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
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'posts';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'deleted_post',
		'transition_post_status',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'post-deleted'             => __( 'Deleted', 'pulse' ),
			'post-draft-saved'         => __( 'Draft Saved', 'pulse' ),
			'post-drafted'             => __( 'Drafted', 'pulse' ),
			'post-future-published'    => __( 'Future Published', 'pulse' ),
			'post-future'              => __( 'Future', 'pulse' ),
			'post-pending'             => __( 'Pending', 'pulse' ),
			'post-privately-published' => __( 'Privately Published', 'pulse' ),
			'post-published'           => __( 'Published', 'pulse' ),
			'post-trashed'             => __( 'Trashed', 'pulse' ),
			'post-unpublished'         => __( 'Unpublished', 'pulse' ),
			'post-untrashed'           => __( 'Untrashed', 'pulse' ),
			'post-updated'             => __( 'Updated', 'pulse' ),
			'posts'                    => __( 'Posts', 'pulse' ),
			'post'                     => __( 'Post', 'pulse' ),
			'wp_global_styles'         => __( 'Global Styles', 'pulse' ),
			'wp_navigation'            => __( 'Navigation', 'pulse' ),
			'wp_template'              => __( 'Template', 'pulse' ),
		];
	}

	/**
	 * Get links.
	 *
	 * @param object $record The pulse record.
	 *
	 * @return array The links.
	 */
	public static function get_links( $record ) {
		$links = [];

		if ( false === isset( $record->object_id ) ) {
			return $links;
		}

		$edit_media_link = get_edit_post_link( $record->object_id );

		if ( false === empty( $edit_media_link ) ) {
			$links[ __( 'Edit', 'pulse' ) ] = html_entity_decode( $edit_media_link, ENT_QUOTES | ENT_HTML5 );
		}

		$permalink = get_permalink( $record->object_id );

		if ( false === empty( $permalink ) ) {
			$links[ __( 'View', 'pulse' ) ] = html_entity_decode( $permalink, ENT_QUOTES | ENT_HTML5 );
		}

		return $links;
	}

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
			'post-deleted',
			sprintf(
				/* translators: %1$s: Post type label. %2$s: Post title. */
				__( '%1$s "%2$s" deleted from the trash.', 'pulse' ),
				$post_details['post_type_label'],
				$post_details['post_title']
			),
			$this->pulse_slug,
			$post_details['post_type'],
			null,
			$post_id,
			$post_details
		);
	}

	/**
	 * Transition post status callback.
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 */
	public function callback_transition_post_status( $new_status, $old_status, $post ) {

		$meta_box_loader = filter_input( INPUT_GET, 'meta-box-loader', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// Don't log anything from the meta box loader.
		if ( false === empty( $meta_box_loader ) ) {
			return;
		}

		// If the post type is excluded, we don't care about it.
		if ( true === in_array( $post->post_type, $this->get_excluded_post_types(), true ) ) {
			return;
		}

		// If the new status is excluded, we don't care about it.
		if ( true === in_array( $new_status, $this->get_excluded_starting_post_statuses(), true ) ) {
			return;
		}

		// Don't log anything from autosaves.
		if ( true === defined( 'DOING_AUTOSAVE' ) && true === DOING_AUTOSAVE ) {
			return;
		}

		$current_user = wp_get_current_user();

		$action  = '';
		$message = '';

		if ( 'draft' === $new_status && 'publish' === $old_status ) {
			$action = 'post-unpublished';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" unpublished.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'trash' === $old_status && 'trash' !== $new_status ) {
			$action = 'post-untrashed';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" untrashed.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'draft' === $old_status && 'draft' === $new_status ) {
			$action = 'post-draft-saved';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" draft saved.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'publish' === $new_status && false === in_array( $old_status, [ 'future', 'publish' ], true ) ) {
			$action = 'post-published';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" published.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'draft' === $new_status ) {
			$action = 'post-drafted';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" drafted.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'pending' === $new_status ) {
			$action = 'post-pending';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" pending review.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'future' === $new_status ) {
			$action = 'post-future';

			$message = sprintf(
				/* translators: %s: Post title. %s: Post date. */
				__( 'Post "%1$s" scheduled for %2$s.', 'pulse' ),
				$post->post_title,
				$post->post_date
			);
		} elseif ( 'future' === $old_status && 'publish' === $new_status ) {
			$action = 'post-future-published';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%1$s" published.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'private' === $new_status ) {
			$action = 'post-privately-published';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" privately published.', 'pulse' ),
				$post->post_title
			);
		} elseif ( 'trash' === $new_status ) {
			$action = 'post-trashed';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" trashed.', 'pulse' ),
				$post->post_title
			);
		} else {
			$action = 'post-updated';

			$message = sprintf(
				/* translators: %s: Post title. */
				__( 'Post "%s" updated.', 'pulse' ),
				$post->post_title
			);
		}

		// If we don't have an action or message, we don't have anything to log.
		if ( true === empty( $action ) || true === empty( $message ) ) {
			return;
		}

		// For WordPress 5.6+.
		if ( true === function_exists( 'wp_get_latest_revision_id_and_total_count' ) ) {
			$revision_data = wp_get_latest_revision_id_and_total_count( $post->ID );

			if ( true === is_wp_error( $revision_data ) ) {
				$revision_id = null;
			} else {
				$revision_id = $revision_data['latest_id'];
			}
		} else {
			// For older versions of WordPress.
			$revisions       = wp_get_post_revisions(
				$post->ID,
				[
					'order'          => 'DESC',
					'posts_per_page' => 1,
				]
			);
			$latest_revision = array_shift( $revisions );
			$revision_id     = true === property_exists( $latest_revision, 'ID' ) ? $latest_revision->ID : null;
		}

		// Build out log details.
		$details = compact( 'old_status' );

		if ( false === empty( $revision_id ) ) {
			$details['revision_id'] = $revision_id;
		}

		$details = array_merge( $details, $this->get_post_details( $post->ID ) );
		$details = array_merge( $details, \WP_Pulse\Helpers\Users\get_user_details( $current_user->ID ) );

		Log::log(
			$action,
			$message,
			$this->pulse_slug,
			$post->post_type,
			$current_user->ID,
			$post->ID,
			$details
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
	 * Get excluded starting post statuses.
	 *
	 * @return array Post statuses to exclude.
	 */
	public function get_excluded_starting_post_statuses() {
		return apply_filters(
			'wp_pulse_posts_excluded_starting_post_statuses',
			[
				'auto-draft',
				'inherit',
				'new',
			]
		);
	}

	/**
	 * Get post details.
	 *
	 * @param int $post_id Post ID.
	 * @return array Post details.
	 */
	private function get_post_details( $post_id ) {
		$post = get_post( $post_id );

		if ( false === $post instanceof \WP_Post ) {
			return [];
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$post_type_label  = true === $post_type_object instanceof \WP_Post_Type ? $post_type_object->labels->singular_name : ucfirst( $post->post_type );

		return [
			'post_author'       => $post->post_author,
			'post_date_gmt'     => $post->post_date_gmt,
			'post_date'         => $post->post_date,
			'post_id'           => $post->ID,
			'post_modified'     => $post->post_modified,
			'post_modified_gmt' => $post->post_modified_gmt,
			'post_parent'       => $post->post_parent,
			'post_status'       => $post->post_status,
			'post_title'        => $post->post_title ? $post->post_title : __( '(no title)', 'pulse' ),
			'post_type_label'   => $post_type_label,
			'post_type'         => $post->post_type,
		];
	}
}
