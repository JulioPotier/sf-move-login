<?php // secupress adminbar menu
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/**
 * Get modules title, icon, description and other informations.
 *
 * @since 1.0.5 Includes information about numbers of free and pro options
 * @author Gregory Viguier
 * @since 1.0
 * @author Geoffrey Crofte
 *
 * @return (array) All informations related to the modules.
 */
function movelogin_get_modules() {
	$should_be_pro = ! movelogin_is_pro();

	$modules = [
		'users-login'     => [
			'title'       => __( 'Users & Login', 'movelogin' ),
			'icon'        => 'user-login',
			'dashicon'    => 'groups',
			'summaries'   => [
				'small'   => __( 'Protect your users', 'movelogin' ),
			],
			'submodules'  => [
							'row-move-login_activated'                        => __( 'Move Login Page', 'movelogin' ),
							'row-move-login_singlesignon'                     => ! is_multisite() ? '' : '>' . __( 'Single Sign-On (SSO)', 'movelogin' ),
							// Other modules are available in the full SecuPress version
							'login-protection_type_bannonexistsuser'          => '*' . __( 'Ban Non-Existent Users', 'movelogin' ),
							'login-protection_type_limitloginattempts'        => '*' . __( 'Bad Login Attempts', 'movelogin' ),
							'login-protection_type_passwordspraying'          => '*' . __( 'Bad Password Attempts', 'movelogin' ),
							'row-login-protection_sessions_control'           => '*' . __( 'Session Control', 'movelogin' ),
							'row-login-protection_login_errors'               => '*' . __( 'Login Errors', 'movelogin' ),
							'row-double-auth_type'                            => '*' . __( '2 Factors Authentication', 'movelogin' ),
							'row-double-auth_force-strong-encryption'         => '*' . __( 'Force Strong Pass Encryption', 'movelogin' ),
							'row-double-auth_prevent-low-encryption'          => '*' . __( 'Prevent Other Encryption System', 'movelogin' ),
							'row-double-auth_prevent-hash-reuse'              => '*' . __( 'Prevent Reuse of Password Hashes', 'movelogin' ),
							'row-captcha_activate'                            => '*' . __( 'Captcha', 'movelogin' ),
							'row-password-policy_strong_passwords'            => '*' . __( 'Strong Password', 'movelogin' ),
							'row-password-policy_password_expiration'         => '*' . __( 'Password Lifespan', 'movelogin' ),
							'row-password-policy_send-emails'                 => '*' . __( 'Force Reset Passwords', 'movelogin' ),
							'row-password-policy_force-logout'                => '*' . __( 'Force Logout Everyone', 'movelogin' ),
							'row-blacklist-logins_user-creation-protection'   => '*' . __( 'Protect User Creation', 'movelogin' ),
							'row-blacklist-logins_prevent-user-creation'      => '*' . __( 'Forbid User Creation', 'movelogin' ),
							'row-blacklist-logins_bad-email-domains'          => '*' . __( 'Forbid Bad Email Domains', 'movelogin' ),
							'row-blacklist-logins_same-email-domain'          => '*' . __( 'Forbid Same Email Domain', 'movelogin' ),
							'row-blacklist-logins_activated'                  => '*' . __( 'Forbid Bad Usernames', 'movelogin' ),
							'row-blacklist-logins_admin'                      => '*' . sprintf( __( 'Forbid «%s» Usernames', 'movelogin' ), 'admin' ),
							'row-blacklist-logins_lexicomatisation'           => '*' . __( 'Rename public user names', 'movelogin' ),
							'row-blacklist-logins_stop-user-enumeration'      => '*' . __( 'Forbid User Enumeration', 'movelogin' ),
							'row-blacklist-logins_prevent-reset-password'     => '*' . __( 'Prevent Password Reset', 'movelogin' ),
							'row-blacklist-logins_default-role-activated'     => '*' . __( 'Lock Default Role', 'movelogin' ),
							'row-blacklist-logins_membership-activated'       => '*' . __( 'Lock Membership', 'movelogin' ),
							'row-blacklist-logins_admin-email-activated'      => '*' . __( 'Lock Admin Email', 'movelogin' ),
						]
		],
		'plugins-themes'  => [
			'title'       => __( 'Plugins &amp; Themes', 'movelogin' ),
			'icon'        => 'themes-plugins',
			'dashicon'    => 'admin-appearance',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Check your plugins &amp; themes', 'movelogin' ),
			],
			'submodules'  => [
							'row-uploads_activate'           => sprintf( __( 'Disallow %s uploads', 'movelogin' ), 'zip' ),
							'row-plugins_actions'            => __( 'Plugin Actions Back-end', 'movelogin' ),
							'row-plugins_installation-pro'   => movelogin_is_submodule_active( 'plugins-themes', 'plugin-installation' ) ? '>*' . __( 'Plugin Actions FTP', 'movelogin' ) : '',
							'row-plugins_show-all'           => __( 'Show All Plugins', 'movelogin' ),
							'row-plugins_detect_bad_plugins' => '*' . __( 'Detect Bad Plugins', 'movelogin' ),
							'row-themes_actions'             => __( 'Theme Actions', 'movelogin' ),
							'row-themes_detect_bad_themes'   => '*' . __( 'Detect Bad Themes', 'movelogin' ),
						]
		],
		'wordpress-core'  => [
			'title'       => __( 'WordPress Core', 'movelogin' ),
			'title-alt'   => __( 'WordPress Core & Config File', 'movelogin' ),
			'icon'        => 'wordpress',
			'dashicon'    => 'wordpress',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Tweak the core', 'movelogin' ),
			],
			'submodules'  => [
							'row-auto-update_minor'                     => __( 'Minor Updates', 'movelogin' ),
							'row-auto-update_major'                     => __( 'Major Updates', 'movelogin' ),
							'module-database'                           => '*' . __( 'Database Prefix', 'movelogin' ),
							'row-wp-config_script-concat'               => __( 'Scripts Concatenation', 'movelogin' ),
							'row-wp-config_skip-bundle'                 => __( 'Skip New Bundles', 'movelogin' ),
							'row-wp-config_debugging'                   => __( 'Debug Settings', 'movelogin' ),
							'row-wp-config_locations'                   => __( 'URL Relocation', 'movelogin' ),
							'row-wp-config_disallow_file_edit'          => __( 'File Edit', 'movelogin' ),
							'row-wp-config_disallow_unfiltered_uploads' => __( 'Unfiltered Uploads', 'movelogin' ),
							'row-wp-config_dieondberror'                => __( 'DB Error Display', 'movelogin' ),
							'row-wp-config_repair'                      => __( 'DB Repair Page', 'movelogin' ),
							'row-wp-config_cookiehash'                  => __( 'WP Cookie Name', 'movelogin' ),
							'row-wp-config_saltkeys'                    => __( 'WP Security Keys', 'movelogin' ),
							'row-'                                      => '>' . __( 'Regenerate Keys', 'movelogin' ),
						]
		],
		'sensitive-data'  => [
			'title'       => __( 'Sensitive Data', 'movelogin' ),
			'icon'        => 'sheet',
			'dashicon'    => 'text',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Keep your data safe', 'movelogin' ),
			],
			'submodules'  => [
							'row-wp-endpoints_xmlrpc'               => __( 'XML-RPC Management', 'movelogin' ),
							'row-wp-endpoints_author_base'          => __( 'Author Page Base', 'movelogin' ),
							'row-content-protect_hotlink'           => '*' . __( 'Anti Hotlink', 'movelogin' ),
							'row-content-protect_404guess'          => '*' . __( 'Anti 404 Guessing', 'movelogin' ),
							'row-content-protect_blackhole'         => __( 'Blackhole', 'movelogin' ),
							'row-content-protect_directory-listing' => __( 'Directory Listing', 'movelogin' ),
							'row-content-protect_php-disclosure'    => __( 'PHP Disclosure', 'movelogin' ),
							'row-content-protect_php-version'       => __( 'PHP Version Disclosure', 'movelogin' ),
							'row-content-protect_wp-version'        => __( 'WP Version Disclosure', 'movelogin' ),
							'row-content-protect_readmes'           => __( 'Protect Readme Files', 'movelogin' ),
							'row-content-protect_bad-url-access'    => __( 'Bad URL Access', 'movelogin' ),
						]
		],
		'firewall'        => [
			'title'       => __( 'Firewall &amp; GeoIP', 'movelogin' ),
			'title-alt'   => __( 'Firewall &amp; GeoIP Management', 'movelogin' ),
			'icon'        => 'firewall',
			'dashicon'    => 'shield',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Block Bad Requests', 'movelogin' ),
			],
			'submodules'  => [
							'row-bbq-headers_user-agents-header'     => __( 'Block Bad User Agents', 'movelogin' ),
							'row-bbq-headers_fake-google-bots'       => __( 'Block Fake SEO Bots', 'movelogin' ),
							'row-bbq-headers_bad-referer'            => '*' . __( 'Block Bad Referers', 'movelogin' ),
							'row-bbq-headers_block-ai'               => '*' . __( 'Block AI Bots', 'movelogin' ),
							'row-bbq-url-content_bad-contents'       => __( 'Block Bad Content', 'movelogin' ),
							'row-bbq-url-content_ban-404-php'        => __( 'Block 404 requests on PHP files', 'movelogin' ),
							'module-geoip-system'                    => '*' . __( 'GeoIP Management', 'movelogin' ),
						]
		],
		'file-system'     => [
			'title'       => __( 'Malware Scanners', 'movelogin' ),
			'icon'        => 'radar',
			'dashicon'    => 'search',
			'summaries'   => [
				'small'   => __( 'Check your files &amp; DB', 'movelogin' ),
				'normal'  => __( 'Check file permissions, run monitoring and antivirus on your installation to verify file integrity.', 'movelogin' ),
			],
			'with_form'      => false,
			'with_reset_box' => false,
			'mark_as_pro'    => true,
		],
		'ssl'             => [
			'new'         => true, //// remove this in 2.4
			'title'       => __( 'SSL & HTTPS', 'movelogin' ),
			'icon'        => 'sensitive-data',
			'dashicon'    => 'privacy',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Secure your requests', 'movelogin' ),
			],
			'submodules'  => [
							'row-ssl_force-https'                    => __( 'Force HTTPS', 'movelogin' ),
							'row-ssl_https-redirection'              => __( 'Redirect HTTP-&gt;HTTPS', 'movelogin' ),
							'row-ssl_mixed-content'                  => __( 'Fix Mixed Content', 'movelogin' ),
						]
		],
		'antispam'        => [
			'title'       => __( 'Spam & Phishing', 'movelogin' ),
			'icon'        => 'antispam',
			'dashicon'    => 'email-alt',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Block Malicious Bots', 'movelogin' ),
			],
			'submodules'  => [
							'row-antispam_antispam'                  => __( 'Anti-Spam', 'movelogin' ),
							'row-antiphishing_activated'             => __( 'Anti-Phishing', 'movelogin' ),
						]
		],
		'logs'            => [
			'title'       => _x( 'Logs and IPs', 'post type general name', 'movelogin' ),
			'icon'        => 'logs',
			'dashicon'    => 'welcome-write-blog',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Monitor everything', 'movelogin' ),
			],
			'with_form'      => false,
			'with_reset_box' => false,
			'submodules'     => [
							'row-banned-ips_banned-ips'      => __( 'Banned IPs', 'movelogin' ),
							'row-banned-ips_whitelist'       => __( 'Allowed IPs', 'movelogin' ),
							'row-logs_action-logs-activated' => __( 'Action Logs Activation', 'movelogin' ),
							'row-logs_404-logs-activated'    => __( '404 Logs Activation', 'movelogin' ),
							// 'row-logs_http-logs-activated'   => __( 'HTTP Logs Activation', 'movelogin' ),
						]
		],
		'backups'         => [
			'title'       => __( 'Backups', 'movelogin' ),
			'icon'        => 'backups',
			'dashicon'    => 'database-view',
			'summaries'   => [
				'small'   => __( 'Ensure Data Recovery', 'movelogin' ),
			],
			'with_form'      => false,
			'with_reset_box' => false,
			'mark_as_pro'    => true,
			'submodules'     => [
							'module-backups-storage'                            => '*' . __( 'Backup Storage', 'movelogin' ),
							'movelogin-settings-module_backups--backup-history' => '*' . __( 'Backup History', 'movelogin' ),
							'movelogin-settings-module_backups--backup-db'      => '*' . __( 'Database Backup', 'movelogin' ),
							'movelogin-settings-module_backups--backup-file'    => '*' . __( 'Files Backup', 'movelogin' ),
						]
		],
		'alerts'          => [
			'title'       => __( 'Alerts & Notifications', 'movelogin' ),
			'icon'        => 'bell',
			'dashicon'    => 'megaphone',
			'summaries'   => [
				'small'   => __( 'React to attacks', 'movelogin' ),
			],
			'with_reset_box' => false,
			'mark_as_pro'    => true,
			'submodules'     => [
							'module-notifications'   => '*' . __( 'Notifications', 'movelogin' ),
							'module-event-alerts'    => '*' . __( 'Event Alerts', 'movelogin' ),
							'module-daily-reporting' => '*' . __( 'Daily Reports', 'movelogin' ),
						]
		],
		'schedules'       => [
			'title'       => __( 'Schedules', 'movelogin' ),
			'title-alt'   => __( 'Schedule your Tasks', 'movelogin' ),
			'icon'        => 'schedule',
			'dashicon'    => 'calendar-alt',
			'summaries'   => [
				'small'   => __( 'Automate your tasks', 'movelogin' ),
			],
			'mark_as_pro'    => true,
			'with_reset_box' => false,
			'submodules'     => [
							'module-backups'          => '*' . __( 'Backups', 'movelogin' ),
							'module-scanners'         => '*' . __( 'Scanners', 'movelogin' ),
							'module-files-monitoring' => '*' . __( 'File Monitoring', 'movelogin' ),
						]
		],
		'addons'          => [
			'title'       => __( 'Add-ons', 'movelogin' ),
			'title-alt'   => __( 'Add-ons from Partners', 'movelogin' ),
			'icon'        => 'cogs',
			'dashicon'    => 'admin-tools',
			'mark_as_pro' => true,
			'summaries'   => [
				'small'   => __( 'Enhance further', 'movelogin' ),
				'normal'  => __( 'More security with our partners.', 'movelogin' ),
			],
			'with_form'      => false,
			'with_reset_box' => false,
		],
		'services'        => [ 
			'title'       => __( 'Security Services', 'movelogin' ),
			'title-alt'   => __( 'Our Pro Services', 'movelogin' ),
			'icon'        => 'ask',
			'dashicon'    => 'businessman',
			'summaries'   => [
				'small'   => __( 'Hire our experts', 'movelogin' ),
				'normal'  => __( 'The page contains our services designed to help you with the plugin.', 'movelogin' ),
			],
			'description' => array(
				__( 'The page contains our services designed to help you with the plugin.', 'movelogin' ),
			),
			'with_reset_box' => false,
			'mark_as_pro'    => true,
		],
	];

	return $modules;
}

