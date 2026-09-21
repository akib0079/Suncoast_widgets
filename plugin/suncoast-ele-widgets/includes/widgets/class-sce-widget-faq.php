<?php
/**
 * Widget: FAQ & Form.
 *
 * A dark lead-capture card beside an accordion of common questions.
 *
 * The card can render the plugin's own form — which posts to SCE_Forms and
 * triggers the admin notification template, exactly like the hero banner — or
 * an Elementor saved template containing an Elementor Pro Form.
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

class SCE_Widget_Faq extends Widget_Base {

	public function get_name() {
		return 'suncoast_faq';
	}

	public function get_title() {
		return __( 'Suncoast FAQ & Form', 'suncoast-ele-widgets' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_categories() {
		return array( SCE_Plugin::CATEGORY );
	}

	public function get_keywords() {
		return array( 'suncoast', 'faq', 'accordion', 'questions', 'form', 'quote', 'contact' );
	}

	public function get_style_depends() {
		return array( 'sce-faq' );
	}

	public function get_script_depends() {
		return array( 'sce-faq' );
	}

	/* ====================================================================
	   CONTROLS
	   ==================================================================== */

	protected function register_controls() {
		$this->content_layout();
		$this->content_form();
		$this->content_fields();
		$this->content_notify();
		$this->content_faq();
		$this->style_section();
		$this->style_card();
		$this->style_faq();
	}

	private function content_layout() {
		$this->start_controls_section( 'sec_layout', array( 'label' => __( 'Layout', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'form_position',
			array(
				'label'       => __( 'Form position', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'left',
				'options'     => array(
					'left'  => __( 'Left (Figma)', 'suncoast-ele-widgets' ),
					'right' => __( 'Right', 'suncoast-ele-widgets' ),
				),
				'description' => __( 'The form always comes first in the markup, so screen readers and the single-column layout are unaffected.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'section_id',
			array(
				'label'       => __( 'Anchor ID', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'quote',
				'placeholder' => 'quote',
				'description' => __( 'Lets the header nav and every “Request a quote” button scroll here.', 'suncoast-ele-widgets' ),
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
					'builtin'   => __( 'This plugin (recommended)', 'suncoast-ele-widgets' ),
					'elementor' => __( 'Elementor saved template', 'suncoast-ele-widgets' ),
				),
			)
		);
		$this->add_control(
			'form_template',
			array(
				'label'       => __( 'Template', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->template_options(),
				'default'     => '',
				'condition'   => array( 'form_source' => 'elementor' ),
				'description' => __( 'Any saved template. An Elementor Pro Form inside it is re-skinned to match this card, and its own Actions After Submit send the notification.', 'suncoast-ele-widgets' ),
			)
		);

		$this->add_control(
			'card_title',
			array(
				'label'       => __( 'Card heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt; — serif italic in gold.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Request Your Free<br>Design <em>Consultation</em>',
				'dynamic'     => array( 'active' => true ),
				'separator'   => 'before',
			)
		);
		$this->add_control(
			'card_sub',
			array(
				'label'   => __( 'Card sub-text', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Sizing, options and a firm number — no obligation.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'submit_text',
			array(
				'label'   => __( 'Submit label', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Request my free consultation', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'note_text',
			array(
				'label'       => __( 'Note under the button', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'No spam. No pushy sales calls.', 'suncoast-ele-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_fields() {
		$this->start_controls_section(
			'sec_fields',
			array(
				'label'     => __( 'Fields', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);

		$this->add_control(
			'fields_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Name and phone are always required — the endpoint rejects a lead without them. Switch off any of the rest.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		foreach ( $this->field_map() as $key => $def ) {
			if ( ! $def['optional'] ) {
				continue;
			}
			$this->add_control(
				'show_' . $key,
				array(
					/* translators: %s: field label */
					'label'        => sprintf( __( 'Show “%s”', 'suncoast-ele-widgets' ), $def['label'] ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => $def['on'] ? 'yes' : '',
					'return_value' => 'yes',
				)
			);
		}

		foreach ( $this->field_map() as $key => $def ) {
			$this->add_control(
				'label_' . $key,
				array(
					/* translators: %s: field name */
					'label'     => sprintf( __( '%s label', 'suncoast-ele-widgets' ), $def['label'] ),
					'type'      => Controls_Manager::TEXT,
					'default'   => $def['label'],
					'separator' => 'name' === $key ? 'before' : '',
					'condition' => $def['optional'] ? array( 'show_' . $key => 'yes' ) : array(),
				)
			);
		}

		$this->add_control(
			'select_placeholder',
			array(
				'label'     => __( 'Project type placeholder', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Select an option', 'suncoast-ele-widgets' ),
				'separator' => 'before',
				'condition' => array( 'show_project_type' => 'yes' ),
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'label',
			array(
				'label'   => __( 'Option', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Option', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'select_options',
			array(
				'label'       => __( 'Project type options', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_project_type' => 'yes' ),
				'default'     => array(
					array( 'label' => __( 'Louvered pergola', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Screen enclosure', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Pool cage', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Patio cover', 'suncoast-ele-widgets' ) ),
					array( 'label' => __( 'Not sure yet', 'suncoast-ele-widgets' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function content_notify() {
		$this->start_controls_section(
			'sec_notify',
			array(
				'label'     => __( 'Notification', 'suncoast-ele-widgets' ),
				'condition' => array( 'form_source' => 'builtin' ),
			)
		);

		$this->add_control(
			'notify_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The recipient is read from these saved settings on the server, never from the browser, so the endpoint cannot be tricked into mailing someone else. Leads are also listed under <strong>Suncoast Leads</strong>.', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'admin_email',
			array(
				'label'       => __( 'Send notifications to', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => get_option( 'admin_email' ),
				'description' => __( 'Comma-separate several addresses. Empty uses the site admin address.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'admin_subject',
			array(
				'label'       => __( 'Subject', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'New consultation request — {name} ({zip})', 'suncoast-ele-widgets' ),
				'description' => __( 'Placeholders: {name}, {zip}, {project}.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'msg_success',
			array(
				'label'     => __( 'Success message', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Thank you — we will be in touch within one business day.', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'msg_invalid',
			array(
				'label'   => __( 'Validation message', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Please complete the highlighted fields.', 'suncoast-ele-widgets' ),
			)
		);
		$this->add_control(
			'msg_error',
			array(
				'label'   => __( 'Error message', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Something went wrong. Please call us instead.', 'suncoast-ele-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	private function content_faq() {
		$this->start_controls_section( 'sec_faq', array( 'label' => __( 'FAQ', 'suncoast-ele-widgets' ) ) );

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Sub-heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Answers first', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'suncoast-ele-widgets' ),
				'description' => __( 'Wrap the accent in &lt;em&gt;. &lt;br&gt; splits the masked reveal into lines.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Everything You Want<br>To Know, <em>Answered.</em>',
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
			'intro',
			array(
				'label'   => __( 'Paragraph', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'No jargon and no guesswork. These are the questions homeowners ask us most before an install, answered the same way we would answer them standing on your patio — real timelines, real numbers, and nothing buried in the small print. Still unclear on something? Put it in the form and we will cover it on the call.', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'eyebrow_2',
			array(
				'label'     => __( 'List sub-heading', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Common questions', 'suncoast-ele-widgets' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'List heading', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Before you book a consultation', 'suncoast-ele-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'q',
			array(
				'label'   => __( 'Question', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Question', 'suncoast-ele-widgets' ),
			)
		);
		$rep->add_control(
			'a',
			array(
				'label'   => __( 'Answer', 'suncoast-ele-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '',
			)
		);
		$rep->add_control(
			'open',
			array(
				'label'        => __( 'Open by default', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Questions', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ q }}}',
				'separator'   => 'before',
				'default'     => array(
					array(
						'q'    => __( 'How long does an installation take?', 'suncoast-ele-widgets' ),
						'a'    => __( 'Most louvered pergolas are installed in one to three days once the structure arrives. Permitting and fabrication run four to six weeks before that, and we give you the real calendar up front — not a best case.', 'suncoast-ele-widgets' ),
						'open' => 'yes',
					),
					array(
						'q' => __( 'Will it hold up to a Florida storm?', 'suncoast-ele-widgets' ),
						'a' => __( 'Every structure is engineered and permitted to the wind load for your address, using heavy-gauge aluminium extrusions and concealed structural fasteners. You get the stamped engineering with your package.', 'suncoast-ele-widgets' ),
					),
					array(
						'q' => __( 'Do you handle permits and inspections?', 'suncoast-ele-widgets' ),
						'a' => __( 'Yes. We prepare the drawings, submit to your county or municipality, and meet the inspector on site. You never have to chase a permit office.', 'suncoast-ele-widgets' ),
					),
					array(
						'q' => __( 'What does it cost?', 'suncoast-ele-widgets' ),
						'a' => __( 'It depends on span, mounting and options such as motorised louvers, screens or lighting. After the free consultation you get one fixed number that covers the build, the permit and the install.', 'suncoast-ele-widgets' ),
					),
					array(
						'q' => __( 'What maintenance does it need?', 'suncoast-ele-widgets' ),
						'a' => __( 'Rinse the louvers a few times a year. The powder-coated finish is warranted against peeling and chalking, and the drainage is built into the frame so there is nothing to clear out.', 'suncoast-ele-widgets' ),
					),
				),
			)
		);

		$this->add_control(
			'single_open',
			array(
				'label'        => __( 'One answer open at a time', 'suncoast-ele-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
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
				'raw'             => __( 'Leave a field empty to keep the Figma value (1072px content, columns 445 / 585, 42px gap).', 'suncoast-ele-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->add_control(
			'bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'max_w',
			array(
				'label'      => __( 'Content width', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 600, 'max' => 1600 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-max: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'shell_pad',
			array(
				'label'      => __( 'Side gutter', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-shell-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'cols',
			array(
				'label'       => __( 'Columns', 'suncoast-ele-widgets' ),
				'description' => __( 'CSS grid-template-columns. Figma: <code>445fr 585fr</code>.', 'suncoast-ele-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '445fr 585fr',
				'selectors'   => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-cols: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'col_gap',
			array(
				'label'      => __( 'Column gap', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 140 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_top',
			array(
				'label'      => __( 'Padding top', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 260 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-pad-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 260 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-pad-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_card() {
		$this->start_controls_section(
			'sec_style_card',
			array( 'label' => __( 'Form card', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Card background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-card-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_radius',
			array(
				'label'      => __( 'Corner radius', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-card-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Card padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .sce-faq' => '--sce-faq-card-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'card_title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-faq__card-title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card-title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'card_title_typo', 'selector' => '{{WRAPPER}} .sce-faq__card-title' )
		);

		$this->add_control(
			'fld_heading',
			array( 'label' => __( 'Fields', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'fld_bg',
			array(
				'label'     => __( 'Field background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-fld-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'fld_ink',
			array(
				'label'     => __( 'Field text', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-fld-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'fld_border',
			array(
				'label'     => __( 'Field border', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-fld-border: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'fld_h',
			array(
				'label'      => __( 'Field height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 34, 'max' => 80 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-fld-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'fld_gap',
			array(
				'label'      => __( 'Space between fields', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-fld-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'btn_heading',
			array( 'label' => __( 'Submit button', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'btn_bg',
			array(
				'label'     => __( 'Background', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-btn-bg: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => __( 'Background (hover)', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-btn-bg-hover: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_ink',
			array(
				'label'     => __( 'Label colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-btn-ink: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_h',
			array(
				'label'      => __( 'Height', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 34, 'max' => 90 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq__card .sce-form' => '--sce-btn-h: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	private function style_faq() {
		$this->start_controls_section(
			'sec_style_faq',
			array( 'label' => __( 'FAQ column', 'suncoast-ele-widgets' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => __( 'Sub-heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Heading colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'title_accent',
			array(
				'label'     => __( 'Accent colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__title em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .sce-faq__title' )
		);
		$this->add_control(
			'intro_color',
			array(
				'label'     => __( 'Paragraph colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-faq__intro' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'intro_typo', 'selector' => '{{WRAPPER}} .sce-faq__intro' )
		);

		$this->add_control(
			'q_heading',
			array( 'label' => __( 'Accordion', 'suncoast-ele-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' )
		);
		$this->add_control(
			'q_color',
			array(
				'label'     => __( 'Question colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__q' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'q_typo', 'selector' => '{{WRAPPER}} .sce-faq__q' )
		);
		$this->add_control(
			'a_color',
			array(
				'label'     => __( 'Answer colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'a_typo', 'selector' => '{{WRAPPER}} .sce-faq__a' )
		);
		$this->add_control(
			'faq_line',
			array(
				'label'     => __( 'Divider colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-line: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon colour', 'suncoast-ele-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sce-faq__icon' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'item_pad',
			array(
				'label'      => __( 'Row padding', 'suncoast-ele-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 56 ) ),
				'selectors'  => array( '{{WRAPPER}} .sce-faq' => '--sce-faq-item-pad: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/* ====================================================================
	   RENDER
	   ==================================================================== */

	/**
	 * The canonical field set. Names match what SCE_Forms reads, so the
	 * banner form and this one land in the same inbox and the same CPT.
	 */
	private function field_map() {
		return array(
			'name'         => array( 'label' => __( 'Full name', 'suncoast-ele-widgets' ), 'optional' => false, 'on' => true ),
			'phone'        => array( 'label' => __( 'Phone', 'suncoast-ele-widgets' ), 'optional' => false, 'on' => true ),
			'email'        => array( 'label' => __( 'Email', 'suncoast-ele-widgets' ), 'optional' => true, 'on' => true ),
			'zip'          => array( 'label' => __( 'Zip code', 'suncoast-ele-widgets' ), 'optional' => true, 'on' => true ),
			'project_type' => array( 'label' => __( 'Project type', 'suncoast-ele-widgets' ), 'optional' => true, 'on' => true ),
			'message'      => array( 'label' => __( 'Tell us about your space', 'suncoast-ele-widgets' ), 'optional' => true, 'on' => false ),
		);
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$tag   = in_array( $s['title_tag'], array( 'h2', 'h3', 'h4', 'div' ), true ) ? $s['title_tag'] : 'h2';
		$id    = sanitize_html_class( (string) $s['section_id'] );
		$right = 'right' === $s['form_position'];
		?>
		<section class="sce-faq sce-scope<?php echo $right ? ' sce-faq--form-right' : ''; ?>"
			<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>
			data-faq-single="<?php echo 'yes' === $s['single_open'] ? 'yes' : 'no'; ?>">
			<div class="sce-faq__inner">
				<div class="sce-faq__grid">

					<div class="sce-faq__aside">
						<div class="sce-faq__card sce-rv" style="--sce-rv-d:60ms">
							<?php $this->render_card_head( $s ); ?>
							<?php $this->render_form( $s ); ?>
						</div>
					</div>

					<div class="sce-faq__main">
						<?php $this->render_faq( $s, $tag ); ?>
					</div>

				</div>
			</div>
		</section>
		<?php
	}

	private function render_card_head( $s ) {
		$allow = array(
			'em'     => array(),
			'i'      => array(),
			'strong' => array(),
			'b'      => array(),
			'br'     => array(),
		);
		if ( ! empty( $s['card_title'] ) ) {
			echo '<div class="sce-faq__card-title">' . wp_kses( $s['card_title'], $allow ) . '</div>';
		}
		if ( ! empty( $s['card_sub'] ) ) {
			echo '<p class="sce-faq__card-sub">' . esc_html( $s['card_sub'] ) . '</p>';
		}
	}

	private function render_faq( $s, $tag ) {
		$items = ! empty( $s['items'] ) ? $s['items'] : array();
		?>
		<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
			<span class="sce-faq__eyebrow sce-rv" style="--sce-rv-d:40ms"><?php echo esc_html( $s['eyebrow'] ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $s['title'] ) ) : ?>
			<?php
			printf( '<%s class="sce-faq__title">', esc_attr( $tag ) );
			echo SCE_Plugin::reveal_lines( $s['title'], 110, 90 ); // phpcs:ignore WordPress.Security.EscapeOutput
			printf( '</%s>', esc_attr( $tag ) );
			?>
		<?php endif; ?>

		<?php if ( ! empty( $s['intro'] ) ) : ?>
			<p class="sce-faq__intro sce-rv" style="--sce-rv-d:260ms"><?php echo esc_html( $s['intro'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $s['eyebrow_2'] ) ) : ?>
			<span class="sce-faq__eyebrow sce-faq__eyebrow--2 sce-rv" style="--sce-rv-d:320ms"><?php echo esc_html( $s['eyebrow_2'] ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $s['subtitle'] ) ) : ?>
			<div class="sce-faq__subtitle sce-rv" style="--sce-rv-d:360ms"><?php echo esc_html( $s['subtitle'] ); ?></div>
		<?php endif; ?>

		<?php if ( $items ) : ?>
			<div class="sce-faq__list">
				<?php foreach ( $items as $i => $it ) : ?>
					<?php
					$uid  = $this->get_id() . '-' . $i;
					$open = 'yes' === $it['open'];
					?>
					<div class="sce-faq__item sce-rv<?php echo $open ? ' is-open' : ''; ?>"
					     style="--sce-rv-d:<?php echo (int) ( 420 + ( $i * 70 ) ); ?>ms">
						<h3 class="sce-faq__qw">
							<button class="sce-faq__q" type="button"
							        id="sce-faq-q-<?php echo esc_attr( $uid ); ?>"
							        aria-expanded="<?php echo $open ? 'true' : 'false'; ?>"
							        aria-controls="sce-faq-p-<?php echo esc_attr( $uid ); ?>">
								<span><?php echo esc_html( $it['q'] ); ?></span>
								<span class="sce-faq__icon" aria-hidden="true"></span>
							</button>
						</h3>
						<div class="sce-faq__panel" id="sce-faq-p-<?php echo esc_attr( $uid ); ?>"
						     role="region" aria-labelledby="sce-faq-q-<?php echo esc_attr( $uid ); ?>"
						     <?php echo $open ? '' : 'hidden'; ?>>
							<div class="sce-faq__a"><?php echo wp_kses_post( $it['a'] ); ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/* ---------------------------------------------------------------- form */

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
				echo '<p class="sce-faq__card-sub">' . esc_html__( 'Pick a saved template under Form → Template.', 'suncoast-ele-widgets' ) . '</p>';
			}
			return;
		}

		$wid = $this->get_id();
		?>
		<form class="sce-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
		      data-ajax="yes"
		      data-msg-invalid="<?php echo esc_attr( $s['msg_invalid'] ); ?>"
		      data-msg-sending="<?php esc_attr_e( 'Sending…', 'suncoast-ele-widgets' ); ?>"
		      data-msg-success="<?php echo esc_attr( $s['msg_success'] ); ?>"
		      data-msg-error="<?php echo esc_attr( $s['msg_error'] ); ?>">

			<input type="hidden" name="post_id" value="<?php echo (int) get_the_ID(); ?>">
			<input type="hidden" name="widget_id" value="<?php echo esc_attr( $wid ); ?>">
			<input type="hidden" name="sce_t" value="<?php echo (int) time(); ?>">
			<?php /* Honeypot: off-screen, not display:none, so bots still fill it. */ ?>
			<div class="sce-sr" aria-hidden="true">
				<label for="sce-hp-<?php echo esc_attr( $wid ); ?>"><?php esc_html_e( 'Leave this field empty', 'suncoast-ele-widgets' ); ?></label>
				<input type="text" id="sce-hp-<?php echo esc_attr( $wid ); ?>" name="sce_hp" tabindex="-1" autocomplete="off">
			</div>

			<p class="sce-form__msg" aria-live="polite"></p>

			<div class="sce-form__fields">
				<?php
				$this->text_field( 'name', 'full_name', 'text', $s, array( 'autocomplete' => 'name', 'required' => true ) );
				$this->text_field( 'phone', 'phone', 'tel', $s, array( 'autocomplete' => 'tel', 'required' => true, 'data-mask' => 'tel' ) );
				$this->text_field( 'email', 'email', 'email', $s, array( 'autocomplete' => 'email' ) );
				$this->text_field(
					'zip',
					'zip',
					'text',
					$s,
					array(
						'autocomplete' => 'postal-code',
						'data-mask'    => 'zip',
						'inputmode'    => 'numeric',
						'pattern'      => '\d{5}',
						'maxlength'    => '5',
					)
				);
				$this->select_field( $s );
				$this->textarea_field( $s );
				?>
			</div>

			<button class="sce-form__submit" type="submit"><?php echo esc_html( $s['submit_text'] ); ?></button>
			<?php if ( ! empty( $s['note_text'] ) ) : ?>
				<p class="sce-form__note"><?php echo esc_html( $s['note_text'] ); ?></p>
			<?php endif; ?>
		</form>
		<?php
	}

	/** True when an optional field is switched on (required ones always are). */
	private function shows( $key, $s ) {
		$map = $this->field_map();
		if ( empty( $map[ $key ]['optional'] ) ) {
			return true;
		}
		return 'yes' === ( isset( $s[ 'show_' . $key ] ) ? $s[ 'show_' . $key ] : '' );
	}

	private function label_for( $key, $s ) {
		$map = $this->field_map();
		$set = isset( $s[ 'label_' . $key ] ) ? trim( (string) $s[ 'label_' . $key ] ) : '';
		return '' !== $set ? $set : $map[ $key ]['label'];
	}

	private function text_field( $key, $name, $type, $s, $attrs = array() ) {
		if ( ! $this->shows( $key, $s ) ) {
			return;
		}
		$id    = 'sce-faq-' . $key . '-' . $this->get_id();
		$label = $this->label_for( $key, $s );
		?>
		<div class="sce-form__group">
			<label class="sce-form__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<div class="sce-form__field">
				<input class="sce-form__input" id="<?php echo esc_attr( $id ); ?>"
				       name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>"
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
		</div>
		<?php
	}

	private function select_field( $s ) {
		if ( ! $this->shows( 'project_type', $s ) || empty( $s['select_options'] ) ) {
			return;
		}
		$id = 'sce-faq-type-' . $this->get_id();
		?>
		<div class="sce-form__group">
			<label class="sce-form__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $this->label_for( 'project_type', $s ) ); ?>
			</label>
			<div class="sce-form__field">
				<select class="sce-form__select" id="<?php echo esc_attr( $id ); ?>" name="project_type">
					<option value="" disabled selected><?php echo esc_html( $s['select_placeholder'] ); ?></option>
					<?php foreach ( $s['select_options'] as $o ) : ?>
						<option value="<?php echo esc_attr( $o['label'] ); ?>"><?php echo esc_html( $o['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<svg class="sce-form__chev" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">
					<path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>
		</div>
		<?php
	}

	private function textarea_field( $s ) {
		if ( ! $this->shows( 'message', $s ) ) {
			return;
		}
		$id = 'sce-faq-message-' . $this->get_id();
		?>
		<div class="sce-form__group">
			<label class="sce-form__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $this->label_for( 'message', $s ) ); ?>
			</label>
			<div class="sce-form__field">
				<textarea class="sce-form__input sce-form__textarea" id="<?php echo esc_attr( $id ); ?>"
				          name="message" rows="3"></textarea>
			</div>
		</div>
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
