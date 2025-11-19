<?php
/**
 * Plugin Name: SP Move Login
 * Plugin URI: https://secupress.me
 * Description: Move your WordPress login page to protect it from bots. A SecuPress module!
 * Author: SecuPress
 * Author URI: https://secupress.me/blog/movelogin/
 * Version: 2.6
 * Contributors: SecuPress, juliobox, GregLone
 * License: GPLv2
 * Domain Path: /languages/
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * Copyright 2012-2025 SecuPress
 */

defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** DEFINES ===================================================================================== */
/** --------------------------------------------------------------------------------------------- */

// Common constants
define( 'MOVELOGIN_FILE', __FILE__ );
define( 'MOVELOGIN_VERSION'               , '2.6' );
define( 'MOVELOGIN_MAJOR_VERSION'         , '2.6' );
define( 'MOVELOGIN_PATH'                  , realpath( dirname( MOVELOGIN_FILE ) ) . DIRECTORY_SEPARATOR );
define( 'MOVELOGIN_INC_PATH'              , MOVELOGIN_PATH . 'free' . DIRECTORY_SEPARATOR );
define( 'MOVELOGIN_ACTIVE_SUBMODULES'     , 'movelogin_active_submodules' );
define( 'MOVELOGIN_SETTINGS_SLUG'         , 'movelogin_settings' );
define( 'MOVELOGIN_RATE_URL'              , 'https://wordpress.org/support/view/plugin-reviews/sf-move-login?filter=5#topic' );
define( 'MOVELOGIN_WEB_MAIN'              , 'https://secupress.me/' );
define( 'MOVELOGIN_MODULES_PATH'          , MOVELOGIN_INC_PATH . 'modules/' );
define( 'MOVELOGIN_ADMIN_PATH'            , MOVELOGIN_INC_PATH . 'admin/' );
define( 'MOVELOGIN_CLASSES_PATH'          , MOVELOGIN_INC_PATH . 'classes/' );
define( 'MOVELOGIN_ADMIN_SETTINGS_MODULES', MOVELOGIN_ADMIN_PATH . 'modules/' );
define( 'MOVELOGIN_PLUGIN_URL'            , plugin_dir_url( MOVELOGIN_FILE ) );
define( 'MOVELOGIN_FREE_URL'              , MOVELOGIN_PLUGIN_URL . 'free/' );
define( 'MOVELOGIN_FRONT_URL'             , MOVELOGIN_FREE_URL . 'front/' );
define( 'MOVELOGIN_ADMIN_URL'             , MOVELOGIN_FREE_URL . 'admin/' );
define( 'MOVELOGIN_ASSETS_URL'            , MOVELOGIN_PLUGIN_URL . 'assets/' );
define( 'MOVELOGIN_ADMIN_CSS_URL'         , MOVELOGIN_ASSETS_URL . 'admin/css/' );
define( 'MOVELOGIN_ADMIN_JS_URL'          , MOVELOGIN_ASSETS_URL . 'admin/js/' );
define( 'MOVELOGIN_ADMIN_IMAGES_URL'      , MOVELOGIN_ASSETS_URL . 'admin/images/' );
define( 'MOVELOGIN_PHP_MIN'               , '8.0' );
define( 'MOVELOGIN_WP_MIN'                , '6.7' );
define( 'MOVELOGIN_INT_MAX'               , PHP_INT_MAX - 20 );

/** --------------------------------------------------------------------------------------------- */
/** INIT ======================================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Requires hotfixes first because it's hot.
 */ 
require_once( MOVELOGIN_INC_PATH . 'functions/hotfixes.php' );

add_action( 'init', 'movelogin_init_i18n', 0 );
/**
 * Load the i18n here since WP6.7 is doing sh*t
 *
 * @since 2.2.6
 * @author Julio Potier
 */
function movelogin_init_i18n() {
	// Load translations.
	movelogin_load_plugin_textdomain_translations();
}