/**
 * Depending on the value of `$activate`, will activate or deactivate a sub-module.
 *
 * @since 1.0
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 * @param (bool)   $activate  True to activate, false to deactivate.
 */
function movelogin_manage_submodule( $module, $submodule, $activate ) {
	if ( $activate ) {
		movelogin_activate_submodule( $module, $submodule );
	} else {
		movelogin_deactivate_submodule( $module, $submodule );
	}
}

/**
 * Activate a sub-module.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $module                  The module.
 * @param (string) $submodule               The sub-module.
 * @param (array)  $incompatible_submodules An array of sub-modules to deactivate.
 *
 * @return (bool) True on success. False on failure or if the submodule was already active.
 */
function movelogin_activate_submodule( $module, $submodule, $incompatible_submodules = array() ) {
	$file_path = movelogin_get_submodule_file_path( $module, $submodule );

	if ( ! $file_path ) {
		return false;
	}

	$is_active = movelogin_is_submodule_active( $module, $submodule );
	$submodule = sanitize_key( $submodule );

	if ( ! $is_active ) {
		// Deactivate incompatible sub-modules.
		if ( ! empty( $incompatible_submodules ) ) {
			movelogin_deactivate_submodule( $module, $incompatible_submodules );
		}

		// Activate the sub-module.
		update_site_option( 'movelogin_active_submodule_' . $submodule, $module );

		if ( is_array( $file_path ) ) {
			foreach ( $file_path as $path ) {
				require_once( $path );
			}
		} else {
			if ( file_exists( $file_path ) ) {
				require_once( $file_path );
			}
		}
		movelogin_add_module_notice( $module, $submodule, 'activation' );
	}

	/**
	 * Fires once a sub-module is activated, even if it was already active.
	 *
	 * @since 1.0
	 *
	 * @param (bool) $is_active True if the sub-module was already active.
	 */
	do_action( 'movelogin.modules.activate_submodule_' . $submodule, $is_active );

	/**
	 * Fires once any sub-module is activated, even if it was already active.
	 *
	 * @since 1.0
	 *
	 * @param (string) $submodule The sub-module slug.
	 * @param (bool)   $is_active True if the sub-module was already active.
	 */
	do_action( 'movelogin.modules.activate_submodule', $submodule, $is_active );

	if ( ! $is_active ) {
		movelogin_delete_site_transient( MOVELOGIN_ACTIVE_SUBMODULES );
	}

	return ! $is_active;
}

