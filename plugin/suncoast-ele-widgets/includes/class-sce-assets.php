<?php
/**
 * Asset registration.
 *
 * Nothing is enqueued globally. Each widget declares its handles through
 * get_style_depends()/get_script_depends(), so a page that uses neither widget
 * downloads none of this.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

final class SCE_Assets {

	/** @var SCE_Assets|null */
	private static $instance = null;

	/** Google Fonts handle, shared by both widgets. */
	const FONTS = 'sce-fonts';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Register early on the front end…
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 5 );
		// …and inside the editor preview / panel, where Elementor resolves
		// get_style_depends() against the registry.
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register' ) );

		add_filter( 'wp_resource_hints', array( $this, 'resource_hints' ), 10, 2 );
	}

	/**
	 * Idempotent — the hooks above can fire more than once per request.
	 */
	public function register() {
		if ( wp_style_is( 'sce-base', 'registered' ) ) {
			return;
		}

		$css = SCE_URL . 'assets/css/';
		$js  = SCE_URL . 'assets/js/';

		if ( $this->load_fonts() ) {
			wp_register_style( self::FONTS, $this->fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}

		wp_register_style( 'sce-base', $css . 'sce-base.css', array(), SCE_VERSION );

		$base = $this->load_fonts() ? array( self::FONTS, 'sce-base' ) : array( 'sce-base' );

		wp_register_style( 'sce-header', $css . 'sce-header.css', $base, SCE_VERSION );
		wp_register_style( 'sce-banner', $css . 'sce-banner.css', $base, SCE_VERSION );
		wp_register_style( 'sce-benefits', $css . 'sce-benefits.css', $base, SCE_VERSION );
		// Maps Elementor Pro's form markup onto the Figma card design.
		wp_register_style( 'sce-form-elementor', $css . 'sce-form-elementor.css', array( 'sce-banner' ), SCE_VERSION );

		wp_register_script( 'sce-header', $js . 'sce-header.js', array(), SCE_VERSION, true );
		wp_register_script( 'sce-banner', $js . 'sce-banner.js', array(), SCE_VERSION, true );
		wp_register_script( 'sce-benefits', $js . 'sce-benefits.js', array(), SCE_VERSION, true );

		wp_localize_script(
			'sce-banner',
			'SCE_FORM',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => SCE_Forms::ACTION,
				'nonce'   => wp_create_nonce( SCE_Forms::NONCE ),
			)
		);
	}

	/**
	 * Only three families, only the weights the design actually uses.
	 *
	 * Poppins 400/500/600 · Inter 400/500/600/700 · Playfair Display 400/600 + 400i
	 */
	public function fonts_url() {
		return add_query_arg(
			array(
				'family'  => rawurlencode( 'Inter:wght@400;500;600;700' )
					. '&family=' . rawurlencode( 'Playfair Display:ital,wght@0,400;0,600;1,400' )
					. '&family=' . rawurlencode( 'Poppins:wght@400;500;600' ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css2'
		);
	}

	/**
	 * Sites that already self-host these families can switch ours off:
	 *
	 *   add_filter( 'sce/load_google_fonts', '__return_false' );
	 */
	public function load_fonts() {
		return (bool) apply_filters( 'sce/load_google_fonts', true );
	}

	/**
	 * Warm up the font origins so the CSS request isn't waiting on DNS + TLS.
	 *
	 * @param array  $hints    Existing hints.
	 * @param string $relation Relation type.
	 * @return array
	 */
	public function resource_hints( $hints, $relation ) {
		if ( 'preconnect' !== $relation || ! $this->load_fonts() ) {
			return $hints;
		}
		if ( ! wp_style_is( self::FONTS, 'enqueued' ) ) {
			return $hints;
		}
		$hints[] = 'https://fonts.googleapis.com';
		$hints[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
		return $hints;
	}
}
