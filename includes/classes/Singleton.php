<?php
/**
 * Class Singleton
 *
 * @package WP_Pulse
 * @subpackage Singleton
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Class Singleton.
 */
abstract class Singleton {

	use Traits\Hookable;

	/**
	 * Array of singleton instances.
	 *
	 * @var array<string, Singleton>
	 */
	protected static $instances = [];

	/**
	 * Creates a new instance of a singleton class (via late static binding), accepting a variable-length argument list.
	 *
	 * @return self
	 */
	final public static function instance(): Singleton {
		if ( false === isset( static::$instances[ static::class ] ) ) {
			static::$instances[ static::class ] = new static();

			// Call 'addDocHooks' to parse and fire object doc actions/filters.
			if ( method_exists( self::$instances[ static::class ], 'add_doc_hooks' ) ) {
				call_user_func_array( [ self::$instances[ static::class ], 'add_doc_hooks' ], [] );
			}

			// Call 'init' bootstrap method if it's defined in the inheriting class.
			if ( method_exists( self::$instances[ static::class ], 'init' ) ) {
				call_user_func_array( [ self::$instances[ static::class ], 'init' ], func_get_args() );
			}
		}

		return static::$instances[ static::class ];
	}

	/**
	 * Prevents direct instantiation.
	 *
	 * @return void
	 */
	final private function __construct() {
	}

	/**
	 * Prevents cloning the singleton instance.
	 *
	 * @return void
	 */
	final public function __clone() {
	}

	/**
	 * Prevents unserializing the singleton instance.
	 *
	 * @return void
	 */
	final public function __wakeup() {
	}
}
