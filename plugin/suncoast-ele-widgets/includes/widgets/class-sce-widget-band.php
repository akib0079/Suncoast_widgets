<?php
/**
 * Widget: Suncoast Benefits Band (dark).
 *
 * Eyebrow + mixed-face heading over a four-up card row on a #1A1A1A band.
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

class SCE_Widget_Band extends Widget_Base {

	public function get_name() {
		return 'suncoast_band';
	}

	public function get_title() {
		return __( 'Suncoast Benefits Band', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'benefits', 'band', 'dark', 'features', 'icons' );
	}

	public function get_style_depends() {
		return array( 'sce-band' );
	}

	public function get_script_depends() {
		return array( 'sce-band' );
	}

	/**
	 * The four Figma glyphs, inlined so they recolour with the icon control
	 * and cost no extra request. The tile (46x46, radius 12, gold @10% on a
	 * white @10% hairline) is drawn in CSS, so only the glyph lives here.
	 */
	private static function glyphs() {
		return array(
			'rain' => 'M23.275 30C23.475 29.9833 23.6458 29.9042 23.7875 29.7625C23.9292 29.6208 24 29.45 24 29.25C24 29.0167 23.925 28.8292 23.775 28.6875C23.625 28.5458 23.4333 28.4833 23.2 28.5C22.5167 28.55 21.7917 28.3625 21.025 27.9375C20.2583 27.5125 19.775 26.7417 19.575 25.625C19.5417 25.4417 19.4542 25.2917 19.3125 25.175C19.1708 25.0583 19.0083 25 18.825 25C18.5917 25 18.4 25.0875 18.25 25.2625C18.1 25.4375 18.05 25.6417 18.1 25.875C18.3833 27.3917 19.05 28.475 20.1 29.125C21.15 29.775 22.2083 30.0667 23.275 30ZM23 33C20.7167 33 18.8125 32.2167 17.2875 30.65C15.7625 29.0833 15 27.1333 15 24.8C15 23.1333 15.6625 21.3208 16.9875 19.3625C18.3125 17.4042 20.3167 15.2833 23 13C25.6833 15.2833 27.6875 17.4042 29.0125 19.3625C30.3375 21.3208 31 23.1333 31 24.8C31 27.1333 30.2375 29.0833 28.7125 30.65C27.1875 32.2167 25.2833 33 23 33ZM23 31C24.7333 31 26.1667 30.4125 27.3 29.2375C28.4333 28.0625 29 26.5833 29 24.8C29 23.5833 28.4958 22.2083 27.4875 20.675C26.4792 19.1417 24.9833 17.4667 23 15.65C21.0167 17.4667 19.5208 19.1417 18.5125 20.675C17.5042 22.2083 17 23.5833 17 24.8C17 26.5833 17.5667 28.0625 18.7 29.2375C19.8333 30.4125 21.2667 31 23 31Z',
			'alum' => 'M17.75 32L17.5 29.8L20.35 21.95C20.6 22.1833 20.8708 22.3792 21.1625 22.5375C21.4542 22.6958 21.7667 22.8167 22.1 22.9L19.35 30.45L17.75 32ZM28.25 32L26.65 30.45L23.9 22.9C24.2333 22.8167 24.5458 22.6958 24.8375 22.5375C25.1292 22.3792 25.4 22.1833 25.65 21.95L28.5 29.8L28.25 32ZM23 22C22.1667 22 21.4583 21.7083 20.875 21.125C20.2917 20.5417 20 19.8333 20 19C20 18.35 20.1875 17.7708 20.5625 17.2625C20.9375 16.7542 21.4167 16.4 22 16.2V14H24V16.2C24.5833 16.4 25.0625 16.7542 25.4375 17.2625C25.8125 17.7708 26 18.35 26 19C26 19.8333 25.7083 20.5417 25.125 21.125C24.5417 21.7083 23.8333 22 23 22ZM23 20C23.2833 20 23.5208 19.9042 23.7125 19.7125C23.9042 19.5208 24 19.2833 24 19C24 18.7167 23.9042 18.4792 23.7125 18.2875C23.5208 18.0958 23.2833 18 23 18C22.7167 18 22.4792 18.0958 22.2875 18.2875C22.0958 18.4792 22 18.7167 22 19C22 19.2833 22.0958 19.5208 22.2875 19.7125C22.4792 19.9042 22.7167 20 23 20Z',
			'vent' => 'M22.5 31.5C21.6667 31.5 20.9583 31.2083 20.375 30.625C19.7917 30.0417 19.5 29.3333 19.5 28.5H21.5C21.5 28.7833 21.5958 29.0208 21.7875 29.2125C21.9792 29.4042 22.2167 29.5 22.5 29.5C22.7833 29.5 23.0208 29.4042 23.2125 29.2125C23.4042 29.0208 23.5 28.7833 23.5 28.5C23.5 28.2167 23.4042 27.9792 23.2125 27.7875C23.0208 27.5958 22.7833 27.5 22.5 27.5H13V25.5H22.5C23.3333 25.5 24.0417 25.7917 24.625 26.375C25.2083 26.9583 25.5 27.6667 25.5 28.5C25.5 29.3333 25.2083 30.0417 24.625 30.625C24.0417 31.2083 23.3333 31.5 22.5 31.5ZM13 21.5V19.5H26.5C26.9333 19.5 27.2917 19.3583 27.575 19.075C27.8583 18.7917 28 18.4333 28 18C28 17.5667 27.8583 17.2083 27.575 16.925C27.2917 16.6417 26.9333 16.5 26.5 16.5C26.0667 16.5 25.7083 16.6417 25.425 16.925C25.1417 17.2083 25 17.5667 25 18H23C23 17.0167 23.3375 16.1875 24.0125 15.5125C24.6875 14.8375 25.5167 14.5 26.5 14.5C27.4833 14.5 28.3125 14.8375 28.9875 15.5125C29.6625 16.1875 30 17.0167 30 18C30 18.9833 29.6625 19.8125 28.9875 20.4875C28.3125 21.1625 27.4833 21.5 26.5 21.5H13ZM29.5 29.5V27.5C29.9333 27.5 30.2917 27.3583 30.575 27.075C30.8583 26.7917 31 26.4333 31 26C31 25.5667 30.8583 25.2083 30.575 24.925C30.2917 24.6417 29.9333 24.5 29.5 24.5H13V22.5H29.5C30.4833 22.5 31.3125 22.8375 31.9875 23.5125C32.6625 24.1875 33 25.0167 33 26C33 26.9833 32.6625 27.8125 31.9875 28.4875C31.3125 29.1625 30.4833 29.5 29.5 29.5Z',
			'snow' => 'M22 33V28.85L18.75 32.05L17.35 30.65L22 26V24H20L15.35 28.65L13.95 27.25L17.15 24H13V22H17.15L13.95 18.75L15.35 17.35L20 22H22V20L17.35 15.35L18.75 13.95L22 17.15V13H24V17.15L27.25 13.95L28.65 15.35L24 20V22H26L30.65 17.35L32.05 18.75L28.85 22H33V24H28.85L32.05 27.25L30.65 28.65L26 24H24V26L28.65 30.65L27.25 32.05L24 28.85V33H22Z',
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
				'default' => __( 'Designed for Northwest living', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Because Around Here, a Patio Cover<br>Needs to Handle More Than <em>Sunshine.</em>',
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
				'description'  => __( 'Off (default) matches the design: serif italic.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
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
				'default' => 'rain',
				'options' => array(
					'rain'   => __( 'Droplet (Rain management)', 'suncoast-ele-widgets' ),
					'alum'   => __( 'Gauge (Aluminum)', 'suncoast-ele-widgets' ),
					'vent'   => __( 'Airflow (Ventilation)', 'suncoast-ele-widgets' ),
					'snow'   => __( 'Snowflake (More seasons)', 'suncoast-ele-widgets' ),
					'custom' => __( 'Custom image / SVG', 'suncoast-ele-widgets' ),
					'none'   => __( 'None', 'suncoast-ele-widgets' ),
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
				'default' => __( 'Benefit', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'desc',
			array(
				'label'   => __( 'Description', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => '',
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
					array( 'icon' => 'rain', 'name' => __( 'Rain management', 'suncoast-ele-widgets' ), 'desc' => __( 'Integrated guttering', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'alum', 'name' => __( 'Aluminum', 'suncoast-ele-widgets' ), 'desc' => __( 'Durable + low maintenance', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'vent', 'name' => __( 'Ventilation', 'suncoast-ele-widgets' ), 'desc' => __( 'Adjustable airflow', 'suncoast-ele-widgets' ) ),
					array( 'icon' => 'snow', 'name' => __( 'More seasons', 'suncoast-ele-widgets' ), 'desc' => __( 'Screens, heat + light', 'suncoast-ele-widgets' ) ),
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
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-cols: {{VALUE}};' ),
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1070px content, #1A1A1A band, 94/120 padding).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-pad-bottom: {{SIZE}}{{UNIT}};' ),
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
				'selectors' => array( '{{WRAPPER}} .sce-band__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'eyebrow_typo', 'selector' => '{{WRAPPER}} .sce-band__eyebrow' )
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-band__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-band__title' )
		);
		$this->add_responsive_control(
			'head_w',
			array(
				'label'      => __( 'Heading column width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 300, 'max' => 1200 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-head-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'head_mb',
			array(
				'label'      => __( 'Space below heading', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-head-mb: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_cards() {
		$this->start_controls_section(
			'sec_style_cards',
			array( 'label' => __( 'Cards', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-card-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-card-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-card-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-card-pad: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'icon_heading',
			array( 'label' => __( 'Icon', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'Tile size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 96 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-icon: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'icon_radius',
			array(
				'label'      => __( 'Tile radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-band' => '--sce-band-icon-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Tile background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-icon-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'icon_ring',
			array(
				'label'     => __( 'Tile hairline', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band' => '--sce-band-icon-ring: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Glyph colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band__icon' => 'color: {{VALUE}};' ),
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
				'selectors' => array( '{{WRAPPER}} .sce-band__name' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'name_typo', 'selector' => '{{WRAPPER}} .sce-band__name' )
		);
		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Card description', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-band__desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'desc_typo', 'selector' => '{{WRAPPER}} .sce-band__desc' )
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
		<section class="sce-band sce-scope">
			<div class="sce-band__inner">

				<?php if ( ! empty( $s['eyebrow'] ) || ! empty( $s['title'] ) ) : ?>
					<div class="sce-band__head">
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<span class="sce-band__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $s['title'] ) ) : ?>
							<?php
							printf(
								'<%1$s class="sce-band__title%2$s">',
								esc_attr( $tag ),
								'yes' === $s['accent_upright'] ? ' sce-band__title--upright' : ''
							);
							echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
							printf( '</%s>', esc_attr( $tag ) );
							?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['cards'] ) ) : ?>
					<ul class="sce-band__grid">
						<?php foreach ( $s['cards'] as $i => $c ) : ?>
							<li class="sce-band__card sce-rv" style="--sce-rv-d:<?php echo (int) ( 280 + ( $i * 90 ) ); ?>ms;--sce-rv-y:20px">
								<?php $this->icon( $c ); ?>
								<?php if ( ! empty( $c['name'] ) ) : ?>
									<h3 class="sce-band__name"><?php echo esc_html( $c['name'] ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $c['desc'] ) ) : ?>
									<p class="sce-band__desc"><?php echo esc_html( $c['desc'] ); ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div>
		</section>
		<?php
	}

	private function icon( $c ) {
		$key = isset( $c['icon'] ) ? $c['icon'] : 'rain';
		if ( 'none' === $key ) {
			return;
		}
		echo '<span class="sce-band__icon" aria-hidden="true">';
		if ( 'custom' === $key ) {
			if ( ! empty( $c['icon_custom']['url'] ) ) {
				printf( '<img src="%s" alt="" decoding="async">', esc_url( $c['icon_custom']['url'] ) );
			}
		} else {
			$glyphs = self::glyphs();
			if ( isset( $glyphs[ $key ] ) ) {
				printf(
					'<svg viewBox="0 0 46 46" fill="none" aria-hidden="true" focusable="false"><path d="%s" fill="currentColor"/></svg>',
					esc_attr( $glyphs[ $key ] )
				);
			}
		}
		echo '</span>';
	}
}