add_action( 'plugins_loaded', 'movelogin_init', 0 );
/**
 * Tell WP what to do when the plugin is loaded.
 *
 * @since 2.2.6 wp-login.php || is_admin()
 * @author Julio Potier
 * @since 1.0
 * @author Grégory Viguier
 */
function movelogin_init() {
	global $pagenow;
	// Nothing to do if autosave.
	if ( defined( 'DOING_AUTOSAVE' ) ) {
		return;
	}

	// Functions.
	movelogin_load_functions();

	// Last constants.
	define( 'MOVELOGIN_PLUGIN_NAME', 'Move Login' );
	define( 'MOVELOGIN_PLUGIN_SLUG', sanitize_title( MOVELOGIN_PLUGIN_NAME ) );


	if ( 'wp-login.php' === $pagenow || is_admin() ) {

		// Hooks.
		require_once( MOVELOGIN_ADMIN_PATH . 'options.php' );
		require_once( MOVELOGIN_ADMIN_PATH . 'settings.php' );
		require_once( MOVELOGIN_ADMIN_PATH . 'admin.php' );
		require_once( MOVELOGIN_ADMIN_PATH . 'ajax-post-callbacks.php' );
	}
	// require_once( MOVELOGIN_ADMIN_PATH . 'notices.php' );

	/**
	 * Fires when SecuPress is correctly loaded.
	 *
	 * @since 1.0
	 */
	do_action( 'movelogin.loaded' );
	// Load the upgrader after the load of our plugins, SecuPress is still considered "loaded" even without this file since it's not usefull for security
	if ( is_admin() ) {
		require_once( MOVELOGIN_ADMIN_PATH . 'upgrader.php' );
		movelogin_upgrader();
	}
	require_once( MOVELOGIN_INC_PATH . 'functions/migrations.php' );
}

add_action( 'movelogin.loaded', 'movelogin_load_plugins' );
/**
 * Load modules.
 *
 * @author Grégory Viguier
 * @since 1.0
 */
