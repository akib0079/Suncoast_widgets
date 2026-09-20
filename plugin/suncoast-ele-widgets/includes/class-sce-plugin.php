<?php
/**
 * Plugin orchestrator: category + widget registration.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

final class SCE_Plugin {

	/** @var SCE_Plugin|null */
	private static $instance = null;

	const CATEGORY = 'suncoast-ele-widgets';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		SCE_Assets::instance();
		SCE_Forms::instance();

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * The dedicated "Suncoast Ele Widgets" panel section.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			self::CATEGORY,
			array(
				'title' => __( 'Suncoast Ele Widgets', 'suncoast-ele-widgets' ),
				'icon'  => 'eicon-tools',
			)
		);
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once SCE_PATH . 'includes/widgets/class-sce-widget-header.php';
		require_once SCE_PATH . 'includes/widgets/class-sce-widget-banner.php';
		require_once SCE_PATH . 'includes/widgets/class-sce-widget-benefits.php';
		require_once SCE_PATH . 'includes/widgets/class-sce-widget-projects.php';

		$widgets_manager->register( new SCE_Widget_Header() );
		$widgets_manager->register( new SCE_Widget_Banner() );
		$widgets_manager->register( new SCE_Widget_Benefits() );
		$widgets_manager->register( new SCE_Widget_Projects() );
	}


	/**
	 * Split a heading on <br> so every line gets its own masked reveal.
	 *
	 * Shared by the banner, benefits and projects widgets so the markup and
	 * the allowed inline tags stay identical across them.
	 *
	 * @param string $text Raw control value.
	 * @param int    $base First line's delay in ms.
	 * @param int    $step Added per subsequent line.
	 * @return string
	 */
	public static function reveal_lines( $text, $base = 120, $step = 90 ) {
		$allow = array(
			'em'     => array(),
			'i'      => array(),
			'strong' => array(),
			'b'      => array(),
			'span'   => array( 'class' => array() ),
		);
		$out = '';
		foreach ( preg_split( '#<\s*br\s*/?\s*>#i', (string) $text ) as $i => $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$out .= sprintf(
				'<span class="sce-rvline" style="--sce-rv-d:%dms"><span>%s</span></span>',
				(int) ( $base + ( $i * $step ) ),
				wp_kses( $line, $allow )
			);
		}
		return $out;
	}

	/**
	 * Turn an associative array of CSS custom properties into an inline style
	 * string. Empty values are dropped so a blank control never emits
	 * `--x:;` (which would invalidate the whole declaration block).
	 *
	 * @param array $vars name => value.
	 * @return string
	 */
	public static function css_vars( array $vars ) {
		$out = '';
		foreach ( $vars as $name => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}
			$out .= $name . ':' . $value . ';';
		}
		return $out;
	}

	/**
	 * Build a `--var:value` map from a responsive Elementor slider control.
	 *
	 * Elementor stores the tablet/mobile values under `{key}_tablet` /
	 * `{key}_mobile`; we emit them as separate custom properties that the
	 * stylesheet picks up inside its own media queries. That keeps every
	 * breakpoint in the hard-scoped CSS instead of Elementor's generated
	 * stylesheet, which is what makes the widget theme-proof.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $key      Control key.
	 * @param string $var      CSS variable base name (without --).
	 * @param string $unit     Fallback unit.
	 * @return array
	 */
	public static function responsive_var( array $settings, $key, $var, $unit = 'px' ) {
		$map  = array();
		$pair = array(
			''         => '--' . $var,
			'_tablet'  => '--' . $var . '-tablet',
			'_mobile'  => '--' . $var . '-mobile',
		);

		foreach ( $pair as $suffix => $prop ) {
			$raw = isset( $settings[ $key . $suffix ] ) ? $settings[ $key . $suffix ] : null;
			if ( ! is_array( $raw ) || ! isset( $raw['size'] ) || '' === $raw['size'] ) {
				continue;
			}
			$u           = ! empty( $raw['unit'] ) ? $raw['unit'] : $unit;
			$map[ $prop ] = $raw['size'] . ( 'custom' === $u ? '' : $u );
		}

		return $map;
	}
}
