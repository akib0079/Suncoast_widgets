<?php
/**
 * Widget: Transform.
 *
 * Two columns — eyebrow, heading, a ticked feature list and a button on the
 * left, a single photograph on the right.
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

class SCE_Widget_Transform extends Widget_Base {

	public function get_name() {
		return 'suncoast_transform';
	}

	public function get_title() {
		return __( 'Suncoast Transform', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'transform', 'features', 'checklist', 'image', 'two column' );
	}

	public function get_style_depends() {
		return array( 'sce-transform' );
	}

	public function get_script_depends() {
		return array( 'sce-reveal' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_main();
		$this->content_items();
		$this->content_media();
		$this->style_section();
		$this->style_text();
		$this->style_button();
	}

	private function content_main() {
		$this->start_controls_section( 'sec_main', array( 'label' => __( 'Heading', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Do not just cover the patio', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — gold, same weight. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Transform <em>It.</em>',
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
			'btn_text',
			array(
				'label'     => __( 'Button label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Explore our story', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'btn_link',
			array(
				'label'       => __( 'Button link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#about' ),
				'placeholder' => '#about',
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

		$rep = new Repeater();
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Title', 'suncoast-ele-widgets' ),
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
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name' => __( 'Dinner — even when it rains', 'suncoast-ele-widgets' ),
						'text' => __( 'Keep the evening going when rain appears.', 'suncoast-ele-widgets' ),
					),
					array(
						'name' => __( 'Shade when you want it', 'suncoast-ele-widgets' ),
						'text' => __( 'Block afternoon sun while air circulates.', 'suncoast-ele-widgets' ),
					),
					array(
						'name' => __( 'Sunshine when you want it', 'suncoast-ele-widgets' ),
						'text' => __( 'Open the roof for natural light.', 'suncoast-ele-widgets' ),
					),
					array(
						'name' => __( 'More nights outside', 'suncoast-ele-widgets' ),
						'text' => __( 'Add lighting, heat, and screens.', 'suncoast-ele-widgets' ),
					),
				),
			)
		);

		$this->add_control(
			'tick_size',
			array(
				'label'      => __( 'Tick size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-tick: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'tick_color',
			array(
				'label'     => __( 'Tick colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__tick' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_media() {
		$this->start_controls_section( 'sec_media', array( 'label' => __( 'Photo', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => 'https://suncoastenclosures.com/wp-content/uploads/2026/09/Container-5.png' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'image_alt',
			array(
				'label'       => __( 'Image description', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Covered patio with motorised screens open to the view', 'suncoast-ele-widgets' ),
				'description' => __( 'Leave empty for a purely decorative photo — it is then hidden from screen readers.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'media_position',
			array(
				'label'       => __( 'Photo position', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'right',
				'options'     => array(
					'right' => __( 'Right (Figma)', 'suncoast-ele-widgets' ),
					'left'  => __( 'Left', 'suncoast-ele-widgets' ),
				),
				'description' => __( 'The copy always comes first in the markup, so screen readers and the single-column layout are unaffected.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_responsive_control(
			'media_h',
			array(
				'label'      => __( 'Photo height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 180, 'max' => 900 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-media-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'media_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-media-radius: {{SIZE}}{{UNIT}};' ),
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1072px content, columns 429 / 643, photo 643 × 599).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cols',
			array(
				'label'       => __( 'Columns', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS grid-template-columns. Figma: <code>429fr 643fr</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '429fr 643fr',
				'selectors'   => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'col_gap',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 140 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 260 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 260 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_text() {
		$this->start_controls_section(
			'sec_style_text',
			array( 'label' => __( 'Typography', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Heading size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-title-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-tf__title' )
		);

		$this->add_control(
			'item_heading',
			array( 'label' => __( 'Features', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Title colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__name' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'name_typo', 'selector' => '{{WRAPPER}} .sce-tf__name' )
		);
		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Description colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf__desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'desc_typo', 'selector' => '{{WRAPPER}} .sce-tf__desc' )
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => __( 'Space between features', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 64 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'title_mb',
			array(
				'label'      => __( 'Space below heading', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-title-mb: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_button() {
		$this->start_controls_section(
			'sec_style_button',
			array( 'label' => __( 'Button', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-btn-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-btn-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_ink',
			array(
				'label'     => __( 'Label colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-btn-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'btn_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-btn-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'list_mb',
			array(
				'label'      => __( 'Space above button', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 90 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-tf' => '--sce-tf-list-mb: {{SIZE}}{{UNIT}};' ),
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
		$left  = 'left' === $s['media_position'];
		?>
		<section class="sce-tf sce-scope<?php echo $left ? ' sce-tf--media-left' : ''; ?>">
			<div class="sce-tf__inner">
				<div class="sce-tf__grid">

					<div class="sce-tf__col">
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<span class="sce-tf__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $s['title'] ) ) : ?>
							<?php
							printf( '<%s class="sce-tf__title">', esc_attr( $tag ) );
							echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
							printf( '</%s>', esc_attr( $tag ) );
							?>
						<?php endif; ?>

						<?php if ( $items ) : ?>
							<ul class="sce-tf__list">
								<?php foreach ( $items as $i => $it ) : ?>
									<li class="sce-tf__item sce-rv" style="--sce-rv-d:<?php echo (int) ( 240 + ( $i * 80 ) ); ?>ms;--sce-rv-y:14px">
										<?php $this->icon_tick(); ?>
										<?php if ( ! empty( $it['name'] ) ) : ?>
											<span class="sce-tf__name"><?php echo esc_html( $it['name'] ); ?></span>
										<?php endif; ?>
										<?php if ( ! empty( $it['text'] ) ) : ?>
											<span class="sce-tf__desc"><?php echo esc_html( $it['text'] ); ?></span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! empty( $s['btn_text'] ) ) : ?>
							<?php
							$url = ! empty( $s['btn_link']['url'] ) ? $s['btn_link']['url'] : '#';
							// esc_url() strips a bare fragment, so anchors take the attr escape.
							$att = ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
							if ( ! empty( $s['btn_link']['is_external'] ) ) {
								$att .= ' target="_blank"';
							}
							if ( empty( $s['btn_link']['nofollow'] ) && ! empty( $s['btn_link']['is_external'] ) ) {
								$att .= ' rel="noopener"';
							}
							if ( ! empty( $s['btn_link']['nofollow'] ) ) {
								$att .= ' rel="noopener nofollow"';
							}
							?>
							<a class="sce-tf__btn sce-rv" style="--sce-rv-d:<?php echo (int) ( 280 + ( count( $items ) * 80 ) ); ?>ms"<?php echo $att; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
								<?php echo esc_html( $s['btn_text'] ); ?>
								<?php if ( 'yes' === $s['btn_arrow'] ) : ?>
									<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
										<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
									</svg>
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</div>

					<?php
					$img = ! empty( $s['image']['url'] ) ? $s['image']['url'] : '';
					$alt = trim( (string) $s['image_alt'] );
					?>
					<?php if ( $img ) : ?>
						<div class="sce-tf__media sce-rv" style="--sce-rv-d:160ms">
							<img class="sce-tf__img" src="<?php echo esc_url( $img ); ?>"
							     alt="<?php echo esc_attr( $alt ); ?>"
							     <?php echo '' === $alt ? 'aria-hidden="true" ' : ''; ?>
							     loading="lazy" decoding="async">
						</div>
					<?php endif; ?>

				</div>
			</div>
		</section>
		<?php
	}

	private function icon_tick() {
		?>
		<svg class="sce-tf__tick" viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
			<path d="M2.7 8.6 6.3 12.2 13.4 4.4" stroke="currentColor" stroke-width="2"
			      stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php
	}
}