/**
 * Deactivate a sub-module.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string)       $module     The module.
 * @param (string|array) $submodules The sub-module. Can be an array, deactivate multiple sub-modules.
 */
function movelogin_deactivate_submodule( $module, $submodules ) {
	$submodules = (array) $submodules;

	if ( ! $submodules ) {
		return;
	}

	$delete_cache = false;

	foreach ( $submodules as $submodule ) {
		$is_active   = movelogin_is_submodule_active( $module, $submodule );
		$submodule   = sanitize_key( $submodule );

		if ( $is_active ) {
			// Deactivate the sub-module.
			delete_site_option( 'movelogin_active_submodule_' . $submodule );
			$delete_cache = true;

			movelogin_add_module_notice( $module, $submodule, 'deactivation' );
		}

		/**
		 * Fires once a sub-module is deactivated.
		 *
		 * @since 1.0
		 *
		 * @param (array) $args        deprecated.
		 * @param (bool)  $is_active   False if the sub-module was already inactive.
		 */
		do_action( 'movelogin.modules.deactivate_submodule_' . $submodule, [], ! $is_active );

		/**
		 * Fires once any sub-module is deactivated.
		 *
		 * @since 1.0
		 *
		 * @param (string) $submodule   The sub-module slug.
		 * @param (array)  $args        Some arguments.
		 * @param (bool)   $is_active   False if the sub-module was already inactive.
		 */
		do_action( 'movelogin.modules.deactivate_submodule', $submodule, [], ! $is_active );
	}

	if ( $delete_cache ) {
		movelogin_delete_site_transient( MOVELOGIN_ACTIVE_SUBMODULES );
	}
}


