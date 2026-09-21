<?php
/**
 * Built-in lead form: AJAX endpoint, spam traps, lead storage, admin e-mail.
 *
 * Security note: the notification recipient is NEVER read from the request.
 * It is resolved server-side out of the Elementor document's saved widget
 * settings, so a crafted POST cannot redirect mail to an attacker.
 *
 * @package SuncoastEleWidgets
 */

defined( 'ABSPATH' ) || exit;

final class SCE_Forms {

	/** @var SCE_Forms|null */
	private static $instance = null;

	const ACTION    = 'sce_lead';
	const NONCE     = 'sce_lead_nonce';
	const POST_TYPE = 'sce_lead';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( $this, 'handle' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column' ), 10, 2 );
	}

	/* ---------------------------------------------------------------- CPT */

	public function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Suncoast Leads', 'suncoast-ele-widgets' ),
					'singular_name' => __( 'Lead', 'suncoast-ele-widgets' ),
					'menu_name'     => __( 'Suncoast Leads', 'suncoast-ele-widgets' ),
					'all_items'     => __( 'All Leads', 'suncoast-ele-widgets' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 26,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'has_archive'     => false,
				'rewrite'         => false,
				'show_in_rest'    => false,
			)
		);
	}

	public function columns( $cols ) {
		return array(
			'cb'        => isset( $cols['cb'] ) ? $cols['cb'] : '',
			'title'     => __( 'Name', 'suncoast-ele-widgets' ),
			'sce_phone' => __( 'Phone', 'suncoast-ele-widgets' ),
			'sce_zip'   => __( 'Zip', 'suncoast-ele-widgets' ),
			'sce_type'  => __( 'Project', 'suncoast-ele-widgets' ),
			'sce_src'   => __( 'Source', 'suncoast-ele-widgets' ),
			'date'      => __( 'Received', 'suncoast-ele-widgets' ),
		);
	}

	public function column( $col, $post_id ) {
		$map = array(
			'sce_phone' => '_sce_phone',
			'sce_zip'   => '_sce_zip',
			'sce_type'  => '_sce_project_type',
			'sce_src'   => '_sce_source',
		);
		if ( isset( $map[ $col ] ) ) {
			echo esc_html( (string) get_post_meta( $post_id, $map[ $col ], true ) );
		}
	}

	/* ------------------------------------------------------------ handler */

	public function handle() {
		$fail = function ( $message, $code = 400 ) {
			wp_send_json_error( array( 'message' => $message ), $code );
		};

		if ( ! check_ajax_referer( self::NONCE, 'nonce', false ) ) {
			$fail( __( 'Your session expired. Please refresh the page and try again.', 'suncoast-ele-widgets' ), 403 );
		}

		// Spam trap 1: hidden field a human never sees.
		if ( ! empty( $_POST['sce_hp'] ) ) {
			wp_send_json_success( array( 'message' => __( 'Thank you.', 'suncoast-ele-widgets' ) ) );
		}

		// Spam trap 2: bots submit instantly.
		$started = isset( $_POST['sce_t'] ) ? absint( $_POST['sce_t'] ) : 0;
		if ( $started && ( time() - $started ) < 2 ) {
			$fail( __( 'That was too quick — please try again.', 'suncoast-ele-widgets' ) );
		}

		// Spam trap 3: per-IP throttle.
		$ip  = $this->client_ip();
		$key = 'sce_rl_' . md5( $ip );
		$hits = (int) get_transient( $key );
		if ( $hits >= 5 ) {
			$fail( __( 'Too many submissions. Please call us on 1-877-449-5106.', 'suncoast-ele-widgets' ), 429 );
		}
		set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

		$fields = array(
			'name'         => sanitize_text_field( wp_unslash( $_POST['full_name'] ?? '' ) ),
			'phone'        => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
			'zip'          => sanitize_text_field( wp_unslash( $_POST['zip'] ?? '' ) ),
			'project_type' => sanitize_text_field( wp_unslash( $_POST['project_type'] ?? '' ) ),
			'email'        => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
			'message'      => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
		);

		if ( '' === $fields['name'] || '' === $fields['phone'] ) {
			$fail( __( 'Please enter your name and phone number.', 'suncoast-ele-widgets' ) );
		}
		if ( strlen( preg_replace( '/\D/', '', $fields['phone'] ) ) < 7 ) {
			$fail( __( 'That phone number looks incomplete.', 'suncoast-ele-widgets' ) );
		}

		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$widget_id = sanitize_key( wp_unslash( $_POST['widget_id'] ?? '' ) );
		$settings  = $this->widget_settings( $post_id, $widget_id );

		$meta = array(
			'source'  => $post_id ? get_permalink( $post_id ) : home_url( '/' ),
			'page'    => $post_id ? get_the_title( $post_id ) : '',
			'ip'      => $ip,
			'ua'      => substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ), 0, 255 ),
			'time'    => current_time( 'mysql' ),
			'referer' => esc_url_raw( wp_unslash( $_POST['referer'] ?? '' ) ),
		);

		$lead_id = $this->store( $fields, $meta );
		$this->notify( $fields, $meta, $settings );

		/**
		 * Fires after a lead is stored and the admin has been notified.
		 *
		 * @param int   $lead_id Lead post ID (0 if storage is disabled).
		 * @param array $fields  Sanitised fields.
		 * @param array $meta    Request metadata.
		 */
		do_action( 'sce/lead_submitted', $lead_id, $fields, $meta );

		$success = isset( $settings['msg_success'] ) && '' !== $settings['msg_success']
			? $settings['msg_success']
			: __( "Thank you — we'll be in touch within one business day.", 'suncoast-ele-widgets' );

		wp_send_json_success( array( 'message' => wp_kses_post( $success ) ) );
	}

	/* ------------------------------------------------------------ storage */

	private function store( $fields, $meta ) {
		if ( ! apply_filters( 'sce/store_leads', true ) ) {
			return 0;
		}

		$id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $fields['name'] ? $fields['name'] : __( '(no name)', 'suncoast-ele-widgets' ),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		update_post_meta( $id, '_sce_phone', $fields['phone'] );
		update_post_meta( $id, '_sce_zip', $fields['zip'] );
		update_post_meta( $id, '_sce_project_type', $fields['project_type'] );
		update_post_meta( $id, '_sce_email', $fields['email'] );
		update_post_meta( $id, '_sce_message', $fields['message'] );
		update_post_meta( $id, '_sce_source', $meta['source'] );
		update_post_meta( $id, '_sce_ip', $meta['ip'] );
		update_post_meta( $id, '_sce_ua', $meta['ua'] );

		return $id;
	}

	/* ---------------------------------------------------------------- mail */

	private function notify( $fields, $meta, $settings ) {
		$to = '';
		if ( ! empty( $settings['admin_email'] ) ) {
			$list = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', $settings['admin_email'] ) ) ) );
			$to   = implode( ',', $list );
		}
		if ( '' === $to ) {
			$to = get_option( 'admin_email' );
		}

		$subject = ! empty( $settings['admin_subject'] )
			? $settings['admin_subject']
			: __( 'New consultation request — Suncoast Enclosures', 'suncoast-ele-widgets' );

		$subject = str_replace(
			array( '{name}', '{zip}', '{project}' ),
			array( $fields['name'], $fields['zip'], $fields['project_type'] ),
			$subject
		);

		ob_start();
		include SCE_PATH . 'templates/email-admin.php';
		$body = ob_get_clean();

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		if ( ! empty( $fields['email'] ) && is_email( $fields['email'] ) ) {
			$headers[] = 'Reply-To: ' . $fields['name'] . ' <' . $fields['email'] . '>';
		}

		$to      = apply_filters( 'sce/admin_email_to', $to, $fields, $meta );
		$subject = apply_filters( 'sce/admin_email_subject', $subject, $fields, $meta );
		$body    = apply_filters( 'sce/admin_email_body', $body, $fields, $meta );

		wp_mail( $to, wp_specialchars_decode( $subject, ENT_QUOTES ), $body, $headers );
	}

	/* --------------------------------------------------------------- utils */

	/**
	 * Read the saved settings of the widget that rendered this form, straight
	 * out of the Elementor document. Never trust the request for these.
	 */
	private function widget_settings( $post_id, $widget_id ) {
		if ( ! $post_id || ! $widget_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return array();
		}

		$doc = \Elementor\Plugin::$instance->documents->get( $post_id );
		if ( ! $doc ) {
			return array();
		}

		$found = $this->find_element( (array) $doc->get_elements_data(), $widget_id );
		return ( $found && isset( $found['settings'] ) ) ? (array) $found['settings'] : array();
	}

	private function find_element( array $nodes, $id ) {
		foreach ( $nodes as $node ) {
			if ( isset( $node['id'] ) && $node['id'] === $id ) {
				return $node;
			}
			if ( ! empty( $node['elements'] ) ) {
				$hit = $this->find_element( (array) $node['elements'], $id );
				if ( $hit ) {
					return $hit;
				}
			}
		}
		return null;
	}

	private function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		return $ip ? $ip : '0.0.0.0';
	}
}
