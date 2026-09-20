<?php
/**
 * Widget: Suncoast Featured Projects.
 *
 * Heading row (eyebrow + mixed-face heading on the left, intro bottom-aligned
 * on the right) over two card grids: a 658/400 feature row and a four-up row.
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

class SCE_Widget_Projects extends Widget_Base {

	const U    = 'https://suncoastenclosures.com/wp-content/uploads/2026/09/';
	const IMG1 = self::U . 'generated-image-7.webp';
	const IMG2 = self::U . 'generated-image-8.webp';
	const IMG3 = self::U . '4-Small-Cards-Grid.png';
	const IMG4 = self::U . 'Container-1.png';

	public function get_name() {
		return 'suncoast_projects';
	}

	public function get_title() {
		return __( 'Suncoast Featured Projects', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'projects', 'portfolio', 'gallery', 'grid', 'featured' );
	}

	public function get_style_depends() {
		return array( 'sce-projects' );
	}

	public function get_script_depends() {
		return array( 'sce-projects' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_heading();
		$this->content_rows();

		$this->style_section();
		$this->style_heading();
		$this->style_cards();
	}

	/* ------------------------------------------------------------ content */

	private function content_heading() {
		$this->start_controls_section( 'sec_head', array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Real outdoor living projects', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — it switches to the serif face in gold. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Imagine What We Could<br>Build at <em>Your Home.</em>',
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
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'   => __( 'Paragraph', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Every pergola is designed around the home, the space, and the way people want to live outside.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * One repeater definition reused by both rows.
	 */
	private function card_fields( $with_arrow = true ) {
		$rep = new Repeater();
		$rep->add_control(
			'image',
			array(
				'label' => __( 'Background image', 'suncoast-ele-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$rep->add_control(
			'label',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Project', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#projects',
			)
		);
		if ( $with_arrow ) {
			$rep->add_control(
				'arrow',
				array(
					'label'        => __( 'Show arrow', 'suncoast-ele-widgets' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'return_value' => 'yes',
				)
			);
		}
		return $rep;
	}

	private function content_rows() {
		$this->start_controls_section( 'sec_rows', array( 'label' => __( 'Projects', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'top_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Two rows: a feature row (wide + square) and a four-up row. Both are repeaters — add or remove freely.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'cards_top',
			array(
				'label'       => __( 'Feature row', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $this->card_fields( true )->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'image' => array( 'url' => self::IMG1 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Open-Sky Dining', 'suncoast-ele-widgets' ),
						'arrow' => 'yes',
					),
					array(
						'image' => array( 'url' => self::IMG2 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Outdoor Kitchen', 'suncoast-ele-widgets' ),
						'arrow' => 'yes',
					),
				),
			)
		);

		$this->add_control(
			'top_cols',
			array(
				'label'       => __( 'Feature row columns', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS grid-template-columns. Figma: <code>658fr 400fr</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '658fr 400fr',
				'selectors'   => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-top-cols: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'cards_bottom',
			array(
				'label'       => __( 'Four-up row', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $this->card_fields( false )->get_controls(),
				'title_field' => '{{{ name }}}',
				'separator'   => 'before',
				'default'     => array(
					array(
						'image' => array( 'url' => self::IMG3 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Rain-Ready Patio', 'suncoast-ele-widgets' ),
					),
					array(
						'image' => array( 'url' => self::IMG4 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Heated Lounge', 'suncoast-ele-widgets' ),
					),
					array(
						'image' => array( 'url' => self::IMG2 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Screened Retreat', 'suncoast-ele-widgets' ),
					),
					array(
						'image' => array( 'url' => self::IMG1 ),
						'label' => __( 'Portland/Vancouver Area', 'suncoast-ele-widgets' ),
						'name'  => __( 'Entertainment Space', 'suncoast-ele-widgets' ),
					),
				),
			)
		);

		$this->add_responsive_control(
			'bottom_cols',
			array(
				'label'      => __( 'Four-up row columns', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::NUMBER,
				'min'        => 1,
				'max'        => 6,
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-bottom-cols: {{VALUE}};' ),
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
			'note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Leave a field empty to keep the Figma value (1070px content, white background). Note the design gives this section <strong>no top padding</strong> — it relies on the cream section above it.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-pad-bottom: {{SIZE}}{{UNIT}};' ),
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
				'selectors' => array( '{{WRAPPER}} .sce-fp__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'eyebrow_typo', 'selector' => '{{WRAPPER}} .sce-fp__eyebrow' )
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-fp__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-fp__title' )
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'accent_typo',
				'label'    => __( 'Accent typography', 'suncoast-ele-widgets' ),
				'selector' => '{{WRAPPER}} .sce-fp__title em',
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => __( 'Paragraph colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-fp__intro' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'intro_w',
			array(
				'label'      => __( 'Paragraph column width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 200, 'max' => 700 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-intro-w: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'intro_typo', 'selector' => '{{WRAPPER}} .sce-fp__intro' )
		);

		$this->add_responsive_control(
			'head_mb',
			array(
				'label'      => __( 'Space below heading row', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-head-mb: {{SIZE}}{{UNIT}};' ),
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
			'top_gap',
			array(
				'label'      => __( 'Feature row gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-top-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Space between rows', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bottom_gap',
			array(
				'label'      => __( 'Four-up row gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-bottom-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'ratio_heading',
			array( 'label' => __( 'Aspect ratios', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_responsive_control(
			'ratio_a',
			array(
				'label'       => __( 'Feature card 1', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS aspect-ratio. Figma: <code>658 / 400</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '658 / 400',
				'selectors'   => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-ratio-a: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'ratio_b',
			array(
				'label'       => __( 'Feature card 2+', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '400 / 400',
				'selectors'   => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-ratio-b: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'ratio_sm',
			array(
				'label'       => __( 'Four-up cards', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '255.5 / 288',
				'selectors'   => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-ratio-sm: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'veil_heading',
			array( 'label' => __( 'Overlay & text', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'veil',
			array(
				'label'       => __( 'Bottom veil', 'suncoast-ele-widgets' ),
				'description' => __( 'Full CSS background-image. Figma is a 80%→0% black gradient over the lower half.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'placeholder' => 'linear-gradient(to top, rgba(26,26,26,.8) 0%, rgba(26,26,26,0) 50%)',
				'selectors'   => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-veil: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Card sub-heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp__label' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Card heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp__name' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_lg_typo',
				'label'    => __( 'Feature card heading', 'suncoast-ele-widgets' ),
				'selector' => '{{WRAPPER}} .sce-fp__card--lg .sce-fp__name',
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_sm_typo',
				'label'    => __( 'Four-up card heading', 'suncoast-ele-widgets' ),
				'selector' => '{{WRAPPER}} .sce-fp__card--sm .sce-fp__name',
			)
		);

		$this->add_control(
			'arrow_heading',
			array( 'label' => __( 'Arrow', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-arrow: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'arrow_inset',
			array(
				'label'      => __( 'Inset', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-arrow-inset: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'arrow_ring',
			array(
				'label'     => __( 'Ring colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-arrow-ring: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'arrow_ink',
			array(
				'label'     => __( 'Glyph colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-fp' => '--sce-fp-arrow-ink: {{VALUE}};' ),
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
		<section class="sce-fp sce-scope">
			<div class="sce-fp__inner">
				<?php $this->render_head( $s ); ?>

				<?php if ( ! empty( $s['cards_top'] ) ) : ?>
					<div class="sce-fp__row sce-fp__row--top">
						<?php foreach ( $s['cards_top'] as $i => $c ) : ?>
							<?php $this->render_card( $c, 'lg', 120 + ( $i * 110 ) ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['cards_bottom'] ) ) : ?>
					<div class="sce-fp__row sce-fp__row--bottom">
						<?php foreach ( $s['cards_bottom'] as $i => $c ) : ?>
							<?php $this->render_card( $c, 'sm', 340 + ( $i * 80 ) ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function render_head( $s ) {
		if ( empty( $s['eyebrow'] ) && empty( $s['title'] ) && empty( $s['intro'] ) ) {
			return;
		}
		$tag = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		?>
		<div class="sce-fp__head">
			<div class="sce-fp__head-main">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<span class="sce-fp__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $s['title'] ) ) : ?>
					<?php
					printf(
						'<%1$s class="sce-fp__title%2$s">',
						esc_attr( $tag ),
						'yes' === $s['accent_italic'] ? ' sce-fp__title--italic' : ''
					);
					echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
					printf( '</%s>', esc_attr( $tag ) );
					?>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $s['intro'] ) ) : ?>
				<p class="sce-fp__intro sce-rv" style="--sce-rv-d:300ms"><?php echo esc_html( $s['intro'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * @param array  $c     Repeater row.
	 * @param string $size  'lg' or 'sm'.
	 * @param int    $delay Reveal delay in ms.
	 */
	private function render_card( $c, $size, $delay ) {
		$url   = ! empty( $c['link']['url'] ) ? $c['link']['url'] : '';
		$tag   = $url ? 'a' : 'div';
		$attrs = 'class="sce-fp__card sce-fp__card--' . esc_attr( $size ) . ' sce-rv" style="--sce-rv-d:' . (int) $delay . 'ms;--sce-rv-y:22px"';

		if ( $url ) {
			$attrs .= ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
			if ( ! empty( $c['link']['is_external'] ) ) {
				$attrs .= ' target="_blank" rel="noopener"';
			}
			if ( ! empty( $c['link']['nofollow'] ) ) {
				$attrs .= ' rel="nofollow"';
			}
		}
		?>
		<<?php echo $tag . ' ' . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php if ( ! empty( $c['image']['url'] ) ) : ?>
				<?php $this->img( $c['image'], $c['name'] ); ?>
			<?php endif; ?>
			<span class="sce-fp__veil"></span>

			<span class="sce-fp__body">
				<?php if ( ! empty( $c['label'] ) ) : ?>
					<span class="sce-fp__label"><?php echo esc_html( $c['label'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $c['name'] ) ) : ?>
					<span class="sce-fp__name"><?php echo esc_html( $c['name'] ); ?></span>
				<?php endif; ?>
			</span>

			<?php if ( 'lg' === $size && ( ! isset( $c['arrow'] ) || 'yes' === $c['arrow'] ) ) : ?>
				<span class="sce-fp__arrow" aria-hidden="true">
					<svg viewBox="0 0 16 16" fill="none" focusable="false">
						<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
					</svg>
				</span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}

	private function img( $media, $alt = '' ) {
		$id = ! empty( $media['id'] ) ? (int) $media['id'] : 0;
		if ( $id ) {
			echo wp_get_attachment_image(
				$id,
				'large',
				false,
				array(
					'class'    => 'sce-fp__img',
					'alt'      => $alt ? $alt : '',
					'decoding' => 'async',
					'loading'  => 'lazy',
				)
			);
			return;
		}
		printf(
			'<img class="sce-fp__img" src="%s" alt="%s" decoding="async" loading="lazy">',
			esc_url( $media['url'] ),
			esc_attr( $alt )
		);
	}
}
