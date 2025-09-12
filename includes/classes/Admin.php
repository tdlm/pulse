<?php
/**
 * Where all the admin stuff happens.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Admin class.
 */
class Admin extends Singleton {

	/**
	 * Notices array.
	 *
	 * @var array
	 */
	public static $notices = [];

	/**
	 * The menu slug.
	 *
	 * @var string
	 */
	public $menu_slug = 'wp-pulse-settings';

	/**
	 * The option key.
	 *
	 * @var string
	 */
	public $option_key = 'wp-pulse';

	/**
	 * Add the menu page.
	 *
	 * @action admin_menu
	 */
	public function add_menu_page() {
		$hook = add_menu_page(
			'Pulse',
			'Pulse',
			'manage_options',
			'wp-pulse',
			[ $this, 'render_pulse_page' ],
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IS0tIFVwbG9hZGVkIHRvOiBTVkcgUmVwbywgd3d3LnN2Z3JlcG8uY29tLCBHZW5lcmF0b3I6IFNWRyBSZXBvIE1peGVyIFRvb2xzIC0tPgo8c3ZnIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTkgMkM5LjQzMDQzIDIgOS44MTI1NyAyLjI3NTQzIDkuOTQ4NjggMi42ODM3N0wxNSAxNy44Mzc3TDE3LjA1MTMgMTEuNjgzOEMxNy4xODc0IDExLjI3NTQgMTcuNTY5NiAxMSAxOCAxMUgyMkMyMi41NTIzIDExIDIzIDExLjQ0NzcgMjMgMTJDMjMgMTIuNTUyMyAyMi41NTIzIDEzIDIyIDEzSDE4LjcyMDhMMTUuOTQ4NyAyMS4zMTYyQzE1LjgxMjYgMjEuNzI0NiAxNS40MzA0IDIyIDE1IDIyQzE0LjU2OTYgMjIgMTQuMTg3NCAyMS43MjQ2IDE0LjA1MTMgMjEuMzE2Mkw5IDYuMTYyMjhMNi45NDg2OCAxMi4zMTYyQzYuODEyNTcgMTIuNzI0NiA2LjQzMDQzIDEzIDYgMTNIMkMxLjQ0NzcyIDEzIDEgMTIuNTUyMyAxIDEyQzEgMTEuNDQ3NyAxLjQ0NzcyIDExIDIgMTFINS4yNzkyNEw4LjA1MTMyIDIuNjgzNzdDOC4xODc0MyAyLjI3NTQzIDguNTY5NTcgMiA5IDJaIiBmaWxsPSIjMDAwMDAwIi8+Cjwvc3ZnPg==',
			2.75
		);

		add_action( "load-$hook", [ $this, 'add_screen_options' ] );

		add_submenu_page(
			'wp-pulse',
			'Pulse Settings',
			'Settings',
			'manage_options',
			$this->menu_slug,
			[ $this, 'render_settings_page' ],
			2.75
		);
	}

	/**
	 * Add screen options (per-page setting).
	 *
	 * @return void
	 */
	public function add_screen_options() {
		add_screen_option(
			'per_page',
			[
				'label'   => __( 'Pulse records per page', 'wp-pulse' ),
				'default' => 20,
				'option'  => 'pulse_per_page',
			]
		);
	}

