<?php
/**
 * Keeps a finger on the pulse of media-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Media
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Log;
use WP_Pulse\Pulse;

/**
 * Media class.
 */
class Media extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'media';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'add_attachment',
		'edit_attachment',
		'delete_attachment',
		'wp_save_image_editor_file',
		'wp_save_image_file',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'media-added'    => __( 'Added', 'pulse' ),
			'media-attached' => __( 'Attached', 'pulse' ),
			'media-deleted'  => __( 'Deleted', 'pulse' ),
			'media-edited'   => __( 'Edited', 'pulse' ),
			'media-updated'  => __( 'Updated', 'pulse' ),
			'media-uploaded' => __( 'Uploaded', 'pulse' ),
			'media'          => __( 'Media', 'pulse' ),
			'archive'        => __( 'Archive', 'pulse' ),
			'code'           => __( 'Code', 'pulse' ),
			'audio'          => __( 'Audio', 'pulse' ),
			'document'       => __( 'Document', 'pulse' ),
			'image'          => __( 'Image', 'pulse' ),
			'interactive'    => __( 'Interactive', 'pulse' ),
			'spreadsheet'    => __( 'Spreadsheet', 'pulse' ),
			'text'           => __( 'Text', 'pulse' ),
			'video'          => __( 'Video', 'pulse' ),
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
			$links[ __( 'Edit Media', 'pulse' ) ] = html_entity_decode( $edit_media_link, ENT_QUOTES | ENT_HTML5 );
		}

		$permalink = get_permalink( $record->object_id );

		if ( false === empty( $permalink ) ) {
			$links[ __( 'View', 'pulse' ) ] = html_entity_decode( $permalink, ENT_QUOTES | ENT_HTML5 );
		}

		return $links;
	}

	/**
	 * Add attachment callback.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return void
	 */
	public function callback_add_attachment( $post_id ) {
		$current_user = wp_get_current_user();

		$attachment = get_post( $post_id );

		$attachment_type = \WP_Pulse\Helpers\Media\get_attachment_type_by_file_uri( $attachment->guid );

		if ( false === empty( $attachment->post_parent ) ) {
			$parent_post = get_post( $attachment->post_parent );

			$message = sprintf(
				/* translators: %1$s: Attachment title. %2$s: Parent post title. */
				__( 'Attached "%1$s" to post "%2$s".', 'pulse' ),
				$attachment->post_title,
				$parent_post->post_title
			);

			$action = 'media-attached';
		} else {
			$message = sprintf(
				/* translators: %s: Attachment title. */
				__( 'Added "%s" to Media Library', 'pulse' ),
				$attachment->post_title
			);

			$action = 'media-added';
		}

		Log::log(
			$action,
			$message,
			$this->pulse_slug,
			$attachment_type,
			$current_user->ID,
			$post_id,
			$attachment
		);
	}

	/**
	 * Edit attachment callback.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return void
	 */
	public function callback_edit_attachment( $post_id ) {
		$current_user = wp_get_current_user();

		$attachment = get_post( $post_id );

		$attachment_type = \WP_Pulse\Helpers\Media\get_attachment_type_by_file_uri( $attachment->guid );

		Log::log(
			'media-updated',
			sprintf(
				/* translators: %s: Attachment title. */
				__( 'Updated "%s" attachment.', 'pulse' ),
				$attachment->post_title
			),
			$this->pulse_slug,
			$attachment_type,
			$current_user->ID,
			$post_id,
			$attachment
		);
	}

	/**
	 * Delete attachment callback.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return void
	 */
	public function callback_delete_attachment( $post_id ) {
		$current_user = wp_get_current_user();

		$attachment = get_post( $post_id );

		$attachment_type = \WP_Pulse\Helpers\Media\get_attachment_type_by_file_uri( $attachment->guid );

		Log::log(
			'media-deleted',
			sprintf(
				/* translators: %s: Attachment title. */
				__( 'Deleted "%s" attachment.', 'pulse' ),
				$attachment->post_title
			),
			$this->pulse_slug,
			$attachment_type,
			$current_user->ID,
			$post_id,
			$attachment
		);
	}

	/**
	 * Save image editor file callback.
	 *
	 * @param bool   $override Whether to override the file.
	 * @param string $filename The filename.
	 * @param string $image The image.
	 * @param string $mime_type The mime type.
	 * @param int    $post_id The post ID.
	 *
	 * @return void
	 */
	public function callback_wp_save_image_editor_file( $override, $filename, $image, $mime_type, $post_id ) {
		$current_user = wp_get_current_user();

		$attachment = get_post( $post_id );

		$attachment_type = \WP_Pulse\Helpers\Media\get_attachment_type_by_file_uri( $attachment->guid );

		$file_name = basename( $filename );

		Log::log(
			'media-edited',
			sprintf(
				/* translators: %s: Attachment title. */
				__( 'Edited "%s" image.', 'pulse' ),
				$file_name
			),
			$this->pulse_slug,
			$attachment_type,
			$current_user->ID,
			$post_id,
			$attachment
		);
	}

	/**
	 * Save image file callback.
	 *
	 * @param bool   $override Whether to override the file.
	 * @param string $filename The filename.
	 * @param string $image The image.
	 * @param string $mime_type The mime type.
	 * @param int    $post_id The post ID.
	 */
	public function callback_wp_save_image_file( $override, $filename, $image, $mime_type, $post_id ) {
		$this->callback_wp_save_image_editor_file( $override, $filename, $image, $mime_type, $post_id );
	}
}