/**
 * Activate a sub-module silently. This will remove a previous activation notice and trigger no activation hook.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 */
function movelogin_activate_submodule_silently( $module, $submodule ) {
	$file_path = movelogin_get_submodule_file_path( $module, $submodule );

	if ( ! $file_path ) {
		return;
	}

	// Remove deactivation notice.
	movelogin_remove_module_notice( $module, $submodule, 'deactivation' );

	if ( movelogin_is_submodule_active( $module, $submodule ) ) {
		return;
	}

	$submodule = sanitize_key( $submodule );

	// Activate the submodule.
	update_site_option( 'movelogin_active_submodule_' . $submodule, $module );

	if ( is_array( $file_path ) ) {
		foreach ( $file_path as $path ) {
			require_once( $path );
		}
	} else {
		if ( file_exists( $file_path ) ) {
			require_once( $file_path );
		}
	}

	movelogin_delete_site_transient( MOVELOGIN_ACTIVE_SUBMODULES );
}


/**
 * Deactivate a sub-module silently. This will remove all previous activation notices and trigger no deactivation hook.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string)       $module     The module.
 * @param (string|array) $submodules The sub-module. Can be an array, deactivate multiple sub-modules.
 */
function movelogin_deactivate_submodule_silently( $module, $submodules ) {
	$submodules = (array) $submodules;

	if ( ! $submodules ) {
		return;
	}

	$delete_cache = false;

	foreach ( $submodules as $submodule ) {
		// Remove activation notice.
		movelogin_remove_module_notice( $module, $submodule, 'activation' );

		if ( ! movelogin_is_submodule_active( $module, $submodule ) ) {
			continue;
		}

		// Deactivate the submodule.
		delete_site_option( 'movelogin_active_submodule_' . $submodule );
		$delete_cache = true;
	}

	if ( $delete_cache ) {
		movelogin_delete_site_transient( MOVELOGIN_ACTIVE_SUBMODULES );
	}
}


