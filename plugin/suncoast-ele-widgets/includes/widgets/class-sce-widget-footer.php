<?php
/**
 * Widget: Footer.
 *
 * Logo, phone block and an outlined CTA on the top row; a hairline divider;
 * navigation links and legal text on the bottom row.
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

class SCE_Widget_Footer extends Widget_Base {

	public function get_name() {
		return 'suncoast_footer';
	}

	public function get_title() {
		return __( 'Suncoast Footer', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'footer', 'bottom', 'links', 'legal', 'contact' );
	}

	public function get_style_depends() {
		return array( 'sce-footer' );
	}

	public function get_script_depends() {
		return array( 'sce-reveal' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_brand();
		$this->content_links();
		$this->style_section();
		$this->style_text();
	}

	private function content_brand() {
		$this->start_controls_section( 'sec_brand', array( 'label' => __( 'Top row', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'logo',
			array(
				'label'   => __( 'Logo', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => '' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'logo_text',
			array(
				'label'       => __( 'Wordmark fallback', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Suncoast Enclosures', 'suncoast-ele-widgets' ),
				'description' => __( 'Shown when no logo image is set, and used as the logo’s alt text.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'logo_link',
			array(
				'label'       => __( 'Logo link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '/' ),
				'placeholder' => '/',
			)
		);

		$this->add_control(
			'phone_label',
			array(
				'label'     => __( 'Phone label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Speak to a designer', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'phone',
			array(
				'label'   => __( 'Phone number', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '1-877-449-5106',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Button label', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Request a quote', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label'       => __( 'Button link', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#quote' ),
				'placeholder' => '#quote',
			)
		);

		$this->end_controls_section();
	}

	private function content_links() {
		$this->start_controls_section( 'sec_links', array( 'label' => __( 'Bottom row', 'suncoast-ele-widgets' ) ) );

		$rep = new Repeater();
		$rep->add_control(
			'text',
			array(
				'label'   => __( 'Label', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Link', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => __( 'Navigation links', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Louvered pergolas', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#benefits' ) ),
					array( 'text' => __( 'Projects', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#projects' ) ),
					array( 'text' => __( 'The process', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#process' ) ),
					array( 'text' => __( 'FAQ', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#faq' ) ),
					array( 'text' => __( 'Contact', 'suncoast-ele-widgets' ), 'link' => array( 'url' => '#quote' ) ),
				),
			)
		);

		$this->add_control(
			'legal',
			array(
				'label'       => __( 'Legal text', 'suncoast-ele-widgets' ),
				'description' => __( '{year} is replaced with the current year. Links are allowed.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( '© {year} Suncoast Enclosures. Licensed &amp; insured. All rights reserved.', 'suncoast-ele-widgets' ),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'show_rule',
			array(
				'label'        => __( 'Show divider', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1072px content, #181818 band, 52 / 51 padding).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'logo_h',
			array(
				'label'      => __( 'Logo height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 140 ) ),
				'separator'  => 'before',
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-logo-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'rule_color',
			array(
				'label'     => __( 'Divider colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-rule: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_text() {
		$this->start_controls_section(
			'sec_style_text',
			array( 'label' => __( 'Text & links', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'ink',
			array(
				'label'     => __( 'Muted text colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'ink_hover',
			array(
				'label'     => __( 'Link colour (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-ink-hover: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'link_typo', 'selector' => '{{WRAPPER}} .sce-footer__link' )
		);
		$this->add_responsive_control(
			'link_gap',
			array(
				'label'      => __( 'Space between links', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 4, 'max' => 64 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-link-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'phone_color',
			array(
				'label'     => __( 'Phone number colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-footer__contact-value' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'phone_typo', 'selector' => '{{WRAPPER}} .sce-footer__contact-value' )
		);
		$this->add_control(
			'cta_ink',
			array(
				'label'     => __( 'Button colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-footer__cta' => 'color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Button radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-footer' => '--sce-ftr-btn-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	protected function render() {
		$s     = $this->get_settings_for_display();
		$links = ! empty( $s['links'] ) ? $s['links'] : array();
		?>
		<footer class="sce-footer sce-scope">
			<div class="sce-footer__inner">

				<div class="sce-footer__top">
					<?php $this->render_brand( $s ); ?>
					<?php $this->render_phone( $s ); ?>
					<?php $this->render_cta( $s ); ?>
				</div>

				<?php if ( 'yes' === $s['show_rule'] ) : ?>
					<div class="sce-footer__rule" role="presentation"></div>
				<?php endif; ?>

				<div class="sce-footer__bottom">
					<?php if ( $links ) : ?>
						<nav class="sce-footer__nav sce-rv" style="--sce-rv-d:220ms"
						     aria-label="<?php esc_attr_e( 'Footer', 'suncoast-ele-widgets' ); ?>">
							<?php foreach ( $links as $l ) : ?>
								<?php if ( empty( $l['text'] ) ) { continue; } ?>
								<a class="sce-footer__link"<?php echo $this->link_attrs( $l['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
									<?php echo esc_html( $l['text'] ); ?>
								</a>
							<?php endforeach; ?>
						</nav>
					<?php endif; ?>

					<?php if ( ! empty( $s['legal'] ) ) : ?>
						<p class="sce-footer__legal sce-rv" style="--sce-rv-d:280ms">
							<?php
							$legal = str_replace( '{year}', gmdate( 'Y' ), $s['legal'] );
							echo wp_kses(
								$legal,
								array(
									'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
									'br'     => array(),
									'strong' => array(),
									'em'     => array(),
								)
							);
							?>
						</p>
					<?php endif; ?>
				</div>

			</div>
		</footer>
		<?php
	}

	private function render_brand( $s ) {
		$url  = ! empty( $s['logo']['url'] ) ? $s['logo']['url'] : '';
		$name = (string) $s['logo_text'];
		if ( '' === $url && '' === $name ) {
			return;
		}
		?>
		<a class="sce-footer__brand sce-rv" style="--sce-rv-d:40ms"<?php echo $this->link_attrs( $s['logo_link'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php if ( $url ) : ?>
				<img class="sce-footer__logo" src="<?php echo esc_url( $url ); ?>"
				     alt="<?php echo esc_attr( $name ); ?>" loading="lazy" decoding="async">
			<?php else : ?>
				<span class="sce-footer__wordmark"><?php echo esc_html( $name ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}

	private function render_phone( $s ) {
		if ( empty( $s['phone'] ) ) {
			return;
		}
		$digits = preg_replace( '/[^\d+]/', '', $s['phone'] );
		?>
		<a class="sce-footer__contact sce-rv" style="--sce-rv-d:110ms" href="tel:<?php echo esc_attr( $digits ); ?>">
			<span class="sce-footer__contact-icon" aria-hidden="true">
				<svg viewBox="0 0 16 16" fill="none" focusable="false">
					<path d="M14.6 11.3v2.1a1.4 1.4 0 0 1-1.53 1.4 13.9 13.9 0 0 1-6.05-2.15 13.65 13.65 0 0 1-4.2-4.2A13.9 13.9 0 0 1 .67 2.38 1.4 1.4 0 0 1 2.06.85h2.1a1.4 1.4 0 0 1 1.4 1.2c.09.67.25 1.32.48 1.95a1.4 1.4 0 0 1-.32 1.48l-.89.89a11.2 11.2 0 0 0 4.2 4.2l.89-.89a1.4 1.4 0 0 1 1.48-.31c.63.23 1.28.39 1.95.47a1.4 1.4 0 0 1 1.2 1.42Z"
					      stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
			<span>
				<?php if ( ! empty( $s['phone_label'] ) ) : ?>
					<span class="sce-footer__contact-label"><?php echo esc_html( $s['phone_label'] ); ?></span>
				<?php endif; ?>
				<span class="sce-footer__contact-value"><?php echo esc_html( $s['phone'] ); ?></span>
			</span>
		</a>
		<?php
	}

	private function render_cta( $s ) {
		if ( empty( $s['cta_text'] ) ) {
			return;
		}
		?>
		<a class="sce-footer__cta sce-rv" style="--sce-rv-d:180ms"<?php echo $this->link_attrs( $s['cta_link'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php echo esc_html( $s['cta_text'] ); ?>
			<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">
				<path d="M12.175 9H0V7h12.175l-5.6-5.6L8 0l8 8-8 8-1.425-1.4L12.175 9Z" fill="currentColor"/>
			</svg>
		</a>
		<?php
	}

	/**
	 * esc_url() strips a bare fragment, so anchors take the attribute escape.
	 */
	private function link_attrs( $link ) {
		$url = ! empty( $link['url'] ) ? $link['url'] : '#';
		$out = ' href="' . ( 0 === strpos( $url, '#' ) ? esc_attr( $url ) : esc_url( $url ) ) . '"';
		if ( ! empty( $link['is_external'] ) ) {
			$out .= ' target="_blank" rel="noopener"';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$out .= ' rel="nofollow"';
		}
		return $out;
	}
}
