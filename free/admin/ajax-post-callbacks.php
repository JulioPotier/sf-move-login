<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** ADMIN POST / AJAX CALLBACKS ================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_sanitize_move_login_slug', 'movelogin_sanitize_move_login_slug_ajax_post_cb' );
/**
 * Sanitize a value for a Move Login slug.
 *
 * @since 1.2.5
 * @author Grégory Viguier
 */
function movelogin_sanitize_move_login_slug_ajax_post_cb() {
	// Make all security tests.
	movelogin_check_admin_referer( 'sanitize_move_login_slug' );
	movelogin_check_user_capability();

	if ( empty( $_GET['default'] ) || ! isset( $_GET['slug'] ) ) {
		wp_send_json_error();
	}

	$default = sanitize_title( $_GET['default'] );

	if ( ! $default ) {
		wp_send_json_error();
	}

	if ( 'login' === $default ) {
		$slug = sanitize_title( $_GET['slug'], '', 'display' );
		// See secupress/inc/modules/users-login/settings/move-login.php.
		$slug = $slug ? $slug : '##-' . strtoupper( sanitize_title( __( 'Choose your login URL', 'movelogin' ), '', 'display' ) ) . '-##';
	} else {
		$slug = sanitize_title( $_GET['slug'], $default, 'display' );
	}

	wp_send_json_success( $slug );
}


add_action( 'admin_post_nopriv_movelogin_unlock_admin', 'movelogin_unlock_admin_ajax_post_cb' );
/**
 * Send an unlock email if the provided address is from an admin
 *
 * @author Julio Potier
 * @since 1.3.2
 **/
function movelogin_unlock_admin_ajax_post_cb() {
	if ( ! isset( $_POST['_wpnonce'], $_POST['email'] ) || ! is_email( $_POST['email'] ) || ! check_ajax_referer( 'movelogin-unban-ip-admin', '_wpnonce' ) ) { // WPCS: CSRF ok.
		wp_die( 'Something went wrong.' );
	}
	$_CLEAN          = [];
	$_CLEAN['email'] = is_email( $_POST['email'] );
	if ( ! $_CLEAN['email'] ) {
		wp_die( 'Something went wrong.' );
	}
	$user            = get_user_by( 'email', $_CLEAN['email'] );
	$capa            = movelogin_get_capability( true, 'unlock_administrator' );
	if ( ! movelogin_is_user( $user ) || ! user_can( $user, $capa ) ) {
		wp_die( 'Something went wrong.' );
	}
	$url_remember = wp_login_url();

	$subject      = sprintf( __( '[%s] Unlock a lost user', 'movelogin' ), '###SITENAME###' );
	$message      = sprintf( __( 'Hello %1$s,
It seems you are locked out from the website ###SITENAME###.

You can now follow this link to your login page (remember it now!):
%2$s

Have a nice day !

Regards,
All at ###SITENAME###
###SITEURL###', 'movelogin' ),
							$user->display_name,
							$url_remember
					);

	$capa             = movelogin_get_capability(); // Do it again to get the usual capa for managing options.
	if ( $capa && apply_filters( 'movelogin.plugins.move_login.email.deactivation_link', true ) ) {
		$token        = md5( movelogin_generate_key() );
		$url_remove   = add_query_arg( [ '_wpnonce' => $token, 'user_email' => $user->user_email ], admin_url( 'admin-post.php?action=movelogin_deactivate_module&module=move-login' ) );
		set_transient( 'movelogin_unlock_admin_key-' . $user->user_email, $token, HOUR_IN_SECONDS );
		$message     .= "\n" . sprintf( __( "ps: you can also deactivate the Move Login module:\n%s", 'movelogin' ), $url_remove . ' ' . __( '(Valid 1 hour)', 'movelogin' ) );
	}

	/**
	 * Filter the mail subject
	 * @param (string) $subject
	 * @param (WP_User) $user
	 * @since 2.2
	 */
	$subject = apply_filters( 'movelogin.mail.unlock_administrator.subject', $subject, $user );
	/**
	 * Filter the mail message
	 * @param (string) $message
	 * @param (WP_User) $user
	 * @param (string) $url_remove
	 * @param (string) $url_remember
	 * @since 2.2
	 */
	$message = apply_filters( 'movelogin.mail.unlock_administrator.message', $message, $user, $url_remove, $url_remember );


	$sent = movelogin_send_mail( $_CLEAN['email'], $subject, $message );
	movelogin_die( $sent ? __( 'Email sent, check your mailbox.', 'movelogin' ) : __( 'Email not sent, please contact the support.', 'movelogin' ), __( 'Email', 'movelogin' ), array( 'force_die' => true ) );
}

add_action( 'admin_post_nopriv_movelogin_deactivate_module', 'movelogin_deactivate_module_admin_post_cb' );
/**
 * Can deactivate a module from a link sent by movelogin_unlock_admin_ajax_post_cb()
 *
 * @since 1.3.2
 * @author Julio Potier
 **/
function movelogin_deactivate_module_admin_post_cb() {
	$tr_key_name = 'movelogin_unlock_admin_key-' . ( isset( $_GET['user_email'] ) ? $_GET['user_email'] : '' );
	if ( ! isset( $_GET['_wpnonce'], $_GET['module'], $_GET['user_email'] ) || 
		empty( $_GET['_wpnonce'] ) || 
		! get_transient( $tr_key_name ) || 
		! hash_equals( get_transient( $tr_key_name ), $_GET['_wpnonce'] )
	) {
		delete_transient( $tr_key_name );
		wp_die( 'Something went wrong.' );
	}
	delete_transient( $tr_key_name );
	movelogin_deactivate_submodule( 'users-login', array( 'move-login' ) );
	wp_redirect( wp_login_url( movelogin_admin_url( 'modules', 'users-login' ) ) );
	die();
}
