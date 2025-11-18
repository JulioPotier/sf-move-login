<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** ON MODULE SETTINGS SAVE ===================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Callback to filter, sanitize, validate and de/activate submodules.
 *
 * @since 1.0
 *
 * @param (array) $settings The module settings.
 *
 * @return (array) The sanitized and validated settings.
 */
function movelogin_users_login_settings_callback( $settings ) {
	$modulenow = 'users-login';
	$activate  = movelogin_get_submodule_activations( $modulenow );
	$settings  = $settings && is_array( $settings ) ? $settings : array();

	if ( isset( $settings['sanitized'] ) ) {
		return $settings;
	}
	$settings['sanitized'] = 1;

	/*
	 * Each submodule has its own sanitization function.
	 * The `$settings` parameter is passed by reference.
	 */

	// Move Login.
	movelogin_move_login_settings_callback( $modulenow, $settings, $activate );


	/**
	 * Filter the settings before saving.
	 *
	 * @since 1.4.9
	 *
	 * @param (array)      $settings The module settings.
	 * @param (array\bool) $activate Contains the activation rules for the different modules
	 */
	$settings = apply_filters( "movelogin_{$modulenow}_settings_callback", $settings, $activate );

	return $settings;
}

/**
 * (De)Activate Move Login plugin. Sanitize and validate settings.
 *
 * @since 1.0
 *
 * @param (string)     $modulenow Current module.
 * @param (array)      $settings  The module settings, passed by reference.
 * @param (array|bool) $activate  An array containing the fields related to the sub-module being activated. False if not on this module page.
 */