/**
 * Add a sub-module (de)activation notice.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 * @param (string) $action    "activation" or "deactivation".
 */
function movelogin_add_module_notice( $module, $submodule, $action ) {
	$is_for_user_only    = false !== strpos( $module, '###USER###' );
	if ( $is_for_user_only ) {
		$module = '';
	}
	$submodule_name      = movelogin_get_module_data( $module, $submodule )['Name'];
	$is_silent           = false !== strpos( $action, 'silent-' );
	$action              = str_replace( 'silent-', '', $action );
	movelogin_remove_module_notice( $module, $submodule, 'activation' === $action ? 'deactivation' : 'activation' );
	$transient_name      = 'movelogin_module_' . $action . '_' . get_current_user_id();
	$transient_value     = movelogin_get_site_transient( $transient_name );
	$transient_value     = is_array( $transient_value ) ? $transient_value : array();
	if ( $is_for_user_only ) {
		$submodule_name .= ' ' . movelogin_tag_me( __( '(just for you)', 'movelogin' ), 'em' );
	}
	$transient_value[]   = $submodule_name;
	if ( movelogin_is_pro() && ! $is_silent ) {
		switch( $action ) {
			case 'activation' :
				movelogin_remove_submodule_alert( $module, $submodule );
			break;

			case 'deactivation' :
				movelogin_set_submodule_alert( $module, $submodule );
			break;
		}
	}		

	movelogin_set_site_transient( $transient_name, $transient_value );

	/**
	 * Fires once a sub-module (de)activation notice is created.
	 * The dynamic part of this hook name is "activation" or "deactivation".
	 *
	 * @since 1.0
	 *
	 * @param (string) $module    The module.
	 * @param (string) $submodule The sub-module slug.
	 */
	do_action( 'movelogin.modules.notice_' . $action, $module, $submodule );
}


/**
 * Remove a sub-module (de)activation notice.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 * @param (string) $action    "activation" or "deactivation".
 */
function movelogin_remove_module_notice( $module, $submodule, $action ) {
	$transient_name  = 'movelogin_module_' . $action . '_' . get_current_user_id();
	$transient_value = movelogin_get_site_transient( $transient_name );

	if ( ! $transient_value || ! is_array( $transient_value ) ) {
		return;
	}

	$submodule_name  = movelogin_get_module_data( $module, $submodule )['Name'];
	$transient_value = array_flip( $transient_value );

	if ( ! isset( $transient_value[ $submodule_name ] ) ) {
		return;
	}

	unset( $transient_value[ $submodule_name ] );

	if ( $transient_value ) {
		$transient_value = array_flip( $transient_value );
		movelogin_set_site_transient( $transient_name, $transient_value );
	} else {
		movelogin_delete_site_transient( $transient_name );
	}
}


/**
 * Get a sub-module data (name, parent module, version, description, author).
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 *
 * @return (array)
 */
function movelogin_get_module_data( $module, $submodule ) {
	$default_headers = array(
		'Name'        => 'Module Name',
		'Module'      => 'Main Module',
		'Version'     => 'Version',
		'Description' => 'Description',
		'Author'      => 'Author',
	);

	$file_path = movelogin_get_submodule_file_path( $module, $submodule );
	$data      = [];
	if ( $file_path && ! is_array( $file_path ) ) {
		$data  = get_file_data( $file_path, $default_headers, 'module' );
	}

	if ( empty( $data['Name'] ) ) {
		$data['Name'] = $submodule;
	}

	return $data;
}


/**
 * Remove (rewrite) rules from the `.htaccess`/`web.config` file.
 * An error notice is displayed on nginx systems or if the file is not writable.
 * This is usually used on the module deactivation.
 *
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 *
 * @param (string) $marker      Marker used in "BEGIN SecuPress ***".
 * @param (string) $module_name The module name.
 *
 * @return (bool) True if the file has been edited.
 */