function movelogin_load_plugins() {
	// All modules - Load all modules for settings.php and callbacks.php, but only users-login for tools.php.
	$modules = movelogin_get_modules();
	$allowed_modules_for_tools = array( 'users-login' );

	if ( $modules ) {
		foreach ( $modules as $key => $dummy ) {
			// Only load tools.php for allowed modules (users-login).
			if ( in_array( $key, $allowed_modules_for_tools, true ) ) {
				if ( movelogin_has_pro() ) {
					$file = MOVELOGIN_PRO_MODULES_PATH . sanitize_key( $key ) . '/tools.php';

					if ( file_exists( $file ) ) {
						require_once( $file );
					}
				}

				$file = MOVELOGIN_MODULES_PATH . sanitize_key( $key ) . '/tools.php';

				if ( file_exists( $file ) ) {
					require_once( $file );
				}
			}

			// Load callbacks.php for all modules (needed for settings pages).
			if ( ! is_admin() ) {
				continue;
			}

			if ( movelogin_has_pro() ) {
				$file = MOVELOGIN_PRO_MODULES_PATH . sanitize_key( $key ) . '/callbacks.php';

				if ( file_exists( $file ) ) {
					require_once( $file );
				}
			}

			$file = MOVELOGIN_MODULES_PATH . sanitize_key( $key ) . '/callbacks.php';

			if ( file_exists( $file ) ) {
				require_once( $file );
			}
		}
	}

	// Active sub-modules - Only load move-login.
	$modules = movelogin_get_active_submodules();

	if ( $modules ) {
		foreach ( $modules as $module => $plugins ) {
			// Only process users-login module.
			if ( 'users-login' !== $module ) {
				continue;
			}

			foreach ( $plugins as $plugin ) {
				// Only load move-login submodule.
				if ( 'move-login' !== $plugin ) {
					continue;
				}

				// Only load free version, not pro.
				if ( ! movelogin_submodule_is_pro( $module, $plugin ) ) {
					$file_path = movelogin_get_submodule_file_path( $module, $plugin );
					if ( is_array( $file_path ) ) {
						// If both free and pro exist, only load free.
						if ( isset( $file_path['free'] ) && file_exists( $file_path['free'] ) ) {
							require_once( $file_path['free'] );
						}
					} else {
						if ( file_exists( $file_path ) ) {
							require_once( $file_path );
						}
					}
				}
			}
		}
	}

	$has_activation = false;

	if ( is_admin() && movelogin_get_site_transient( 'movelogin_activation' ) ) {
		$has_activation = true;

		movelogin_delete_site_transient( 'movelogin_activation' );

		/**
		 * Fires once SecuPress is activated, after the SecuPress's plugins are loaded.
		 *
		 * @since 1.0
		 * @see `movelogin_activation()`
		 */
		do_action( 'movelogin.plugins.activation' );
	}

	if ( movelogin_is_pro() && is_admin() && movelogin_get_site_transient( 'movelogin_pro_activation' ) ) {
		$has_activation = true;

		movelogin_delete_site_transient( 'movelogin_pro_activation' );

		/**
		 * Fires once SecuPress Pro is activated, after the SecuPress's plugins are loaded.
		 *
		 * @since 1.1.4
		 * @see `movelogin_pro_activation()`
		 */
		do_action( 'movelogin.pro.plugins.activation' );
	}

	if ( $has_activation ) {
		/**
		 * Fires once SecuPress or SecuPress Pro is activated, after the SecuPress's plugins are loaded.
		 *
		 * @since 1.1.4
		 */
		do_action( 'movelogin.all.plugins.activation' );
	}

	/**
	 * Fires once all our plugins/submodules has been loaded.
	 *
	 * @since 1.0
	 */
	do_action( 'movelogin.plugins.loaded' );
	/**
	 * Fires once all our plugins/submodules has been loaded in front-office or ajax.
	 *
	 * @since 2.2.6
	 */
	if ( ! is_admin() || wp_doing_ajax() ) {
		do_action( 'movelogin.plugins.loaded.front' );
	}
	/**
	 * Fires once all our plugins/submodules has been loaded in back-office.
	 *
	 * @since 2.2.6
	 */
	if ( is_admin() && ! wp_doing_ajax() ) {
		do_action( 'movelogin.plugins.loaded.back' );
	}
}

/**
 * Check is the $locale if a FR one
 *
 * @author Julio Potier
 * @since 2.2
 * 
 * @param (string) $locale The locale to be tested
 * 
 * @return (bool) True if $locale is fr_FR (france) or fr_BE (belgium) or fr_CA (canada)
 **/
function movelogin_locale_is_FR( $locale ) {
	return 'fr_FR' === $locale || 'fr_CA' === $locale || 'fr_BE' === $locale;
}

/**
 * Check is the $locale if a DE one
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @param (string) $locale The locale to be tested
 * @return (bool) True if $locale is de_DE, de_DE_formal, de_CH_informal, de_AT, de_CH
 **/
function movelogin_locale_is_DE( $locale ) {
	return 'de_DE' === $locale || 'de_DE_formal' === $locale || 'de_CH_informal' === $locale || 'de_AT' === $locale || 'de_CH' === $locale;
}

/**
 * Include files that contain our functions.
 *
 * @since 2.2.6 wp-login.php || is_admin()
 * @author Julio Potier
 * @since 1.2.3
 * @since 1.2.5 Includes requirement checks.
 * @author Grégory Viguier
 */
