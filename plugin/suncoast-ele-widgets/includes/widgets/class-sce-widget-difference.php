<?php
/**
 * Widget: The Suncoast Difference (dark compare band).
 *
 * Heading block and button on the left, two columns of label/description
 * pairs on the right, on a #1A1A1A band.
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

class SCE_Widget_Difference extends Widget_Base {

	public function get_name() {
		return 'suncoast_difference';
	}

	public function get_title() {
		return __( 'Suncoast Difference', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-menu-card';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'compare', 'difference', 'specs', 'features', 'dark' );
	}

	public function get_style_depends() {
		return array( 'sce-diff' );
	}

	public function get_script_depends() {
		return array( 'sce-reveal' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_lead();
		$this->content_items();
		$this->style_section();
		$this->style_lead();
		$this->style_items();
	}

	private function content_lead() {
		$this->start_controls_section( 'sec_lead', array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'The Suncoast difference', 'suncoast-ele-widgets' ),
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
				'default'     => 'Beautiful Below.<br>Serious Engineering <em>Above.</em>',
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
			'btn_text',
			array(
				'label'     => __( 'Button label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Learn more', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'btn_link',
			array(
				'label'       => __( 'Button link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#quote' ),
				'placeholder' => '#quote',
			)
		);
		$this->add_control(
			'btn_arrow',
			array(
				'label'        => __( 'Show arrow', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function content_items() {
		$this->start_controls_section( 'sec_items', array( 'label' => __( 'Features', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'items_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Items fill the two columns top-to-bottom, left column first — six items gives the Figma 3 × 2.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'text',
			array(
				'label'   => __( 'Description', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => '',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Features', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'label' => __( 'Motorized louvers', 'suncoast-ele-widgets' ), 'text' => __( 'Touch-button control', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Architectural extrusions', 'suncoast-ele-widgets' ), 'text' => __( 'Heavy-gauge aluminum structural louvers', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Smart controls', 'suncoast-ele-widgets' ), 'text' => __( 'Remote, phone + smart home', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Engineered rotation', 'suncoast-ele-widgets' ), 'text' => __( 'Full-range adaptive angle control', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Drainage', 'suncoast-ele-widgets' ), 'text' => __( 'Concealed water management', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Custom layouts', 'suncoast-ele-widgets' ), 'text' => __( 'Attached or freestanding', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->add_control(
			'item_cols',
			array(
				'label'   => __( 'Feature columns', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 3,
				'default' => 2,
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1072px content, #1A1A1A band, columns 432 / 286 / 286).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cols',
			array(
				'label'       => __( 'Columns', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS grid-template-columns. Figma: <code>432fr 286fr 286fr</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '432fr 286fr 286fr',
				'selectors'   => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'col_gap',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_lead() {
		$this->start_controls_section(
			'sec_style_lead',
			array( 'label' => __( 'Heading & button', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-diff__title' )
		);

		$this->add_control(
			'btn_heading',
			array( 'label' => __( 'Button', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-btn-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-btn-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_ink',
			array(
				'label'     => __( 'Label colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-btn-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'btn_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-btn-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_items() {
		$this->start_controls_section(
			'sec_style_items',
			array( 'label' => __( 'Features', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-diff__label' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'label_typo', 'selector' => '{{WRAPPER}} .sce-diff__label' )
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Description colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-diff__text' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'text_typo', 'selector' => '{{WRAPPER}} .sce-diff__text' )
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Space between features', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 90 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'label_gap',
			array(
				'label'      => __( 'Space below label', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-diff' => '--sce-diff-label-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s     = $this->get_settings_for_display();
		$tag   = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		$items = ! empty( $s['items'] ) ? $s['items'] : array();
		$cols  = max( 1, min( 3, (int) $s['item_cols'] ) );

		// Fill the columns top-to-bottom, left column first, so six items land
		// as the Figma 3 x 2 rather than snaking across the rows.
		$per    = (int) ceil( count( $items ) / $cols );
		$chunks = $per > 0 ? array_chunk( $items, $per ) : array();
		?>
		<section class="sce-diff sce-scope">
			<div class="sce-diff__inner">
				<div class="sce-diff__grid">

					<div class="sce-diff__lead">
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<span class="sce-diff__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $s['title'] ) ) : ?>
							<?php
							printf(
								'<%1$s class="sce-diff__title%2$s">',
								esc_attr( $tag ),
								'yes' === $s['accent_upright'] ? ' sce-diff__title--upright' : ''
							);
							echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
							printf( '</%s>', esc_attr( $tag ) );
							?>
						<?php endif; ?>

						<?php if ( ! empty( $s['btn_text'] ) ) : ?>
							<?php
							$url = ! empty( $s['btn_link']['url'] ) ? $s['btn_link']['url'] : '#';
							$att = ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
							if ( ! empty( $s['btn_link']['is_external'] ) ) {
								$att .= ' target="_blank" rel="noopener"';
							}
							if ( ! empty( $s['btn_link']['nofollow'] ) ) {
								$att .= ' rel="nofollow"';
							}
							?>
							<a class="sce-diff__btn sce-rv" style="--sce-rv-d:320ms"<?php echo $att; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
								<?php echo esc_html( $s['btn_text'] ); ?>
								<?php if ( 'yes' === $s['btn_arrow'] ) : ?>
									<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
										<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
									</svg>
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</div>

					<?php foreach ( $chunks as $ci => $chunk ) : ?>
						<ul class="sce-diff__col">
							<?php foreach ( $chunk as $ii => $it ) : ?>
								<li class="sce-diff__item sce-rv"
								    style="--sce-rv-d:<?php echo (int) ( 220 + ( ( ( $ci * 3 ) + $ii ) * 70 ) ); ?>ms;--sce-rv-y:16px">
									<?php if ( ! empty( $it['label'] ) ) : ?>
										<span class="sce-diff__label"><?php echo esc_html( $it['label'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $it['text'] ) ) : ?>
										<span class="sce-diff__text"><?php echo esc_html( $it['text'] ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>

				</div>
			</div>
		</section>
		<?php
	}
}
