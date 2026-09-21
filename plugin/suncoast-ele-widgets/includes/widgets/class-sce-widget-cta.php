<?php
/**
 * Widget: Call to Action (final emotional close).
 *
 * Full-bleed photograph behind a scrim, centred heading + sub-copy, one or two
 * buttons and a reassurance line.
 *
 * Style controls ship without defaults on purpose: untouched means the
 * hard-scoped CSS supplies the Figma values at every breakpoint.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class SCE_Widget_Cta extends Widget_Base {

	public function get_name() {
		return 'suncoast_cta';
	}

	public function get_title() {
		return __( 'Suncoast Call to Action', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'cta', 'call to action', 'banner', 'close', 'quote' );
	}

	public function get_style_depends() {
		return array( 'sce-cta' );
	}

	public function get_script_depends() {
		return array( 'sce-reveal' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_main();
		$this->content_buttons();
		$this->style_section();
		$this->style_text();
		$this->style_buttons();
	}

	private function content_main() {
		$this->start_controls_section( 'sec_main', array( 'label' => __( 'Content', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'bg_image',
			array(
				'label'   => __( 'Background image', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => '' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'bg_alt',
			array(
				'label'       => __( 'Image description', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Leave empty for a purely decorative photo — it is then hidden from screen readers.', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => 'The Best Part of Your Home<br>Was Never Going to Be<br><em>Inside.</em>',
				'dynamic'     => array( 'active' => true ),
				'separator'   => 'before',
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
			'sub',
			array(
				'label'   => __( 'Sub-text', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'One conversation is all it takes. We will measure, design and price your space — and you will know exactly what it costs before anyone picks up a tool.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'note',
			array(
				'label'   => __( 'Reassurance line', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Free consultation · No obligation · Licensed & insured', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	private function content_buttons() {
		$this->start_controls_section( 'sec_buttons', array( 'label' => __( 'Buttons', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'btn_text',
			array(
				'label'   => __( 'Primary label', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get my free design consultation', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'btn_link',
			array(
				'label'       => __( 'Primary link', 'suncoast-ele-widgets' ),
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
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'btn2_text',
			array(
				'label'       => __( 'Secondary label', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Call 1-877-449-5106', 'suncoast-ele-widgets' ),
				'separator'   => 'before',
				'description' => __( 'Leave empty to show a single button.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'btn2_link',
			array(
				'label'       => __( 'Secondary link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'placeholder' => 'tel:18774495106',
				'condition'   => array( 'btn2_text!' => '' ),
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
			'style_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Leave a field empty to keep the Figma value (600px stage, 860px content, centred).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_responsive_control(
			'min_h',
			array(
				'label'      => __( 'Minimum height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 260, 'max' => 900 ),
					'vh' => array( 'min' => 30, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-min-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 400, 'max' => 1400 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_block',
			array(
				'label'      => __( 'Vertical padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 240 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-pad-block: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'overlay',
			array(
				'label'       => __( 'Overlay', 'suncoast-ele-widgets' ),
				'description' => __( 'Any CSS background value. Darker keeps the white heading readable on a bright photo.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'linear-gradient(180deg, rgba(8,7,5,.62) 0%, rgba(8,7,5,.66) 100%)',
				'selectors'   => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-overlay: {{VALUE}};' ),
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
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-cta__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-cta__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'title_size',
			array(
				'label'      => __( 'Heading size', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-title-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-cta__title' )
		);
		$this->add_control(
			'sub_color',
			array(
				'label'     => __( 'Sub-text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-cta__sub' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'sub_typo', 'selector' => '{{WRAPPER}} .sce-cta__sub' )
		);
		$this->add_control(
			'note_color',
			array(
				'label'     => __( 'Reassurance colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-cta__note' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_buttons() {
		$this->start_controls_section(
			'sec_style_buttons',
			array( 'label' => __( 'Buttons', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-btn-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-btn-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_ink',
			array(
				'label'     => __( 'Label colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-btn-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'btn_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-cta' => '--sce-cta-btn-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s   = $this->get_settings_for_display();
		$tag = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		$bg  = ! empty( $s['bg_image']['url'] ) ? $s['bg_image']['url'] : '';
		$alt = trim( (string) $s['bg_alt'] );
		?>
		<section class="sce-cta sce-scope">
			<div class="sce-cta__stage">
				<?php if ( $bg ) : ?>
					<?php /* eager + high priority: this is usually the last large paint on the page */ ?>
					<img class="sce-cta__bg" src="<?php echo esc_url( $bg ); ?>"
					     alt="<?php echo esc_attr( $alt ); ?>"
					     <?php echo '' === $alt ? 'aria-hidden="true" ' : ''; ?>
					     loading="lazy" decoding="async">
				<?php endif; ?>
				<div class="sce-cta__scrim"></div>

				<div class="sce-cta__inner">
					<?php if ( ! empty( $s['title'] ) ) : ?>
						<?php
						printf( '<%s class="sce-cta__title">', esc_attr( $tag ) );
						echo SCE_Plugin::reveal_lines( $s['title'], 120, 100 ); // phpcs:ignore WordPress.Security.EscapeOutput
						printf( '</%s>', esc_attr( $tag ) );
						?>
					<?php endif; ?>

					<?php if ( ! empty( $s['sub'] ) ) : ?>
						<p class="sce-cta__sub sce-rv" style="--sce-rv-d:340ms"><?php echo esc_html( $s['sub'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $s['btn_text'] ) || ! empty( $s['btn2_text'] ) ) : ?>
						<div class="sce-cta__actions sce-rv" style="--sce-rv-d:440ms">
							<?php
							$this->button( $s['btn_text'], $s['btn_link'], '', 'yes' === $s['btn_arrow'] );
							$this->button( $s['btn2_text'], $s['btn2_link'], ' sce-cta__btn--ghost', false );
							?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $s['note'] ) ) : ?>
						<span class="sce-cta__note sce-rv" style="--sce-rv-d:540ms"><?php echo esc_html( $s['note'] ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}

	private function button( $text, $link, $extra_class, $arrow ) {
		if ( empty( $text ) ) {
			return;
		}

		$url = ! empty( $link['url'] ) ? $link['url'] : '#';
		// esc_url() strips a bare fragment, so anchors take the attr escape.
		$att = ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
		if ( ! empty( $link['is_external'] ) ) {
			$att .= ' target="_blank" rel="noopener"';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$att .= ' rel="nofollow"';
		}
		?>
		<a class="sce-cta__btn<?php echo esc_attr( $extra_class ); ?>"<?php echo $att; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php echo esc_html( $text ); ?>
			<?php if ( $arrow ) : ?>
				<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
					<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
				</svg>
			<?php endif; ?>
		</a>
		<?php
	}
}
