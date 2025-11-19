<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/** --------------------------------------------------------------------------------------------- */
/** CSS, JS, FOOTER ============================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'doing_dark_mode', 'movelogin_add_settings_scripts_for_dark_mode', 11 );
/**
 * Add some CSS for Dark Mode
 *
 * @since 1.4.7
 *
 */
function movelogin_add_settings_scripts_for_dark_mode() {
	$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$version   = $suffix ? MOVELOGIN_VERSION : time();
	// SecuPress Dark Mode
	wp_enqueue_style( 'movelogin-dark-mode', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-dark-mode' . $suffix . '.css', array( 'movelogin-wordpress-css' ), $version );
}

add_action( 'admin_enqueue_scripts', 'movelogin_add_settings_scripts', 10 );
/**
 * Add some CSS and JS to our settings pages.
 *
 * @since 1.0
 *
 * @param (string) $hook_suffix The current admin page.
 */
function movelogin_add_settings_scripts( $hook_suffix ) {

	$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$version   = $suffix ? MOVELOGIN_VERSION : time();
	$css_depts = array();
	$js_depts  = array( 'jquery' );

	// Deactivation Modal removed in 2.1 for now
	// if ( ! function_exists( 'wp_get_environment_type' ) || 'production' === wp_get_environment_type() ) {
	// 	if ( 'plugins.php' === $hook_suffix || 'plugins-network.php' === $hook_suffix ) {
	// 		wp_enqueue_style( 'movelogin-modal', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-modal' . $suffix . '.css', null, MOVELOGIN_VERSION );
	// 		wp_enqueue_script( 'movelogin-modal', MOVELOGIN_ADMIN_JS_URL . 'movelogin-modal' . $suffix . '.js', null, MOVELOGIN_VERSION, true );
	// 	}
	// }

	// Sweet Alert.
	if ( 'settings_page_movelogin_modules' === $hook_suffix ) {
		// CSS.
		$css_depts = array( 'wpmedia-css-sweetalert2' );
		wp_enqueue_style( 'wpmedia-css-sweetalert2', MOVELOGIN_ADMIN_CSS_URL . 'sweetalert2' . $suffix . '.css', array(), '1.3.4' );
		// JS.
		$js_depts  = array( 'jquery', 'wpmedia-js-sweetalert2' );
		wp_enqueue_script( 'wpmedia-js-sweetalert2', MOVELOGIN_ADMIN_JS_URL . 'sweetalert2' . $suffix . '.js', array(), '1.3.4', true );
	}

	// WordPress Common CSS.
	wp_enqueue_style( 'movelogin-wordpress-css', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-wordpress' . $suffix . '.css', $css_depts, $version );

	// WordPress Common JS.
	wp_enqueue_script( 'movelogin-wordpress-js', MOVELOGIN_ADMIN_JS_URL . 'movelogin-wordpress' . $suffix . '.js', $js_depts, $version, true );

	$localize_wp = array(
		'confirmText'         => __( 'OK', 'movelogin' ),
		'cancelText'          => _x( 'Cancel', 'verb', 'movelogin' ),
	);

	wp_localize_script( 'movelogin-wordpress-js', 'MoveLogini18n', $localize_wp );

	$pages = array(
		'settings_page_movelogin_modules'  => 1,
	);

	if ( ! isset( $pages[ $hook_suffix ] ) ) {
		return;
	}

   	// SecuPress Common CSS.
	wp_enqueue_style( 'movelogin-common-css', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-common' . $suffix . '.css', array( 'movelogin-wordpress-css' ), $version );

	// WordPress Common JS.
	wp_enqueue_script( 'movelogin-common-js', MOVELOGIN_ADMIN_JS_URL . 'movelogin-common' . $suffix . '.js', array( 'movelogin-wordpress-js' ), $version, true );

	wp_localize_script( 'movelogin-common-js', 'MoveLogini18nCommon', array(
		'confirmText'         => __( 'OK', 'movelogin' ),
		'cancelText'          => _x( 'Cancel', 'verb', 'movelogin' ),
		'closeText'           => _x( 'Close', 'verb', 'movelogin' ),
	) );

	// Settings page.
	if ( MOVELOGIN_PLUGIN_SLUG . '_page_' . MOVELOGIN_PLUGIN_SLUG . '_settings' === $hook_suffix ) {
		// CSS.
		wp_enqueue_style( 'movelogin-settings-css', MOVELOGIN_ADMIN_CSS_URL . 'movelogin-settings' . $suffix . '.css', array( 'movelogin-common-css' ), $version );
	}
	// Modules page.
	elseif ( 'settings_page_movelogin_modules' === $hook_suffix ) {
		// CSS.
		wp_enqueue_style( 'movelogin-modules-css',  MOVELOGIN_ADMIN_CSS_URL . 'movelogin-modules' . $suffix . '.css', array( 'movelogin-common-css' ), $version );

		// JS.
		wp_enqueue_script( 'movelogin-modules-js',  MOVELOGIN_ADMIN_JS_URL . 'movelogin-modules' . $suffix . '.js', array( 'movelogin-common-js' ), $version, true );

		wp_localize_script( 'movelogin-modules-js', 'MoveLogini18nModules', array(
		) );

	}

}


/** --------------------------------------------------------------------------------------------- */
/** PLUGINS LIST ================================================================================ */
/** --------------------------------------------------------------------------------------------- */

add_filter( ( is_multisite() ? 'network_admin_' : '' ) . 'plugin_action_links_' . plugin_basename( MOVELOGIN_FILE ), 'movelogin_settings_action_links' );
/**
 * Add links to the plugin row.
 *
 * @since 2.2.6 Add links for multisite, FINALLY!
 * @since 2.0 Add my license link
 * @since 1.0
 *
 * @param (array) $actions An array of links.
 *
 * @return (array) The array of links + our links.
 */
function movelogin_settings_action_links( $actions ) {
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', esc_url( trailingslashit( set_url_scheme( MOVELOGIN_WEB_MAIN, 'https' ) ) . _x( 'support', 'link to website (Only FR or EN!)', 'secupress' ) ), _x( 'Support', 'noon', 'secupress' ) ) );
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', esc_url( __( 'https://docs.secupress.me/', 'secupress' ) . 'search?query=login' ), __( 'Docs', 'secupress' ) ) );
	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', esc_url( movelogin_admin_url( 'modules' ) ), __( 'Settings' ) ) );

	return $actions;
}


/** --------------------------------------------------------------------------------------------- */
/** ADMIN MENU ================================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( ( is_multisite() ? 'network_' : '' ) . 'admin_menu', 'movelogin_create_menus' );
/**
 * Create the plugin menu and submenus.
 *
 * @since 1.0
 */
function movelogin_create_menus() {
	$cap = movelogin_get_capability();
	if ( ! current_user_can( $cap ) ) {
		return;
	}

	// Add "Move Login" link in the Settings menu.
	add_options_page(
		__( 'Move Login', 'movelogin' ),
		__( 'Move Login', 'movelogin' ),
		$cap,
		'movelogin_modules',
		'movelogin_modules'
	);
}


/** --------------------------------------------------------------------------------------------- */
/** SETTINGS PAGES ============================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Settings page.
 *
 * @since 1.0
 */
function movelogin_global_settings() {
	if ( ! class_exists( 'MoveLogin_Settings' ) ) {
		movelogin_require_class( 'settings' );
	}

	$class_name = 'MoveLogin_Settings_Global';

	if ( ! class_exists( $class_name ) ) {
		movelogin_require_class( 'settings', 'global' );
	}

	if ( movelogin_is_pro() ) {
		$class_name = 'MoveLogin_Pro_Settings_Global';

		if ( ! class_exists( $class_name ) ) {
			movelogin_pro_require_class( 'settings', 'global' );
		}
	}

	$class_name::get_instance()->print_page();
}


/**
 * Modules page.
 *
 * @since 1.0
 */
function movelogin_modules() {
	if ( ! class_exists( 'MoveLogin_Settings' ) ) {
		movelogin_require_class( 'settings' );
	}
	if ( ! class_exists( 'MoveLogin_Settings_Modules' ) ) {
		movelogin_require_class( 'settings', 'modules' );
	}

	MoveLogin_Settings_Modules::get_instance()->print_page();
}

/**
 * Scanners page.
 *
 * @since 1.0
 */
function movelogin_scanners() {
	// Scanner functionality removed
	$counts      = array( 'good' => 0, 'bad' => 0, 'warning' => 0, 'notscannedyet' => 0, 'grade' => 'N/A', 'letter' => 'N/A', 'text' => '', 'subtext' => '' );
	$items       = array();
	$reports     = array();
	$last_report = '—';
	$time_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	$use_grade   = false;

	if ( $items ) {
		$last_percent = -1;

		foreach ( $items as $item ) {
			$reports[]    = movelogin_formate_latest_scans_list_item( $item, $last_percent );
			$last_percent = $item['percent'];
		}

		$last_report = end( $items );
		$last_report = date_i18n( _x( 'M dS, Y \a\t h:ia', 'Latest scans', 'movelogin' ), $last_report['time'] + $time_offset );
	}

	if ( isset( $_GET['step'] ) && '1' === $_GET['step'] ) {
		movelogin_set_old_report();
	}

	$currently_scanning_text = '
		<span aria-hidden="true" class="movelogin-second-title">' . __( 'Currently scanning', 'movelogin' ) . '</span>
		<span class="movelogin-scanned-items">
			' . sprintf(
				__( '%1$s&nbsp;/&nbsp;%2$s points' , 'movelogin' ),
				'<span class="movelogin-scanned-current">0</span>',
				'<span class="movelogin-scanned-total">1</span>'
			) . '
		</span>';
	?>
	<div class="wrap">

		<?php movelogin_admin_heading( __( 'Scanners', 'movelogin' ) ); ?>

		<div class="movelogin-wrapper">
			<div class="movelogin-section-dark movelogin-scanners-header<?php echo $reports ? '' : ' movelogin-not-scanned-yet'; ?>">

				<div class="movelogin-heading movelogin-flex movelogin-wrap">
					<div class="movelogin-logo-block movelogin-flex">
						<div class="movelogin-lb-logo">
							<?php echo movelogin_get_logo( array( 'width' => 59 ) ); ?>
						</div>
						<div class="movelogin-lb-name">
							<p class="movelogin-lb-title">
							<?php echo movelogin_get_logo_word( array( 'width' => 98, 'height' => 23 ) ); ?>
							</p>
						</div>
					</div>
					<?php if ( ! $reports ) { ?>
					<div class="movelogin-col-text">
						<p class="movelogin-text-medium"><?php _e( 'First scan', 'movelogin' ); ?></p>
						<p><?php _e( 'Here’s how it’s going to work', 'movelogin' ); ?></p>
					</div>
					<?php } ?>
					<p class="movelogin-label-with-icon movelogin-last-scan-result<?php if ( ! $use_grade ) { echo ' hidden'; } ?>">
						<i class="movelogin-icon-movelogin" aria-hidden="true"></i>
						<span class="movelogin-upper"><?php _ex( 'Scan results', 'noon', 'movelogin' ); ?></span>
						<span class="movelogin-primary"><?php echo $last_report; ?></span>
					</p>
					<p class="movelogin-text-end hide-if-no-js">
						<a href="#movelogin-more-info" class="movelogin-link-icon movelogin-open-moreinfo<?php echo $reports ? '' : ' movelogin-activated dont-trigger-hide'; ?>" data-trigger="slidedown" data-target="movelogin-more-info">
							<span class="icon" aria-hidden="true">
								<i class="movelogin-icon-info"></i>
							</span>
							<span class="text">
								<?php _e( 'How does it work?', 'movelogin' ); ?>
							</span>
						</a>
					</p>
				</div><!-- .movelogin-heading -->

				<?php
				if ( ( movelogin_get_scanner_pagination() === 1 || movelogin_get_scanner_pagination() === 4 ) ) { ?>
					<div class="movelogin-scan-header-main movelogin-flex">
						<?php if ( $use_grade ) { ?>
						<div id="sp-tab-scans" class="movelogin-tabs-contents movelogin-flex">
							<div id="movelogin-scan" class="movelogin-tab-content" role="tabpanel" aria-labelledby="movelogin-l-scan">
								<div class="movelogin-flex movelogin-chart">

									<div class="movelogin-chart-container">
										<canvas class="movelogin-chartjs" id="status_chart" width="180" height="180"></canvas>
										<div class="movelogin-score"><?php echo $counts['letter']; ?></div>
									</div>

									<div class="movelogin-chart-legends-n-note">

										<div class="movelogin-scan-infos">
											<p class="movelogin-score-text movelogin-text-big movelogin-m0">
												<?php echo $counts['text']; ?>
											</p>
											<p class="movelogin-score movelogin-score-subtext movelogin-m0"><?php echo $counts['subtext']; ?></p>
										</div>

										<ul class="movelogin-chart-legend hide-if-no-js">
											<li class="status-good" data-status="good">
												<span class="movelogin-carret"></span>
												<?php _ex( 'Good', 'scan result', 'movelogin' ); ?>
												<span class="movelogin-count-good"></span>
											</li>
											<?php if ( $counts['warning'] > 0 ) : ?>
											<li class="status-warning" data-status="warning">
												<span class="movelogin-carret"></span>
												<?php _ex( 'Pending', 'scan result', 'movelogin' ); ?>
												<span class="movelogin-count-warning"></span>
											</li>
											<?php endif; ?>
											<li class="status-bad" data-status="bad">
												<span class="movelogin-carret"></span>
												<?php _ex( 'Bad', 'scan result', 'movelogin' ); ?>
												<span class="movelogin-count-bad"></span>
											</li>
											<?php if ( $counts['notscannedyet'] > 0 ) : ?>
											<li class="status-notscannedyet" data-status="notscannedyet">
												<span class="movelogin-carret"></span>
												<?php _ex( 'New Scan', 'scan result', 'movelogin' ); ?>
												<span class="movelogin-count-notscannedyet"></span>
											</li>
											<?php endif; ?>
										</ul><!-- .movelogin-chart-legend -->

										<?php if ( ! movelogin_is_white_label() ) { ?>
											<div id="tweeterA" class="hidden">
												<p>
													<q>
													<?php
													/** Translators: %s is the plugin name */
													$quote = sprintf( __( 'Wow! My website just got an %s grade for security using @SecuPress, what about yours?', 'movelogin' ), movelogin_get_scanner_counts( 'grade' ) );
													// echo and not _e() because we need the quote later again.
													echo $quote;
													?>
													</q>
												</p>

												<a class="movelogin-button movelogin-button-mini" target="_blank" title="<?php esc_attr_e( 'Open in a new window.', 'movelogin' ); ?>" href="https://twitter.com/intent/tweet?url=<?php
													echo rawurlencode( 'https://secupress.me' ); ?>&amp;text=<?php echo rawurlencode( html_entity_decode( $quote ) ); ?>">
													<span class="icon" aria-hidden="true"><span class="dashicons dashicons-twitter"></span></span>
													<span class="text"><?php esc_html_e( 'Tweet this', 'movelogin' ); ?></span>
												</a>

											</div><!-- #tweeterA -->
										<?php } ?>
									</div><!-- .movelogin-chart-legends-n-note -->

								</div><!-- .movelogin-chart.movelogin-flex -->
							</div><!-- .movelogin-tab-content -->

							<div id="movelogin-latest" class="movelogin-tab-content hide-if-js" role="tabpanel" aria-labelledby="movelogin-l-latest">

								<h3 class="movelogin-text-medium hide-if-js"><?php _e( 'Your last scans', 'movelogin' ); ?></h3>

								<div class="movelogin-latest-list">
									<ul class="movelogin-reports-list">
										<?php
										if ( (bool) $reports ) {
											echo implode( "\n", $reports );
										} else {
											echo '<li class="movelogin-empty"><em>' . __( 'You have no other reports for now.', 'movelogin' ) . "</em></li>\n";
										}
										?>
									</ul>
								</div><!-- .movelogin-latest-list -->

							</div><!-- .movelogin-tab-content -->


							<div id="movelogin-schedule" class="movelogin-tab-content hide-if-js" role="tabpanel" aria-labelledby="movelogin-l-schedule">
								<p class="movelogin-text-medium">
									<?php _e( 'Schedule your security analysis', 'movelogin' ); ?>
								</p>
								<p><?php _e( 'Stay updated on the security of your website. With our automatic scans, there is no need to log in to your WordPress admin to run a scan.', 'movelogin' ); ?></p>

								<?php if ( movelogin_is_pro() ) :
									$last_schedule = movelogin_get_last_scheduled_scan();
									$last_schedule = $last_schedule ? date_i18n( _x( 'Y-m-d \a\t h:ia', 'Schedule date', 'movelogin' ), $last_schedule ) : '&mdash;';
									$next_schedule = movelogin_get_next_scheduled_scan();
									$next_schedule = $next_schedule ? date_i18n( _x( 'Y-m-d \a\t h:ia', 'Schedule date', 'movelogin' ), $next_schedule ) : '&mdash;';
									?>
									<div class="movelogin-schedules-infos is-pro">
										<p class="movelogin-schedule-last-one">
											<i class="movelogin-icon-clock-o" aria-hidden="true"></i>
											<span><?php printf( __( 'Last automatic scan: %s', 'movelogin' ), $last_schedule ); ?></span>
										</p>
										<p class="movelogin-schedule-next-one">
											<i class="movelogin-icon-clock-o" aria-hidden="true"></i>
											<span><?php printf( __( 'Next automatic scan: %s', 'movelogin' ), $next_schedule ); ?></span>
										</p>

										<p class="movelogin-cta">
											<a href="<?php echo esc_url( movelogin_admin_url( 'modules', 'schedules' ) ); ?>#module-scanners" class="movelogin-button movelogin-button-primary" target="_blank"><?php _e( 'Schedule your next analysis', 'movelogin' ); ?></a>
										</p>
									</div><!-- .movelogin-schedules-infos -->
								<?php else : ?>
									<div class="movelogin-schedules-infos">
										<p class="movelogin-schedule-last-one">
											<i class="movelogin-icon-clock-o" aria-hidden="true"></i>
											<span><?php printf( __( 'Last automatic scan: %s', 'movelogin' ), '&mdash;' ); ?></span>
										</p>
										<p class="movelogin-schedule-next-one">
											<i class="movelogin-icon-clock-o" aria-hidden="true"></i>
											<span><?php printf( __( 'Next automatic scan: %s', 'movelogin' ), '&mdash;' ); ?></span>
										</p>

										<p class="movelogin-cta">
											<a href="<?php echo esc_url( movelogin_admin_url( 'modules', 'schedules' ) ); ?>#module-scanners" class="movelogin-button movelogin-button-tertiary" target="_blank"><?php _e( 'Schedule your next analysis', 'movelogin' ); ?></a>
										</p>
										<p class="movelogin-cta-detail"><?php _e( 'Available in the PRO version', 'movelogin' ); ?></p>
									</div><!-- .movelogin-schedules-infos -->
								<?php endif; ?>

							</div><!-- .movelogin-tab-content -->
						</div><!-- .movelogin-tabs-contents -->
						<?php } ?>
						<div class="movelogin-tabs-controls <?php if ( ! $use_grade ) { echo 'movelogin-inline-block '; } ?>hide-if-no-js">
							<ul class="movelogin-tabs movelogin-tabs-controls-list" role="tablist" data-content="#sp-tab-scans">
								<li role="presentation"<?php if ( ! $use_grade ) { echo 'class="hidden"'; } ?>>
									<a id="movelogin-l-latest" href="#movelogin-latest" role="tab" aria-selected="false" aria-controls="movelogin-latest">
										<span class="movelogin-label-with-icon">
											<i class="movelogin-icon-back rounded" aria-hidden="true"></i>
											<span class="movelogin-upper"><?php _e( 'Latest scans', 'movelogin' ); ?></span>
											<span class="movelogin-description"><?php _e( 'View your previous scans', 'movelogin' ); ?></span>
										</span>
									</a>
								</li>
								<?php $schedule_scan_url = $use_grade ? '#movelogin-schedule' : movelogin_admin_url( 'modules', 'schedules#module-scanners' ); ?>
								<li role="presentation">
									<a id="movelogin-l-schedule" href="<?php echo $schedule_scan_url; ?>" role="tab" aria-selected="false" aria-controls="movelogin-schedule">
										<span class="movelogin-label-with-icon">
											<i class="movelogin-icon-calendar rounded" aria-hidden="true"></i>
											<span class="movelogin-upper"><?php _e( 'Schedule Scans', 'movelogin' ); ?></span>
											<span class="movelogin-description"><?php _e( 'Manage your recurring scans', 'movelogin' ); ?></span>
										</span>
									</a>
								</li>
								<li role="presentation"<?php if ( $use_grade ) { echo 'class="hi dden"'; } ?>>
									<a id="movelogin-l-scan" href="#movelogin-scan" role="tab" aria-selected="false" aria-controls="movelogin-scan" class="movelogin-current">
										<span class="movelogin-label-with-icon">
											<i class="movelogin-icon-movelogin" aria-hidden="true"></i>
											<span class="movelogin-upper"><?php esc_html_e( 'Scan results', 'movelogin' ); ?></span>
											<span class="movelogin-primary"><?php echo $last_report; ?></span>
										</span>
									</a>
								</li>
							</ul>
							<div class="movelogin-rescan-progress-infos">
								<h3>
									<i class="movelogin-icon-movelogin" aria-hidden="true"></i><br>

									<?php echo $currently_scanning_text; ?>
								</h3>
							</div>
						</div>
					</div><!-- .movelogin-scan-header-main -->
					<?php
				}

				if ( ! $reports ) {
					?>
					<div class="movelogin-introduce-first-scan movelogin-text-center">
						<h3>
							<i class="movelogin-icon-movelogin" aria-hidden="true"></i><br>
							<span class="movelogin-init-title"><?php _e( 'Click to launch first scan', 'movelogin' ); ?></span>

							<?php echo $currently_scanning_text; ?>
						</h3>

						<p class="movelogin-start-one-click-scan">
							<button class="movelogin-button movelogin-button-primary movelogin-button-scan" type="button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'movelogin-update-oneclick-scan-date' ) ); ?>">
								<span class="icon" aria-hidden="true">
									<i class="movelogin-icon-radar"></i>
								</span>
								<span class="text">
									<?php _e( 'Scan my website', 'movelogin' ); ?>
								</span>

								<span class="movelogin-progressbar-val" style="width:2%;">
									<span class="movelogin-progress-val-txt">2 %</span>
								</span>

							</button>
						</p>
					</div><!-- .movelogin-introduce-first-scan -->
					<?php
				}
				?>

				<div class="movelogin-scanner-steps">
					<?php
					/**
					 * SecuPress Steps work this way:
					 * - current step with li.movelogin-current
					 * - passed step(s) with li.movelogin-past
					 * - that's all
					 */
					$steps = [
						'1' => [ 'title' => esc_html__( 'Security Report', 'movelogin' ) ],
						'2' => [ 'title' => esc_html__( 'Auto-Fix', 'movelogin' ) ],
						'3' => [ 'title' => esc_html__( 'Manual Operations', 'movelogin' ) ],
						'4' => [ 'title' => esc_html__( 'Resolution Report', 'movelogin' ) ],
					];
					$step              = movelogin_get_scanner_pagination();
					$steps[2]['state'] = '';
					$steps[3]['state'] = '';
					$steps[4]['state'] = '';

					switch ( $step ) {
						case 1:
							$steps[1]['state'] = ' movelogin-current';
						break;
						case 2:
							$steps[1]['state'] = ' movelogin-past';
							$steps[2]['state'] = ' movelogin-current';
						break;
						case 3:
							$steps[1]['state'] = ' movelogin-past';
							$steps[2]['state'] = ' movelogin-past';
							$steps[3]['state'] = ' movelogin-current';
						break;
						case 4:
							$steps[1]['state'] = ' movelogin-past';
							$steps[2]['state'] = ' movelogin-past';
							$steps[3]['state'] = ' movelogin-past';
							$steps[4]['state'] = ' movelogin-current';
						break;
					}
					$current_step_class = 'movelogin-is-step-' . $step;
					unset( $step );
					?>
					<ol class="movelogin-flex movelogin-counter <?php echo esc_attr( $current_step_class ); ?>">
						<?php
						foreach ( $steps as $i => $step ) {
							?>
							<li class="movelogin-col-1-3 movelogin-counter-put movelogin-flex<?php echo $step['state']; ?>" aria-labelledby="sp-step-<?php echo $i; ?>-l" aria-describedby="sp-step-<?php echo $i; ?>-d">
								<span class="movelogin-step-name" id="sp-step-<?php echo $i; ?>-l"><?php echo $step['title']; ?></span>
								<?php if ( 3 === $i ) { ?>
									<span class="movelogin-step-name alt" aria-hidden="true"><?php echo $steps[4]['title']; ?></span>
								<?php } ?>
							</li>
							<?php
						}
						?>
					</ol>

					<div id="movelogin-more-info" class="<?php echo $reports ? ' hide-if-js' : ' movelogin-open'; ?>">
						<div class="movelogin-flex movelogin-flex-top">
							<div class="movelogin-col-1-4 step1">
								<div class="movelogin-blob">
									<div class="movelogin-blob-icon" aria-hidden="true">
										<i class="movelogin-icon-radar"></i>
									</div>
									<p class="movelogin-blob-title"><?php _e( 'Site Health', 'movelogin' ); ?></p>
									<div class="movelogin-blob-content" id="sp-step-1-d">
										<p><?php _e( 'Start to check all security items with the Scan your website button.', 'movelogin' ); ?></p>
									</div>
								</div>
							</div><!-- .movelogin-col-1-4 -->
							<div class="movelogin-col-1-4 step2">
								<div class="movelogin-blob">
									<div class="movelogin-blob-icon" aria-hidden="true">
										<i class="movelogin-icon-autofix"></i>
									</div>
									<p class="movelogin-blob-title"><?php _e( 'Auto-Fix', 'movelogin' ) ?></p>
									<div class="movelogin-blob-content" id="sp-step-2-d">
										<p><?php _e( 'Launch the auto-fix on selected issues.', 'movelogin' ); ?></p>
									</div>
								</div>
							</div><!-- .movelogin-col-1-4 -->
							<div class="movelogin-col-1-4 step3">
								<div class="movelogin-blob">
									<div class="movelogin-blob-icon" aria-hidden="true">
										<i class="movelogin-icon-manuals"></i>
									</div>
									<p class="movelogin-blob-title"><?php _e( 'Manual Operations', 'movelogin' ) ?></p>
									<div class="movelogin-blob-content" id="sp-step-3-d">
										<p><?php esc_html_e( 'Go further and take a look at the items you have to fix with specific operations.', 'movelogin' ); ?></p>
									</div>
								</div>
							</div><!-- .movelogin-col-1-4 -->
							<div class="movelogin-col-1-4 step4">
								<div class="movelogin-blob">
									<div class="movelogin-blob-icon" aria-hidden="true">
										<i class="movelogin-icon-pad-check"></i>
									</div>
									<p class="movelogin-blob-title"><?php esc_html_e( 'Resolution Report', 'movelogin' ); ?></p>
									<div class="movelogin-blob-content" id="sp-step-4-d">
										<p><?php esc_html_e( 'Get the new site health report for your website.', 'movelogin' ); ?></p>
									</div>
								</div><!-- .movelogin-blob -->
							</div><!-- .movelogin-col-1-4 -->
						</div><!-- .movelogin-flex -->

						<p class="movelogin-text-end movelogin-m0">
							<a href="#movelogin-more-info" class="movelogin-link-icon movelogin-movelogin-icon-right movelogin-close-moreinfo<?php echo $reports ? '' : ' dont-trigger-hide'; ?>" data-trigger="slideup" data-target="movelogin-more-info">
								<span class="icon" aria-hidden="true">
									<i class="movelogin-icon-cross"></i>
								</span>
								<span class="text">
									<?php _e( 'I’ve got it!', 'movelogin' ); ?>
								</span>
							</a>
						</p>
					</div><!-- #movelogin-more-info -->
				</div><!-- .movelogin-scanner-steps -->

			</div><!-- .movelogin-section-dark -->

			<div class="movelogin-scanner-main-content movelogin-section-gray movelogin-bordered">

				<div class="movelogin-step-content-container">
					<?php
					movelogin_scanners_template();
					?>
				</div><!-- .movelogin-step-content-container-->

			</div>

			<?php wp_nonce_field( 'movelogin_score', 'movelogin_score', false ); ?>
		</div>
	</div><!-- .wrap -->
	<?php
}


/** --------------------------------------------------------------------------------------------- */
/** TEMPLATE TAGS =============================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Print the settings page title.
 *
 * @since 1.0
 *
 * @param (string) $title The title.
 */
function movelogin_admin_heading( $title = '' ) {
	printf( '<h1 class="movelogin-page-title screen-reader-text">%1$s <sup>%2$s</sup> %3$s</h1>', MOVELOGIN_PLUGIN_NAME, MOVELOGIN_VERSION, $title );
}

/**
 * Print the dark header of settings pages
 *
 * @since 1.0
 * @author Geoffrey
 *
 * @param (array) $titles The title and subtitle.
 */
function movelogin_settings_heading( $titles = array() ) {
	$title    = ! empty( $titles['title'] )    ? $titles['title']    : '';
	$subtitle = ! empty( $titles['subtitle'] ) ? $titles['subtitle'] : '';
	?>
	<div class="movelogin-section-dark movelogin-settings-header movelogin-header-mini movelogin-flex">
		<div class="movelogin-col-1-3 movelogin-col-logo movelogin-text-center">
			<div class="movelogin-logo-block movelogin-flex">
				<div class="movelogin-lb-logo">
					<?php echo movelogin_get_logo( array( 'width' => 131 ) ); ?>
				</div>
				<div class="movelogin-lb-name">
					<p class="movelogin-lb-title">
					<?php echo movelogin_get_logo_word( array( 'width' => 100, 'height' => 24 ) ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="movelogin-col-1-3 movelogin-col-text">
			<p class="movelogin-text-medium"><?php echo $title; ?></p>
			<?php if ( $subtitle ) { ?>
			<p><?php echo $subtitle; ?></p>
			<?php } ?>
		</div>
		<?php if ( ! movelogin_is_white_label() ) { ?>
		<div class="movelogin-col-1-3 movelogin-col-rateus movelogin-text-end">
			<p class="movelogin-rateus">
				<strong><?php _e( 'Do you like this plugin?', 'movelogin' ) ?></strong>
				<br>
				<?php printf( __( 'Please take a few seconds to rate us on %1$sWordPress.org%2$s', 'movelogin' ), '<a target="_blank" title="' . esc_attr__( 'Open in a new window.', 'movelogin' ) . '" href="' . MOVELOGIN_RATE_URL . '">', '</a>' ); ?>
			</p>
			<p class="movelogin-rateus-link">
				<a target="_blank" title="<?php esc_attr_e( 'Open in a new window.', 'movelogin' ); ?>" href="<?php echo MOVELOGIN_RATE_URL; ?>">
					<i class="movelogin-icon-star" aria-hidden="true"></i>
					<i class="movelogin-icon-star" aria-hidden="true"></i>
					<i class="movelogin-icon-star" aria-hidden="true"></i>
					<i class="movelogin-icon-star" aria-hidden="true"></i>
					<i class="movelogin-icon-star" aria-hidden="true"></i>
					<span class="screen-reader-text"><?php echo _x( 'Give us five stars', 'hidden text', 'movelogin' ); ?></span>
				</a>
			</p>
		</div>
		<?php } ?>
	</div>
	<?php
}


/**
 * Print the scanners page content.
 *
 * @since 1.0
 */
function movelogin_scanners_template() {
	// Scanner functionality removed - no scanner files available
	echo '<div class="movelogin-section-gray movelogin-bordered" style="padding: 2em; text-align: center;">';
	echo '<p style="font-size: 1.2em; margin-bottom: 1em;">' . esc_html__( 'Scanner functionality is not available in this version.', 'movelogin' ) . '</p>';
	echo '<p>' . esc_html__( 'The scanner feature has been removed from this plugin. Please use the full SecuPress version for scanner functionality.', 'movelogin' ) . '</p>';
	echo '</div>';
}


/**
 * Print a box with title.
 *
 * @since 1.0
 *
 * @param (array) $args An array containing the box title, content and id.
 */
function movelogin_sidebox( $args ) {
	$args = wp_parse_args( $args, array(
		'id'      => '',
		'title'   => 'Missing',
		'content' => 'Missing',
	) );

	echo '<div class="movelogin-postbox postbox" id="' . $args['id'] . '">';
		echo '<h3 class="hndle"><span><b>' . $args['title'] . '</b></span></h3>';
		echo'<div class="inside">' . $args['content'] . '</div>';
	echo "</div>\n";
}


/**
 * Will return the current scanner step number.
 *
 * @since 1.0
 * @author Julio Potier
 *
 * @return (int) Returns 1 if first scan never done.
 */
function movelogin_get_scanner_pagination() {
	$scans = array_filter( (array) get_site_option( MOVELOGIN_SCAN_TIMES ) );

	if ( empty( $_GET['step'] ) || ! is_numeric( $_GET['step'] ) || empty( $scans ) || 0 > $_GET['step'] ) {
		$step = 1;
	} else {
		$step = (int) $_GET['step'];
		if ( $step > 4 ) {
			movelogin_is_jarvis();
		}
	}

	return $step;
}