function movelogin_move_login_settings_callback( $modulenow, &$settings, $activate ) {
	$old_settings = get_site_option( "movelogin_{$modulenow}_settings" );
	// Slugs.
	$slugs        = movelogin_move_login_slug_labels();
	// Handle forbidden slugs and duplicates.
	$errors       = array( 'forbidden' => array(), 'duplicates' => array() );
	// `postpass`, `retrievepassword` and `rp` are forbidden if they are not customizable.
	$forbidden    = array( 'postpass' => 1, 'retrievepassword' => 1, 'rp' => 1 );
	$forbidden    = array_diff_key( $forbidden, $slugs );
	$dones        = array();
	
	foreach ( $slugs as $default_slug => $label ) {
		$option_name = 'move-login_slug-' . $default_slug;
		
		// Build a fallback slug. Try the old value first.
		$fallback_slug = ! empty( $old_settings[ $option_name ] ) ? sanitize_title( $old_settings[ $option_name ] ) : '';
		// Then fallback to the default value.
		if ( ! $fallback_slug || isset( $forbidden[ $fallback_slug ] ) || isset( $dones[ $fallback_slug ] ) ) {
			$fallback_slug = $default_slug;
		}
		// Last chance, add an increment.
		if ( isset( $forbidden[ $fallback_slug ] ) || isset( $dones[ $fallback_slug ] ) ) {
			$i = 1;
			while ( isset( $forbidden[ $fallback_slug . $i ] ) || isset( $dones[ $fallback_slug . $i ] ) ) {
				++$i;
			}
			$fallback_slug .= $i;
		}
		
		// Sanitize the value provided.
		$new_slug = ! empty( $settings[ $option_name ] ) ? sanitize_title( $settings[ $option_name ] ) : '';
		
		if ( ! $new_slug ) {
			/**
			 * Sanitization did its job til the end, or the field was empty.
			 * For the "login" slug don't fallback to the default slug: we'll keep it empty and trigger an error.
			 */
			if ( 'login' !== $default_slug ) {
				$new_slug = $fallback_slug;
			}
		} else {
			// Validation.
			// Test for forbidden slugs.
			if ( isset( $forbidden[ $new_slug ] ) ) {
				$errors['forbidden'][] = $new_slug;
				$new_slug = $fallback_slug;
			}
			// Test for duplicates.
			elseif ( isset( $dones[ $new_slug ] ) ) {
				$errors['duplicates'][] = $new_slug;
				$new_slug = $fallback_slug;
			}
		}
		
		$dones[ $new_slug ]       = 1;
		$settings[ $option_name ] = $new_slug;
	}
	
	// Access to `wp-login.php`.
	if ( isset( $settings['move-login_login-access'] ) ) {
		$settings['move-login_login-access'] = sanitize_text_field( $settings['move-login_login-access'] );
	}
	
	// Handle validation errors.
	$errors['forbidden']  = array_unique( $errors['forbidden'] );
	$errors['duplicates'] = array_unique( $errors['duplicates'] );
	
	if ( false !== $activate && ! empty( $activate['move-login_activated'] ) ) {
		if ( empty( $settings['move-login_slug-login'] ) ) {
			$message  = sprintf( __( '%s:', 'movelogin' ), __( 'Move Login', 'movelogin' ) ) . ' ';
			$message .= __( 'Please select your login page.', 'movelogin' );
			movelogin_add_settings_error( "movelogin_{$modulenow}_settings", 'forbidden-slugs', $message, 'error' );
		}
		
		if ( $nbr_forbidden = count( $errors['forbidden'] ) ) {
			$message  = sprintf( __( '%s:', 'movelogin' ), __( 'Move Login', 'movelogin' ) ) . ' ';
			$message .= sprintf( _n( 'The slug %s is forbidden.', 'The slugs %s are forbidden.', $nbr_forbidden, 'movelogin' ), wp_sprintf( '<code>%l</code>', $errors['forbidden'] ) );
			movelogin_add_settings_error( "movelogin_{$modulenow}_settings", 'forbidden-slugs', $message, 'error' );
		}
		
		if ( ! empty( $errors['duplicates'] ) ) {
			$message  = sprintf( __( '%s:', 'movelogin' ), __( 'Move Login', 'movelogin' ) ) . ' ';
			$message .= __( 'The links can’t have the same slugs.', 'movelogin' );
			movelogin_add_settings_error( "movelogin_{$modulenow}_settings", 'duplicate-slugs', $message, 'error' );
		}
	}
	
	// (De)Activation.
	if ( false !== $activate ) {
		if ( empty( $settings['move-login_slug-login'] ) ) {
			movelogin_deactivate_submodule( $modulenow, array( 'move-login' ) );
		} else {
			movelogin_manage_submodule( $modulenow, 'move-login',   ! empty( $activate['move-login_activated'] ) );
		}
	}
	$settings['move-login_whattodo'] = 'sperror';
	movelogin_remove_module_rules_or_notice( 'move_login', __( 'Move Login', 'movelogin' ) );
}


/** --------------------------------------------------------------------------------------------- */
/** DEFAULT VALUES ============================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Move Login: return the list of customizable login actions.
 *
 * @since 1.0
 *
 * @return (array) Return an array with the action names as keys and field labels as values.
 */
function movelogin_move_login_slug_labels() {
	$labels = array(
		'login'        => __( 'Log in' ),
		'logout'       => __( 'Log out' ),
		'register'     => __( 'Register' ),
		'lostpassword' => __( 'Lost Password' ),
		'resetpass'    => __( 'Password Reset' ),
		'confirm_admin_email'    => __( 'Confirm Admin Email' ),
	);

	/**
	 * Add custom actions to the list of customizable actions.
	 *
	 * @since 1.0
	 *
	 * @param (array) $new_slugs An array with the action names as keys and field labels as values. An empty array by default.
	*/
	$new_slugs = apply_filters( 'sfml_additional_slugs', array() );

	if ( $new_slugs && is_array( $new_slugs ) ) {
		$new_slugs = array_diff_key( $new_slugs, $labels );
		$labels    = array_merge( $labels, $new_slugs );
	}

	return $labels;
}
