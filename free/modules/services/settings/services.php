<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

$this->add_section( __( 'Our Professional Services', 'movelogin' ), array( 'with_save_button' => false ) );

$this->add_field( array(
	'title'        => __( 'Malware removal', 'movelogin' ),
	'description'  => __( 'We will clean up your website from any security issues<br>for a fee of $360', 'movelogin' ),
	'name'         => $this->get_field_name( 'got-hacked' ),
	'disabled'     => true,
	'type'         => 'field_button',
	'style'        => 'primary',
	'label'        => __( 'Request a Website cleansing', 'movelogin' ),
	'url'          => trailingslashit( set_url_scheme( MOVELOGIN_WEB_MAIN, 'https' ) ) . _x( 'checkout/?currency=USD', 'link to website (Only FR or EN!)', 'movelogin' ) . '&edd_action=add_to_cart&download_id=4811',
) );

$this->add_field( array(
	'title'        => __( 'Security Monitoring', 'movelogin' ),
	'description'  => __( 'Remove the hassle of checking security yourself with our Website Security Monitoring Services.<br>We have plan starting at $39', 'movelogin' ),
	'name'         => $this->get_field_name( 'monitoring' ),
	'disabled'     => true,
	'type'         => 'field_button',
	'style'        => 'primary',
	'label'        => __( 'Visit our page for comparing plans', 'movelogin' ),
	'url'          => trailingslashit( set_url_scheme( MOVELOGIN_WEB_MAIN, 'https' ) ) . _x( 'monitoring/?currency=USD', 'link to website (Only FR or EN!)','movelogin' ),
) );