	/**
	 * Hook for saving the per-page option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @return mixed
	 *
	 * @filter set-screen-option
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'pulse_per_page' === $option ) {
			return (int) $value;
		}

		return $status;
	}

	/**
	 * Save the screen options.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function save_screen_options() {
		$enable_live_update_nonce = filter_input( INPUT_POST, 'pulse_enable_live_update_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$enable_live_update_user  = filter_input( INPUT_POST, 'pulse_enable_live_update_user', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$enable_live_update       = filter_input( INPUT_POST, 'pulse_enable_live_update', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wp_screen_options        = filter_input( INPUT_POST, 'wp_screen_options', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( false === empty( $enable_live_update_nonce ) && false === empty( $wp_screen_options ) ) {
			// TODO: Bring back nonce check.
			update_user_option(
				$enable_live_update_user,
				'pulse_live_update',
				'on' === $enable_live_update ? 'on' : 'off',
				true
			);
		}
	}

	/**
	 * Add a body class.
	 *
	 * @param mixed $classes Classes.
	 *
	 * @action admin_body_class
	 *
	 * @return mixed
	 */
	public function admin_body_class( $classes ) {
		$pulse_classes = [];

		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( 'wp-pulse' === $page ) {
			$pulse_classes[] = 'wp-pulse';
		}

		$pulse_classes = apply_filters( 'wp_pulse_admin_body_classes', $pulse_classes );
		$pulse_classes = implode( ' ', array_filter( $pulse_classes ) );

		return $classes . ' ' . $pulse_classes;
	}

