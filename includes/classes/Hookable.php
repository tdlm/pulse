<?php
/**
 * Trait Hookable
 *
 * @package WP_Pulse
 * @subpackage Hookable
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Trait Hookable.
 */
trait Hookable {

	/**
	 * Add doc hooks.
	 */
	public function add_doc_hooks() {
		// Get instanced class to relate the callback to.
		$object = static::$instances[ static::class ];

		// Start a reflector.
		$reflector = new \ReflectionObject( $object );

		foreach ( $reflector->getMethods() as $method ) {
			$phpdoc    = $method->getDocComment();
			$arg_count = $method->getNumberOfParameters();

			// Handle hooks.
			if ( preg_match_all(
				'#\* @(?P<type>filter|action|shortcode)\s+(?P<name>[a-z0-9\/\=\-\._]+)(?:,\s+(?P<priority>\d+))?#',
				$phpdoc,
				$matches,
				PREG_SET_ORDER
			) ) {
				foreach ( $matches as $match ) {
					$type     = $match['type'];
					$name     = $match['name'];
					$priority = empty( $match['priority'] ) ? 11 : intval( $match['priority'] );
					$callback = [ $this, $method->getName() ];
					call_user_func(
						[
							self::class,
							'add_' . $type,
						],
						$name,
						$callback,
						compact( 'priority', 'arg_count' )
					);
				}
			}

			// Handle base plugin functionality.
			if ( preg_match_all(
				'#\* @(?P<type>on_activate|on_deactivate)#',
				$phpdoc,
				$matches,
				PREG_SET_ORDER
			) ) {
				foreach ( $matches as $match ) {
					$type     = $match['type'];
					$callback = [ $this, $method->getName() ];

					switch ( $type ) {
						case 'on_activate':
							call_user_func(
								[ '\\register_activation_hook' ],
								$method->getFileName(),
								$callback
							);
							break;
						case 'on_deactivate':
							call_user_func(
								[ '\\register_deactivation_hook' ],
								$method->getFileName(),
								$callback
							);
							break;
					}
				}
			}

			// Handle CLI commands.
			if ( preg_match_all(
				'#\* @(?P<type>command)\s+(?P<name>[a-z0-9\/\=\-\._\: ]+)?#',
				$phpdoc,
				$matches,
				PREG_SET_ORDER
			) ) {
				foreach ( $matches as $match ) {
					$type     = $match['type'];
					$name     = $match['name'];
					$callback = [ $this, $method->getName() ];
					call_user_func( [ '\\WP_CLI', 'add_' . $type ], $name, $callback );
				}
			}

			// Ajax handler.
			if ( preg_match_all(
				'#\* @(?P<type>ajax)\s+?(?P<name>[a-z0-9\/\=\-\._]+)?#',
				$phpdoc,
				$matches,
				PREG_SET_ORDER
			) ) {
				foreach ( $matches as $match ) {
					$name     = $match['name'] ?? Helpers\Strings\to_snake_case( $method->getName() );
					$priority = empty( $match['priority'] ) ? 11 : intval( $match['priority'] );
					$callback = [ $this, $method->getName() ];
					foreach ( [ 'wp_ajax', 'wp_ajax_nopriv' ] as $ajax_hook ) {
						call_user_func(
							[
								self::class,
								'add_action',
							],
							sprintf( '%s_%s', $ajax_hook, $name ),
							$callback,
							compact( 'priority', 'arg_count' )
						);
					}
				}
			}
		}
	}

	/**
	 * Hooks a function on to a specific action.
	 *
	 * @param string $name    The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_action(
		$name,
		$callback,
		$args = []
	) {
		// Merge defaults.
		$args = array_merge(
			[
				'priority'  => 10,
				'arg_count' => PHP_INT_MAX,
			],
			$args
		);

		return $this->add_hook( 'action', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific filter.
	 *
	 * @param string $name    The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	public function add_filter(
		$name,
		$callback,
		$args = []
	) {
		// Merge defaults.
		$args = array_merge(
			[
				'priority'  => 10,
				'arg_count' => PHP_INT_MAX,
			],
			$args
		);

		return $this->add_hook( 'filter', $name, $callback, $args );
	}

	/**
	 * Hooks a function on to a specific shortcode.
	 *
	 * @param string $name    The shortcode name.
	 * @param array  $callback The class object and method.
	 *
	 * @return mixed
	 */
	public function add_shortcode(
		$name,
		$callback
	) {
		return $this->add_hook( 'shortcode', $name, $callback );
	}

	/**
	 * Hooks a function on to a specific action/filter.
	 *
	 * @param string $type    The hook type. Options are action/filter.
	 * @param string $name    The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 *
	 * @return mixed
	 */
	protected function add_hook(
		$type,
		$name,
		$callback,
		$args = []
	) {
		$priority  = $args['priority'] ?? 10;
		$arg_count = $args['arg_count'] ?? PHP_INT_MAX;
		$fn        = sprintf( '\add_%s', $type );

		return \call_user_func( $fn, $name, $callback, $priority, $arg_count );
	}
}
