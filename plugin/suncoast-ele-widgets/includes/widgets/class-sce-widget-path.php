<?php
/**
 * Widget: Suncoast Testimonials + Path.
 *
 * Two columns — a cross-fading testimonial slider with pagination dots, and a
 * numbered path whose steps light up on a timer (pointer or keyboard focus
 * takes over). Both are repeaters, so the content is fully editable.
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

class SCE_Widget_Path extends Widget_Base {

	public function get_name() {
		return 'suncoast_path';
	}

	public function get_title() {
		return __( 'Suncoast Testimonials + Path', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'testimonial', 'slider', 'reviews', 'steps', 'process', 'path' );
	}

	public function get_style_depends() {
		return array( 'sce-path' );
	}

	public function get_script_depends() {
		return array( 'sce-path' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_testimonials();
		$this->content_path();
		$this->style_section();
		$this->style_card();
		$this->style_path();
	}

	private function content_testimonials() {
		$this->start_controls_section( 'sec_tm', array( 'label' => __( 'Testimonials', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'tm_eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'What local homeowners are saying', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'tm_title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Built Around Your Home.<br>Installed With <em>Care.</em>',
			)
		);
		$this->add_control(
			'tm_mark',
			array(
				'label'        => __( 'Decorative quote mark', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'stars',
			array(
				'label'   => __( 'Stars', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 5,
				'default' => 5,
			)
		);
		$rep->add_control(
			'quote',
			array(
				'label'   => __( 'Quote', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => __( '"First rate company. The crew was careful and exacting, and communication was very good from start to finish."', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '— John Lee', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'badge',
			array(
				'label'   => __( 'Badge', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Verified', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'testimonials',
			array(
				'label'       => __( 'Slides', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'stars' => 5,
						'quote' => __( '"First rate company. The crew was careful and exacting, and communication was very good from start to finish."', 'suncoast-ele-widgets' ),
						'name'  => __( '— John Lee', 'suncoast-ele-widgets' ),
						'badge' => __( 'Verified', 'suncoast-ele-widgets' ),
					),
					array(
						'stars' => 5,
						'quote' => __( '"They designed around our deck instead of forcing a stock size on us. The louvers are quiet and the gutters actually work in a Portland downpour."', 'suncoast-ele-widgets' ),
						'name'  => __( '— Marta R.', 'suncoast-ele-widgets' ),
						'badge' => __( 'Verified', 'suncoast-ele-widgets' ),
					),
					array(
						'stars' => 5,
						'quote' => __( '"Install took two days and the crew left the yard cleaner than they found it. We use the patio nine months a year now."', 'suncoast-ele-widgets' ),
						'name'  => __( '— Dan & Priya K.', 'suncoast-ele-widgets' ),
						'badge' => __( 'Verified', 'suncoast-ele-widgets' ),
					),
				),
			)
		);

		$this->add_control(
			'tm_auto',
			array(
				'label'        => __( 'Autoplay', 'suncoast-ele-widgets' ),
				'description'  => __( 'Pauses on hover, on focus, and whenever the section is off-screen.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'tm_delay',
			array(
				'label'     => __( 'Slide duration (ms)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1500,
				'max'       => 20000,
				'step'      => 100,
				'default'   => 6000,
				'condition' => array( 'tm_auto' => 'yes' ),
			)
		);
		$this->add_control(
			'tm_dots',
			array(
				'label'        => __( 'Pagination dots', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function content_path() {
		$this->start_controls_section( 'sec_path', array( 'label' => __( 'Path', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'path_eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'A simple path forward', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'path_title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'From Backyard Idea to Finished Outdoor <em>Space.</em>',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'num',
			array(
				'label'       => __( 'Number', 'suncoast-ele-widgets' ),
				'description' => __( 'Leave empty to number automatically (01, 02, …).', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
			)
		);
		$rep->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Step', 'suncoast-ele-widgets' ),
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

		$this->add_control(
			'steps',
			array(
				'label'       => __( 'Steps', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => __( 'Tell us about your space', 'suncoast-ele-widgets' ), 'desc' => __( 'Share the patio, deck, or backyard idea.', 'suncoast-ele-widgets' ) ),
					array( 'title' => __( 'Design consultation', 'suncoast-ele-widgets' ), 'desc' => __( 'Explore layouts, colors, and options.', 'suncoast-ele-widgets' ) ),
					array( 'title' => __( 'Custom design', 'suncoast-ele-widgets' ), 'desc' => __( 'Tailored to the home and your goals.', 'suncoast-ele-widgets' ) ),
					array( 'title' => __( 'Professional installation', 'suncoast-ele-widgets' ), 'desc' => __( 'Installed and demonstrated locally.', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->add_control(
			'step_auto',
			array(
				'label'        => __( 'Advance automatically', 'suncoast-ele-widgets' ),
				'description'  => __( '01 → 02 → 03 → 04. Hovering or tabbing a step takes over.', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'step_delay',
			array(
				'label'     => __( 'Step duration (ms)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 800,
				'max'       => 12000,
				'step'      => 100,
				'default'   => 2600,
				'condition' => array( 'step_auto' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Link label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Start my free design consultation', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label'       => __( 'Link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#quote' ),
				'placeholder' => '#quote',
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1070px content, columns 442 / 581 with a 47px gap).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path' => '--sce-path-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-path-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cols',
			array(
				'label'       => __( 'Columns', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS grid-template-columns. Figma: <code>442fr 581fr</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '442fr 581fr',
				'selectors'   => array( '{{WRAPPER}} .sce-path' => '--sce-path-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'col_gap',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-path-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-path-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-path-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_card() {
		$this->start_controls_section(
			'sec_style_card',
			array( 'label' => __( 'Testimonial card', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path' => '--sce-card-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_border',
			array(
				'label'     => __( 'Border', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path' => '--sce-card-border: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-card-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-card-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_min_h',
			array(
				'label'      => __( 'Minimum height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 800 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-card-min-h: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'tm_text_heading',
			array( 'label' => __( 'Text', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'tm_eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tm_title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tm_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'tm_title_typo', 'selector' => '{{WRAPPER}} .sce-tm__title' )
		);
		$this->add_control(
			'star_color',
			array(
				'label'     => __( 'Stars', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__stars' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'quote_color',
			array(
				'label'     => __( 'Quote colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__quote' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'quote_typo', 'selector' => '{{WRAPPER}} .sce-tm__quote' )
		);
		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Active dot', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tm__dot.is-active' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_path() {
		$this->start_controls_section(
			'sec_style_path',
			array( 'label' => __( 'Path', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'path_eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'path_title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'path_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'path_title_typo', 'selector' => '{{WRAPPER}} .sce-path__title' )
		);

		$this->add_control(
			'step_heading',
			array( 'label' => __( 'Steps', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'num_color',
			array(
				'label'     => __( 'Number colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__num' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'step_dim',
			array(
				'label'       => __( 'Inactive number opacity', 'suncoast-ele-widgets' ),
				'description' => __( 'Figma: 0.4', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '' ),
				'range'       => array( '' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'selectors'   => array( '{{WRAPPER}} .sce-path' => '--sce-step-dim: {{SIZE}};' ),
			)
		);
		$this->add_control(
			'rule_color',
			array(
				'label'     => __( 'Connector line', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path' => '--sce-step-rule: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'step_pitch',
			array(
				'label'      => __( 'Space between steps', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-path' => '--sce-step-pitch: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'step_title_color',
			array(
				'label'     => __( 'Step heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__step-title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'step_desc_color',
			array(
				'label'     => __( 'Step description', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-path__step-desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Link colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-path__cta' => 'color: {{VALUE}}; border-bottom-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s  = $this->get_settings_for_display();
		$id = $this->get_id();

		$this->add_render_attribute(
			'root',
			array(
				'class'             => 'sce-path sce-scope',
				'data-slider-auto'  => 'yes' === $s['tm_auto'] ? 'yes' : 'no',
				'data-slider-delay' => (string) ( ! empty( $s['tm_delay'] ) ? (int) $s['tm_delay'] : 6000 ),
				'data-step-auto'    => 'yes' === $s['step_auto'] ? 'yes' : 'no',
				'data-step-delay'   => (string) ( ! empty( $s['step_delay'] ) ? (int) $s['step_delay'] : 2600 ),
			)
		);
		?>
		<section <?php $this->print_render_attribute_string( 'root' ); ?>>
			<div class="sce-path__inner">
				<div class="sce-path__grid">
					<?php $this->render_card( $s, $id ); ?>
					<?php $this->render_path( $s ); ?>
				</div>
			</div>
		</section>
		<?php
	}

	private function render_card( $s, $id ) {
		$slides = ! empty( $s['testimonials'] ) ? $s['testimonials'] : array();
		?>
		<div class="sce-tm sce-rv" style="--sce-rv-d:80ms;--sce-rv-y:22px">
			<?php if ( ! empty( $s['tm_eyebrow'] ) ) : ?>
				<span class="sce-tm__eyebrow"><?php echo esc_html( $s['tm_eyebrow'] ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $s['tm_title'] ) ) : ?>
				<h2 class="sce-tm__title"><?php echo SCE_Plugin::reveal_lines( $s['tm_title'], 140, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>

			<?php if ( 'yes' === $s['tm_mark'] ) : ?>
				<span class="sce-tm__mark" aria-hidden="true">&rdquo;</span>
			<?php endif; ?>

			<?php if ( $slides ) : ?>
				<div class="sce-tm__stage" aria-live="polite">
					<?php foreach ( $slides as $i => $t ) : ?>
						<article class="sce-tm__slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
						         id="<?php echo esc_attr( 'sce-tm-' . $id . '-' . $i ); ?>"
						         aria-hidden="<?php echo 0 === $i ? 'false' : 'true'; ?>"
						         <?php echo 0 === $i ? '' : 'inert'; ?>>

							<?php $this->stars( isset( $t['stars'] ) ? (int) $t['stars'] : 5 ); ?>

							<?php if ( ! empty( $t['quote'] ) ) : ?>
								<p class="sce-tm__quote"><?php echo nl2br( esc_html( $t['quote'] ) ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $t['name'] ) || ! empty( $t['badge'] ) ) : ?>
								<footer class="sce-tm__foot">
									<span class="sce-tm__name"><?php echo esc_html( $t['name'] ); ?></span>
									<?php if ( ! empty( $t['badge'] ) ) : ?>
										<span class="sce-tm__badge"><?php echo esc_html( $t['badge'] ); ?></span>
									<?php endif; ?>
								</footer>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( 'yes' === $s['tm_dots'] && count( $slides ) > 1 ) : ?>
					<div class="sce-tm__dots" role="tablist"
					     aria-label="<?php esc_attr_e( 'Choose a testimonial', 'suncoast-ele-widgets' ); ?>">
						<?php foreach ( $slides as $i => $t ) : ?>
							<button class="sce-tm__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button"
							        role="tab"
							        aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
							        aria-controls="<?php echo esc_attr( 'sce-tm-' . $id . '-' . $i ); ?>"
							        tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
								<span class="sce-sr">
									<?php
									/* translators: %d: slide number */
									printf( esc_html__( 'Testimonial %d', 'suncoast-ele-widgets' ), (int) $i + 1 );
									?>
								</span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_path( $s ) {
		$steps = ! empty( $s['steps'] ) ? $s['steps'] : array();
		?>
		<div class="sce-path__col">
			<?php if ( ! empty( $s['path_eyebrow'] ) ) : ?>
				<span class="sce-path__eyebrow sce-rv" style="--sce-rv-d:160ms"><?php echo esc_html( $s['path_eyebrow'] ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $s['path_title'] ) ) : ?>
				<h2 class="sce-path__title"><?php echo SCE_Plugin::reveal_lines( $s['path_title'], 220, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>

			<?php if ( $steps ) : ?>
				<ol class="sce-path__steps">
					<?php foreach ( $steps as $i => $st ) : ?>
						<li class="sce-path__step<?php echo 0 === $i ? ' is-active' : ''; ?> sce-rv"
						    style="--sce-rv-d:<?php echo (int) ( 300 + ( $i * 90 ) ); ?>ms;--sce-rv-y:16px"
						    tabindex="0">
							<span class="sce-path__num" aria-hidden="true">
								<?php echo esc_html( ! empty( $st['num'] ) ? $st['num'] : sprintf( '%02d', $i + 1 ) ); ?>
							</span>
							<?php if ( ! empty( $st['title'] ) ) : ?>
								<span class="sce-path__step-title"><?php echo esc_html( $st['title'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $st['desc'] ) ) : ?>
								<span class="sce-path__step-desc"><?php echo esc_html( $st['desc'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( ! empty( $s['cta_text'] ) ) : ?>
				<?php
				$url = ! empty( $s['cta_link']['url'] ) ? $s['cta_link']['url'] : '#';
				$att = ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
				if ( ! empty( $s['cta_link']['is_external'] ) ) {
					$att .= ' target="_blank" rel="noopener"';
				}
				if ( ! empty( $s['cta_link']['nofollow'] ) ) {
					$att .= ' rel="nofollow"';
				}
				?>
				<a class="sce-path__cta sce-rv" style="--sce-rv-d:700ms"<?php echo $att; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php echo esc_html( $s['cta_text'] ); ?>
					<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
						<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
					</svg>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	private function stars( $n ) {
		$n = max( 0, min( 5, $n ) );
		if ( ! $n ) {
			return;
		}
		$star = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"%s><path d="M12 1.5l3.09 6.26 6.91 1-5 4.87 1.18 6.87L12 17.25l-6.18 3.25L7 13.63l-5-4.87 6.91-1L12 1.5Z" fill="currentColor"/></svg>';
		printf(
			'<span class="sce-tm__stars" role="img" aria-label="%s">',
			/* translators: %d: number of stars */
			esc_attr( sprintf( _n( '%d out of 5 stars', '%d out of 5 stars', $n, 'suncoast-ele-widgets' ), $n ) )
		);
		for ( $i = 0; $i < 5; $i++ ) {
			printf( $star, $i < $n ? '' : ' class="is-empty"' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</span>';
	}
}
