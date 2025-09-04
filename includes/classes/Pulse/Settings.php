<?php
/**
 * Keeps a finger on the pulse of settings-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Settings
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;
use WP_Pulse\Helpers\Option;

/**
 * Settings class.
 */
class Settings extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'settings';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'update_option',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'settings' => __( 'Settings', 'pulse' ),
		];
	}

	/**
	 * Callback for update_option.
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $old_value Old value.
	 * @param mixed  $new_value New value.
	 *
	 * @return void
	 */
	public function callback_update_option( $option_name, $old_value, $new_value ) {
		global $whitelist_options, $new_whitelist_options;

		$current_user = wp_get_current_user();

		if ( true === Option\should_ignore_option( $option_name ) ) {
			return;
		}

		$options = array_merge(
			(array) $whitelist_options,
			(array) $new_whitelist_options,
			[
				'permalink' => [
					'category_base',
					'permalink_structure',
					'tag_base',
				],
			],
			[
				'network' => [
					'add_new_users',
					'admin_email',
					'banned_email_domains',
					'blog_count',
					'blog_upload_space',
					'fileupload_maxk',
					'first_comment_author',
					'first_comment_url',
					'first_comment',
					'first_page',
					'first_post',
					'global_terms_enabled',
					'illegal_names',
					'limited_email_domains',
					'menu_items',
					'new_admin_email',
					'registration',
					'registrationnotification',
					'site_name',
					'upload_filetypes',
					'upload_space_check_disabled',
					'user_count',
					'welcome_email',
					'welcome_user_email',
					'WPLANG',
				],
			]
		);

		$context = '';

		foreach ( $options as $option_key => $option_names ) {
			if ( true === in_array( $option_name, $option_names, true ) ) {
				$context = $option_key;
				break;
			}
		}

		if ( true === empty( $context ) ) {
			$context = 'settings';
		}

		$changed_options = [];

		if ( true === Option\is_option_group( $new_value ) ) {
			foreach ( Option\get_changed_keys( $old_value, $new_value ) as $field_key ) {
				$key_context = self::get_context_by_key( $option_name, $field_key );

				$changed_options[] = [
					'label'      => self::get_serialized_field_label( $option_name, $field_key ),
					'option'     => $option_name,
					'option_key' => $field_key,
					'context'    => $key_context ?? $context,
					'old_value'  => true === isset( $old_value[ $field_key ] ) ? self::sanitize_value( $old_value[ $field_key ] ) : '',
					'new_value'  => true === isset( $new_value[ $field_key ] ) ? self::sanitize_value( $new_value[ $field_key ] ) : '',
				];
			}
		} else {
			$changed_options[] = [
				'label'      => $this->get_field_label( $option_name ),
				'option'     => $option_name,
				'context'    => $context,
				'old_value' => self::sanitize_value( $old_value ),
				'new_value'  => self::sanitize_value( $new_value ),
			];
		}

		foreach ( $changed_options as $changed_option ) {
			Log::log(
				'option-updated',
				sprintf(
					/* translators: %1$s: Option name. %2$s: Old value. %3$s: New value. */
					__( 'Option %1$s updated.', 'pulse' ),
					$changed_option['label'],
					$changed_option['old_value'],
					$changed_option['new_value']
				),
				$this->pulse_slug,
				$changed_option['context'],
				$current_user->ID,
				null,
				$changed_option
			);
		}
	}

	/**
	 * Get context by key.
	 *
	 * @param string $option_name Option name.
	 * @param string $key Key.
	 *
	 * @return string|false Context by key.
	 */
	private static function get_context_by_key( $option_name, $key ) {
		$contexts = [
			'custom_header' => [
				'header_image',
				'header_textcolor',
			],
			'theme_mods'    => [
				'custom_background' => [
					'background_image',
					'background_position_x',
					'background_repeat',
					'background_attachment',
					'background_color',
				],
			],
		];

		if ( true === isset( $contexts[ $option_name ] ) ) {
			foreach ( $contexts[ $option_name ] as $context => $keys ) {
				if ( true === in_array( $key, $keys, true ) ) {
					return $context;
				}
			}
		}

		return false;
	}

	/**
	 * Get serialized field label.
	 *
	 * @param string $option_name Option name.
	 * @param string $field_key Field key.
	 *
	 * @return string Serialized field label.
	 */
	public static function get_serialized_field_label( $option_name, $field_key ) {
		$labels = [
			'theme_mods' => [
				'background_attachment'   => esc_html__( 'Background Attachment', 'pulse' ),
				'background_color'        => esc_html__( 'Background Color', 'pulse' ),
				'background_image'        => esc_html__( 'Background Image', 'pulse' ),
				'background_position_x'   => esc_html__( 'Background Position', 'pulse' ),
				'background_repeat'       => esc_html__( 'Background Repeat', 'pulse' ),
				'color_scheme'            => esc_html__( 'Color Scheme', 'pulse' ),
				'featured_content_layout' => esc_html__( 'Layout', 'pulse' ),
				'header_background_color' => esc_html__( 'Header and Sidebar Background Color', 'pulse' ),
				'header_image'            => esc_html__( 'Header Image', 'pulse' ),
				'header_textcolor'        => esc_html__( 'Text Color', 'pulse' ),
				'link_color'              => esc_html__( 'Link Color', 'pulse' ),
				'main_text_color'         => esc_html__( 'Main Text Color', 'pulse' ),
				'page_background_color'   => esc_html__( 'Page Background Color', 'pulse' ),
				'secondary_text_color'    => esc_html__( 'Secondary Text Color', 'pulse' ),
				'sidebar_textcolor'       => esc_html__( 'Header and Sidebar Text Color', 'pulse' ),
			],
		];

		/**
		 * Filter allows for insertion of serialized labels
		 *
		 * @param  array  $labels  Serialized labels
		 * @return array  Updated array of serialzed labels
		 */
		$labels = apply_filters( 'pulse_serialized_labels', $labels );

		if ( true === isset( $labels[ $option_name ], $labels[ $option_name ][ $field_key ] ) ) {
			return $labels[ $option_name ][ $field_key ];
		}

		return $field_key;
	}

    /**
     * Get field label.
     *
     * @param string $field_key Field key.
     *
     * @return string Field label.
     */
    public function get_field_label( $field_key ) {
        $labels = $this->get_labels();

		if ( true === isset( $labels[ $field_key ] ) ) {
			return $labels[ $field_key ];
		}

		return $field_key;
	}

	/**
	 * Sanitize value.
	 *
	 * @param mixed $value Value.
	 *
	 * @return string Sanitized value.
	 */
	public static function sanitize_value( $value ) {
		if ( true === is_array( $value ) ) {
			return '';
		} elseif ( true === is_object( $value ) && false === in_array( '__toString', get_class_methods( $value ), true ) ) {
			return '';
		}

		return strval( $value );
	}
}