	/**
	 * Modify the screen controls.
	 *
	 * @param mixed $status Status.
	 * @param mixed $args   Args.
	 *
	 * @filter screen_settings
	 *
	 * @return string
	 */
	public function modify_screen_controls( $status, $args ) {
		unset( $status, $args );

		$user_id   = get_current_user_id();
		$heartbeat = 120;
		$option    = get_user_option( 'pulse_live_update', $user_id );
		$nonce     = wp_create_nonce( 'pulse_enable_live_update_nonce' );

		return View::render_template(
			'admin/dashboard-screen-controls',
			compact(
				'nonce',
				'user_id',
				'heartbeat',
				'option',
			)
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_pulse_page() {
		$pulse_id = filter_input( INPUT_GET, 'pulse_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( false === empty( $pulse_id ) ) {
			self::render_pulse_detail_page( $pulse_id );
		} else {
			self::render_pulse_dashboard_page();
		}
	}

	/**
	 * Render the pulse dashboard page.
	 *
	 * @return void
	 */
	public static function render_pulse_dashboard_page() {
		$user_id  = get_current_user_id();
		$per_page = get_user_option( 'pulse_per_page', $user_id );

		$action     = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$context    = filter_input( INPUT_GET, 'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$created_at = filter_input( INPUT_GET, 'created_at', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$ip         = filter_input( INPUT_GET, 'ip', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$page       = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$pulse      = filter_input( INPUT_GET, 'pulse', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$search     = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$user_id    = filter_input( INPUT_GET, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( false === is_numeric( $per_page ) ) {
			$per_page = 20;
		}

		$offset = $page < 2 ? 0 : $page * $per_page;

		$records = Database::get_records(
			[
				'action'     => $action,
				'context'    => $context,
				'created_at' => $created_at,
				'ip'         => $ip,
				'limit'      => $per_page,
				'offset'     => $offset,
				'pulse'      => $pulse,
				'search'     => $search,
				'user_id'    => $user_id,
			]
		);

		Helpers\Media\enqueue_script(
			'pulse/admin-dashboard.tsx',
			[
				[
					'object_name' => 'PulseAdminDashboard',
					'value'       => [
						'count'    => intval( $records['count'] ),
						'items'    => $records['items'],
						'offset'   => $offset,
						'pages'    => intval( ceil( $records['count'] / $per_page ) ),
						'limit'    => intval( $per_page ),
						'users'    => $records['users'],
						'settings' => [
							'admin_url'            => admin_url(),
							'dashboard_base_url'   => admin_url( 'admin.php?page=wp-pulse' ),
							'live_updates_enabled' => self::are_live_updates_enabled(),
						],
					],
				],
			]
		);
		Helpers\Media\enqueue_style( 'pulse/admin-dashboard.tsx' );

		View::include_template( 'admin/dashboard' );
	}

	/**
	 * Render the pulse detail page.
	 *
	 * @param int $pulse_id The pulse ID.
	 *
	 * @return void
	 */
	public static function render_pulse_detail_page( $pulse_id ) {
		$records = Database::get_records(
			[
				'id' => $pulse_id,
			]
		);

		if ( 1 > intval( $records['count'] ) ) {
			self::notice( 'Pulse not found.', 'error' );
		}

		$record = $records['items'][0] ?? [];

		$meta = Database::get_record_meta( $pulse_id );

		Helpers\Media\enqueue_script(
			'pulse/admin-pulse-detail.tsx',
			[
				[
					'object_name' => 'PulseAdminPulseDetail',
					'value'       => [
						'record' => $record,
						'meta'   => $meta,
					],
				],
			]
		);
		Helpers\Media\enqueue_style( 'pulse/admin-pulse-detail.tsx' );

		View::include_template( 'admin/pulse-detail' );
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( false === current_user_can( 'manage_options' ) ) {
			return;
		}

		$schema      = self::get_settings_schema();
		$current_tab = self::get_current_tab();

		$tabs = $schema['tabs'];

		Helpers\Media\enqueue_script(
			'pulse/admin-settings.ts',
			[
				[
					'object_name' => 'PulseAdminSettings',
				],
			]
		);
		Helpers\Media\enqueue_style( 'pulse/admin-settings.ts' );

		View::include_template(
			'admin/settings',
			compact(
				'current_tab',
				'tabs'
			)
		);
	}

	/**
	 * Display the admin help notice.
	 *
	 * @action admin_notices
	 */
	public function display_admin_help_notice() {
		$screen = get_current_screen();

		if ( false === $screen instanceof \WP_Screen ) {
			return;
		}

		if ( false === in_array( $screen->id, [ 'pulse_page_wp-pulse-settings' ], true ) ) {
			return;
		}

		self::notice(
			'Let me know if you have any feedback or suggestions. <a href="https://github.com/tdlm/pulse/issues/new" target="_blank">Create an issue</a>',
			'info'
		);
	}

	/**
	 * Get the current tab.
	 *
	 * @return string
	 */
	public static function get_current_tab() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( false === isset( $tab ) ) {
			$tab = 'general';
		}

		return $tab;
	}

	/**
	 * Get the settings schema.
	 *
	 * @return array{tabs: array}
	 */
	public static function get_settings_schema() {
		return [
			'tabs' => [
				'general'  => [
					'title'  => 'General',
					'fields' => [
						[
							'class'   => 'pulse-keep-forever',
							'default' => false,
							'desc'    => sprintf( '%s<br /><strong>%s</strong> %s', esc_html__( 'If enabled, pulse records will never be deleted.' ), esc_html__( 'WARNING:' ), esc_html__( 'Trimming older records will keep the database running more smoothly.' ) ),
							'help'    => 'Enabled',
							'id'      => 'keep_forever',
							'label'   => 'Keep records forever',
							'type'    => 'checkbox',
						],
						[
							'class'   => 'pulse-keep-days',
							'default' => 30,
							'desc'    => esc_html__( 'How many days to keep records for.' ),
							'help'    => 'days',
							'id'      => 'keep_days',
							'label'   => 'Keep records for',
							'max'     => 999,
							'min'     => 1,
							'type'    => 'number',
						],
					],
				],
				'advanced' => [
					'title'  => 'Advanced',
					'fields' => [
						[
							'class'   => 'button button-link button-link-delete',
							'default' => false,
							'desc'    => __( 'WARNING: This will delete all pulse records from the database!', 'wp-pulse' ),
							'id'      => 'reset_all_pulses',
							'label'   => __( 'Reset Pulse Database', 'wp-pulse' ),
							'type'    => 'button',
						],
					],
				],
			],
		];
	}

	/**
	 * Register the settings.
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'pulse_settings_group',
			$this->option_key,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_pulse_options' ],
			]
		);

		$settings = self::get_settings_schema();

		foreach ( $settings['tabs'] as $tab_key => $tab_settings ) {
			add_settings_section(
				"pulse_section_{$tab_key}",
				$tab_settings['title'],
				function () {},
				"pulse_settings_{$tab_key}"
			);

			foreach ( $tab_settings['fields'] as $field ) {
				add_settings_field(
					$field['id'],
					$field['label'],
					[ $this, 'render_field' ],
					"pulse_settings_{$tab_key}",
					"pulse_section_{$tab_key}",
					[
						'tab'   => $tab_key,
						'field' => $field,
					]
				);
			}
		}

		$options = get_option( $this->option_key );

		if ( false === $options ) {
			$defaults = [];

			foreach ( $settings['tabs'] as $tab_key => $tab ) {
				foreach ( $tab['fields'] as $field ) {
					if ( false === isset( $defaults[ $tab_key ] ) ) {
						$defaults[ $tab_key ] = [];
					}

					$defaults[ $tab_key ][ $field['id'] ] = $field['default'] ?? '';
				}
			}

			update_option( $this->option_key, $defaults );
		}
	}

	/**
	 * Render a field.
	 *
	 * @param array $args The arguments.
	 *
	 * @return void
	 */
	public function render_field( array $args ) {
		$field = $args['field'];
		$id    = $field['id'];
		$type  = $field['type'] ?? 'text';
		$opts  = get_option( $this->option_key, [] );
		$tab   = $args['tab'];

		$class = true === isset( $field['class'] ) ? esc_attr( $field['class'] ) : '';
		$label = true === isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
		$value = true === isset( $opts[ $tab ][ $id ] ) ? $opts[ $tab ][ $id ] : ( $field['default'] ?? '' );
		$name  = $this->option_key . '[' . $tab . '][' . $id . ']';
		$help  = true === isset( $field['help'] ) ? esc_html( $field['help'] ) : '';
		$title = true === isset( $field['title'] ) ? esc_html( $field['title'] ) : '';

		switch ( $type ) {
			case 'button':
				echo '<button type="button" class="' . esc_attr( $class ) . '" id="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</button>';

				if ( false === empty( $field['desc'] ) ) {
					echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
				}
				break;
			case 'link':
				echo '<a href="' . esc_attr( $field['href'] ) . '" class="' . esc_attr( $field['class'] ) . '">' . esc_html( $field['label'] ) . '</a>';

				if ( false === empty( $field['desc'] ) ) {
					echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
				}
				break;

			case 'checkbox':
				echo '<label><input type="hidden" name="' . esc_attr( $name ) . '" value="0" />';
				printf(
					'<input type="checkbox" name="%s" value="1" %s class="%s" /> %s</label>',
					esc_attr( $name ),
					true === checked( (bool) $value, true, false ),
					esc_attr( $class ),
					esc_html( $help )
				);
				if ( false === empty( $field['desc'] ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<p class="description">' . $field['desc'] . '</p>';
				}
				break;

			case 'number':
				printf(
					'<label><input type="number" name="%s" value="%s" min="%s" max="%s" step="%s" class="%s" /> %s</label>',
					esc_attr( $name ),
					esc_attr( $value ),
					true === isset( $field['min'] ) ? esc_attr( $field['min'] ) : '',
					true === isset( $field['max'] ) ? esc_attr( $field['max'] ) : '',
					true === isset( $field['step'] ) ? esc_attr( $field['step'] ) : '1',
					esc_attr( $class ),
					esc_html( $help )
				);
				if ( false === empty( $field['desc'] ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<p class="description">' . $field['desc'] . '</p>';
				}
				break;

			case 'select':
				echo '<select name="' . esc_attr( $name ) . '">';
				foreach ( $field['choices'] as $k => $label ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), true === selected( $value, $k, false ), esc_html( $label ) );
				}
				echo '</select>';
				if ( false === empty( $field['desc'] ) ) {
					echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
				}
				break;

			case 'textarea':
				printf(
					'<textarea name="%s" rows="5" class="large-text">%s</textarea>',
					esc_attr( $name ),
					esc_textarea( $value )
				);
				if ( false === empty( $field['desc'] ) ) {
					echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
				}
				break;

			default:
				printf(
					'<input type="text" name="%s" value="%s" class="regular-text" />',
					esc_attr( $name ),
					esc_attr( $value )
				);
				if ( false === empty( $field['desc'] ) ) {
					echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
				}
				break;
		}
	}

	/**
	 * Sanitize the options.
	 *
	 * @param array $input The input.
	 *
	 * @return array
	 */
	public function sanitize_pulse_options( $input ) {
		if ( false === is_array( $input ) ) {
			$input = [];
		}

		$schema = self::get_settings_schema();

		// Start from existing so other-tab values aren't wiped.
		$out = get_option( $this->option_key, [] );

		// Merge the input with the existing options.
		$merged = array_merge( $out, $input );

		// Sanitize the merged options.
		foreach ( $schema['tabs'] as $tab_key => $tab ) {
			foreach ( $tab['fields'] as $f ) {
				$id = $f['id'];

				if ( false === isset( $merged[ $tab_key ] ) ) {
					$merged[ $tab_key ] = [];
				}

				if ( false === isset( $merged[ $tab_key ][ $id ] ) ) {
					$merged[ $tab_key ][ $id ] = '';
				}

				$val  = $merged[ $tab_key ][ $id ];
				$type = $f['type'] ?? 'text';

				switch ( $type ) {
					case 'checkbox':
						if ( false === isset( $merged[ $tab_key ] ) ) {
							$merged[ $tab_key ] = [];
						}

						if ( false === isset( $merged[ $tab_key ][ $id ] ) ) {
							$merged[ $tab_key ][ $id ] = '';
						}

						$merged[ $tab_key ][ $id ] = $val ? 1 : 0;
						break;

					case 'number':
						$num = true === is_numeric( $val ) ? 0 + $val : ( $f['default'] ?? 0 );
						if ( true === isset( $f['min'] ) ) {
							$num = max( $num, (float) $f['min'] ); }
						if ( true === isset( $f['max'] ) ) {
							$num = min( $num, (float) $f['max'] ); }
						$merged[ $tab_key ][ $id ] = $num;
						break;

					case 'select':
						$allowed                   = array_keys( $f['choices'] ?? [] );
						$merged[ $tab_key ][ $id ] = true === in_array( (string) $val, $allowed, true )
						? (string) $val
						: ( $f['default'] ?? '' );
						break;

					case 'textarea':
						$merged[ $tab_key ][ $id ] = wp_kses_post( true === is_scalar( $val ) ? (string) $val : '' );
						break;

					default:
						$merged[ $tab_key ][ $id ] = sanitize_text_field( true === is_scalar( $val ) ? (string) $val : '' );
				}
			}
		}

		return $merged;
	}

	/**
	 * Check if live updates are enabled.
	 *
	 * @return bool
	 */
	public static function are_live_updates_enabled() {
		$user_id = get_current_user_id();

		$live_updates_enabled = get_user_option( 'pulse_live_update', $user_id );

		if ( true === empty( $live_updates_enabled ) ) {
			$live_updates_enabled = 'on';
		}

		return 'on' === $live_updates_enabled;
	}

	/**
	 * Add a notice.
	 *
	 * @param string $message The message.
	 * @param string $type    The type.
	 *
	 * @return void
	 */
	public static function notice( $message, $type = 'success' ) {
		self::$notices[] = compact( 'message', 'type' );
	}

	/**
	 * Render the notices.
	 *
	 * @action shutdown
	 *
	 * @return void
	 */
	public function render_notices() {
		foreach ( self::$notices as $notice ) {
			View::include_template( 'admin/notice', $notice );
		}
	}
}