function movelogin_remove_module_rules_or_notice( $marker, $module_name ) {
	global $is_apache, $is_nginx, $is_iis7;

	// Apache.
	if ( $is_apache && ! movelogin_write_htaccess( $marker ) ) {
		$message  = sprintf( __( '%s:', 'movelogin' ), $module_name ) . ' ';
		$message .= sprintf(
			/** Translators: 1 is a file name, 2 and 3 are small parts of code. */
			__( 'Your %1$s file is not writable, you have to edit it manually. Please remove the rules between %2$s and %3$s from the %1$s file.', 'movelogin' ),
			'<code>.htaccess</code>',
			"<code># BEGIN SecuPress $marker</code>",
			'<code># END SecuPress</code>'
		);
		movelogin_add_settings_error( 'general', 'apache_manual_edit', $message, 'error' );
		return false;
	}

	// IIS7.
	if ( $is_iis7 && ! movelogin_insert_iis7_nodes( $marker ) ) {
		$message  = sprintf( __( '%s:', 'movelogin' ), $module_name ) . ' ';
		$message .= sprintf(
			/** Translators: 1 is a file name, 2 is a small part of code. */
			__( 'Your %1$s file is not writable, you have to edit it manually. Please remove the rules with %2$s from the %1$s file.', 'movelogin' ),
			'<code>web.config</code>',
			"<code>SecuPress $marker</code>"
		);
		movelogin_add_settings_error( 'general', 'iis7_manual_edit', $message, 'error' );
		return false;
	}

	// Nginx.
	if ( $is_nginx ) {
		$message  = sprintf( __( '%s:', 'movelogin' ), $module_name ) . ' ';
		$message .= sprintf(
			/** Translators: 1 and 2 are small parts of code, 3 is a file name. */
			__( 'Your server runs <strong>Nginx</strong>. You have to edit the configuration file manually. Please remove all rules between %1$s and %2$s from the %3$s file.', 'movelogin' ),
			"<code># BEGIN SecuPress $marker</code>",
			'<code># END SecuPress</code>',
			'<code>nginx.conf</code>'
		);
		if ( apply_filters( 'movelogin.nginx.notice', true ) ) {
			movelogin_add_settings_error( 'general', 'nginx_manual_edit', $message, 'error' );
		}
		return false;
	}

	return true;
}


/**
 * Add (rewrite) rules to the `.htaccess`/`web.config` file.
 * An error notice is displayed on nginx or not supported systems, or if the file is not writable.
 * This is usually used on the module activation.
 *
 * @since 2.3.13 Add filters for $message
 * @author Julio Potier
 * @since 1.0
 * @since 1.3 Moved to global scope.
 * @author Grégory Viguier
 * 
 * @param (array) $args An array of arguments.
 *
 * @return (bool) True if the file has been edited.
 */
