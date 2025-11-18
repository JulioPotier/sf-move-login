<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** SECUPRESS UPGRADER ========================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Tell WP what to do when admin is loaded aka upgrader
 *
 * @since 1.0
 */
function movelogin_upgrader() {
	$actual_version = movelogin_get_option( 'version' );

	// You can hook the upgrader to trigger any action when SecuPress is upgraded.
	// First install.
	if ( ! $actual_version ) {
		/**
		 * Allow to prevent plugin first install hooks to fire.
		 *
		 * @since 1.0
		 *
		 * @param (bool) $prevent True to prevent triggering first install hooks. False otherwise.
		 */
		if ( ! apply_filters( 'movelogin.prevent_first_install', false ) ) {
			/**
			 * Fires on the plugin first install.
			 *
			 * @since 1.0
			 *
			 * @param (string) $module The module to reset. "all" means all modules at once.
			 */
			do_action( 'movelogin.first_install', 'all' );
		}

	}
	// Already installed but got updated.
	elseif ( MOVELOGIN_VERSION !== $actual_version ) {
		$new_version = MOVELOGIN_VERSION;
		/**
		 * Fires when SecuPress is upgraded.
		 *
		 * @since 1.0
		 *
		 * @param (string) $new_version    The version being upgraded to.
		 * @param (string) $actual_version The previous version.
		 */
		do_action( 'movelogin.upgrade', $new_version, $actual_version );
	}


	// If any upgrade has been done, we flush and update version.
	if ( did_action( 'movelogin.first_install' ) || did_action( 'movelogin.upgrade' ) || did_action( 'movelogin_pro.first_install' ) || did_action( 'movelogin_pro.upgrade' ) ) {

		// Do not use movelogin_get_option() here.
		$options = get_site_option( MOVELOGIN_SETTINGS_SLUG );
		$options = is_array( $options ) ? $options : array();

		// Free version.
		$options['version'] = MOVELOGIN_VERSION;

		movelogin_update_options( $options );

		/**
		* Fires when an updated has been done.
		*
		* @since 2.0
		* @author Julio Potier
		*
		* @param (string) $actual_version
		* @param (string) $new_version
		* @param (array)  $options
		*/
		do_action( 'movelogin.did_upgrade', $actual_version, MOVELOGIN_VERSION, $options );
	}
}

add_action( 'movelogin.first_install', 'movelogin_install_users_login_module' );
/**
 * Create default option on install and reset.
 *
 * @since 1.0
 *
 * @param (string) $module The module(s) that will be reset to default. `all` means "all modules".
 */
function movelogin_install_users_login_module( $module ) {
	// First install.
	$retro   = get_option( 'sfml' );
	if ( 'all' === $module ) {
		if ( isset( $retro['slugs.login'] ) ) {
			$login_slug = $retro['slugs.login'];
			delete_option( 'sfml' );
			movelogin_activate_submodule( 'users-login', 'move-login' );
			movelogin_update_module_option( 'move-login_slug-login', $login_slug, 'users-login' );
		}
	}
}