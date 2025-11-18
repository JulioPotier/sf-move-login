<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** MODULES OPTIONS ============================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'admin_init', 'movelogin_register_all_settings' );
/**
 * Register all modules settings.
 *
 * @since 1.0
 */
function movelogin_register_all_settings() {
	$modules = movelogin_get_modules();

	if ( $modules ) {
		foreach ( $modules as $key => $module_data ) {
			movelogin_register_setting( $key );
		}
	}
}

// Scanner functionality removed - all scanner hooks and functions have been removed