function movelogin_add_module_rules_or_notice( $args ) {
	global $is_apache, $is_nginx, $is_iis7;

	$args = array_merge( [
		'rules'      => '',
		'marker'     => '',
		'iis_args'   => [],
		'title'      => '', // Submodule name.
	], $args );

	$rules      = $args['rules'];
	$marker     = $args['marker'];
	$iis_args   = $args['iis_args'];
	$title      = $args['title'];

	// Apache.
	if ( $is_apache ) {
		// Write in `.htaccess` file.
		if ( ! movelogin_write_htaccess( $marker, $rules ) ) {
			// File not writable.
			$rules    = esc_html( $rules );
			$message  = sprintf( __( '%s:', 'movelogin' ), $title ) . ' ';
			$message .= sprintf(
				/** Translators: 1 is a file name, 2 is some code. */
				__( 'Your %1$s file is not writable. Please add the following lines at the beginning of the file: %2$s', 'movelogin' ),
				'<code>.htaccess</code>',
				"<pre># BEGIN SecuPress $marker\n$rules# END SecuPress</pre>"
			);
			$message = apply_filters( 'movelogin.apache.notice.message', $message, $args );
			movelogin_add_settings_error( 'general', 'apache_manual_edit', $message, 'error' );
			return false;
		}

		return true;
	}

	// IIS7.
	if ( $is_iis7 ) {
		$iis_args['nodes_string'] = $rules;

		// Write in `web.config` file.
		if ( ! movelogin_insert_iis7_nodes( $marker, $iis_args ) ) {
			// File not writable.
			$path     = ! empty( $iis_args['path'] ) ? $iis_args['path'] : '';
			$path_end = ! $path && strpos( ltrim( $rules ), '<rule ' ) === 0 ? '/rewrite/rules' : '';
			$path     = '/configuration/system.webServer' . ( $path ? '/' . trim( $path, '/' ) : '' ) . $path_end;
			$spaces   = explode( '/', trim( $path, '/' ) );
			$spaces   = count( $spaces ) - 1;
			$spaces   = str_repeat( ' ', $spaces * 2 );
			$rules    = esc_html( $rules );
			$message  = sprintf( __( '%s:', 'movelogin' ), $title ) . ' ';

			if ( ! empty( $iis_args['node_types'] ) ) {
				$message .= sprintf(
					/** Translators: 1 is a file name, 2 is a tag name, 3 is a folder path (kind of), 4 is some code. */
					__( 'Your %1$s file is not writable. Please remove any previous %2$s tag and add the following lines inside the tags hierarchy %3$s (create it if does not exist): %4$s', 'movelogin' ),
					'<code>web.config</code>',
					'<code class="movelogin-iis7-node-type">' . $iis_args['node_types'] . '</code>',
					'<code class="movelogin-iis7-path">' . $path . '</code>',
					"<pre>{$spaces}{$rules}</pre>"
				);
			} else {
				$message .= sprintf(
					/** Translators: 1 is a file name, 2 is a folder path (kind of), 3 is some code. */
					__( 'Your %1$s file is not writable. Please add the following lines inside the tags hierarchy %2$s (create it if does not exist): %3$s', 'movelogin' ),
					'<code>web.config</code>',
					'<code class="movelogin-iis7-path">' . $path . '</code>',
					"<pre>{$spaces}{$rules}</pre>"
				);
			}
			$message = apply_filters( 'movelogin.iis7.notice.message', $message, $args );
			movelogin_add_settings_error( 'general', 'iis7_manual_edit', $message, 'error' );
			return false;
		}

		return true;
	}

	// Nginx.
	if ( $is_nginx ) {
		// We can't edit the file, so we'll tell the user how to do.
		$message  = sprintf( __( '%s:', 'movelogin' ), $title ) . ' ';
		$message .= sprintf(
			/** Translators: 1 is a file name, 2 is some code */
			__( 'Your server runs <strong>Nginx</strong>. You have to edit the configuration file manually. Please add the following code to your %1$s file: %2$s', 'movelogin' ),
			'<code>nginx.conf</code>',
			"<pre>$rules</pre>"
		);
		if ( apply_filters( 'movelogin.nginx.notice', true ) ) {
			$message = apply_filters( 'movelogin.nginx.notice.message', $message, $args );
			movelogin_add_settings_error( 'general', 'nginx_manual_edit', $message, 'error' );
		}
		return false;
	}

	// Server not supported.
	$message  = sprintf( __( '%s:', 'movelogin' ), $title ) . ' ';
	$message .= __( 'It seems your server does not use <strong>Apache</strong>, <strong>Nginx</strong>, nor <strong>IIS7</strong>. This module won’t work.', 'movelogin' );
	$message = apply_filters( 'movelogin.unknown_os.notice.message', $message, $args );
	movelogin_add_settings_error( 'general', 'unknown_os', $message, 'error' );

	return false;
}


/**
 * Get the counts of Free & Pro modules, or Free or Pro individually.
 *
 * @since 1.0.5
 * @author Geoffrey Crofte
 *
 * @param  (string) $type Null by default, "free" or "pro" string expected.
 *
 * @return (array|int)    Array of both types of module count, or an individual count
 */
function movelogin_get_options_counts( $type = null ) {
	$modules = movelogin_get_modules();
	$counts = array( 'free' => 0, 'pro' => 0 );

	foreach ( $modules as $mod ) {
		$counts['free'] = ! empty( $mod['counts']['free_options'] ) ? $counts['free'] + $mod['counts']['free_options'] : $counts['free'];
		$counts['pro']  = ! empty( $mod['counts']['pro_options'] ) ? $counts['pro'] + $mod['counts']['pro_options'] : $counts['pro'];
	}

	return ! empty( $counts[ $type ] ) ? $counts[ $type ] : $counts;
}


/**
 * Get a list of all active sub-modules.
 *
 * @since 1.0
 * @author Grégory Viguier
 *
 * @return (array) An array of arrays with the modules as keys and lists of sub-modules as values.
 */
function movelogin_get_active_submodules() {
	global $wpdb;

	// Try to get the cache.
	$active_submodules = movelogin_get_site_transient( MOVELOGIN_ACTIVE_SUBMODULES );
	if ( is_array( $active_submodules ) ) {
		return $active_submodules;
	}

	if ( is_multisite() ) {
		$results = $wpdb->get_results( "SELECT meta_value AS module, REPLACE( meta_key, 'movelogin_active_submodule_', '' ) AS submodule FROM $wpdb->sitemeta WHERE meta_key LIKE 'movelogin\_active\_submodule\_%' ORDER BY meta_value, meta_key" );
	} else {
		$results = $wpdb->get_results( "SELECT option_value AS module, REPLACE( option_name, 'movelogin_active_submodule_', '' ) AS submodule FROM $wpdb->options WHERE option_name LIKE 'movelogin\_active\_submodule\_%' ORDER BY option_value, option_name" );
	}

	if ( ! $results ) {
		movelogin_set_site_transient( MOVELOGIN_ACTIVE_SUBMODULES, array() );
		return array();
	}

	$active_submodules = array();

	foreach ( $results as $result ) {
		if ( ! isset( $active_submodules[ $result->module ] ) ) {
			$active_submodules[ $result->module ] = array();
		}

		$active_submodules[ $result->module ][] = sanitize_key( $result->submodule );
	}

	movelogin_set_site_transient( MOVELOGIN_ACTIVE_SUBMODULES, $active_submodules );

	return $active_submodules;
}


