<?php
/**
 * Widget: Suncoast Hero Banner (content + lead form + marquee).
 *
 * Unlike the header, nothing here is ever re-homed, so controls can use plain
 * {{WRAPPER}} declarations and Elementor's native typography groups.
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

class SCE_Widget_Banner extends Widget_Base {

	const HERO = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/1.-Hero-Section.png';

	public function get_name() {
		return 'suncoast_banner';
	}

	public function get_title() {
		return __( 'Suncoast Hero Banner', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'hero', 'banner', 'form', 'marquee', 'ticker' );
	}

	public function get_style_depends() {
		// The Pro-form skin is enqueued from render() instead, where the
		// settings are guaranteed to be parsed.
		return array( 'sce-banner' );
	}

	public function get_script_depends() {
		return array( 'sce-banner' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_background();
		$this->content_main();
		$this->content_card();
		$this->content_form();
		$this->content_marquee();

		$this->style_content();
		$this->style_card();
		$this->style_fields();
		$this->style_marquee();
	}

	/* ------------------------------------------------------------ content */

	private function content_background() {
		$this->start_controls_section( 'sec_bg', array( 'label' => __( 'Background', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'bg_image',
			array(
				'label'   => __( 'Hero image', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => self::HERO ),
			)
		);

		$this->add_control(
			'bg_position',
			array(
				'label'     => __( 'Focal point', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''              => __( 'Centre', 'suncoast-ele-widgets' ),
					'center top'    => __( 'Top', 'suncoast-ele-widgets' ),
					'center bottom' => __( 'Bottom', 'suncoast-ele-widgets' ),
					'left center'   => __( 'Left', 'suncoast-ele-widgets' ),
					'right center'  => __( 'Right', 'suncoast-ele-widgets' ),
				),
				'selectors' => array( '{{WRAPPER}} .sce-banner__img' => 'object-position: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'min_h',
			array(
				'label'       => __( 'Minimum height', 'suncoast-ele-widgets' ),
				'description' => __( 'Figma: 721px. Leave empty to keep it.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'vh' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 1200 ), 'vh' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors'   => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-min-h: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'pad_block',
			array(
				'label'      => __( 'Vertical padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-pad-block: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'       => __( 'Overlay strength', 'suncoast-ele-widgets' ),
				'description' => __( 'The supplied hero already has darkening baked in, so 1 matches Figma.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '' ),
				'range'       => array( '' => array( 'min' => 0, 'max' => 1.6, 'step' => 0.05 ) ),
				'selectors'   => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-overlay-opacity: {{SIZE}};' ),
			)
		);

		$this->add_control(
			'overlay_tint',
			array(
				'label'       => __( 'Overlay tint', 'suncoast-ele-widgets' ),
				'description' => __( 'Optional flat colour behind the gradients.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-tint: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_main() {
		$this->start_controls_section( 'sec_main', array( 'label' => __( 'Content', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Motorized Louvered Pergolas • Portland', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'suncoast-ele-widgets' ),
				'description' => __( 'Use &lt;br&gt; to split lines — each line gets its own masked reveal. Wrap the accent words in &lt;em&gt; for the gold italic.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Open to the Sky.<br>Closed to the <em>Rain.</em>',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML tag', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( "Engineered for the Pacific Northwest. Seamlessly control your outdoor environment with precision-crafted louvers that adapt to Portland and Vancouver's unpredictable weather.", 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'checklist',
			array(
				'label'       => __( 'Checklist', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Precision remote-controlled louvers', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Integrated hidden gutter system', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Aircraft-grade extruded aluminum', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Custom engineered for high snow/wind loads', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function content_card() {
		$this->start_controls_section( 'sec_card', array( 'label' => __( 'Form card', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'card_anchor',
			array(
				'label'       => __( 'Anchor ID', 'suncoast-ele-widgets' ),
				'description' => __( 'The header CTA scrolls here. Default: quote', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'quote',
			)
		);
		$this->add_control(
			'card_eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Free Consultation', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'card_title',
			array(
				'label'   => __( 'Title', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( "Let's Design Your Outdoor Space", 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'card_sub',
			array(
				'label'   => __( 'Subtitle', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Request a free site consultation.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'card_note',
			array(
				'label'   => __( 'Footnote', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'No obligation. Secure & confidential.', 'suncoast-ele-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_form() {
		$this->start_controls_section( 'sec_form', array( 'label' => __( 'Form', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'form_source',
			array(
				'label'   => __( 'Form built with', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'builtin',
				'options' => array(
					'builtin'   => __( 'Built-in (stores leads + e-mails admin)', 'suncoast-ele-widgets' ),
					'elementor' => __( 'Elementor saved template', 'suncoast-ele-widgets' ),
				),
			)
		);

		$this->add_control(
			'form_template',
			array(
				'label'       => __( 'Template', 'suncoast-ele-widgets' ),
				'description' => __( 'Build an Elementor Pro Form, save it as a template, then pick it here. It inherits the card styling automatically; set the admin notification under the form’s “Actions After Submit → Email”.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->template_options(),
				'label_block' => true,
				'condition'   => array( 'form_source' => 'elementor' ),
			)
		);

		$this->add_control(
			'ph_name',
			array(
				'label'     => __( 'Name placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Full Name', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'ph_phone',
			array(
				'label'     => __( 'Phone placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Phone Number', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'ph_zip',
			array(
				'label'     => __( 'Zip placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Zip Code', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'select_label',
			array(
				'label'     => __( 'Select label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'What are you looking to cover?', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'select_placeholder',
			array(
				'label'     => __( 'Select placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Select project type', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);

		$opt = new Repeater();
		$opt->add_control(
			'label',
			array(
				'label'   => __( 'Option', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Patio', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'select_options',
			array(
				'label'       => __( 'Project types', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $opt->get_controls(),
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'form_source' => 'builtin' ),
				'default'     => array(
					array( 'label' => __( 'Patio', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Deck', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Pool area', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Outdoor kitchen', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Three-season room', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Something else', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->add_control(
			'submit_text',
			array(
				'label'     => __( 'Submit label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Get My Free Estimate', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);

		$this->add_control(
			'notify_heading',
			array(
				'label'     => __( 'Admin notification', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'admin_email',
			array(
				'label'       => __( 'Send to', 'suncoast-ele-widgets' ),
				'description' => __( 'Comma-separated. Empty = the WordPress admin e-mail.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'condition'   => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'admin_subject',
			array(
				'label'       => __( 'Subject', 'suncoast-ele-widgets' ),
				'description' => __( 'Placeholders: {name} {zip} {project}', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'New consultation request — {name} ({zip})', 'suncoast-ele-widgets' ),
				'label_block' => true,
				'condition'   => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'msg_success',
			array(
				'label'       => __( 'Success message', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( "Thank you — we'll be in touch within one business day.", 'suncoast-ele-widgets' ),
				'condition'   => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'msg_error',
			array(
				'label'     => __( 'Error message', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Something went wrong. Please call 1-877-449-5106.', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);
		$this->add_control(
			'msg_invalid',
			array(
				'label'     => __( 'Validation message', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Please complete the highlighted fields.', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_marquee() {
		$this->start_controls_section( 'sec_marquee', array( 'label' => __( 'Marquee', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'marquee_on',
			array(
				'label'        => __( 'Show marquee', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Item', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'marquee_items',
			array(
				'label'       => __( 'Items', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'marquee_on' => 'yes' ),
				'default'     => array(
					array( 'text' => __( 'Open sky', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Shade on demand', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Rain ready', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Portland + Vancouver', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Outdoor Living, Reimagined', 'suncoast-ele-widgets' ) ),
					array( 'text' => __( 'Living Experiences', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->add_control(
			'marquee_speed',
			array(
				'label'       => __( 'Speed (px / second)', 'suncoast-ele-widgets' ),
				'description' => __( 'Constant speed — editing the copy never changes the pace.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 55,
				'min'         => 5,
				'max'         => 400,
				'condition'   => array( 'marquee_on' => 'yes' ),
			)
		);

		$this->add_control(
			'marquee_sep',
			array(
				'label'        => __( 'Show chevrons', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'marquee_on' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------- style */

	private function style_content() {
		$this->start_controls_section(
			'sec_style_content',
			array( 'label' => __( 'Content', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'grid_max',
			array(
				'label'      => __( 'Content max width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 800, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-max: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Eyebrow colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-banner__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'eyebrow_typo', 'selector' => '{{WRAPPER}} .sce-banner__eyebrow' )
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-banner__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour (&lt;em&gt;)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-banner__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-banner__title' )
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Description colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-banner__text' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'text_max',
			array(
				'label'      => __( 'Description max width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner__text' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'text_typo', 'selector' => '{{WRAPPER}} .sce-banner__text' )
		);

		$this->add_control(
			'list_color',
			array(
				'label'     => __( 'Checklist colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-banner__list li' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tick_color',
			array(
				'label'     => __( 'Tick colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-banner__tick' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'list_gap',
			array(
				'label'      => __( 'Checklist row gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner__list' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'list_typo', 'selector' => '{{WRAPPER}} .sce-banner__list li' )
		);

		$this->end_controls_section();
	}

	private function style_card() {
		$this->start_controls_section(
			'sec_style_card',
			array( 'label' => __( 'Card', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'card_w',
			array(
				'label'      => __( 'Width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 280, 'max' => 720 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-bnr-card-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .sce-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-card' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_blur',
			array(
				'label'      => __( 'Backdrop blur', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .sce-card' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}) saturate(130%); backdrop-filter: blur({{SIZE}}{{UNIT}}) saturate(130%);',
				),
			)
		);
		$this->add_control(
			'card_eyebrow_color',
			array(
				'label'     => __( 'Eyebrow colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-card__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_title_color',
			array(
				'label'     => __( 'Title colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-card__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'card_title_typo', 'selector' => '{{WRAPPER}} .sce-card__title' )
		);

		$this->end_controls_section();
	}

	private function style_fields() {
		$this->start_controls_section(
			'sec_style_fields',
			array( 'label' => __( 'Fields & button', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$sel_fields = '{{WRAPPER}} .sce-form__input, {{WRAPPER}} .sce-form__select';

		$this->add_control(
			'field_bg',
			array(
				'label'     => __( 'Field background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $sel_fields => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'field_border',
			array(
				'label'     => __( 'Field border', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $sel_fields => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'field_text',
			array(
				'label'     => __( 'Field text', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $sel_fields => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'field_placeholder',
			array(
				'label'     => __( 'Placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .sce-form__input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sce-form__select'             => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'field_h',
			array(
				'label'      => __( 'Field height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 34, 'max' => 80 ) ),
				'selectors'  => array( $sel_fields => 'height: {{SIZE}}{{UNIT}}; line-height: calc({{SIZE}}{{UNIT}} - 2px);' ),
			)
		);
		$this->add_control(
			'field_radius',
			array(
				'label'      => __( 'Field radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array( $sel_fields => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'submit_heading',
			array( 'label' => __( 'Submit button', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'submit_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-form__submit' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'submit_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-form__submit:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'submit_color',
			array(
				'label'     => __( 'Text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-form__submit' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'submit_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 34, 'max' => 84 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-form__submit' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'submit_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-form__submit' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'submit_typo', 'selector' => '{{WRAPPER}} .sce-form__submit' )
		);

		$this->end_controls_section();
	}

	private function style_marquee() {
		$this->start_controls_section(
			'sec_style_marquee',
			array(
				'label'     => __( 'Marquee', 'suncoast-ele-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'marquee_on' => 'yes' ),
			)
		);

		$this->add_control(
			'mq_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-banner' => '--sce-mq-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'mq_ink',
			array(
				'label'     => __( 'Text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-banner' => '--sce-mq-ink: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'mq_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-mq-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'mq_gap',
			array(
				'label'      => __( 'Gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-banner' => '--sce-mq-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'mq_typo', 'selector' => '{{WRAPPER}} .sce-mq__set li' )
		);

		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s = $this->get_settings_for_display();

		$anchor = sanitize_title( $s['card_anchor'] ? $s['card_anchor'] : 'quote' );
		?>
		<section class="sce-banner sce-scope">

			<div class="sce-banner__stage">
				<div class="sce-banner__media">
					<?php $this->render_image( $s ); ?>
					<span class="sce-banner__overlay"></span>
				</div>

				<div class="sce-banner__inner">
					<div class="sce-banner__grid">

						<div class="sce-banner__content">
							<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
								<span class="sce-banner__eyebrow sce-rv" style="--sce-rv-d:60ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
							<?php endif; ?>

							<?php $this->render_title( $s ); ?>

							<?php if ( ! empty( $s['description'] ) ) : ?>
								<p class="sce-banner__text sce-rv" style="--sce-rv-d:380ms"><?php echo esc_html( $s['description'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $s['checklist'] ) ) : ?>
								<ul class="sce-banner__list">
									<?php foreach ( $s['checklist'] as $i => $row ) : ?>
										<li class="sce-rv" style="--sce-rv-d:<?php echo (int) ( 460 + ( $i * 60 ) ); ?>ms">
											<?php $this->icon_tick(); ?>
											<span><?php echo esc_html( $row['text'] ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

						<div class="sce-banner__card-col" id="<?php echo esc_attr( $anchor ); ?>">
							<div class="sce-card sce-rv" style="--sce-rv-d:300ms;--sce-rv-y:26px">
								<?php if ( ! empty( $s['card_eyebrow'] ) ) : ?>
									<span class="sce-card__eyebrow"><?php echo esc_html( $s['card_eyebrow'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $s['card_title'] ) ) : ?>
									<h2 class="sce-card__title"><?php echo esc_html( $s['card_title'] ); ?></h2>
								<?php endif; ?>
								<?php if ( ! empty( $s['card_sub'] ) ) : ?>
									<p class="sce-card__sub"><?php echo esc_html( $s['card_sub'] ); ?></p>
								<?php endif; ?>

								<?php $this->render_form( $s ); ?>

								<?php if ( ! empty( $s['card_note'] ) ) : ?>
									<p class="sce-form__note"><?php echo esc_html( $s['card_note'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?php $this->render_marquee( $s ); ?>
		</section>
		<?php
	}

	/* ------------------------------------------------------------ partials */

	private function render_image( $s ) {
		$id  = ! empty( $s['bg_image']['id'] ) ? (int) $s['bg_image']['id'] : 0;
		$url = ! empty( $s['bg_image']['url'] ) ? $s['bg_image']['url'] : self::HERO;

		if ( $id ) {
			// wp_get_attachment_image() gives us srcset/sizes for free.
			echo wp_get_attachment_image(
				$id,
				'full',
				false,
				array(
					'class'         => 'sce-banner__img',
					'alt'           => '',
					'role'          => 'presentation',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
					'loading'       => 'eager',
				)
			);
			return;
		}
		?>
		<img class="sce-banner__img" src="<?php echo esc_url( $url ); ?>" alt="" role="presentation"
		     fetchpriority="high" decoding="async" loading="eager">
		<?php
	}

	/**
	 * Split the title on <br> so every line gets its own masked reveal.
	 */
	private function render_title( $s ) {
		if ( empty( $s['title'] ) ) {
			return;
		}

		$tag   = in_array( $s['title_tag'], array( 'h1', 'h2', 'h3', 'div' ), true ) ? $s['title_tag'] : 'h1';
		$lines = preg_split( '#<\s*br\s*/?\s*>#i', $s['title'] );
		$allow = array(
			'em'     => array(),
			'i'      => array(),
			'strong' => array(),
			'b'      => array(),
			'span'   => array( 'class' => array() ),
		);

		echo '<' . esc_attr( $tag ) . ' class="sce-banner__title">';
		foreach ( $lines as $i => $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			printf(
				'<span class="sce-rvline" style="--sce-rv-d:%dms"><span>%s</span></span>',
				(int) ( 140 + ( $i * 100 ) ),
				wp_kses( $line, $allow )
			);
		}
		echo '</' . esc_attr( $tag ) . '>';
	}

	private function render_form( $s ) {
		if ( 'elementor' === $s['form_source'] ) {
			wp_enqueue_style( 'sce-form-elementor' );
			$tpl = (int) $s['form_template'];
			if ( $tpl ) {
				echo '<div class="sce-form sce-form--elementor">';
				// Renders the saved template, including an Elementor Pro Form.
				echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tpl ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</div>';
			} elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p class="sce-card__sub">' . esc_html__( 'Pick a saved template under Form → Template.', 'suncoast-ele-widgets' ) . '</p>';
			}
			return;
		}

		$options = ! empty( $s['select_options'] ) ? $s['select_options'] : array();
		?>
		<form class="sce-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
		      data-ajax="yes"
		      data-msg-invalid="<?php echo esc_attr( $s['msg_invalid'] ); ?>"
		      data-msg-sending="<?php esc_attr_e( 'Sending…', 'suncoast-ele-widgets' ); ?>"
		      data-msg-success="<?php echo esc_attr( $s['msg_success'] ); ?>"
		      data-msg-error="<?php echo esc_attr( $s['msg_error'] ); ?>">

			<input type="hidden" name="post_id" value="<?php echo (int) get_the_ID(); ?>">
			<input type="hidden" name="widget_id" value="<?php echo esc_attr( $this->get_id() ); ?>">
			<input type="hidden" name="sce_t" value="<?php echo (int) time(); ?>">
			<?php /* Honeypot: off-screen, not display:none, so bots still fill it. */ ?>
			<div class="sce-sr" aria-hidden="true">
				<label for="sce-hp-<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Leave this field empty', 'suncoast-ele-widgets' ); ?></label>
				<input type="text" id="sce-hp-<?php echo esc_attr( $this->get_id() ); ?>" name="sce_hp" tabindex="-1" autocomplete="off">
			</div>

			<p class="sce-form__msg" aria-live="polite"></p>

			<div class="sce-form__fields">
				<?php
				$this->field( 'full_name', 'text', $s['ph_name'], array( 'autocomplete' => 'name', 'required' => true ) );
				$this->field( 'phone', 'tel', $s['ph_phone'], array( 'autocomplete' => 'tel', 'required' => true, 'data-mask' => 'tel' ) );
				$this->field(
					'zip',
					'text',
					$s['ph_zip'],
					array(
						'autocomplete' => 'postal-code',
						'required'     => true,
						'data-mask'    => 'zip',
						'inputmode'    => 'numeric',
						'pattern'      => '\d{5}',
						'maxlength'    => '5',
					)
				);
				?>
			</div>

			<?php if ( $options ) : ?>
				<div class="sce-form__group">
					<label class="sce-form__label" for="sce-type-<?php echo esc_attr( $this->get_id() ); ?>">
						<?php echo esc_html( $s['select_label'] ); ?>
					</label>
					<div class="sce-form__field">
						<select class="sce-form__select" id="sce-type-<?php echo esc_attr( $this->get_id() ); ?>" name="project_type" required>
							<option value="" disabled selected><?php echo esc_html( $s['select_placeholder'] ); ?></option>
							<?php foreach ( $options as $o ) : ?>
								<option value="<?php echo esc_attr( $o['label'] ); ?>"><?php echo esc_html( $o['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php $this->icon_chevron( 'sce-form__chev' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<button class="sce-form__submit" type="submit"><?php echo esc_html( $s['submit_text'] ); ?></button>
		</form>
		<?php
	}

	private function field( $name, $type, $placeholder, $attrs = array() ) {
		$id = 'sce-' . $name . '-' . $this->get_id();
		?>
		<div class="sce-form__field">
			<label class="sce-sr" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $placeholder ); ?></label>
			<input class="sce-form__input" id="<?php echo esc_attr( $id ); ?>"
			       name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>"
			       placeholder="<?php echo esc_attr( $placeholder ); ?>"
			       <?php
				foreach ( $attrs as $k => $v ) {
					if ( true === $v ) {
						echo esc_attr( $k ) . ' ';
					} else {
						printf( '%s="%s" ', esc_attr( $k ), esc_attr( $v ) );
					}
				}
				?>
			>
		</div>
		<?php
	}

	private function render_marquee( $s ) {
		if ( 'yes' !== $s['marquee_on'] || empty( $s['marquee_items'] ) ) {
			return;
		}
		$speed = ! empty( $s['marquee_speed'] ) ? (int) $s['marquee_speed'] : 55;
		?>
		<div class="sce-banner__marquee">
			<div class="sce-mq" data-speed="<?php echo esc_attr( $speed ); ?>">
				<div class="sce-mq__track">
					<?php for ( $set = 0; $set < 2; $set++ ) : ?>
						<ul class="sce-mq__set"<?php echo 1 === $set ? ' aria-hidden="true"' : ''; ?>>
							<?php foreach ( $s['marquee_items'] as $row ) : ?>
								<li>
									<?php echo esc_html( $row['text'] ); ?>
									<?php if ( 'yes' === $s['marquee_sep'] ) { $this->icon_chevron( 'sce-mq__sep', true ); } ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endfor; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/* --------------------------------------------------------------- icons */

	private function icon_tick() {
		?>
		<svg class="sce-banner__tick" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
			<circle cx="10" cy="10" r="10" fill="currentColor"/>
			<path d="M5.9 10.25 8.4 12.75 14.1 7.1" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php
	}

	private function icon_chevron( $class, $right = false ) {
		if ( $right ) {
			?>
			<svg class="<?php echo esc_attr( $class ); ?>" viewBox="0 0 7 12" fill="none" aria-hidden="true" focusable="false">
				<path d="m1 1 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<?php
			return;
		}
		?>
		<svg class="<?php echo esc_attr( $class ); ?>" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
			<path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php
	}

	/* --------------------------------------------------------------- utils */

	private function template_options() {
		$out   = array( '' => __( '— Select —', 'suncoast-ele-widgets' ) );
		$posts = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'posts_per_page' => 100,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		foreach ( $posts as $p ) {
			$out[ $p->ID ] = $p->post_title ? $p->post_title : '#' . $p->ID;
		}
		return $out;
	}
}
