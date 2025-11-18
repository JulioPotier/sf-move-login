<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/**
 * Enqueue SweetAlert script and style.
 *
 * @since 1.0
 * @author Grégory Viguier
 */
function movelogin_enqueue_sweet_alert() {
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
	$version = $suffix ? '1.3.4' : time();

	// Enqueue Swal2 CSS.
	wp_enqueue_style( 'wpmedia-css-sweetalert2', MOVELOGIN_ADMIN_CSS_URL . 'sweetalert2' . $suffix . '.css', array(), $version );
	// Enqueue Swal2 JS.
	wp_enqueue_script( 'wpmedia-js-sweetalert2', MOVELOGIN_ADMIN_JS_URL . 'sweetalert2' . $suffix . '.js', array( 'jquery' ), $version, true );
}


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
 * Return a <table> containing 2 strings displayed with the Diff_Renderer from WP Core.
 *
 * @since 1.0
 *
 * @param (string) $left_string  1st text to compare.
 * @param (string) $right_string 2nd text to compare.
 * @param (array)  $args         An array of arguments (titles).
 *
 * @return (string)
 */
function movelogin_text_diff( $left_string, $right_string, $args = array() ) {
	global $wp_local_package;

	if ( ! class_exists( 'WP_Text_Diff_Renderer_Table' ) ) {
		require_once( ABSPATH . WPINC . '/wp-diff.php' );
	}

	if ( ! class_exists( 'SecuPress_Text_Diff_Renderer_Table' ) ) {

		/**
		 * Table renderer to display the diff lines.
		 *
		 * @since 1.0
		 * @uses WP_Text_Diff_Renderer_Table Extends
		 */
		class SecuPress_Text_Diff_Renderer_Table extends WP_Text_Diff_Renderer_Table {
			/**
			 * Number of leading context "lines" to preserve.
			 *
			 * @var int
			 * @access public
			 * @since 1.0
			 */
			public $_leading_context_lines  = 0;
			/**
			 * Number of trailing context "lines" to preserve.
			 *
			 * @var int
			 * @access public
			 * @since 1.0
			 */
			public $_trailing_context_lines = 0;
		}
	}

	$args         = wp_parse_args( $args, array(
		'title'       => __( 'File Differences', 'movelogin' ),
		'title_left'  => __( 'Real file', 'movelogin' ),
		'title_right' => __( 'Your file', 'movelogin' ),
	) );
	$left_string  = normalize_whitespace( $left_string );
	$right_string = normalize_whitespace( $right_string );
	$left_lines   = explode( "\n", $left_string );
	$right_lines  = explode( "\n", $right_string );
	$text_diff    = new Text_Diff( $left_lines, $right_lines );
	$renderer     = new SecuPress_Text_Diff_Renderer_Table( $args );
	$diff         = $renderer->render( $text_diff );

	if ( ( ! $wp_local_package && ! $diff ) ||
		( $wp_local_package && ( ! $diff || trim( strip_tags( $diff ) ) === '&nbsp;&nbsp;$wp_local_package = \'' . $wp_local_package . '\';' ) )
		) {
		$diff = '<tr><td>// ' . __( 'No differences', 'movelogin' ) . '</td><td>// ' . __( 'No differences', 'movelogin' ) . '</td></tr>';
	}

	$r = "<table class='diff is-split-view'>\n";
		$r .= '<thead>';
			$r .= '<tr class="diff-title"><th colspan="2">' . $args['title'] . "</th></tr>\n";
		$r .= "</thead>\n";

		$r .= '<tbody>';
			$r .= "<tr class='diff-sub-title'>\n";
				$r .= "\t<th>$args[title_left]</th>\n";
	 			$r .= "\t<th>$args[title_right]</th>\n";
			$r .= "</tr>\n";
			$r .= $diff;
		$r .= "</tbody>\n";

	$r .= "</table>\n";

	return $r;
}


/**
 * Keep the old scan report (grade + status) to be compared on step4
 *
 * @since 1.0
 * @author Julio Potier
 */
function movelogin_set_old_report() {
	$grade  = movelogin_get_scanner_counts( 'grade' );
	$report = movelogin_get_scan_results();
	update_option( 'movelogin_step1_report', array( 'grade' => $grade, 'report' => $report ) );
}


/**
 * Return the old scan report.
 *
 * @since 1.0
 * @author Julio Potier
 * @see movelogin_set_old_report()
 *
 * @return (array|false)
 */
function movelogin_get_old_report() {
	return get_option( 'movelogin_step1_report' );
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