/**
 * Check whether a sub-module is active.
 *
 * @since 1.0
 *
 * @param (string) $module    A module.
 * @param (string) $submodule A sub-module.
 * @author Grégory Viguier
 *
 * @return (bool)
 */
function movelogin_is_submodule_active( $module, $submodule ) {
	$submodule = sanitize_key( $submodule );

	if ( wp_doing_ajax() ) {
		$is_active = get_site_option( 'movelogin_active_submodule_' . $submodule );
		$is_active = $is_active && $module === $is_active;

		if ( $is_active && ! movelogin_is_pro() && movelogin_submodule_is_pro( $module, $submodule ) ) {
			return false;
		}

		return $is_active;
	}

	$active_submodules = movelogin_get_active_submodules();

	if ( empty( $active_submodules[ $module ] ) || ! is_array( $active_submodules[ $module ] ) ) {
		return false;
	}

	$active_submodules[ $module ] = array_flip( $active_submodules[ $module ] );

	$is_active = isset( $active_submodules[ $module ][ $submodule ] );

	if ( $is_active && ! movelogin_is_pro() && movelogin_submodule_is_pro( $module, $submodule ) ) {
		return false;
	}

	return $is_active;
}


/**
 * Get a list of all active Pro sub-modules.
 *
 * @since 1.1.4
 * @author Grégory Viguier
 *
 * @return (array) An array of arrays with the modules as keys and lists of sub-modules as values.
 */
function movelogin_get_active_pro_submodules() {
	static $active_submodules_cache;
	static $active_pro_submodules;

	$active_submodules_current = movelogin_get_active_submodules();

	if ( $active_submodules_cache !== $active_submodules_current ) {
		$active_submodules_cache = $active_submodules_current;
		unset( $active_pro_submodules );
	}

	if ( isset( $active_pro_submodules ) ) {
		return $active_pro_submodules;
	}

	$active_pro_submodules = array();

	if ( $active_submodules_current ) {
		foreach ( $active_submodules_current as $module => $submodules ) {
			foreach ( $submodules as $i => $submodule ) {
				if ( movelogin_submodule_is_pro( $module, $submodule ) ) {
					if ( empty( $active_pro_submodules[ $module ] ) ) {
						$active_pro_submodules[ $module ] = array();
					}
					$active_pro_submodules[ $module ][] = $submodule;
				}
			}
		}
	}

	return $active_pro_submodules;
}


/**
 * Tell if a sub-module is Pro.
 *
 * @since 1.1.4
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 *
 * @return (bool) True if Pro. False otherwize.
 */
function movelogin_submodule_is_pro( $module, $submodule ) {
	static $paths = array();

	$key = $module . '|' . $submodule;

	if ( ! isset( $paths[ $key ] ) ) {
		$file_path = sanitize_key( $module ) . '/plugins/' . sanitize_key( $submodule ) . '.php';

		if ( defined( 'MOVELOGIN_PRO_MODULES_PATH' ) ) {
			$paths[ $key ] = file_exists( MOVELOGIN_PRO_MODULES_PATH . $file_path );
		} else {
			$paths[ $key ] = ! file_exists( MOVELOGIN_MODULES_PATH . $file_path );
		}
	}

	return $paths[ $key ];
}


/**
 * Get a sub-module file path. Pro, free, both.
 *
 * @since 1.4.9 $both Param
 * @since 1.0
 * @author Grégory Viguier
 *
 * @param (string) $module    The module.
 * @param (string) $submodule The sub-module.
 * @param (bool)   $both      True will return all the found files path
 *
 * @return (string|bool) The file path on success. False on failure.
 */
function movelogin_get_submodule_file_path( $module, $submodule ) {
	$file_path = sanitize_key( $module ) . '/plugins/' . sanitize_key( $submodule ) . '.php';
	$paths     = [];

	if ( file_exists( MOVELOGIN_MODULES_PATH . $file_path ) ) {
		$paths['free'] = MOVELOGIN_MODULES_PATH . $file_path;
	}

	if ( defined( 'MOVELOGIN_PRO_MODULES_PATH' ) && file_exists( MOVELOGIN_PRO_MODULES_PATH . $file_path ) ) {
		$paths['pro'] = MOVELOGIN_PRO_MODULES_PATH . $file_path;
	}

	if ( empty( $paths ) ) {
		return false;
	} elseif ( isset( $paths['pro'], $paths['free'] ) ) {
		return $paths;
	} elseif( isset( $paths['pro'] ) ) {
		return $paths['pro'];
	} else {
		return $paths['free'];
	}
}
