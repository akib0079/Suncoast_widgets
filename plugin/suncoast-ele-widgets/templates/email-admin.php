<?php
/**
 * Admin notification e-mail.
 *
 * Table-based, inline-styled and ~600px wide — the only layout Outlook and
 * Gmail both render predictably. Included from SCE_Forms::notify(), which
 * provides $fields and $meta.
 *
 * @package SuncoastEleWidgets
 *
 * @var array $fields name, phone, zip, project_type, email, message
 * @var array $meta   source, page, ip, ua, time, referer
 */

defined( 'ABSPATH' ) || exit;

$sce_gold  = '#EBB04D';
$sce_ink   = '#1A1C1C';
$sce_muted = '#6B7280';
$sce_line  = '#E8E6E1';

$sce_message = isset( $fields['message'] ) ? (string) $fields['message'] : '';

$sce_rows = array(
	__( 'Name', 'suncoast-ele-widgets' )         => $fields['name'],
	__( 'Phone', 'suncoast-ele-widgets' )        => $fields['phone'],
	__( 'Zip code', 'suncoast-ele-widgets' )     => $fields['zip'],
	__( 'Project type', 'suncoast-ele-widgets' ) => $fields['project_type'],
	__( 'Email', 'suncoast-ele-widgets' )        => $fields['email'],
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php esc_html_e( 'New consultation request', 'suncoast-ele-widgets' ); ?></title>
</head>
<body style="margin:0;padding:0;background:#F4F2ED;">

<div style="display:none;max-height:0;overflow:hidden;opacity:0;">
	<?php
	printf(
		/* translators: 1: lead name, 2: zip code */
		esc_html__( '%1$s · %2$s — new consultation request', 'suncoast-ele-widgets' ),
		esc_html( $fields['name'] ),
		esc_html( $fields['zip'] )
	);
	?>
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#F4F2ED;padding:28px 12px;">
<tr><td align="center">

	<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#FFFFFF;border:1px solid <?php echo esc_attr( $sce_line ); ?>;border-radius:14px;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

		<!-- header -->
		<tr>
			<td style="background:<?php echo esc_attr( $sce_ink ); ?>;padding:24px 32px;">
				<p style="margin:0 0 6px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:<?php echo esc_attr( $sce_gold ); ?>;">
					<?php esc_html_e( 'Suncoast Enclosures · Portland PNW', 'suncoast-ele-widgets' ); ?>
				</p>
				<h1 style="margin:0;font-size:21px;line-height:1.3;font-weight:700;color:#FFFFFF;">
					<?php esc_html_e( 'New consultation request', 'suncoast-ele-widgets' ); ?>
				</h1>
			</td>
		</tr>

		<!-- lead detail -->
		<tr>
			<td style="padding:28px 32px 8px;">
				<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
					<?php foreach ( $sce_rows as $sce_label => $sce_value ) : ?>
						<?php if ( '' === (string) $sce_value ) { continue; } ?>
						<tr>
							<td style="padding:11px 0;border-bottom:1px solid <?php echo esc_attr( $sce_line ); ?>;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:<?php echo esc_attr( $sce_muted ); ?>;width:38%;vertical-align:top;">
								<?php echo esc_html( $sce_label ); ?>
							</td>
							<td style="padding:11px 0;border-bottom:1px solid <?php echo esc_attr( $sce_line ); ?>;font-size:15px;font-weight:600;color:<?php echo esc_attr( $sce_ink ); ?>;vertical-align:top;">
								<?php
								if ( __( 'Phone', 'suncoast-ele-widgets' ) === $sce_label ) {
									printf(
										'<a href="tel:%s" style="color:%s;text-decoration:none;">%s</a>',
										esc_attr( preg_replace( '/\D/', '', $sce_value ) ),
										esc_attr( $sce_ink ),
										esc_html( $sce_value )
									);
								} elseif ( __( 'Email', 'suncoast-ele-widgets' ) === $sce_label ) {
									printf(
										'<a href="mailto:%s" style="color:%s;text-decoration:none;">%s</a>',
										esc_attr( $sce_value ),
										esc_attr( $sce_ink ),
										esc_html( $sce_value )
									);
								} else {
									echo esc_html( $sce_value );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( '' !== $sce_message ) : ?>
						<tr>
							<td colspan="2" style="padding:16px 0 0;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:<?php echo esc_attr( $sce_muted ); ?>;">
								<?php esc_html_e( 'Message', 'suncoast-ele-widgets' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding:8px 0 11px;border-bottom:1px solid <?php echo esc_attr( $sce_line ); ?>;font-size:14px;line-height:1.55;color:<?php echo esc_attr( $sce_ink ); ?>;">
								<?php echo nl2br( esc_html( $sce_message ) ); ?>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</td>
		</tr>

		<!-- call-to-action -->
		<tr>
			<td style="padding:22px 32px 4px;">
				<table role="presentation" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="background:<?php echo esc_attr( $sce_gold ); ?>;border-radius:4px;">
							<a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', $fields['phone'] ) ); ?>"
							   style="display:inline-block;padding:13px 26px;font-size:13px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:<?php echo esc_attr( $sce_ink ); ?>;text-decoration:none;">
								<?php esc_html_e( 'Call this lead back', 'suncoast-ele-widgets' ); ?>
							</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<!-- context -->
		<tr>
			<td style="padding:22px 32px 28px;">
				<p style="margin:0;font-size:12px;line-height:1.7;color:<?php echo esc_attr( $sce_muted ); ?>;">
					<?php esc_html_e( 'Submitted', 'suncoast-ele-widgets' ); ?>
					<strong style="color:<?php echo esc_attr( $sce_ink ); ?>;"><?php echo esc_html( $meta['time'] ); ?></strong><br>
					<?php esc_html_e( 'Page', 'suncoast-ele-widgets' ); ?>
					<a href="<?php echo esc_url( $meta['source'] ); ?>" style="color:<?php echo esc_attr( $sce_muted ); ?>;"><?php echo esc_html( $meta['page'] ? $meta['page'] : $meta['source'] ); ?></a><br>
					<?php esc_html_e( 'IP', 'suncoast-ele-widgets' ); ?> <?php echo esc_html( $meta['ip'] ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<td style="background:#FBF9F4;padding:16px 32px;border-top:1px solid <?php echo esc_attr( $sce_line ); ?>;">
				<p style="margin:0;font-size:11px;color:<?php echo esc_attr( $sce_muted ); ?>;">
					<?php esc_html_e( 'Sent automatically by Suncoast Ele Widgets. A copy is saved under Suncoast Leads in the WordPress admin.', 'suncoast-ele-widgets' ); ?>
				</p>
			</td>
		</tr>
	</table>

</td></tr>
</table>
</body>
</html>
