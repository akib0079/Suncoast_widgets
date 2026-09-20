<?php
/**
 * Widget: Suncoast Benefits + Video.
 *
 * Heading block, a three-up benefit grid, and a video teaser that opens an
 * accessible modal. The modal node is moved to <body> by sce-benefits.js, so
 * its two styling controls are written inline rather than through
 * {{WRAPPER}} selectors, which would stop matching after the move.
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

class SCE_Widget_Benefits extends Widget_Base {

	const IMG_1 = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Suncoast-louvered-pergola-fully-closed-creating-a-solid-waterproof-roof-over-an-outdoor-kitchen-sleek-charcoal-finish-modern-aesthetic.png';
	const IMG_2 = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Background-4.png';
	const IMG_3 = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Background-3.png';
	const VIDEO_BG = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Video-thumbnail-background-scaled.png';

	public function get_name() {
		return 'suncoast_benefits';
	}

	public function get_title() {
		return __( 'Suncoast Benefits + Video', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'benefits', 'features', 'video', 'lightbox', 'modes' );
	}

	public function get_style_depends() {
		return array( 'sce-benefits' );
	}

	public function get_script_depends() {
		return array( 'sce-benefits' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_heading();
		$this->content_cards();
		$this->content_video();

		$this->style_section();
		$this->style_heading();
		$this->style_cards();
		$this->style_video();
	}

	/* ------------------------------------------------------------ content */

	private function content_heading() {
		$this->start_controls_section( 'sec_head', array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'One patio, three modes', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent words in &lt;em&gt; for the gold highlight. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Whatever the Weather Decides to <em>Do.</em>',
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
			'accent_italic',
			array(
				'label'        => __( 'Italic accent', 'suncoast-ele-widgets' ),
				'description'  => __( 'Off matches this section (upright). The hero uses italic.', 'suncoast-ele-widgets' ),
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
				'default' => __( 'Open for sun. Angle for shade. Close when the rain moves in.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	private function content_cards() {
		$this->start_controls_section( 'sec_cards', array( 'label' => __( 'Benefit cards', 'suncoast-ele-widgets' ) ) );

		$rep = new Repeater();
		$rep->add_control(
			'image',
			array(
				'label' => __( 'Image', 'suncoast-ele-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$rep->add_control(
			'icon_type',
			array(
				'label'   => __( 'Icon', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sun',
				'options' => array(
					'sun'     => __( 'Sun (Open)', 'suncoast-ele-widgets' ),
					'rays'    => __( 'Rays (Adjust)', 'suncoast-ele-widgets' ),
					'droplet' => __( 'Droplet (Close)', 'suncoast-ele-widgets' ),
					'custom'  => __( 'Custom image / SVG', 'suncoast-ele-widgets' ),
					'none'    => __( 'None', 'suncoast-ele-widgets' ),
				),
			)
		);
		$rep->add_control(
			'icon_custom',
			array(
				'label'     => __( 'Custom icon', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'icon_type' => 'custom' ),
			)
		);
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Open', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'desc',
			array(
				'label'   => __( 'Description', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
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
					array(
						'image'     => array( 'url' => self::IMG_1 ),
						'icon_type' => 'sun',
						'name'      => __( 'Open', 'suncoast-ele-widgets' ),
						'desc'      => __( 'Enjoy full sun on a clear Portland day. Maximize natural light and uninterrupted views of the sky.', 'suncoast-ele-widgets' ),
					),
					array(
						'image'     => array( 'url' => self::IMG_2 ),
						'icon_type' => 'rays',
						'name'      => __( 'Adjust', 'suncoast-ele-widgets' ),
						'desc'      => __( 'Angle the louvers to block direct glare while maintaining soft, dappled light and refreshing ventilation.', 'suncoast-ele-widgets' ),
					),
					array(
						'image'     => array( 'url' => self::IMG_3 ),
						'icon_type' => 'droplet',
						'name'      => __( 'Close', 'suncoast-ele-widgets' ),
						'desc'      => __( 'Create a watertight seal instantly. Integrated gutters channel rain away, keeping your space perfectly dry.', 'suncoast-ele-widgets' ),
					),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'     => __( 'Columns', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array( '' => __( 'Default', 'suncoast-ele-widgets' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array(
					'{{WRAPPER}} .sce-ben__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->end_controls_section();
	}

	private function content_video() {
		$this->start_controls_section( 'sec_video', array( 'label' => __( 'Video teaser', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'video_on',
			array(
				'label'        => __( 'Show video block', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'video_image',
			array(
				'label'     => __( 'Poster image', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => self::VIDEO_BG ),
				'condition' => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_eyebrow',
			array(
				'label'     => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'See it transform in seconds', 'suncoast-ele-widgets' ),
				'condition' => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_title',
			array(
				'label'     => __( 'Heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'Sun. Shade. Rain protection. You decide.', 'suncoast-ele-widgets' ),
				'condition' => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_note',
			array(
				'label'     => __( 'Caption', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Louvers open • Rotate • Close', 'suncoast-ele-widgets' ),
				'condition' => array( 'video_on' => 'yes' ),
			)
		);

		$this->add_control(
			'video_src_heading',
			array(
				'label'     => __( 'Video', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'video_on' => 'yes' ),
			)
		);

		$this->add_control(
			'video_source',
			array(
				'label'     => __( 'Source', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'youtube',
				'options'   => array(
					'youtube'  => __( 'YouTube', 'suncoast-ele-widgets' ),
					'vimeo'    => __( 'Vimeo', 'suncoast-ele-widgets' ),
					'hosted'   => __( 'Self-hosted (Media Library)', 'suncoast-ele-widgets' ),
					'external' => __( 'Other embed URL', 'suncoast-ele-widgets' ),
				),
				'condition' => array( 'video_on' => 'yes' ),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label'       => __( 'Video URL', 'suncoast-ele-widgets' ),
				'placeholder' => 'https://www.youtube.com/watch?v=…',
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'video_on' => 'yes', 'video_source!' => 'hosted' ),
			)
		);

		$this->add_control(
			'video_file',
			array(
				'label'      => __( 'Video file', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
				'condition'  => array( 'video_on' => 'yes', 'video_source' => 'hosted' ),
			)
		);

		$this->add_control(
			'video_autoplay',
			array(
				'label'        => __( 'Autoplay on open', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_mute',
			array(
				'label'        => __( 'Start muted', 'suncoast-ele-widgets' ),
				'description'  => __( 'Browsers block unmuted autoplay. Leave on if autoplay matters more than sound.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_controls',
			array(
				'label'        => __( 'Show controls', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_loop',
			array(
				'label'        => __( 'Loop', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'video_on' => 'yes' ),
			)
		);
		$this->add_control(
			'video_start',
			array(
				'label'      => __( 'Start at (seconds)', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 0,
				'condition'  => array( 'video_on' => 'yes', 'video_source' => 'youtube' ),
			)
		);
		$this->add_control(
			'video_privacy',
			array(
				'label'        => __( 'Privacy mode', 'suncoast-ele-widgets' ),
				'description'  => __( 'Serves from youtube-nocookie.com.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'video_on' => 'yes', 'video_source' => 'youtube' ),
			)
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------------- style */

	private function style_section() {
		$this->start_controls_section(
			'sec_style_section',
			array( 'label' => __( 'Section', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control(
			'defaults_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Leave a field empty to keep the Figma value (1070px content, #FCFBF4 background).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-pad-bottom: {{SIZE}}{{UNIT}};' ),
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
				'selectors' => array( '{{WRAPPER}} .sce-ben__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'eyebrow_typo', 'selector' => '{{WRAPPER}} .sce-ben__eyebrow' )
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-ben__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-ben__title' )
		);

		$this->add_control(
			'sub_color',
			array(
				'label'     => __( 'Short info colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-ben__sub' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'sub_max',
			array(
				'label'      => __( 'Short info max width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 240, 'max' => 900 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben__sub' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'sub_typo', 'selector' => '{{WRAPPER}} .sce-ben__sub' )
		);

		$this->add_responsive_control(
			'head_mb',
			array(
				'label'      => __( 'Space below heading block', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-head-mb: {{SIZE}}{{UNIT}};' ),
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
			'card_gap',
			array(
				'label'      => __( 'Gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Image radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_ratio',
			array(
				'label'       => __( 'Image aspect ratio', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS aspect-ratio, e.g. <code>340 / 284</code> (Figma) or <code>16 / 9</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '340 / 284',
				'selectors'   => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-media-ratio: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'icon_heading',
			array( 'label' => __( 'Icon', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'icon_color',
			array(
				'label'       => __( 'Colour', 'suncoast-ele-widgets' ),
				'description' => __( 'Applies to the built-in icons. An uploaded image keeps its own colours.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array( '{{WRAPPER}} .sce-ben__icon' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 64 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-icon: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'icon_gap',
			array(
				'label'      => __( 'Gap to heading', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-icon-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'name_heading',
			array( 'label' => __( 'Card heading', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__name' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'name_mt',
			array(
				'label'      => __( 'Space above', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-name-mt: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'name_typo', 'selector' => '{{WRAPPER}} .sce-ben__name' )
		);

		$this->add_control(
			'desc_heading',
			array( 'label' => __( 'Card description', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'desc_mt',
			array(
				'label'      => __( 'Space above', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-desc-mt: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'desc_typo', 'selector' => '{{WRAPPER}} .sce-ben__desc' )
		);

		$this->end_controls_section();
	}

	private function style_video() {
		$this->start_controls_section(
			'sec_style_video',
			array(
				'label'     => __( 'Video teaser', 'suncoast-ele-widgets' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'video_on' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'video_mt',
			array(
				'label'      => __( 'Space above', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-video-mt: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'video_ratio',
			array(
				'label'       => __( 'Aspect ratio', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS aspect-ratio, e.g. <code>1070 / 400</code> (Figma).', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '1070 / 400',
				'selectors'   => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-video-ratio: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'video_overlay',
			array(
				'label'     => __( 'Overlay', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-video-overlay: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'video_gap',
			array(
				'label'      => __( 'Inner gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-video-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'play_heading',
			array( 'label' => __( 'Play button', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_responsive_control(
			'play_size',
			array(
				'label'      => __( 'Size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-play: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'play_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-play-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'play_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben' => '--sce-ben-play-bg-hover: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'vtext_heading',
			array( 'label' => __( 'Text', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'video_eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__video-eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'video_title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__video-title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'video_title_typo', 'selector' => '{{WRAPPER}} .sce-ben__video-title' )
		);
		$this->add_control(
			'video_note_color',
			array(
				'label'     => __( 'Caption colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-ben__video-note' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'modal_heading',
			array( 'label' => __( 'Modal', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'modal_scrim',
			array(
				'label'       => __( 'Backdrop', 'suncoast-ele-widgets' ),
				'description' => __( 'Written inline — the modal is moved to the page body so it escapes any transformed section.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::COLOR,
			)
		);
		$this->add_control(
			'modal_max',
			array(
				'label'      => __( 'Max width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 480, 'max' => 1600 ) ),
			)
		);

		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sce-ben sce-scope">
			<div class="sce-ben__inner">
				<?php $this->render_head( $s ); ?>
				<?php $this->render_cards( $s ); ?>
				<?php $this->render_video( $s ); ?>
			</div>
			<?php $this->render_modal( $s ); ?>
		</section>
		<?php
	}

	private function render_head( $s ) {
		if ( empty( $s['eyebrow'] ) && empty( $s['title'] ) && empty( $s['sub'] ) ) {
			return;
		}
		$tag = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		?>
		<div class="sce-ben__head">
			<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
				<span class="sce-ben__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $s['title'] ) ) : ?>
				<?php
				printf(
					'<%1$s class="sce-ben__title%2$s">',
					esc_attr( $tag ),
					'yes' === $s['accent_italic'] ? ' sce-ben__title--italic' : ''
				);
				echo self::lines( $s['title'], 120, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
				printf( '</%s>', esc_attr( $tag ) );
				?>
			<?php endif; ?>

			<?php if ( ! empty( $s['sub'] ) ) : ?>
				<p class="sce-ben__sub sce-rv" style="--sce-rv-d:280ms"><?php echo esc_html( $s['sub'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_cards( $s ) {
		if ( empty( $s['cards'] ) ) {
			return;
		}
		?>
		<ul class="sce-ben__grid">
			<?php foreach ( $s['cards'] as $i => $c ) : ?>
				<li class="sce-ben__card sce-rv" style="--sce-rv-d:<?php echo (int) ( 340 + ( $i * 110 ) ); ?>ms">
					<?php
					$url  = ! empty( $c['link']['url'] ) ? $c['link']['url'] : '';
					$open = $url ? '<a class="sce-ben__link" href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"'
						. ( ! empty( $c['link']['is_external'] ) ? ' target="_blank"' : '' )
						. ( ! empty( $c['link']['nofollow'] ) ? ' rel="nofollow"' : '' ) . '>' : '';
					echo $open; // phpcs:ignore WordPress.Security.EscapeOutput
					?>

					<?php if ( ! empty( $c['image']['url'] ) ) : ?>
						<div class="sce-ben__media"><?php $this->img( $c['image'], $c['name'] ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $c['name'] ) ) : ?>
						<div class="sce-ben__row">
							<?php $this->icon( $c ); ?>
							<h3 class="sce-ben__name"><?php echo esc_html( $c['name'] ); ?></h3>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $c['desc'] ) ) : ?>
						<p class="sce-ben__desc"><?php echo esc_html( $c['desc'] ); ?></p>
					<?php endif; ?>

					<?php echo $url ? '</a>' : ''; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	private function render_video( $s ) {
		if ( 'yes' !== $s['video_on'] ) {
			return;
		}
		list( $type, $src ) = $this->embed( $s );
		$has = ( '' !== $src );
		?>
		<div class="sce-ben__video sce-rv" style="--sce-rv-d:120ms;--sce-rv-y:24px">
			<?php if ( ! empty( $s['video_image']['url'] ) ) : ?>
				<?php $this->img( $s['video_image'], '', 'sce-ben__video-img' ); ?>
			<?php endif; ?>
			<span class="sce-ben__video-overlay"></span>

			<div class="sce-ben__video-inner">
				<?php if ( ! empty( $s['video_eyebrow'] ) ) : ?>
					<span class="sce-ben__video-eyebrow"><?php echo esc_html( $s['video_eyebrow'] ); ?></span>
				<?php endif; ?>

				<?php if ( $has ) : ?>
					<button class="sce-ben__play" type="button"
					        aria-haspopup="dialog" aria-expanded="false"
					        aria-controls="<?php echo esc_attr( 'sce-modal-' . $this->get_id() ); ?>">
						<span class="sce-sr">
							<?php
							/* translators: %s: video heading */
							printf( esc_html__( 'Play video: %s', 'suncoast-ele-widgets' ), esc_html( wp_strip_all_tags( (string) $s['video_title'] ) ) );
							?>
						</span>
						<svg viewBox="0 0 16 18" fill="none" aria-hidden="true" focusable="false">
							<path d="M15 8.13397c.6667.38494.6667 1.34712 0 1.73206L1.5 17.6603c-.66667.3849-1.5-.0962-1.5-.866V1.20577C0 .436 .83333-.04513 1.5.33981L15 8.13397Z" fill="currentColor"/>
						</svg>
					</button>
				<?php elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
					<span class="sce-ben__video-note"><?php esc_html_e( 'Add a video URL under Video teaser → Video.', 'suncoast-ele-widgets' ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $s['video_title'] ) ) : ?>
					<p class="sce-ben__video-title"><?php echo esc_html( $s['video_title'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $s['video_note'] ) ) : ?>
					<span class="sce-ben__video-note"><?php echo esc_html( $s['video_note'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_modal( $s ) {
		if ( 'yes' !== $s['video_on'] ) {
			return;
		}
		list( $type, $src ) = $this->embed( $s );
		if ( '' === $src ) {
			return;
		}

		$style = '';
		if ( ! empty( $s['modal_max']['size'] ) ) {
			$style .= '--sce-modal-max:' . (int) $s['modal_max']['size'] . 'px;';
		}
		?>
		<div class="sce-modal sce-scope" id="<?php echo esc_attr( 'sce-modal-' . $this->get_id() ); ?>"
		     role="dialog" aria-modal="true" aria-hidden="true" inert
		     aria-label="<?php echo esc_attr( wp_strip_all_tags( (string) $s['video_title'] ) ); ?>"
		     <?php echo $style ? 'style="' . esc_attr( $style ) . '"' : ''; ?>
		     data-type="<?php echo esc_attr( $type ); ?>"
		     data-src="<?php echo esc_url( $src ); ?>"
		     data-title="<?php echo esc_attr( wp_strip_all_tags( (string) $s['video_title'] ) ); ?>"
		     data-autoplay="<?php echo 'yes' === $s['video_autoplay'] ? 'yes' : 'no'; ?>"
		     data-muted="<?php echo 'yes' === $s['video_mute'] ? 'yes' : 'no'; ?>"
		     data-loop="<?php echo 'yes' === $s['video_loop'] ? 'yes' : 'no'; ?>"
		     data-controls="<?php echo 'yes' === $s['video_controls'] ? 'yes' : 'no'; ?>">

			<div class="sce-modal__scrim"<?php echo ! empty( $s['modal_scrim'] ) ? ' style="background-color:' . esc_attr( $s['modal_scrim'] ) . '"' : ''; ?>></div>

			<div class="sce-modal__box"<?php echo ! empty( $s['modal_max']['size'] ) ? ' style="max-width:' . (int) $s['modal_max']['size'] . 'px"' : ''; ?>>
				<button class="sce-modal__close" type="button">
					<span class="sce-sr"><?php esc_html_e( 'Close video', 'suncoast-ele-widgets' ); ?></span>
					<svg viewBox="0 0 14 14" fill="none" aria-hidden="true" focusable="false">
						<line x1="1" y1="1" x2="13" y2="13"></line>
						<line x1="13" y1="1" x2="1" y2="13"></line>
					</svg>
				</button>
				<div class="sce-modal__frame"></div>
			</div>
		</div>
		<?php
	}

	/* ------------------------------------------------------------ partials */

	private function img( $media, $alt = '', $class = '' ) {
		$class = $class ? $class : '';
		$id    = ! empty( $media['id'] ) ? (int) $media['id'] : 0;

		if ( $id ) {
			echo wp_get_attachment_image(
				$id,
				'large',
				false,
				array(
					'class'    => $class,
					'alt'      => $alt ? $alt : '',
					'decoding' => 'async',
					'loading'  => 'lazy',
				)
			);
			return;
		}
		printf(
			'<img%s src="%s" alt="%s" decoding="async" loading="lazy">',
			$class ? ' class="' . esc_attr( $class ) . '"' : '',
			esc_url( $media['url'] ),
			esc_attr( $alt )
		);
	}

	private function icon( $c ) {
		$type = isset( $c['icon_type'] ) ? $c['icon_type'] : 'sun';
		if ( 'none' === $type ) {
			return;
		}

		echo '<span class="sce-ben__icon" aria-hidden="true">';
		if ( 'custom' === $type ) {
			if ( ! empty( $c['icon_custom']['url'] ) ) {
				printf( '<img src="%s" alt="" decoding="async">', esc_url( $c['icon_custom']['url'] ) );
			}
		} else {
			echo self::icon_svg( $type ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</span>';
	}

	/**
	 * The three Figma icons, inlined so they recolour with the icon control
	 * and cost no extra request.
	 */
	private static function icon_svg( $key ) {
		$paths = array(
			'sun'     => array(
				'vb' => '0 0 22 22',
				'd'  => 'M10 3V0H12V3H10ZM10 22V19H12V22H10ZM19 12V10H22V12H19ZM0 12V10H3V12H0ZM17.7 5.7L16.3 4.3L18.05 2.5L19.5 3.95L17.7 5.7ZM3.95 19.5L2.5 18.05L4.3 16.3L5.7 17.7L3.95 19.5ZM18.05 19.5L16.3 17.7L17.7 16.3L19.5 18.05L18.05 19.5ZM4.3 5.7L2.5 3.95L3.95 2.5L5.7 4.3L4.3 5.7ZM11 17C9.33333 17 7.91667 16.4167 6.75 15.25C5.58333 14.0833 5 12.6667 5 11C5 9.33333 5.58333 7.91667 6.75 6.75C7.91667 5.58333 9.33333 5 11 5C12.6667 5 14.0833 5.58333 15.25 6.75C16.4167 7.91667 17 9.33333 17 11C17 12.6667 16.4167 14.0833 15.25 15.25C14.0833 16.4167 12.6667 17 11 17ZM11 15C12.1167 15 13.0625 14.6125 13.8375 13.8375C14.6125 13.0625 15 12.1167 15 11C15 9.88333 14.6125 8.9375 13.8375 8.1625C13.0625 7.3875 12.1167 7 11 7C9.88333 7 8.9375 7.3875 8.1625 8.1625C7.3875 8.9375 7 9.88333 7 11C7 12.1167 7.3875 13.0625 8.1625 13.8375C8.9375 14.6125 9.88333 15 11 15Z',
			),
			'rays'    => array(
				'vb' => '0 0 22 22',
				'd'  => 'M0 12V10H6V12H0ZM6.75 8.15L4.65 6.05L6.05 4.65L8.15 6.75L6.75 8.15ZM10 6V0H12V6H10ZM15.25 8.15L13.85 6.75L15.95 4.65L17.35 6.05L15.25 8.15ZM16 12V10H22V12H16ZM11 14C10.1667 14 9.45833 13.7083 8.875 13.125C8.29167 12.5417 8 11.8333 8 11C8 10.1667 8.29167 9.45833 8.875 8.875C9.45833 8.29167 10.1667 8 11 8C11.8333 8 12.5417 8.29167 13.125 8.875C13.7083 9.45833 14 10.1667 14 11C14 11.8333 13.7083 12.5417 13.125 13.125C12.5417 13.7083 11.8333 14 11 14ZM15.95 17.35L13.85 15.25L15.25 13.85L17.35 15.95L15.95 17.35ZM6.05 17.35L4.65 15.95L6.75 13.85L8.15 15.25L6.05 17.35ZM10 22V16H12V22H10Z',
			),
			'droplet' => array(
				'vb' => '0 0 16 20',
				'd'  => 'M8.275 17C8.475 16.9833 8.64583 16.9042 8.7875 16.7625C8.92917 16.6208 9 16.45 9 16.25C9 16.0167 8.925 15.8292 8.775 15.6875C8.625 15.5458 8.43333 15.4833 8.2 15.5C7.51667 15.55 6.79167 15.3625 6.025 14.9375C5.25833 14.5125 4.775 13.7417 4.575 12.625C4.54167 12.4417 4.45417 12.2917 4.3125 12.175C4.17083 12.0583 4.00833 12 3.825 12C3.59167 12 3.4 12.0875 3.25 12.2625C3.1 12.4375 3.05 12.6417 3.1 12.875C3.38333 14.3917 4.05 15.475 5.1 16.125C6.15 16.775 7.20833 17.0667 8.275 17ZM8 20C5.71667 20 3.8125 19.2167 2.2875 17.65C0.7625 16.0833 0 14.1333 0 11.8C0 10.1333 0.6625 8.32083 1.9875 6.3625C3.3125 4.40417 5.31667 2.28333 8 0C10.6833 2.28333 12.6875 4.40417 14.0125 6.3625C15.3375 8.32083 16 10.1333 16 11.8C16 14.1333 15.2375 16.0833 13.7125 17.65C12.1875 19.2167 10.2833 20 8 20ZM8 18C9.73333 18 11.1667 17.4125 12.3 16.2375C13.4333 15.0625 14 13.5833 14 11.8C14 10.5833 13.4958 9.20833 12.4875 7.675C11.4792 6.14167 9.98333 4.46667 8 2.65C6.01667 4.46667 4.52083 6.14167 3.5125 7.675C2.50417 9.20833 2 10.5833 2 11.8C2 13.5833 2.56667 15.0625 3.7 16.2375C4.83333 17.4125 6.26667 18 8 18Z',
			),
		);

		if ( ! isset( $paths[ $key ] ) ) {
			return '';
		}
		return sprintf(
			'<svg viewBox="%s" fill="none" aria-hidden="true" focusable="false"><path d="%s" fill="currentColor"/></svg>',
			esc_attr( $paths[ $key ]['vb'] ),
			esc_attr( $paths[ $key ]['d'] )
		);
	}

	/**
	 * Split on <br> so every line gets its own masked reveal.
	 *
	 * @param string $text  Raw control value.
	 * @param int    $base  First line's delay in ms.
	 * @param int    $step  Added per subsequent line.
	 * @return string
	 */
	private static function lines( $text, $base = 120, $step = 90 ) {
		$allow = array(
			'em'     => array(),
			'i'      => array(),
			'strong' => array(),
			'b'      => array(),
			'span'   => array( 'class' => array() ),
		);
		$out = '';
		foreach ( preg_split( '#<\s*br\s*/?\s*>#i', $text ) as $i => $line ) {
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
	 * Resolve the configured video into [ type, src ].
	 *
	 * @return array{0:string,1:string}
	 */
	private function embed( $s ) {
		$source = isset( $s['video_source'] ) ? $s['video_source'] : 'youtube';

		if ( 'hosted' === $source ) {
			return array( 'file', ! empty( $s['video_file']['url'] ) ? $s['video_file']['url'] : '' );
		}

		$url = isset( $s['video_url'] ) ? trim( (string) $s['video_url'] ) : '';
		if ( '' === $url ) {
			return array( '', '' );
		}

		$autoplay = ( 'yes' === $s['video_autoplay'] );
		$muted    = ( 'yes' === $s['video_mute'] );
		$loop     = ( 'yes' === $s['video_loop'] );
		$controls = ( 'yes' === $s['video_controls'] );

		if ( 'youtube' === $source ) {
			$id = '';
			if ( preg_match( '#(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{6,})#i', $url, $m ) ) {
				$id = $m[1];
			} elseif ( preg_match( '#^[A-Za-z0-9_-]{6,}$#', $url ) ) {
				$id = $url;
			}
			if ( '' === $id ) {
				return array( '', '' );
			}

			$host = ( 'yes' === $s['video_privacy'] ) ? 'www.youtube-nocookie.com' : 'www.youtube.com';
			$args = array( 'rel' => 0, 'modestbranding' => 1, 'playsinline' => 1 );
			if ( $autoplay ) { $args['autoplay'] = 1; }
			if ( $muted ) { $args['mute'] = 1; }
			if ( ! $controls ) { $args['controls'] = 0; }
			if ( $loop ) { $args['loop'] = 1; $args['playlist'] = $id; }
			if ( ! empty( $s['video_start'] ) ) { $args['start'] = (int) $s['video_start']; }

			return array( 'youtube', add_query_arg( $args, 'https://' . $host . '/embed/' . rawurlencode( $id ) ) );
		}

		if ( 'vimeo' === $source ) {
			$id = '';
			if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#i', $url, $m ) ) {
				$id = $m[1];
			} elseif ( ctype_digit( $url ) ) {
				$id = $url;
			}
			if ( '' === $id ) {
				return array( '', '' );
			}

			$args = array( 'title' => 0, 'byline' => 0, 'portrait' => 0, 'dnt' => 1 );
			if ( $autoplay ) { $args['autoplay'] = 1; }
			if ( $muted ) { $args['muted'] = 1; }
			if ( ! $controls ) { $args['controls'] = 0; }
			if ( $loop ) { $args['loop'] = 1; }

			return array( 'vimeo', add_query_arg( $args, 'https://player.vimeo.com/video/' . rawurlencode( $id ) ) );
		}

		return array( 'external', esc_url_raw( $url ) );
	}
}
