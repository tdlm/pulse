<?php
/**
 * API trait.
 *
 * @package WP_Pulse
 * @subpackage Traits\API
 * @since 1.0.0
 *
 * @phpcs:disable Universal.NamingConventions.NoReservedKeywordParameterNames.namespaceFound
 */

namespace WP_Pulse\Traits;

/**
 * API Trait.
 */
trait API {

	/**
	 * The API namespace.
	 *
	 * @var string
	 */
	private static string $namespace = '';

	/**
	 * The API routes.
	 *
	 * @var array
	 */
	private static array $routes = [];

	/**
	 * The optional API subspace.
	 *
	 * @var string
	 */
	private static string $subspace = '';

	/**
	 * The API version.
	 *
	 * @var string
	 */
	private static string $version = '1';

	/**
	 * Register REST API endpoints.
	 *
	 * @action rest_api_init
	 */
	public static function register_endpoints() {
		// Register each of the defined routes.
		foreach ( self::get_api_routes() as $base => $methods ) {
			if ( true === empty( self::get_api_namespace() ) || true === empty( self::get_api_version() ) ) {
				continue;
			}

			if ( false === empty( self::get_api_subspace() ) ) {
				$route = sprintf( '%s/v%s/%s', self::get_api_namespace(), self::get_api_version(), self::get_api_subspace() );
			} else {
				$route = sprintf( '%s/v%s', self::get_api_namespace(), self::get_api_version() );
			}

			register_rest_route(
				$route,
				$base,
				$methods
			);
		}
	}

	/**
	 * Get API routes.
	 *
	 * @return array The API routes.
	 */
	public static function get_api_routes(): array {
		return self::$routes;
	}

	/**
	 * Get API namespace.
	 *
	 * @return string The API namespace.
	 */
	protected static function get_api_namespace(): string {
		return self::$namespace;
	}

	/**
	 * Get API version.
	 *
	 * @return string The API version.
	 */
	protected static function get_api_version(): string {
		return self::$version;
	}

	/**
	 * Get API subspace.
	 *
	 * @return string The API subspace.
	 */
	protected static function get_api_subspace(): string {
		return self::$subspace;
	}

	/**
	 * Set API routes.
	 *
	 * @param array $routes Array of routes.
	 * @return void
	 */
	public static function set_api_routes( array $routes ) {
		self::$routes = $routes;
	}

	/**
	 * Set API version.
	 *
	 * @param string $version The API version.
	 * @return void
	 */
	protected static function set_api_version( string $version ) {
		self::$version = $version;
	}

	/**
	 * Set API namespace.
	 *
	 * @param string $namespace The API namespace.
	 * @return void
	 */
	protected static function set_api_namespace( string $namespace ) {
		self::$namespace = $namespace;
	}

	/**
	 * Set API subspace.
	 *
	 * @param string $subspace The API subspace.
	 * @return void
	 */
	protected static function set_api_subspace( string $subspace ) {
		self::$subspace = $subspace;
	}

	/**
	 * Add doc hooks function.
	 *
	 * @action init
	 */
	public function add_api_hooks() {
		// Get instanced class to relate the callback to.
		$object = static::$instances[ static::class ];

		// Start a reflector.
		$reflector = new \ReflectionObject( $object );

		if ( false !== preg_match_all(
			'#\* @api-(?P<api_key>\S+)\s+(?P<api_value>.+)#',
			$reflector->getDocComment(),
			$matches,
			PREG_SET_ORDER
		) ) {
			// Iterate over found @filter|action|shortcode tags.
			foreach ( $matches as $match ) {
				// Parse comment block data.
				$api_key   = $match['api_key'];
				$api_value = $match['api_value'];

				$method = 'set_api_' . \WP_Pulse\Helpers\Strings\to_snake_case( $api_key );

				if ( true === method_exists( self::class, $method ) ) {
					call_user_func(
						[ self::class, $method ],
						$api_value
					);
				}
			}
		}
	}
}
