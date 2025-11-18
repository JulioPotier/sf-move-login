<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

global $is_apache, $is_nginx, $is_iis7;

$this->set_current_section( 'move-login' );
$this->add_section( __( 'Login Pages', 'movelogin' ) );


$main_field_name  = $this->get_field_name( 'activated' );
$is_plugin_active = movelogin_is_submodule_active( 'users-login', 'move-login' );

/**
* Allow some plugins to take over SecuPress settings if they are actually activated.
* @param (array) The plugins list ; format 'plugins-path/plugin-file.php' => 'admin-page.php#for-settings'
*/
$override_plugins = apply_filters( 'movelogin.move-login.override-plugins', [ 'wps-hide-login/wps-hide-login.php' => 'options-general.php#whl_page' ] );
foreach ( $override_plugins as $plugin_path => $plugin_page) {
	if ( movelogin_is_plugin_active( $plugin_path ) ) {
		$this->add_field( array(
			'title'             => __( 'Move the login and admin pages', 'movelogin' ),
			'label_for'         => $main_field_name,
			'plugin_activation' => true,
			'type'              => 'checkbox',
			'value'             => false,
			'disabled'          => true,
			'label'             => __( 'Yes, move the login and admin pages', 'movelogin' ),
			'helpers'           => array(
				array(
					'type'        => 'warning',
					'description' => movelogin_plugin_in_usage_string( $plugin_path, $plugin_page ),
				),
			),
		) );

		return;
	}
}

/**
* If pretty permalinks are not active, do not let move login do its works
*/
$wp_rewrite = new WP_Rewrite();
if ( ! $wp_rewrite->using_permalinks() ) {
	$this->add_field( array(
		'title'             => __( 'Move the login and admin pages', 'movelogin' ),
		'label_for'         => $main_field_name,
		'plugin_activation' => true,
		'type'              => 'checkbox',
		'value'             => false,
		'disabled'          => true,
		'label'             => __( 'Yes, move the login and admin pages', 'movelogin' ),
		'helpers'           => array(
			array(
				'type'        => 'warning',
				'description' => sprintf( __( 'Your website is not using <b>Pretty Permalinks</b> but this module needs that. You can activate that in the <a href="%s">Permalinks Settings Page</a> and do not use "Plain" setting.', 'movelogin' ), esc_url( admin_url( 'options-permalink.php' ) ) ),
			),
		),
	) );

	return;
}

$this->add_field( array(
	'title'             => __( 'Move the login and admin pages', 'movelogin' ),
	'label_for'         => $main_field_name,
	'plugin_activation' => true,
	'type'              => 'checkbox',
	'value'             => (int) $is_plugin_active,
	'label'             => __( 'Yes, move the login and admin pages', 'movelogin' ),
) );

if ( defined( 'SFML_ALLOW_LOGIN_ACCESS' ) && constant( 'SFML_ALLOW_LOGIN_ACCESS' ) ) {
	$this->add_field( array(
		'title'             => __( 'Move the login and admin pages', 'movelogin' ),
		'label_for'         => $main_field_name,
		'type'              => 'html',
		'value'             => '',
		'helpers'           => array(
			array(
				'type'        => 'warning',
				'description' => sprintf( __( 'The %1$s constant is set, you cannot use the %2$s module.', 'movelogin' ), '<code>SFML_ALLOW_LOGIN_ACCESS</code>', '<em>' . __( 'Move Login', 'movelogin' ) . '</em>' ),
			),
		),
	) );
	return;
}

$labels    = movelogin_move_login_slug_labels();
$login_url = site_url( '%%slug%%', 'login' );
$login_slug = movelogin_get_module_option( 'move-login_slug-login', 'login', 'users-login' );

foreach ( $labels as $slug => $label ) {
	$name    = $this->get_field_name( 'slug-' . $slug );
	$default = 'login' === $slug ? '' : $slug;
	$value   = isset( $value ) ? $value : movelogin_get_module_option( $name, $slug, 'users-login' );
	$value   = sanitize_title( $value, $default, 'display' );
	if ( ! $value ) {
		if ( 'login' === $slug ) {
			// See `movelogin_sanitize_move_login_slug_ajax_post_cb()`.
			$value = '##-' . strtoupper( sanitize_title( __( 'Choose your login URL', 'movelogin' ), '', 'display' ) ) . '-##';
		} else {
			$value = $default;
		}
	}

	$disabled = 'login' !== $slug;
	$value    = $disabled ? $login_slug . '-' . $slug : $value;
	$this->add_field( array(
		'title'        => esc_html( $label ),
		'depends'      => $main_field_name,
		'label_for'    => $this->get_field_name( 'slug-' . $slug ),
		'type'         => 'text',
		'default'      => $default,
		'disabled'     => $disabled,
		'value'        => $value,
		'label_before' => '<span class="screen-reader-text">' . __( 'URL' ) . '</span>',
		'label_after'  => ' <em class="hide-if-no-js">' . str_replace( '%%slug%%', '<strong class="dynamic-login-url-slug">' . $value . '</strong>', $login_url ) . '</em>',
		'helpers'      => array(
			array(
				'type'        => 'login' === $slug ? 'description' : '',
				'description' => __( 'The following slugs are related to the login page, so they cannot be changed.', 'movelogin' ),
			),
		),
	) );
}

/**
 * If nginx or if `.htaccess`/`web.config` is not writable, display a textarea containing the rewrite rules for Move Login.
 */
if ( $is_plugin_active && function_exists( 'movelogin_move_login_get_rules' ) && apply_filters( 'movelogin.nginx.notice', true ) ) {
	$message = false;

	// Nginx.
	if ( $is_nginx ) {
		/** Translators: 1 is a file name, 2 is a tag name. */
		$message = sprintf( __( 'You need to add the following code from your %1$s file, inside the %2$s block:', 'movelogin' ), '<code>nginx.conf</code>', '<code>server</code>' );
		$rules   = movelogin_move_login_get_nginx_rules( movelogin_move_login_get_rules() );
	}

	if ( $message ) {
		$this->add_field( array(
			'title'        => __( 'Rules', 'movelogin' ),
			'description'  => $message,
			'depends'      => $main_field_name,
			'label_for'    => $this->get_field_name( 'rules' ),
			'type'         => 'textarea',
			'value'        => $rules,
			'attributes'   => array(
				'readonly' => 'readonly',
				'rows'     => substr_count( $rules, "\n" ) + 1,
			),
		) );
	}
}

unset( $main_field_name, $is_plugin_active, $labels, $message, $rules );
