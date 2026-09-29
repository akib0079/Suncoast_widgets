<?php
/**
 * Safe rendering for Elementor saved templates used inside Suncoast widgets.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

final class SCE_Template {

	/** Template IDs currently being rendered, used to prevent recursion. */
	private static $rendering = array();

	/**
	 * Return published Elementor templates for a control selector.
	 *
	 * @return array<int|string,string>
	 */
	public static function options() {
		static $options = null;

		if ( null !== $options ) {
			return $options;
		}

		$options = array( '' => __( 'Select a saved template', 'suncoast-ele-widgets' ) );
		$posts   = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'posts_per_page'         => -1,
				'post_status'            => 'publish',
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title ? $post->post_title : '#' . $post->ID;
		}

		return $options;
	}

	/**
	 * Render one saved template and include its generated Elementor CSS.
	 *
	 * @param int $template_id Elementor library post ID.
	 * @return string Empty when the selection is invalid or unavailable.
	 */
	public static function render( $template_id ) {
		$template_id = absint( $template_id );
		$post        = $template_id ? get_post( $template_id ) : null;

		if ( ! $post || 'elementor_library' !== $post->post_type || 'publish' !== $post->post_status ) {
			return '';
		}

		if ( isset( self::$rendering[ $template_id ] ) ) {
			return '';
		}

		if ( ! class_exists( '\\Elementor\\Plugin' ) || ! \Elementor\Plugin::$instance->frontend ) {
			return '';
		}

		self::$rendering[ $template_id ] = true;
		try {
			$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
		} catch ( \Throwable $error ) {
			$content = '';
		}
		unset( self::$rendering[ $template_id ] );

		return is_string( $content ) ? $content : '';
	}

	/**
	 * Whether Elementor is currently rendering its editor preview.
	 *
	 * @return bool
	 */
	public static function is_edit_mode() {
		return class_exists( '\\Elementor\\Plugin' )
			&& \Elementor\Plugin::$instance->editor
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}
}
