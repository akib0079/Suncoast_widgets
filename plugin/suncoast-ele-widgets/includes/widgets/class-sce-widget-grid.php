<?php
/**
 * Widget: Suncoast Make It Yours (icon card grid).
 *
 * Heading block over a six-up grid of white icon cards. Everything is a
 * control, including the icon size — the design draws the glyphs at their
 * natural ~17px, which reads small against a 341px card, so the default here
 * is 26px.
 *
 * Style controls ship without defaults on purpose: untouched means the
 * hard-scoped CSS supplies the Figma values at every breakpoint.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class SCE_Widget_Grid extends Widget_Base {

	public function get_name() {
		return 'suncoast_grid';
	}

	public function get_title() {
		return __( 'Suncoast Make It Yours', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'icons', 'features', 'grid', 'options', 'add-ons' );
	}

	public function get_style_depends() {
		return array( 'sce-grid' );
	}

	public function get_script_depends() {
		return array( 'sce-reveal' );
	}

	/**
	 * The six Figma glyphs, inlined so they recolour with the icon control and
	 * cost no extra request. Each keeps its own viewBox; preserveAspectRatio
	 * (the default) makes them optically even inside one square box.
	 */
	private static function glyphs() {
		return array(
			'screens' => array( '0 0 17 15', 'M0 15V13.3333H1.66667V0H15V13.3333H16.6667V15H0ZM3.33333 3.33333H10V1.66667H3.33333V3.33333ZM3.33333 6.66667H10V5H3.33333V6.66667ZM3.33333 13.3333H13.3333V8.33333H11.6667V9.85417C11.8611 9.99306 12.0139 10.1667 12.125 10.375C12.2361 10.5833 12.2917 10.8056 12.2917 11.0417C12.2917 11.4444 12.1493 11.7882 11.8646 12.0729C11.5799 12.3576 11.2361 12.5 10.8333 12.5C10.4306 12.5 10.0868 12.3576 9.80208 12.0729C9.51736 11.7882 9.375 11.4444 9.375 11.0417C9.375 10.8056 9.43056 10.5833 9.54167 10.375C9.65278 10.1667 9.80556 9.99306 10 9.85417V8.33333H3.33333V13.3333ZM11.6667 3.33333H13.3333V1.66667H11.6667V3.33333ZM11.6667 6.66667H13.3333V5H11.6667V6.66667Z' ),
			'heaters' => array( '0 0 14 16', 'M1.66667 9.16667C1.66667 9.88889 1.8125 10.5729 2.10417 11.2188C2.39583 11.8646 2.8125 12.4306 3.35417 12.9167C3.34028 12.8472 3.33333 12.7847 3.33333 12.7292C3.33333 12.6736 3.33333 12.6111 3.33333 12.5417C3.33333 12.0972 3.41667 11.6806 3.58333 11.2917C3.75 10.9028 3.99306 10.5486 4.3125 10.2292L6.66667 7.91667L9.02083 10.2292C9.34028 10.5486 9.58333 10.9028 9.75 11.2917C9.91667 11.6806 10 12.0972 10 12.5417C10 12.6111 10 12.6736 10 12.7292C10 12.7847 9.99306 12.8472 9.97917 12.9167C10.5208 12.4306 10.9375 11.8646 11.2292 11.2188C11.5208 10.5729 11.6667 9.88889 11.6667 9.16667C11.6667 8.47222 11.5382 7.81597 11.2812 7.19792C11.0243 6.57986 10.6528 6.02778 10.1667 5.54167C9.88889 5.72222 9.59722 5.85764 9.29167 5.94792C8.98611 6.03819 8.67361 6.08333 8.35417 6.08333C7.49306 6.08333 6.74653 5.79861 6.11458 5.22917C5.48264 4.65972 5.11806 3.95833 5.02083 3.125C4.47917 3.58333 4 4.05903 3.58333 4.55208C3.16667 5.04514 2.81597 5.54514 2.53125 6.05208C2.24653 6.55903 2.03125 7.07639 1.88542 7.60417C1.73958 8.13194 1.66667 8.65278 1.66667 9.16667ZM6.66667 10.25L5.47917 11.4167C5.32639 11.5694 5.20833 11.7431 5.125 11.9375C5.04167 12.1319 5 12.3333 5 12.5417C5 12.9861 5.16319 13.3681 5.48958 13.6875C5.81597 14.0069 6.20833 14.1667 6.66667 14.1667C7.125 14.1667 7.51736 14.0069 7.84375 13.6875C8.17014 13.3681 8.33333 12.9861 8.33333 12.5417C8.33333 12.3194 8.29167 12.1146 8.20833 11.9271C8.125 11.7396 8.00694 11.5694 7.85417 11.4167L6.66667 10.25ZM6.66667 0V2.75C6.66667 3.22222 6.82986 3.61806 7.15625 3.9375C7.48264 4.25694 7.88194 4.41667 8.35417 4.41667C8.60417 4.41667 8.83681 4.36458 9.05208 4.26042C9.26736 4.15625 9.45833 4 9.625 3.79167L10 3.33333C11.0278 3.91667 11.8403 4.72917 12.4375 5.77083C13.0347 6.8125 13.3333 7.94444 13.3333 9.16667C13.3333 11.0278 12.6875 12.6042 11.3958 13.8958C10.1042 15.1875 8.52778 15.8333 6.66667 15.8333C4.80556 15.8333 3.22917 15.1875 1.9375 13.8958C0.645833 12.6042 0 11.0278 0 9.16667C0 7.375 0.600695 5.67361 1.80208 4.0625C3.00347 2.45139 4.625 1.09722 6.66667 0Z' ),
			'lighting' => array( '0 0 13 17', 'M6.25 16.6667C5.79167 16.6667 5.39931 16.5035 5.07292 16.1771C4.74653 15.8507 4.58333 15.4583 4.58333 15H7.91667C7.91667 15.4583 7.75347 15.8507 7.42708 16.1771C7.10069 16.5035 6.70833 16.6667 6.25 16.6667ZM2.91667 14.1667V12.5H9.58333V14.1667H2.91667ZM3.125 11.6667C2.16667 11.0972 1.40625 10.3333 0.84375 9.375C0.28125 8.41667 0 7.375 0 6.25C0 4.51389 0.607639 3.03819 1.82292 1.82292C3.03819 0.607639 4.51389 0 6.25 0C7.98611 0 9.46181 0.607639 10.6771 1.82292C11.8924 3.03819 12.5 4.51389 12.5 6.25C12.5 7.375 12.2188 8.41667 11.6562 9.375C11.0938 10.3333 10.3333 11.0972 9.375 11.6667H3.125ZM3.625 10H8.875C9.5 9.55556 9.98264 9.00694 10.3229 8.35417C10.6632 7.70139 10.8333 7 10.8333 6.25C10.8333 4.97222 10.3889 3.88889 9.5 3C8.61111 2.11111 7.52778 1.66667 6.25 1.66667C4.97222 1.66667 3.88889 2.11111 3 3C2.11111 3.88889 1.66667 4.97222 1.66667 6.25C1.66667 7 1.83681 7.70139 2.17708 8.35417C2.51736 9.00694 3 9.55556 3.625 10Z' ),
			'fans' => array( '0 0 17 17', 'M7.16667 16.6667C6.45833 16.6667 5.92014 16.4549 5.55208 16.0312C5.18403 15.6076 5 15.125 5 14.5833C5 14.2222 5.07986 13.8715 5.23958 13.5312C5.39931 13.191 5.64583 12.9097 5.97917 12.6875C6.28472 12.4931 6.53125 12.2431 6.71875 11.9375C6.90625 11.6319 7.03472 11.3056 7.10417 10.9583C7.02083 10.9167 6.9375 10.875 6.85417 10.8333C6.77083 10.7917 6.69444 10.7431 6.625 10.6875L4.70833 11.375C4.47222 11.4583 4.24306 11.5278 4.02083 11.5833C3.79861 11.6389 3.56944 11.6667 3.33333 11.6667C2.45833 11.6667 1.68403 11.2847 1.01042 10.5208C0.336806 9.75694 0 8.63889 0 7.16667C0 6.45833 0.211806 5.92014 0.635417 5.55208C1.05903 5.18403 1.53472 5 2.0625 5C2.42361 5 2.77778 5.07986 3.125 5.23958C3.47222 5.39931 3.75694 5.64583 3.97917 5.97917C4.17361 6.28472 4.42361 6.53125 4.72917 6.71875C5.03472 6.90625 5.36111 7.03472 5.70833 7.10417C5.75 7.02083 5.79167 6.9375 5.83333 6.85417C5.875 6.77083 5.92361 6.69444 5.97917 6.625L5.29167 4.70833C5.20833 4.47222 5.13889 4.24306 5.08333 4.02083C5.02778 3.79861 5 3.57639 5 3.35417C5 2.46528 5.38194 1.68403 6.14583 1.01042C6.90972 0.336806 8.02778 0 9.5 0C10.2083 0 10.7465 0.211806 11.1146 0.635417C11.4826 1.05903 11.6667 1.53472 11.6667 2.0625C11.6667 2.42361 11.5868 2.77778 11.4271 3.125C11.2674 3.47222 11.0208 3.75694 10.6875 3.97917C10.3819 4.17361 10.1354 4.42361 9.94792 4.72917C9.76042 5.03472 9.63194 5.36111 9.5625 5.70833C9.64583 5.75 9.72917 5.79167 9.8125 5.83333C9.89583 5.875 9.97222 5.92361 10.0417 5.97917L11.9583 5.27083C12.1944 5.1875 12.4201 5.12153 12.6354 5.07292C12.8507 5.02431 13.0764 5 13.3125 5C14.4375 5 15.2778 5.46528 15.8333 6.39583C16.3889 7.32639 16.6667 8.36111 16.6667 9.5C16.6667 10.2083 16.4444 10.7465 16 11.1146C15.5556 11.4826 15.0625 11.6667 14.5208 11.6667C14.1736 11.6667 13.8368 11.5868 13.5104 11.4271C13.184 11.2674 12.9097 11.0208 12.6875 10.6875C12.4931 10.3819 12.2431 10.1354 11.9375 9.94792C11.6319 9.76042 11.3056 9.63194 10.9583 9.5625C10.9167 9.64583 10.875 9.72917 10.8333 9.8125C10.7917 9.89583 10.7431 9.97222 10.6875 10.0417L11.375 11.9583C11.4583 12.1806 11.5278 12.3924 11.5833 12.5938C11.6389 12.7951 11.6667 13.0069 11.6667 13.2292C11.6806 14.1319 11.3056 14.9306 10.5417 15.625C9.77778 16.3194 8.65278 16.6667 7.16667 16.6667ZM8.33333 9.58333C8.68056 9.58333 8.97569 9.46181 9.21875 9.21875C9.46181 8.97569 9.58333 8.68056 9.58333 8.33333C9.58333 7.98611 9.46181 7.69097 9.21875 7.44792C8.97569 7.20486 8.68056 7.08333 8.33333 7.08333C7.98611 7.08333 7.69097 7.20486 7.44792 7.44792C7.20486 7.69097 7.08333 7.98611 7.08333 8.33333C7.08333 8.68056 7.20486 8.97569 7.44792 9.21875C7.69097 9.46181 7.98611 9.58333 8.33333 9.58333ZM7.375 5.58333C7.45833 5.55556 7.54514 5.53125 7.63542 5.51042C7.72569 5.48958 7.8125 5.47222 7.89583 5.45833C8.00694 4.875 8.21875 4.33333 8.53125 3.83333C8.84375 3.33333 9.25694 2.91667 9.77083 2.58333C9.84028 2.52778 9.89583 2.45833 9.9375 2.375C9.97917 2.29167 10 2.1875 10 2.0625C10 1.95139 9.95833 1.85764 9.875 1.78125C9.79167 1.70486 9.66667 1.66667 9.5 1.66667C8.97222 1.66667 8.375 1.78125 7.70833 2.01042C7.04167 2.23958 6.69444 2.6875 6.66667 3.35417C6.66667 3.47917 6.68403 3.59722 6.71875 3.70833C6.75347 3.81944 6.78472 3.92361 6.8125 4.02083L7.375 5.58333ZM3.33333 10C3.52778 10 3.75694 9.95139 4.02083 9.85417L5.58333 9.29167C5.55556 9.20833 5.53125 9.12153 5.51042 9.03125C5.48958 8.94097 5.47222 8.85417 5.45833 8.77083C4.875 8.65972 4.33333 8.44792 3.83333 8.13542C3.33333 7.82292 2.91667 7.40972 2.58333 6.89583C2.52778 6.82639 2.45486 6.77083 2.36458 6.72917C2.27431 6.6875 2.17361 6.66667 2.0625 6.66667C1.9375 6.66667 1.84028 6.70833 1.77083 6.79167C1.70139 6.875 1.66667 7 1.66667 7.16667C1.66667 7.91667 1.80903 8.57639 2.09375 9.14583C2.37847 9.71528 2.79167 10 3.33333 10ZM7.16667 15C7.81944 15 8.46181 14.8681 9.09375 14.6042C9.72569 14.3403 10.0278 13.8819 10 13.2292C10 13.1181 9.98264 13.0139 9.94792 12.9167C9.91319 12.8194 9.88194 12.7292 9.85417 12.6458L9.29167 11.0833C9.20833 11.1111 9.12153 11.1354 9.03125 11.1562C8.94097 11.1771 8.85417 11.1944 8.77083 11.2083C8.65972 11.7917 8.44792 12.3333 8.13542 12.8333C7.82292 13.3333 7.40972 13.75 6.89583 14.0833C6.82639 14.1389 6.76736 14.2118 6.71875 14.3021C6.67014 14.3924 6.65278 14.4861 6.66667 14.5833C6.68056 14.6944 6.72222 14.7917 6.79167 14.875C6.86111 14.9583 6.98611 15 7.16667 15ZM14.5208 10C14.6458 10 14.7569 9.96528 14.8542 9.89583C14.9514 9.82639 15 9.69444 15 9.5C15 8.97222 14.8889 8.37153 14.6667 7.69792C14.4444 7.02431 13.9931 6.68056 13.3125 6.66667C13.1875 6.66667 13.0694 6.68056 12.9583 6.70833C12.8472 6.73611 12.7431 6.76389 12.6458 6.79167L11.0833 7.375C11.1111 7.45833 11.1354 7.54514 11.1562 7.63542C11.1771 7.72569 11.1944 7.8125 11.2083 7.89583C11.7917 8.00694 12.3333 8.21875 12.8333 8.53125C13.3333 8.84375 13.75 9.25694 14.0833 9.77083C14.125 9.84028 14.1875 9.89583 14.2708 9.9375C14.3542 9.97917 14.4375 10 14.5208 10Z' ),
			'privacy' => array( '0 0 15 14', 'M1.66667 13.3333V10H0V8.33333H1.66667V6.66667H0V5H1.66667V2.5L4.16667 0L5.83333 1.66667L7.52083 0L9.1875 1.66667L10.8542 0L13.3542 2.5V5H15V6.66667H13.3542V8.33333H15V10H13.3542V13.3333H1.66667ZM3.33333 5H5V3.1875L4.16667 2.35417L3.33333 3.1875V5ZM6.66667 5H8.33333V3.1875L7.5 2.35417L6.66667 3.1875V5ZM10.0208 5H11.6667V3.1875L10.8333 2.35417L10.0208 3.16667V5ZM3.33333 8.33333H5V6.66667H3.33333V8.33333ZM6.66667 8.33333H8.33333V6.66667H6.66667V8.33333ZM10.0208 8.33333H11.6667V6.66667H10.0208V8.33333ZM3.33333 11.6667H5V10H3.33333V11.6667ZM6.66667 11.6667H8.33333V10H6.66667V11.6667ZM10.0208 11.6667H11.6667V10H10.0208V11.6667Z' ),
			'tv' => array( '0 0 17 15', 'M5 15V13.3333H1.66667C1.20833 13.3333 0.815972 13.1701 0.489583 12.8438C0.163194 12.5174 0 12.125 0 11.6667V1.66667C0 1.20833 0.163194 0.815972 0.489583 0.489583C0.815972 0.163194 1.20833 0 1.66667 0H15C15.4583 0 15.8507 0.163194 16.1771 0.489583C16.5035 0.815972 16.6667 1.20833 16.6667 1.66667V11.6667C16.6667 12.125 16.5035 12.5174 16.1771 12.8438C15.8507 13.1701 15.4583 13.3333 15 13.3333H11.6667V15H5ZM1.66667 11.6667H15V1.66667H1.66667V11.6667Z' ),
		);
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_heading();
		$this->content_cards();
		$this->style_section();
		$this->style_heading();
		$this->style_cards();
	}

	private function content_heading() {
		$this->start_controls_section( 'sec_head', array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Make it yours', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'The Roof Is Just the <em>Beginning.</em>',
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Heading HTML tag', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array( 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ),
			)
		);
		$this->add_control(
			'accent_upright',
			array(
				'label'        => __( 'Upright accent', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'sub',
			array(
				'label'   => __( 'Short info', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'A complete outdoor room, designed around how you gather, relax, and live outside.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	private function content_cards() {
		$this->start_controls_section( 'sec_cards', array( 'label' => __( 'Cards', 'suncoast-ele-widgets' ) ) );

		$rep = new Repeater();
		$rep->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'screens',
				'options' => array(
					'screens'  => __( 'Screens', 'suncoast-ele-widgets' ),
					'heaters'  => __( 'Heaters', 'suncoast-ele-widgets' ),
					'lighting' => __( 'Lighting', 'suncoast-ele-widgets' ),
					'fans'     => __( 'Fans', 'suncoast-ele-widgets' ),
					'privacy'  => __( 'Privacy walls', 'suncoast-ele-widgets' ),
					'tv'       => __( 'TV / entertainment', 'suncoast-ele-widgets' ),
					'custom'   => __( 'Custom image / SVG', 'suncoast-ele-widgets' ),
					'none'     => __( 'None', 'suncoast-ele-widgets' ),
				),
			)
		);
		$rep->add_control(
			'icon_custom',
			array(
				'label'     => __( 'Custom icon', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'icon' => 'custom' ),
			)
		);
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Option', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'desc',
			array(
				'label'   => __( 'Description', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => '',
			)
		);
		$rep->add_control(
			'link',
			array(
				'label'       => __( 'Link (optional)', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#products',
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array( 'icon' => 'screens', 'name' => __( 'Screens', 'suncoast-ele-widgets' ), 'desc' => __( 'Wind, shade, bugs + privacy', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'heaters', 'name' => __( 'Heaters', 'suncoast-ele-widgets' ), 'desc' => __( 'Comfort on cool evenings', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'lighting', 'name' => __( 'Lighting', 'suncoast-ele-widgets' ), 'desc' => __( 'Ambient light after sunset', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'fans', 'name' => __( 'Fans', 'suncoast-ele-widgets' ), 'desc' => __( 'Airflow on warm days', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'privacy', 'name' => __( 'Privacy walls', 'suncoast-ele-widgets' ), 'desc' => __( 'Architectural separation', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'tv', 'name' => __( 'TV + entertainment', 'suncoast-ele-widgets' ), 'desc' => __( 'Designed around gathering', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->add_responsive_control(
			'cols',
			array(
				'label'     => __( 'Columns', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'selectors' => array( '{{WRAPPER}} .sce-my' => '--sce-my-cols: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function style_section() {
		$this->start_controls_section(
			'sec_style_section',
			array( 'label' => __( 'Section', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Leave a field empty to keep the Figma value (1072px content, #FBF9F4 background).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my' => '--sce-my-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_heading() {
		$this->start_controls_section(
			'sec_style_head',
			array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'eyebrow_typo', 'selector' => '{{WRAPPER}} .sce-my__eyebrow' )
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-my__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-my__title' )
		);
		$this->add_control(
			'sub_color',
			array(
				'label'     => __( 'Short info colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-my__sub' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'sub_typo', 'selector' => '{{WRAPPER}} .sce-my__sub' )
		);
		$this->add_responsive_control(
			'head_mb',
			array(
				'label'      => __( 'Space below heading', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 140 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-head-mb: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_cards() {
		$this->start_controls_section(
			'sec_style_cards',
			array( 'label' => __( 'Cards', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control(
			'icon_heading',
			array( 'label' => __( 'Icon', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING )
		);
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'       => __( 'Size', 'suncoast-ele-widgets' ),
				'description' => __( 'The design draws these at ~17px, which reads small on a wide card. Default here is 26px.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 12, 'max' => 96 ) ),
				'selectors'   => array( '{{WRAPPER}} .sce-my' => '--sce-my-icon: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'icon_color',
			array(
				'label'       => __( 'Colour', 'suncoast-ele-widgets' ),
				'description' => __( 'Applies to the built-in icons. An uploaded image keeps its own colours.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array( '{{WRAPPER}} .sce-my' => '--sce-my-icon-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_mb',
			array(
				'label'      => __( 'Space below icon', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-icon-mb: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'card_heading',
			array( 'label' => __( 'Card', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_responsive_control(
			'gap_x',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-gap-x: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'gap_y',
			array(
				'label'      => __( 'Row gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-gap-y: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my' => '--sce-my-card-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_border',
			array(
				'label'     => __( 'Border', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my' => '--sce-my-card-border: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_border_hover',
			array(
				'label'     => __( 'Border (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my' => '--sce-my-card-border-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-card-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-my' => '--sce-my-card-pad: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'text_heading',
			array( 'label' => __( 'Text', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Card heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my__name' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'name_typo', 'selector' => '{{WRAPPER}} .sce-my__name' )
		);
		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Card description', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-my__desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'desc_typo', 'selector' => '{{WRAPPER}} .sce-my__desc' )
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s   = $this->get_settings_for_display();
		$tag = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		?>
		<section class="sce-my sce-scope">
			<div class="sce-my__inner">

				<?php if ( ! empty( $s['eyebrow'] ) || ! empty( $s['title'] ) || ! empty( $s['sub'] ) ) : ?>
					<div class="sce-my__head">
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<span class="sce-my__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $s['title'] ) ) : ?>
							<?php
							printf(
								'<%1$s class="sce-my__title%2$s">',
								esc_attr( $tag ),
								'yes' === $s['accent_upright'] ? ' sce-my__title--upright' : ''
							);
							echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
							printf( '</%s>', esc_attr( $tag ) );
							?>
						<?php endif; ?>
						<?php if ( ! empty( $s['sub'] ) ) : ?>
							<p class="sce-my__sub sce-rv" style="--sce-rv-d:250ms"><?php echo esc_html( $s['sub'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['cards'] ) ) : ?>
					<ul class="sce-my__grid">
						<?php foreach ( $s['cards'] as $i => $c ) : ?>
							<?php
							$url  = ! empty( $c['link']['url'] ) ? $c['link']['url'] : '';
							$tagn = $url ? 'a' : 'div';
							$att  = 'class="sce-my__card"';
							if ( $url ) {
								$att .= ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
								if ( ! empty( $c['link']['is_external'] ) ) {
									$att .= ' target="_blank" rel="noopener"';
								}
							}
							?>
							<li class="sce-rv" style="--sce-rv-d:<?php echo (int) ( 300 + ( $i * 70 ) ); ?>ms;--sce-rv-y:18px">
								<<?php echo $tagn . ' ' . $att; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
									<?php $this->icon( $c ); ?>
									<?php if ( ! empty( $c['name'] ) ) : ?>
										<h3 class="sce-my__name"><?php echo esc_html( $c['name'] ); ?></h3>
									<?php endif; ?>
									<?php if ( ! empty( $c['desc'] ) ) : ?>
										<p class="sce-my__desc"><?php echo esc_html( $c['desc'] ); ?></p>
									<?php endif; ?>
								</<?php echo esc_html( $tagn ); ?>>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div>
		</section>
		<?php
	}

	private function icon( $c ) {
		$key = isset( $c['icon'] ) ? $c['icon'] : 'screens';
		if ( 'none' === $key ) {
			return;
		}
		echo '<span class="sce-my__icon" aria-hidden="true">';
		if ( 'custom' === $key ) {
			if ( ! empty( $c['icon_custom']['url'] ) ) {
				printf( '<img src="%s" alt="" decoding="async">', esc_url( $c['icon_custom']['url'] ) );
			}
		} else {
			$g = self::glyphs();
			if ( isset( $g[ $key ] ) ) {
				printf(
					'<svg viewBox="%s" fill="none" aria-hidden="true" focusable="false"><path d="%s" fill="currentColor"/></svg>',
					esc_attr( $g[ $key ][0] ),
					esc_attr( $g[ $key ][1] )
				);
			}
		}
		echo '</span>';
	}
}