function movelogin_load_functions() {
	global $is_iis7, $wp_version, $pagenow;
	static $done = false;

	if ( $done ) {
		return;
	}
	$done = true;

	/**
	 * Check requirements.
	 */
	// Check php version.
	if ( version_compare( phpversion(), MOVELOGIN_PHP_MIN ) < 0 ) {
		$plugin = plugin_basename( MOVELOGIN_FILE );

		if ( current_filter() !== 'activate_' . $plugin ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( MOVELOGIN_FILE, true );
		}

		movelogin_load_plugin_textdomain_translations();

		wp_die( sprintf( __( '<strong>%1$s</strong> requires PHP %2$s minimum, your website is actually running version %3$s.', 'movelogin' ), 'SecuPress', '<code>' . MOVELOGIN_PHP_MIN . '</code>', '<code>' . phpversion() . '</code>' ) );
	}

	// Check WordPress version.
	if ( version_compare( $wp_version, MOVELOGIN_WP_MIN ) < 0 ) {
		$plugin = plugin_basename( MOVELOGIN_FILE );

		if ( current_filter() !== 'activate_' . $plugin ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( MOVELOGIN_FILE, true );
		}

		movelogin_load_plugin_textdomain_translations();

		wp_die( sprintf( __( '<strong>%1$s</strong> requires WordPress %2$s minimum, your website is actually running version %3$s.', 'movelogin' ), 'SecuPress', '<code>' . MOVELOGIN_WP_MIN . '</code>', '<code>' . $wp_version . '</code>' ) );
	}

	/**
	 * Require our functions.
	 */
	require_once( MOVELOGIN_INC_PATH . 'functions/common.php' );
	require_once( MOVELOGIN_INC_PATH . 'functions/formatting.php' );
	require_once( MOVELOGIN_INC_PATH . 'functions/options.php' );
	require_once( MOVELOGIN_INC_PATH . 'functions/modules.php' );
	require_once( MOVELOGIN_INC_PATH . 'functions/files.php' );
	require_once( MOVELOGIN_INC_PATH . 'functions/htaccess.php' );

	// The Singleton class.
	movelogin_require_class( 'Singleton' );

	// Admin side but need when running cron.
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/settings.php' );

	if ( 'wp-login.php' !== $pagenow && ! is_admin() ) {
		return;
	}

	// Functions for the admin side.
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/admin.php' );
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/options.php' );
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/ajax-post.php' );
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/modules.php' );
	require_once( MOVELOGIN_ADMIN_PATH . 'functions/notices.php' );
}


/** --------------------------------------------------------------------------------------------- */
/** I18N ======================================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_filter( 'load_textdomain_mofile', 'movelogin_load_own_i18n', 10, 2 );
/**
 * Load our own i18n to prevent too long strings or spelling errors from voluteers at translate.wp.org, sorry guys.
 *
 * @since 2.2.6 Usage of movelogin_locale_is_DE()
 * @since 2.2 Usage of movelogin_locale_is_FR()
 * @since 2.0.3 fr_BE & fr_CA = fr_FR
 * @since 2.0
 * @author Julio Potier
 *
 * @param (string)  $mofile The file to be loaded
 * @param (string)  $domain The desired textdomain
 * 
 * @return (string) $mofile
 **/
function movelogin_load_own_i18n( $mofile, $domain ) {
	if ( 'movelogin' === $domain ) {
		$determined_locale = determine_locale();
		$locale = apply_filters( 'plugin_locale', $determined_locale, $domain );
		if ( movelogin_locale_is_FR( $locale ) ) {
			$locale = 'fr_FR';
		} elseif ( movelogin_locale_is_DE( $locale ) ) {
			$locale = 'de_DE';
		}
		$mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( MOVELOGIN_FILE ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
	}
	return $mofile;
}
/**
 * Translations for the plugin textdomain.
 *
 * @author Grégory Viguier
 * @since 1.0
 */
function movelogin_load_plugin_textdomain_translations() {
	static $done = false;

	if ( $done ) {
		return;
	}
	$done = true;

	load_plugin_textdomain( 'movelogin', false, dirname( plugin_basename( MOVELOGIN_FILE ) ) . '/languages' );
	/**
	 * Fires right after the plugin text domain is loaded.
	 *
	 * @since 1.0
	 */
	do_action( 'movelogin.plugin_textdomain_loaded' );

	// Make sure Poedit keeps our plugin headers.
	/** Translators: Plugin Name of the plugin/theme */
	__( 'SP Move Login', 'movelogin' );
	/** Translators: Description of the plugin/theme */
	__( 'Move your WordPress login page to protect it from bots. A SecuPress module!', 'movelogin' );
}
