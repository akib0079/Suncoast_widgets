<?php
/**
 * Widget: Suncoast Header.
 *
 * Every style control writes a CSS custom property rather than a plain
 * declaration, because sce-header.js may re-home `.sce-header__layer` onto
 * <body> (Elementor sections frequently carry a transform, which silently
 * breaks `position:fixed`). The JS copies the resolved variables across; plain
 * declarations scoped to {{WRAPPER}} would be left behind.
 *
 * Style controls deliberately ship with NO defaults: untouched means the
 * hard-scoped CSS supplies the Figma values, including its per-breakpoint ones.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class SCE_Widget_Header extends Widget_Base {

	const LOGO    = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Link-1-1.png';
	const WRAPPER = '{{WRAPPER}} .sce-header, {{WRAPPER}} .sce-header__layer';

	public function get_name() {
		return 'suncoast_header';
	}

	public function get_title() {
		return __( 'Suncoast Header', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'header', 'navbar', 'menu', 'sticky', 'logo' );
	}

	public function get_style_depends() {
		return array( 'sce-header' );
	}

	public function get_script_depends() {
		return array( 'sce-header' );
	}

	/** The header is a singleton bar — reloading the page on save is fine. */
	public function get_custom_help_url() {
		return '';
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_logo();
		$this->content_menu();
		$this->content_actions();
		$this->content_behaviour();

		$this->style_bar();
		$this->style_logo();
		$this->style_nav();
		$this->style_actions();
		$this->style_panel();
	}

	/* ------------------------------------------------------------ content */

	private function content_logo() {
		$this->start_controls_section(
			'sec_logo',
			array( 'label' => __( 'Logo', 'suncoast-ele-widgets' ) )
		);

		$this->add_control(
			'logo',
			array(
				'label'   => __( 'Logo', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => self::LOGO ),
			)
		);

		$this->add_control(
			'logo_mobile',
			array(
				'label'       => __( 'Mobile logo', 'suncoast-ele-widgets' ),
				'description' => __( 'Optional. Used below 768px — handy if you want the lockup without “Portland PNW”.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'logo_alt',
			array(
				'label'   => __( 'Alt text', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Suncoast Enclosures — Portland PNW', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'         => __( 'Logo link', 'suncoast-ele-widgets' ),
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#top' ),
				'placeholder'   => '#top',
				'options'       => array( 'url', 'is_external', 'nofollow' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_menu() {
		$this->start_controls_section(
			'sec_menu',
			array( 'label' => __( 'Menu', 'suncoast-ele-widgets' ) )
		);

		$this->add_control(
			'menu_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use in-page anchors such as <code>#products</code> to scroll to a section. Give the target Elementor section that CSS ID.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'text',
			array(
				'label'   => __( 'Label', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Menu item', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'placeholder' => '#section-id',
			)
		);

		$this->add_control(
			'menu',
			array(
				'label'       => __( 'Menu items', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Products', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#products' ) ),
					array( 'text' => __( 'Projects', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#projects' ) ),
					array( 'text' => __( 'Process', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#process' ) ),
					array( 'text' => __( 'Pricing & FAQ', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#pricing' ) ),
				),
			)
		);

		$this->add_control(
			'mobile_menu_source',
			array(
				'label'   => __( 'Mobile menu', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'same',
				'options' => array(
					'same'   => __( 'Same as desktop', 'suncoast-ele-widgets' ),
					'custom' => __( 'Different items', 'suncoast-ele-widgets' ),
				),
			)
		);

		$this->add_control(
			'mobile_menu',
			array(
				'label'       => __( 'Mobile items', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'mobile_menu_source' => 'custom' ),
				'default'     => array(
					array( 'text' => __( 'Home', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#top' ) ),
					array( 'text' => __( 'Products', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#products' ) ),
					array( 'text' => __( 'Explore', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#projects' ) ),
					array( 'text' => __( 'Locations', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#process' ) ),
					array( 'text' => __( 'Contact', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#quote' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function content_actions() {
		$this->start_controls_section(
			'sec_actions',
			array( 'label' => __( 'Phone & CTA', 'suncoast-ele-widgets' ) )
		);

		$this->add_control(
			'show_phone',
			array(
				'label'        => __( 'Show phone', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'phone_text',
			array(
				'label'     => __( 'Phone label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '1-877-449-5106',
				'condition' => array( 'show_phone' => 'yes' ),
			)
		);
		$this->add_control(
			'phone_number',
			array(
				'label'       => __( 'Dial number', 'suncoast-ele-widgets' ),
				'description' => __( 'Digits only — used for the tel: link.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '18774495106',
				'condition'   => array( 'show_phone' => 'yes' ),
			)
		);

		$this->add_control(
			'show_cta',
			array(
				'label'        => __( 'Show CTA button', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);
		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'CTA label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Request a Quote', 'suncoast-ele-widgets' ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label'       => __( 'CTA link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#quote' ),
				'placeholder' => '#quote',
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_behaviour() {
		$this->start_controls_section(
			'sec_behaviour',
			array( 'label' => __( 'Behaviour', 'suncoast-ele-widgets' ) )
		);

		$this->add_control(
			'stick_at',
			array(
				'label'       => __( 'Compress after (px scrolled)', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 8,
				'min'         => 0,
				'max'         => 600,
			)
		);

		$this->add_control(
			'scrollspy',
			array(
				'label'        => __( 'Highlight active section', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'anchor_offset',
			array(
				'label'       => __( 'Extra anchor gap (px)', 'suncoast-ele-widgets' ),
				'description' => __( 'Breathing room below the bar when a nav link scrolls to its section.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 12,
				'min'         => 0,
				'max'         => 200,
			)
		);

		$this->add_control(
			'hide_on_scroll',
			array(
				'label'        => __( 'Hide when scrolling down', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'overlap_next',
			array(
				'label'        => __( 'Overlay the next section', 'suncoast-ele-widgets' ),
				'description'  => __( 'Off (Figma default) the banner starts below the bar. On, the banner slides up underneath so the blur reads against the photo from the start — set the containing section padding to 0.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'selectors'    => array(
					self::WRAPPER => '--sce-hdr-overlap: var(--sce-hdr-h);',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'     => __( 'Z-index', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'selectors' => array( self::WRAPPER => '--sce-hdr-z: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------- style */

	private function style_bar() {
		$this->start_controls_section(
			'sec_style_bar',
			array(
				'label' => __( 'Bar', 'suncoast-ele-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_defaults_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Leave a field empty to keep the Figma value (81px bar, 72px compressed, 48px gutter).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_responsive_control(
			'bar_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 160 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-h: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'bar_h_stuck',
			array(
				'label'      => __( 'Height when compressed', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 160 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-h-stuck: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'shell_max',
			array(
				'label'      => __( 'Content max width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 960, 'max' => 1920 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-shell-max: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->start_controls_tabs( 'bar_tabs' );

		$this->start_controls_tab( 'bar_tab_rest', array( 'label' => __( 'At rest', 'suncoast-ele-widgets' ) ) );
		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'bar_blur',
			array(
				'label'      => __( 'Backdrop blur', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-blur: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'bar_tab_stuck', array( 'label' => __( 'Compressed', 'suncoast-ele-widgets' ) ) );
		$this->add_control(
			'bar_bg_stuck',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-bg-stuck: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'bar_blur_stuck',
			array(
				'label'      => __( 'Backdrop blur', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-blur-stuck: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'bar_border_stuck',
			array(
				'label'     => __( 'Hairline border', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-border-stuck: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'bar_shadow_stuck',
			array(
				'label'       => __( 'Shadow', 'suncoast-ele-widgets' ),
				'description' => __( 'Full CSS box-shadow value.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '0 10px 34px -18px rgba(24,18,5,.42)',
				'selectors'   => array( self::WRAPPER => '--sce-hdr-shadow-stuck: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	private function style_logo() {
		$this->start_controls_section(
			'sec_style_logo',
			array(
				'label' => __( 'Logo', 'suncoast-ele-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_h',
			array(
				'label'       => __( 'Height', 'suncoast-ele-widgets' ),
				'description' => __( 'Figma pins the lockup at 32px tall; width follows the asset ratio.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 14, 'max' => 80 ) ),
				'selectors'   => array( self::WRAPPER => '--sce-hdr-logo-h: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'logo_h_panel',
			array(
				'label'      => __( 'Height inside mobile panel', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 64 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-logo-h-panel: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function style_nav() {
		$this->start_controls_section(
			'sec_style_nav',
			array(
				'label' => __( 'Navigation', 'suncoast-ele-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'nav_gap',
			array(
				'label'      => __( 'Gap between items', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 80 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-nav-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'nav_size',
			array(
				'label'      => __( 'Font size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 9, 'max' => 24 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-nav-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'nav_weight',
			array(
				'label'     => __( 'Font weight', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array( '' => __( 'Default (500)', 'suncoast-ele-widgets' ), '400' => '400', '500' => '500', '600' => '600', '700' => '700' ),
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-weight: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'nav_ls',
			array(
				'label'      => __( 'Letter spacing', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => -2, 'max' => 6, 'step' => 0.05 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-nav-ls: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'nav_tt',
			array(
				'label'     => __( 'Text transform', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''           => __( 'Default (uppercase)', 'suncoast-ele-widgets' ),
					'none'       => __( 'None', 'suncoast-ele-widgets' ),
					'uppercase'  => __( 'Uppercase', 'suncoast-ele-widgets' ),
					'capitalize' => __( 'Capitalize', 'suncoast-ele-widgets' ),
				),
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-tt: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => __( 'Colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'nav_hover',
			array(
				'label'     => __( 'Hover colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'nav_active',
			array(
				'label'     => __( 'Active colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-active: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'nav_underline',
			array(
				'label'     => __( 'Underline colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-nav-underline: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function style_actions() {
		$this->start_controls_section(
			'sec_style_actions',
			array(
				'label' => __( 'Phone & CTA', 'suncoast-ele-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'phone_heading',
			array( 'label' => __( 'Phone', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING )
		);
		$this->add_control(
			'phone_color',
			array(
				'label'     => __( 'Text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-phone-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'phone_hover',
			array(
				'label'     => __( 'Hover colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-phone-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'phone_icon_color',
			array(
				'label'     => __( 'Icon colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-phone-icon: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'phone_size',
			array(
				'label'      => __( 'Font size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 28 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-phone-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'cta_heading',
			array( 'label' => __( 'CTA button', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'cta_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-cta-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'cta_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-cta-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-cta-color: {{VALUE}}; --sce-cta-color-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-cta-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cta_w',
			array(
				'label'      => __( 'Minimum width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 360 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-cta-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cta_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 80 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-cta-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cta_size',
			array(
				'label'      => __( 'Font size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 9, 'max' => 22 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-cta-size: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function style_panel() {
		$this->start_controls_section(
			'sec_style_panel',
			array(
				'label' => __( 'Mobile panel', 'suncoast-ele-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-panel-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'panel_w',
			array(
				'label'      => __( 'Width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 260, 'max' => 900 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-panel-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'panel_link',
			array(
				'label'     => __( 'Link colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-panel-link: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'panel_link_hover',
			array(
				'label'     => __( 'Link colour (hover / active)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-panel-link-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'panel_size',
			array(
				'label'      => __( 'Link font size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 11, 'max' => 32 ) ),
				'selectors'  => array( self::WRAPPER => '--sce-hdr-panel-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'scrim_color',
			array(
				'label'     => __( 'Overlay colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-scrim: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'burger_color',
			array(
				'label'     => __( 'Hamburger colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( self::WRAPPER => '--sce-hdr-burger-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s = $this->get_settings_for_display();

		$logo        = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : self::LOGO;
		$logo_mobile = ! empty( $s['logo_mobile']['url'] ) ? $s['logo_mobile']['url'] : '';
		$alt         = ! empty( $s['logo_alt'] ) ? $s['logo_alt'] : get_bloginfo( 'name' );

		$desktop_menu = is_array( $s['menu'] ) ? $s['menu'] : array();
		$mobile_menu  = ( 'custom' === $s['mobile_menu_source'] && ! empty( $s['mobile_menu'] ) )
			? $s['mobile_menu']
			: $desktop_menu;

		$this->add_render_attribute(
			'root',
			array(
				'class'              => 'sce-header sce-scope',
				'data-stick-at'      => (string) ( '' !== $s['stick_at'] ? (int) $s['stick_at'] : 8 ),
				'data-hide-on-scroll' => 'yes' === $s['hide_on_scroll'] ? 'yes' : 'no',
				'data-scrollspy'     => 'yes' === $s['scrollspy'] ? 'yes' : 'no',
				'data-anchor-offset' => (string) ( '' !== $s['anchor_offset'] ? (int) $s['anchor_offset'] : 12 ),
			)
		);

		$panel_id = 'sce-panel-' . $this->get_id();
		?>
		<header <?php $this->print_render_attribute_string( 'root' ); ?>>
			<?php /* Fixed layer — sce-header.js may move this whole node to <body>. */ ?>
			<div class="sce-header__layer sce-scope">

				<div class="sce-header__bar">
					<div class="sce-header__inner sce-shell">

						<?php $this->render_brand( $s, $logo, $logo_mobile, $alt ); ?>

						<?php if ( $desktop_menu ) : ?>
							<nav class="sce-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'suncoast-ele-widgets' ); ?>">
								<ul class="sce-header__menu">
									<?php foreach ( $desktop_menu as $i => $item ) : ?>
										<li><?php $this->render_link( $item, 'sce-header__nav-link', 'd' . $i ); ?></li>
									<?php endforeach; ?>
								</ul>
							</nav>
						<?php endif; ?>

						<div class="sce-header__actions">
							<?php if ( 'yes' === $s['show_phone'] ) : ?>
								<?php $this->render_phone( $s ); ?>
							<?php endif; ?>

							<?php if ( 'yes' === $s['show_cta'] && ! empty( $s['cta_text'] ) ) : ?>
								<?php $this->render_link( array( 'text' => $s['cta_text'], 'link' => $s['cta_link'] ), 'sce-btn sce-header__cta', 'cta' ); ?>
							<?php endif; ?>

							<?php if ( $mobile_menu ) : ?>
								<button class="sce-header__burger" type="button"
								        aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
									<span class="sce-sr"><?php esc_html_e( 'Open menu', 'suncoast-ele-widgets' ); ?></span>
									<?php $this->icon_burger(); ?>
								</button>
							<?php endif; ?>
						</div>

					</div>
				</div>

				<?php if ( $mobile_menu ) : ?>
					<div class="sce-header__scrim" role="presentation"></div>

					<div class="sce-header__panel" id="<?php echo esc_attr( $panel_id ); ?>"
					     role="dialog" aria-modal="true"
					     aria-label="<?php esc_attr_e( 'Site menu', 'suncoast-ele-widgets' ); ?>"
					     aria-hidden="true" inert>

						<div class="sce-header__panel-top">
							<img class="sce-header__panel-logo"
							     src="<?php echo esc_url( $logo_mobile ? $logo_mobile : $logo ); ?>"
							     alt="<?php echo esc_attr( $alt ); ?>" decoding="async">
							<button class="sce-header__close" type="button">
								<span class="sce-sr"><?php esc_html_e( 'Close menu', 'suncoast-ele-widgets' ); ?></span>
								<?php $this->icon_close(); ?>
							</button>
						</div>

						<nav aria-label="<?php esc_attr_e( 'Mobile', 'suncoast-ele-widgets' ); ?>">
							<ul class="sce-header__panel-menu">
								<?php foreach ( $mobile_menu as $i => $item ) : ?>
									<li style="--i:<?php echo (int) $i; ?>">
										<?php $this->render_link( $item, 'sce-header__panel-link', 'm' . $i ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>

						<div class="sce-header__panel-foot">
							<?php if ( 'yes' === $s['show_phone'] ) : ?>
								<?php $this->render_phone( $s, 'p' ); ?>
							<?php endif; ?>
							<?php if ( 'yes' === $s['show_cta'] && ! empty( $s['cta_text'] ) ) : ?>
								<?php $this->render_link( array( 'text' => $s['cta_text'], 'link' => $s['cta_link'] ), 'sce-btn', 'ctap' ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</header>
		<?php
	}

	/* ------------------------------------------------------------ partials */

	private function render_brand( $s, $logo, $logo_mobile, $alt ) {
		$key = 'brand';
		$this->add_render_attribute( $key, 'class', 'sce-header__brand' );
		if ( $logo_mobile ) {
			$this->add_render_attribute( $key, 'class', 'has-mobile-logo' );
		}
		$this->add_render_attribute( $key, 'aria-label', $alt );

		$url = ! empty( $s['logo_link']['url'] ) ? $s['logo_link']['url'] : home_url( '/' );
		$this->add_render_attribute( $key, 'href', esc_url( $url ) );
		if ( ! empty( $s['logo_link']['is_external'] ) ) {
			$this->add_render_attribute( $key, 'target', '_blank' );
			$this->add_render_attribute( $key, 'rel', 'noopener' );
		}
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<img class="sce-header__logo sce-header__logo--desktop"
			     src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $alt ); ?>"
			     fetchpriority="high" decoding="async">
			<?php if ( $logo_mobile ) : ?>
				<img class="sce-header__logo sce-header__logo--mobile"
				     src="<?php echo esc_url( $logo_mobile ); ?>" alt="" aria-hidden="true" decoding="async">
			<?php endif; ?>
		</a>
		<?php
	}

	private function render_phone( $s, $suffix = '' ) {
		$digits = preg_replace( '/[^\d+]/', '', (string) $s['phone_number'] );
		?>
		<a class="sce-header__phone" href="tel:<?php echo esc_attr( $digits ); ?>">
			<?php $this->icon_phone(); ?>
			<span class="sce-header__phone-txt"><?php echo esc_html( $s['phone_text'] ); ?></span>
			<?php if ( '' === $suffix ) : ?>
				<span class="sce-sr"><?php echo esc_html( $s['phone_text'] ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}

	/**
	 * Render one link from a repeater row.
	 *
	 * @param array  $item  Row with `text` and `link`.
	 * @param string $class CSS classes.
	 * @param string $key   Unique render-attribute key.
	 */
	private function render_link( $item, $class, $key ) {
		$key = 'lnk_' . $key;
		$url = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#';

		$this->add_render_attribute( $key, 'class', $class );
		// Anchors must stay raw so the JS can match them; esc_url() would mangle nothing
		// here but esc_attr keeps intent clear for in-page hrefs.
		$this->add_render_attribute( $key, 'href', 0 === strpos( $url, '#' ) ? $url : esc_url( $url ) );

		if ( ! empty( $item['link']['is_external'] ) ) {
			$this->add_render_attribute( $key, 'target', '_blank' );
		}
		if ( ! empty( $item['link']['nofollow'] ) ) {
			$this->add_render_attribute( $key, 'rel', 'nofollow' );
		}
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $item['text'] ); ?></a>
		<?php
	}

	/* --------------------------------------------------------------- icons */

	private function icon_phone() {
		?>
		<svg class="sce-header__phone-ico" viewBox="0 0 13 19" fill="none" aria-hidden="true" focusable="false">
			<path d="M9.35836 0.65H3.40002C1.88124 0.65 0.650024 1.88122 0.650024 3.4V14.8583C0.650024 16.3771 1.88124 17.6083 3.40002 17.6083H9.35836C10.8771 17.6083 12.1084 16.3771 12.1084 14.8583V3.4C12.1084 1.88122 10.8771 0.65 9.35836 0.65Z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M5.46252 14.4H7.29586" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php
	}

	private function icon_burger() {
		?>
		<svg class="sce-header__burger-ico" viewBox="0 0 26 18" fill="none" aria-hidden="true" focusable="false">
			<line y1="1" x2="26" y2="1"></line>
			<line y1="9" x2="26" y2="9"></line>
			<line y1="17" x2="26" y2="17"></line>
		</svg>
		<?php
	}

	private function icon_close() {
		?>
		<svg viewBox="0 0 15 15" fill="none" aria-hidden="true" focusable="false">
			<line x1="1.41557" y1="0.707078" x2="14.1435" y2="13.435"></line>
			<line x1="14.1421" y1="0.707229" x2="0.707105" y2="14.1423"></line>
		</svg>
		<?php
	}
}
