<?php
/**
 * View class.
 *
 * @package WP_Pulse
 * @subpackage View
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * View class.
 */
class View {

	/**
	 * The data of the view.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The path of the template of the view.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * View constructor.
	 *
	 * @param string $path The path of the template.
	 * @param array  $data  The data for the template.
	 */
	public function __construct( string $path, array $data = [] ) {
		$this->path = $path;
		$this->data = $data;
	}

	/**
	 * Include a template without rendering it.
	 *
	 * @param string $path The path of the template.
	 * @param array  $data  The data for the template.
	 */
	public static function include_template( string $path, array $data = [] ) {
		( new static( $path, $data ) )->include();
	}

	/**
	 * Return a rendered template.
	 *
	 * @param string $path The path of the template.
	 * @param array  $data  The data for the template.
	 *
	 * @return string Rendered template string.
	 */
	public static function render_template( string $path, array $data = [] ) {
		return ( new static( $path, $data ) )->render();
	}

	/**
	 * Include a template file.
	 */
	public function include() {
		load_template( sprintf( '%s.php', $this->path ), false, $this->data );
	}

	/**
	 * Render a template.
	 *
	 * @return string Rendered template string.
	 */
	public function render(): string {
		ob_start();
		$this->include();
		$render = ob_get_contents();
		ob_end_clean();

		return $render;
	}

	/**
	 * Returns a rendered shortcode.
	 *
	 * @param string $shortcode The shortcode name.
	 * @param array  $atts      The shortcode attributes.
	 *
	 * @return string Rendered shortcode.
	 */
	public static function render_shortcode( string $shortcode, array $atts ): string {
		ob_start();
		the_widget( $shortcode, $atts );
		$widget = ob_get_contents();
		ob_end_clean();

		return $widget;
	}
}
