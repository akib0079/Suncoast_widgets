<?php
/**
 * Plugin Name:       Suncoast Ele Widgets
 * Plugin URI:        https://suncoastenclosures.com/
 * Description:       Pixel-perfect Elementor widgets for the Suncoast Enclosures landing page — sticky blurred header and hero banner with lead form + marquee. All CSS is hard-scoped so the active theme cannot bleed into it.
 * Version:           1.2.1
 * Author:            Avix Digital Agency
 * Text Domain:       suncoast-ele-widgets
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

define( 'SCE_VERSION', '1.2.1' );
define( 'SCE_FILE', __FILE__ );
define( 'SCE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SCE_URL', plugin_dir_url( __FILE__ ) );

define( 'SCE_MIN_ELEMENTOR', '3.5.0' );
define( 'SCE_MIN_PHP', '7.4' );

/**
 * Boot the plugin once all plugins are loaded, so we can reliably see whether
 * Elementor is present and new enough.
 */
function sce_boot() {
	if ( version_compare( PHP_VERSION, SCE_MIN_PHP, '<' ) ) {
		add_action( 'admin_notices', 'sce_notice_php' );
		return;
	}

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'sce_notice_missing_elementor' );
		return;
	}

	if ( ! version_compare( ELEMENTOR_VERSION, SCE_MIN_ELEMENTOR, '>=' ) ) {
		add_action( 'admin_notices', 'sce_notice_old_elementor' );
		return;
	}

	require_once SCE_PATH . 'includes/class-sce-assets.php';
	require_once SCE_PATH . 'includes/class-sce-forms.php';
	require_once SCE_PATH . 'includes/class-sce-plugin.php';

	SCE_Plugin::instance();
}
add_action( 'plugins_loaded', 'sce_boot' );

/**
 * Admin notice helper — keeps the three notices consistent.
 *
 * @param string $message Already-escaped message HTML.
 */
function sce_render_notice( $message ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf( '<div class="notice notice-warning"><p>%s</p></div>', $message ); // phpcs:ignore WordPress.Security.EscapeOutput
}

function sce_notice_missing_elementor() {
	sce_render_notice(
		sprintf(
			/* translators: %s: plugin name */
			esc_html__( '%s needs Elementor to be installed and active.', 'suncoast-ele-widgets' ),
			'<strong>' . esc_html__( 'Suncoast Ele Widgets', 'suncoast-ele-widgets' ) . '</strong>'
		)
	);
}

function sce_notice_old_elementor() {
	sce_render_notice(
		sprintf(
			/* translators: 1: plugin name, 2: required Elementor version */
			esc_html__( '%1$s needs Elementor %2$s or newer.', 'suncoast-ele-widgets' ),
			'<strong>' . esc_html__( 'Suncoast Ele Widgets', 'suncoast-ele-widgets' ) . '</strong>',
			esc_html( SCE_MIN_ELEMENTOR )
		)
	);
}

function sce_notice_php() {
	sce_render_notice(
		sprintf(
			/* translators: 1: plugin name, 2: required PHP version */
			esc_html__( '%1$s needs PHP %2$s or newer.', 'suncoast-ele-widgets' ),
			'<strong>' . esc_html__( 'Suncoast Ele Widgets', 'suncoast-ele-widgets' ) . '</strong>',
			esc_html( SCE_MIN_PHP )
		)
	);
}
