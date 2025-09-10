<?php
/**
 * Keeps a finger on the pulse of taxonomy-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Taxonomy
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;
use WP_Pulse\Registry;

/**
 * Taxonomy class.
 */
class Taxonomy extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'taxonomy';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'created_term',
		'delete_term',
		'edit_term',
		'edited_term',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'category'     => __( 'Category', 'pulse' ),
			'post_tag'     => __( 'Tag', 'pulse' ),
			'term-created' => __( 'Created', 'pulse' ),
			'term-deleted' => __( 'Deleted', 'pulse' ),
			'term-edited'  => __( 'Edited', 'pulse' ),
			'taxonomy'     => __( 'Taxonomy', 'pulse' ),
			'term'         => __( 'Term', 'pulse' ),
		];
	}

	/**
	 * Callback for created_term.
	 *
	 * @param int    $term_id The term ID.
	 * @param int    $tt_id The term taxonomy ID.
	 * @param string $taxonomy The taxonomy.
	 * @param array  $args The arguments.
	 */
	public function callback_created_term( $term_id, $tt_id, $taxonomy, $args ) {
		if ( true === in_array( $taxonomy, $this->get_excluded_taxonomies(), true ) ) {
			return;
		}

		$current_user = wp_get_current_user();

		Log::log(
			'term-created',
			sprintf(
				/* translators: %1$s: Term name. %2$s: Taxonomy. */
				__( '"%1$s" %2$s created.', 'pulse' ),
				$args['name'] ?? '',
				$taxonomy,
			),
			$this->pulse_slug,
			$taxonomy,
			$current_user->ID,
			$term_id,
			[]
		);
	}

	/**
	 * Callback for delete_term.
	 *
	 * @param object $term The term object.
	 * @param int    $tt_id The term taxonomy ID.
	 * @param string $taxonomy The taxonomy.
	 * @param object $deleted_term The deleted term object.
	 * @param array  $object_ids The object IDs.
	 */
	public function callback_delete_term( $term, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		if ( true === in_array( $taxonomy, $this->get_excluded_taxonomies(), true ) ) {
			return;
		}

		$current_user = wp_get_current_user();

		Log::log(
			'term-deleted',
			sprintf(
				/* translators: %1$s: Term name. %2$s: Taxonomy. */
				__( '"%1$s" %2$s deleted.', 'pulse' ),
				$deleted_term->name ?? '',
				$taxonomy
			),
			$this->pulse_slug,
			$taxonomy,
			$current_user->ID,
			$deleted_term->term_id,
			[]
		);
	}

	/**
	 * Callback for edit_term.
	 *
	 * @param int    $term_id The term ID.
	 * @param int    $tt_id The term taxonomy ID.
	 * @param string $taxonomy The taxonomy.
	 * @param array  $args The arguments.
	 */
	public function callback_edit_term( $term_id, $tt_id, $taxonomy, $args ) {
		if ( true === in_array( $taxonomy, $this->get_excluded_taxonomies(), true ) ) {
			return;
		}

		Registry::set( 'edited_term_details', get_term( $term_id, $taxonomy ) );
	}

	/**
	 * Callback for edited_term.
	 *
	 * @param int    $term_id The term ID.
	 * @param int    $tt_id The term taxonomy ID.
	 * @param string $taxonomy The taxonomy.
	 * @param array  $args The arguments.
	 */
	public function callback_edited_term( $term_id, $tt_id, $taxonomy, $args ) {
		if ( true === in_array( $taxonomy, $this->get_excluded_taxonomies(), true ) ) {
			return;
		}

		$current_user = wp_get_current_user();

		$edited_term_details = Registry::get( 'edited_term_details', [] );

		Log::log(
			'term-edited',
			sprintf(
				/* translators: %1$s: Term name. %2$s: Taxonomy. */
				__( '"%1$s" %2$s edited.', 'pulse' ),
				$edited_term_details->name ?? '',
				$taxonomy
			),
			$this->pulse_slug,
			$taxonomy,
			$current_user->ID,
			$term_id,
			[]
		);
	}

	/**
	 * Get excluded taxonomies.
	 *
	 * @return array The excluded taxonomies.
	 */
	public static function get_excluded_taxonomies() {
		return apply_filters(
			'wp_pulse_taxonomy_excluded_taxonomies',
			[
				'nav_menu',
			]
		);
	}
}
