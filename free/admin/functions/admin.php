<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );



/**
 * Enqueue styles for not generic SP notices (OCS, Key API).
 *
 * @since 1.0
 * @author Geoffrey
 */
function movelogin_enqueue_notices_styles() {
	static $done = false;

	if ( $done ) {
		return;
	}
	if ( ! did_action( 'admin_enqueue_scripts' ) ) {
		add_action( 'admin_enqueue_scripts', __FUNCTION__ );
		return;
	}
	$done = true;

	$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$version = $suffix ? MOVELOGIN_VERSION : time();

	wp_enqueue_style( 'movelogin-notices', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-notices' . $suffix . '.css', array(), $version );
}


/**
 * Used for the "last 5 scans", formate each row.
 *
 * @since 1.0
 *
 * @param (array) $item         An item array containing "percent", "time" and "grade".
 * @param (int)   $last_percent Percentage of the previous item. -1 for the first one.
 *
 * @return (string)
 */
function movelogin_formate_latest_scans_list_item( $item, $last_percent = -1 ) {
	$icon        = 'minus';
	$time_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

	if ( $last_percent > -1 ) {
		if ( $last_percent < $item['percent'] ) {
			$icon = 'grade-up';
		} elseif ( $last_percent > $item['percent'] ) {
			$icon = 'grade-down';
		}
	}

	return sprintf(
		'<li>
			<span class="movelogin-latest-list-time timeago">%3$s</span>
			<span class="movelogin-latest-list-date">%4$s</span>
			<strong class="movelogin-latest-list-grade letter l%2$s">%2$s</strong>
			<i class="movelogin-icon-%1$s" aria-hidden="true"></i>
		</li>',
		$icon,
		$item['grade'],
		sprintf( _x( '%s ago', 'date', 'movelogin' ), human_time_diff( $item['time'] ) ),
		date_i18n( _x( 'M dS, Y \a\t h:ia', 'date', 'movelogin' ), $item['time'] + $time_offset )
	);
}


/**
 * Print Marketing block with SecuPress pro advantages.
 *
 * @since 1.0
 * @author Geoffrey Crofte
 */
function movelogin_print_pro_advantages() {
	?>
	<div class="movelogin-pro-advantages">
		<div class="movelogin-landscape-blob">
			<div class="movelogin-col">
				<i class="movelogin-icon-antispam" aria-hidden="true"></i>
			</div>
			<div class="movelogin-col">
				<p class="movelogin-blob-title"><?php _e( 'Anti-Spam', 'movelogin' ); ?></p>
				<p class="movelogin-blob-desc"><?php _e( 'Bots represent about 60% of internet traffic. Don’t let them add their spam to your site!', 'movelogin' ); ?></p>
			</div>
		</div>
		<div class="movelogin-landscape-blob">
			<div class="movelogin-col">
				<i class="movelogin-icon-information" aria-hidden="true"></i>
			</div>
			<div class="movelogin-col">
				<p class="movelogin-blob-title"><?php _e( 'Alerts', 'movelogin' ); ?></p>
				<p class="movelogin-blob-desc"><?php _e( 'Get alerts via SMS, mobile notifications, or even by social networks in addition to email.', 'movelogin' ); ?></p>
			</div>
		</div>
		<div class="movelogin-landscape-blob">
			<div class="movelogin-col">
				<i class="movelogin-icon-firewall" aria-hidden="true"></i>
			</div>
			<div class="movelogin-col">
				<p class="movelogin-blob-title"><?php _e( 'Firewall', 'movelogin' ); ?></p>
				<p class="movelogin-blob-desc"><?php _e( 'Other features of the firewall add an additional level of protection from Internet attacks.', 'movelogin' ); ?></p>
			</div>
		</div>
		<div class="movelogin-landscape-blob">
			<div class="movelogin-col">
				<i class="movelogin-icon-logs" aria-hidden="true"></i>
			</div>
			<div class="movelogin-col">
				<p class="movelogin-blob-title"><?php _ex( 'Logs', 'post type general name', 'movelogin' ); ?></p>
				<p class="movelogin-blob-desc"><?php _e( 'All actions considered dangerous are kept in this log available at any time to check what is happening on your site.', 'movelogin' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}


/** --------------------------------------------------------------------------------------------- */
/** ADMIN NOTICES ============================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( 'admin_notices', 'movelogin_check_default_login_slug_notice' );
/**
 * Display a notice if the login slug is still set to the default value "login".
 *
 * @since 2.6
 * @author Julio Potier
 */
function movelogin_check_default_login_slug_notice() {
	if ( ! current_user_can( movelogin_get_capability() ) ) {
		return;
	}

	$login_slug = movelogin_get_module_option( 'move-login_slug-login', 'login', 'users-login' );
	
	if ( 'login' === $login_slug ) {
		$settings_url = movelogin_admin_url( 'modules', 'users-login' );
		$message = sprintf(
			__( 'The login page slug is still set to its default value "%1$s". Please configure a custom slug in the %2$ssettings%3$s to secure your login page.', 'movelogin' ),
			'<code>login</code>',
			'<a href="' . esc_url( $settings_url ) . '">',
			'</a>'
		);
		
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			$message
		);
	}
}