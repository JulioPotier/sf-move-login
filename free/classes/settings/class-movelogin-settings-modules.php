<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/**
 * Modules settings class.
 *
 * @package MoveLogin
 * @subpackage MoveLogin_Settings
 * @since 1.0
 */
class MoveLogin_Settings_Modules extends MoveLogin_Settings {

	const VERSION = '1.0';

	/**
	 * All the modules, with (mainly) title, icon, description.
	 *
	 * @var (array)
	 */
	protected static $modules;

	/**
	 * The reference to *Singleton* instance of this class.
	 *
	 * @var (object)
	 */
	protected static $_instance;


	/** Setters ================================================================================= */

	/**
	 * Set the modules infos.
	 *
	 * @since 1.0
	 */
	final protected static function set_modules() {
		static::$modules = movelogin_get_modules();
	}


	/**
	 * Set the current module.
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	final protected function set_current_module() {
		$this->modulenow = isset( $_GET['module'] ) ? $_GET['module'] : 'users-login';
		// Accept all modules that exist in the modules list, not just those with settings.php
		$this->modulenow = array_key_exists( $this->modulenow, static::get_modules() ) ? $this->modulenow : 'users-login';
		return $this;
	}


	/** Getters ================================================================================= */

	/**
	 * Set the modules infos.
	 *
	 * @since 1.0
	 *
	 * @return (array) The modules.
	 */
	final public static function get_modules() {
		if ( empty( static::$modules ) ) {
			static::set_modules();
		}

		return static::$modules;
	}


	/**
	 * Get a module title.
	 *
	 * @since 1.0
	 *
	 * @param (string) $module The desired module.
	 *
	 * @return (string)
	*/
	final public function get_module_title( $module = false ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		if ( ! empty( $modules[ $module ]['title-alt'] ) ) {
			return $modules[ $module ]['title-alt'];
		}
		if ( ! empty( $modules[ $module ]['title'] ) ) {
			return $modules[ $module ]['title'];
		}

		return '';
	}


	/**
	 * Get a module descriptions.
	 *
	 * @since 1.0
	 *
	 * @param (string) $module The desired module.
	 *
	 * @return (array)
	*/
	final public function get_module_descriptions( $module = false ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		if ( ! empty( $modules[ $module ]['description'] ) ) {
			return (array) $modules[ $module ]['description'];
		}

		return array();
	}


	/**
	 * Get a module summary.
	 *
	 * @since 1.0
	 *
	 * @param (string) $module The desired module.
	 * @param (string) $size The desired size: small|normal.
	 *
	 * @return (string)
	*/
	final public function get_module_summary( $module = false, $size = 'normal' ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		if ( ! empty( $modules[ $module ]['summaries'][ $size ] ) ) {
			return $modules[ $module ]['summaries'][ $size ];
		}

		return '';
	}


	/**
	 * Get if a module is new
	 *
	 * @since 2.2.6
	 *
	 * @param (string) $module The desired module.
	 *
	 * @return (bool)
	*/
	final public function is_new_module( $module = false ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		return isset( $modules[ $module ]['new'] ) && $this->modulenow !== $module;
	}


	/**
	 * Get a module icon.
	 *
	 * @since 1.0
	 * @author Geoffrey
	 *
	 * @param (string) $module The desired module.
	 *
	 * @return (string)
	 */
	final public function get_module_icon( $module = false ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		if ( ! empty( $modules[ $module ]['icon'] ) ) {
			return $modules[ $module ]['icon'];
		}

		return '';
	}


	/**
	 * Tells if the reset box should be displayed for a specific module.
	 *
	 * @since 1.0
	 *
	 * @param (string) $module The desired module.
	 *
	 * @return (bool)
	*/
	final public function display_module_reset_box( $module = false ) {
		$modules = static::get_modules();
		$module  = $module ? $module : $this->modulenow;

		return isset( $modules[ $module ]['with_reset_box'] ) ? (bool) $modules[ $module ]['with_reset_box'] : true;
	}


	/** Init ==================================================================================== */

	/**
	 * Init: this method is required by the class `MoveLogin_Singleton`.
	 *
	 * @since 1.0
	 */
	protected function _init() {
		parent::_init();

		$modules = static::get_modules();

		$this->with_form = ! ( isset( $modules[ $this->modulenow ]['with_form'] ) && false === $modules[ $this->modulenow ]['with_form'] );

		if ( movelogin_is_pro() ) {
			require_once( MOVELOGIN_PRO_ADMIN_PATH . 'settings.php' );
		}
	}


	/** Main template tags ====================================================================== */

	/**
	 * Print the page content.
	 *
	 * @since 1.0
	 */
	public function print_page() {
		$movelogin_has_sideads = apply_filters( 'movelogin.no_sidebar', true ) && apply_filters( 'movelogin.no_sideads', true );
		?>
		<div class="wrap">

			<?php movelogin_admin_heading( __( 'Modules', 'movelogin' ) ); ?>
			<?php settings_errors(); ?>

			<div class="movelogin-wrapper movelogin-flex movelogin-flex-top<?php echo ( $movelogin_has_sideads ? ' movelogin-has-sideads' : '' ) ?>">
				<div class="movelogin-modules-sidebar">
					<div class="movelogin-sidebar-header">
						<div class="movelogin-flex">
							<div class="movelogin-sh-logo">
								<?php echo movelogin_get_logo(); ?>
							</div>
							<div class="movelogin-sh-name">
								<p class="movelogin-sh-title">
									<?php echo movelogin_get_logo_word( array( 'width' => 81, 'height' => 19 ) ); ?>
								</p>
							</div>
						</div>
					</div>

					<ul id="movelogin-modules-navigation" class="movelogin-modules-list-links">
						<?php $this->print_tabs(); ?>
					</ul>
				</div>
				<div class="movelogin-tab-content movelogin-tab-content-<?php echo $this->get_current_module(); ?>" id="movelogin-tab-content">
					<?php $this->print_current_module(); ?>
				</div>

				<?php $this->print_sideads(); ?>

			</div>

		</div>
		<?php
	}


	/**
	 * Print the tabs to switch between modules.
	 *
	 * @since 1.0
	 */
	protected function print_tabs() {
		foreach ( static::get_modules() as $key => $module ) {
			$icon   = isset( $module['icon'] ) ? $module['icon'] : 'movelogin-simple';
			$icon   = movelogin_is_white_label() ? '' : $icon;
			$class  = $this->get_current_module() === $key ? 'active' : '';
			$class .= ! empty( $module['mark_as_pro'] ) ? ' movelogin-pro-module' : '';
			?>
			<li>
				<a href="<?php echo esc_url( movelogin_admin_url( 'modules', $key ) ); ?>" class="<?php echo $class; ?> module-<?php echo sanitize_key( $key ); ?>">
					<span class="movelogin-tab-name"><?php echo $module['title']; ?></span>
					<span class="movelogin-tab-summary">
					<?php
					if ( apply_filters( 'movelogin.settings.description', true ) ) {
						echo $module['summaries']['small'];
					}
					?>
					</span>
					<i class="movelogin-icon-<?php echo $icon; ?>" aria-hidden="true"></i>
				</a>
			</li>
			<?php
		}
		if ( ! movelogin_is_pro() && ! movelogin_is_white_label() ) {
			$get_pro_url = movelogin_admin_url( 'get-pro' );
			?>
			<li>
				<a href="<?php echo esc_url( $get_pro_url ); ?>" class="module-get-pro"<?php echo current_user_can( 'install_plugins' ) ? '' : ' target="_blank"'; ?>>
					<span class="movelogin-tab-name"><?php _e( 'Unlock all PRO features', 'movelogin' ); ?></span>
					<span class="movelogin-tab-summary"><?php _e( 'Buy SecuPress Pro now', 'movelogin' ); ?></span>
					<i class="icon-movelogin-simple" aria-hidden="true"></i>
				</a>
			</li>
			<?php
		}
	}


	/**
	 * Print the opening form tag.
	 *
	 * @since 1.0
	 */
	final public function print_open_form_tag() {
		?>
		<form id="movelogin-module-form-settings" method="post" action="<?php echo $this->get_form_action(); ?>" enctype="multipart/form-data">
		<?php
	}


	/**
	 * Print the closing form tag and the hidden settings fields.
	 *
	 * @since 1.0
	 */
	final public function print_close_form_tag() {
		settings_fields( 'movelogin_' . $this->get_current_module() . '_settings' );
		echo '</form>';
	}


	/**
	 * Print the current module.
	 *
	 * @since 1.0
	 */
	protected function print_current_module() {
		?>
		<div class="movelogin-tab-content-header">
			<?php
			$this->print_module_title();
			?>
		</div>

		<?php
		if ( $this->get_with_form() ) {
			$this->print_open_form_tag();
		}
		?>

		<div class="movelogin-module-options-block" id="block-advanced_options" data-module="<?php echo $this->get_current_module(); ?>">
			<?php
			$this->load_module_settings();
			$this->print_module_reset_box();
			?>
		</div>

		<?php
		if ( $this->get_with_form() ) {
			$this->print_close_form_tag();
		}
	}


	/**
	 * Print a box allowing to reset the current module settings.
	 *
	 * @since 1.0
	 */
	protected function print_module_reset_box() {
		if ( ! $this->display_module_reset_box() ) {
			return;
		}
		$this->set_current_section( 'reset' );
		$this->set_section_description( __( 'When you need to reset this module’s settings to the default.', 'movelogin' ) );

		$this->set_current_plugin( 'reset' );

		$this->add_field( array(
			'name'       => 'reset',
			'field_type' => 'field_button',
			'style'      => 'small',
			'class'      => 'movelogin-button-secondary',
			'url'        => wp_nonce_url( admin_url( 'admin-post.php?action=movelogin_reset_settings&module=' . $this->get_current_module() ), 'movelogin_reset_' . $this->get_current_module() ),
			'label'      => sprintf( __( 'Reset the %s’s settings', 'movelogin' ), $this->get_module_title() ),
		) );

		$this->do_sections();
	}


	/**
	 * Print the module title.
	 *
	 * @since 1.0
	 *
	 * @param (string) $tag The title tag to use.
	 *
	 * @return (object) The class instance.
	 */
	protected function print_module_title( $tag = 'h2' ) {
		echo "<$tag class=\"movelogin-tc-title\">";
			$this->print_module_icon();
			echo $this->get_module_title();
		echo "</$tag>\n";
		return $this;
	}


	/**
	 * Print the module descriptions.
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	protected function print_module_description() {
		if ( $this->get_module_descriptions() ) {
			echo '<p>' . implode( "</p>\n<p>", $this->get_module_descriptions() ) . "</p>\n";
		}
		return $this;
	}


	/**
	 * Print the module icon.
	 *
	 * @since 1.0
	 * @author Geoffrey
	 *
	 * @return (object) The class instance.
	 */
	protected function print_module_icon() {
		if ( $this->get_module_icon() ) {
			echo '<i class="movelogin-icon-' . $this->get_module_icon() . '" aria-hidden="true"></i>' . "\n";
		}
		return $this;
	}


	/** Specific fields ========================================================================= */

	/**
	 * Non login time slot field.
	 * The field is hidden in the free version.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array of parameters. See `::field()`.
	 */
	protected function countries( $args ) {}


	/**
	 * Non login time slot field.
	 * The field is hidden in the free version.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array of parameters. See `::field()`.
	 */
	protected function non_login_time_slot( $args ) {}


	/**
	 * Displays the scheduled backups.
	 *
	 * @since 1.0
	 */
	protected function scheduled_backups() {
		echo '<a href="' . esc_url( movelogin_admin_url( 'get-pro' ) ) . '" class="movelogin-button movelogin-ghost movelogin-button-tertiary">' . __( 'Learn more about SecuPress Pro', 'movelogin' ) . '</a>';
			_e( 'This feature is available in SecuPress Pro', 'movelogin' );
	}


	/**
	 * Displays the scheduled scan.
	 *
	 * @since 1.0
	 */
	protected function scheduled_scan() {
		echo '<a href="' . esc_url( movelogin_admin_url( 'get-pro' ) ) . '" class="movelogin-button movelogin-ghost movelogin-button-tertiary">' . __( 'Learn more about SecuPress Pro', 'movelogin' ) . '</a>';
			_e( 'This feature is available in SecuPress Pro', 'movelogin' );
	}


	/**
	 * Displays the scheduled file monitoring.
	 *
	 * @since 1.0
	 */
	protected function scheduled_monitoring() {
		echo '<a href="' . esc_url( movelogin_admin_url( 'get-pro' ) ) . '" class="movelogin-button movelogin-ghost movelogin-button-tertiary">' . __( 'Learn more about SecuPress Pro', 'movelogin' ) . '</a>';
			_e( 'This feature is available in SecuPress Pro', 'movelogin' );
	}


	/**
	 * Displays the banned IPs and add actions to delete them or add new ones.
	 *
	 * @since 1.0
	 */
	protected function blacklist_ips() {
		$ban_ips            = get_site_option( MOVELOGIN_BAN_IP );
		$ban_ips            = is_array( $ban_ips ) ? $ban_ips : [];
		$offset             = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$page_url           = movelogin_admin_url( 'modules', 'logs' );
		$referer_arg        = '&_wp_http_referer=' . urlencode( esc_url_raw( $page_url ) );
		$is_search          = false;
		$search_val         = '';
		$empty_list_message = __( 'Empty disallowed IP list', 'movelogin' );

		// Ban form.
		echo '<form id="form-ban-ip" class="hide-if-js" action="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-ban-ip' . $referer_arg ), 'movelogin-ban-ip' ) ) . '" method="post">';
			echo '<label for="movelogin-ban-ip" class="screen-reader-text">' . __( 'Specify an IP to ban.', 'movelogin' ) . '</label><br/>';
			echo '<p class="description">' . sprintf( __( 'You can use %sIP ranges%s.', 'movelogin' ), __( '<a href="https://docs.secupress.me/article/161-ip-range">', 'movelogin' ), '</a>' ) . '</p>';
			echo '<textarea cols="50" id="movelogin-ban-ip" name="ip"></textarea> ';
			echo '<button type="submit" class="movelogin-button movelogin-button-mini">' . _x( 'Ban IP', 'verb', 'movelogin' ) . '</button>';
		echo "</form>\n";

		// Search.
		if ( $ban_ips && ! empty( $_POST['movelogin-search-banned-ip'] ) ) { // WPCS: CSRF ok.
			$search_val = urldecode( trim( $_POST['movelogin-search-banned-ip'] ) ); // WPCS: CSRF ok.
			$is_search  = true;
			$search_val = preg_quote( $search_val, '~' );
			$found_ips  = preg_grep('~' . $search_val . '~', array_keys( $ban_ips ) );
			$found_ips  = array_flip( $found_ips );
			$ban_ips    = array_intersect_key( $ban_ips, $found_ips );

			if ( empty( $ban_ips ) ) {
				$empty_list_message = __( 'IP not found.', 'movelogin' );
			}
		}

		// Search form.
		echo '<form action="' . esc_url_raw( movelogin_get_current_url('raw') ) . '" id="form-search-ip"' . ( $ban_ips || $is_search ? '' : ' class="hidden"' ) . ' method="post">';
			echo '<label for="movelogin-search-banned-ip" class="screen-reader-text">' . __( 'Search IP', 'movelogin' ) . '</label><br/>';
			echo '<input type="search" id="movelogin-search-banned-ip" name="movelogin-search-banned-ip" value="' . esc_attr( wp_unslash( $search_val ) ) . '"/> ';
			echo '<button type="submit" class="movelogin-button movelogin-button-primary" data-loading-i18n="' . esc_attr__( 'Searching&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr__( 'Search IP', 'movelogin' ) . '">' . __( 'Search IP', 'movelogin' ) . '</button> ';
			echo '<span class="spinner movelogin-inline-spinner hide-if-no-js"></span>';
			echo '<a class="movelogin-button movelogin-button-secondary' . ( $search_val ? '' : ' hidden' ) . '" href="' . esc_url( $page_url ) . '" ">' . __( 'Cancel search', 'movelogin' ) . '</a> ';
		echo "</form>\n";

		// Slice the list a bit: limit last results.
		/**
		* How many IP max to display
		*
		* @param (int) $limit 50 by default
		*/
		$limit     = apply_filters( 'movelogin.ip_list.limit_max', 50 );
		$count_ips = count( $ban_ips );
		if ( $count_ips > $limit ) {
			$ban_ips = array_slice( $ban_ips, - $limit );
			echo '<p>' . sprintf( __( 'Last %1$s/%2$s disallowed IPs:', 'movelogin' ), number_format_i18n( $limit ), $count_ips ) . '</p>' . "\n";
		}

		// Display the list.
		echo '<ul id="movelogin-banned-ips-list" class="movelogin-boxed-group">';
		if ( $ban_ips ) {
			foreach ( $ban_ips as $ip => $time ) {
				echo '<li class="movelogin-large-row" data-ip="' . esc_attr( $ip ) . '">';
					$format = __( 'M jS Y', 'movelogin' ) . ' ' . __( 'G:i', 'movelogin' );
					$time   = date_i18n( $format, $time + $offset );
					$href   = wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-unban-ip&ip=' . esc_attr( $ip ) . $referer_arg ), 'movelogin-unban-ip_' . $ip );

					printf( __( '<strong>%s</strong> <em>(Banned until %s)</em>', 'movelogin' ), esc_html( $ip ), $time );
					printf( '<span><a class="a-unban-ip" href="%s">%s</a> <span class="spinner movelogin-inline-spinner hide-if-no-js"></span></span>', esc_url( $href ), _x( 'Remove', 'verb', 'movelogin' ) );
				echo "</li>\n";
			}
			if ( $count_ips > $limit ) {
				echo '<li>' . __( 'Do a search to find more.', 'movelogin' ) . '</li>';
			}
			unset( $count_ips );
		} else {
			echo '<li id="no-ips">' . $empty_list_message . '</li>';
		}
		echo "</ul>\n";

		// Actions.
		echo '<p id="movelogin-banned-ips-actions">';
			// Display a button to unban all IPs.
			$clear_href = wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-clear-ips' . $referer_arg ), 'movelogin-clear-ips' );
			echo '<a class="movelogin-button movelogin-button-secondary' . ( $ban_ips || $is_search ? '' : ' hidden' ) . '" id="movelogin-clear-ips-button" href="' . esc_url( $clear_href ) . '" data-loading-i18n="' . esc_attr__( 'Clearing&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr__( 'Clear all IPs', 'movelogin' ) . '">' . __( 'Clear all IPs', 'movelogin' ) . "</a>\n";
			echo '<span class="spinner movelogin-inline-spinner' . ( $ban_ips || $is_search ? ' hide-if-no-js' : ' hidden' ) . '"></span>';
			// For JS: ban a IP.
			echo '<button type="button" class="movelogin-button movelogin-button-primary hide-if-no-js" id="movelogin-ban-ip-button" data-loading-i18n="' . esc_attr__( 'Ban in progress&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr_x( 'Disallow', 'verb', 'movelogin' ) . '">' . _x( 'Disallow', 'verb', 'movelogin' ) . "</button>\n";
			echo '<span class="spinner movelogin-inline-spinner hide-if-no-js"></span>';
		echo "</p>\n";
	}


	/**
	 * Displays the textarea that lists the IP addresses not to ban.
	 *
	 * @since 1.0
	 * @author Grégory Viguier
	 *
	 * @param (array) $args An array of parameters. See `::field()`.
	 */
	protected function whitelist_ips( $args ) {
		$ban_ips            = get_site_option( MOVELOGIN_WHITE_IP );
		$ban_ips            = is_array( $ban_ips ) ? $ban_ips : [];
		$offset             = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		$page_url           = movelogin_admin_url( 'modules', 'logs' );
		$referer_arg        = '&_wp_http_referer=' . urlencode( esc_url_raw( $page_url ) );
		$is_search          = false;
		$search_val         = '';
		$empty_list_message = __( 'Empty allowed IP list', 'movelogin' );

		// Ban form.
		echo '<form id="form-whitelist-ip" class="hide-if-js" action="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-whitelist-ip' . $referer_arg ), 'movelogin-whitelist-ip' ) ) . '" method="post">';
			echo '<label for="movelogin-whitelist-ip" class="screen-reader-text">' . __( 'Specify an IP to add to the allowed list.', 'movelogin' ) . '</label><br/>';
			echo '<p class="description">' . __( 'You can use <a href="https://docs.secupress.me/article/161-ip-range">IP ranges</a>.', 'movelogin' ) . '</p>';
			echo '<textarea cols="50" id="movelogin-whitelist-ip" name="ip"></textarea> ';
			echo '<button type="submit" class="movelogin-button movelogin-button-mini">' . _x( 'Allow IP', 'verb', 'movelogin' ) . '</button>';
		echo "</form>\n";

		// Search.
		if ( $ban_ips && ! empty( $_POST['movelogin-search-whitelist-ip'] ) ) { // WPCS: CSRF ok.
			$search_val = urldecode( trim( $_POST['movelogin-search-whitelist-ip'] ) ); // WPCS: CSRF ok.
			$is_search  = true;
			$search_val = preg_quote( $search_val, '~' );
			$found_ips  = preg_grep('~' . $search_val . '~', array_keys( $ban_ips ) );
			$found_ips  = array_flip( $found_ips );
			$ban_ips    = array_intersect_key( $ban_ips, $found_ips );

			if ( empty( $ban_ips ) ) {
				$empty_list_message = __( 'IP not found.', 'movelogin' );
			}
		}

		// Search form.
		echo '<form action="' . esc_url_raw( $page_url ) . '" id="form-search-whitelist-ip"' . ( $ban_ips || $is_search ? '' : ' class="hidden"' ) . ' method="post">';
			echo '<label for="movelogin-search-whitelist-ip" class="screen-reader-text">' . __( 'Search IP', 'movelogin' ) . '</label><br/>';
			echo '<input type="search" id="movelogin-search-whitelist-ip" name="movelogin-search-whitelist-ip" value="' . esc_attr( wp_unslash( $search_val ) ) . '"/> ';
			echo '<button type="submit" class="movelogin-button movelogin-button-primary" data-loading-i18n="' . esc_attr__( 'Searching&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr__( 'Search IP', 'movelogin' ) . '">' . __( 'Search IP', 'movelogin' ) . '</button> ';
			echo '<span class="spinner movelogin-inline-spinner hide-if-no-js"></span>';
			echo '<a class="movelogin-button movelogin-button-secondary' . ( $search_val ? '' : ' hidden' ) . '" href="' . esc_url( $page_url ) . '" data-loading-i18n="' . esc_attr__( 'Reset in progress&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr__( 'Reset', 'movelogin' ) . '">' . __( 'Cancel search', 'movelogin' ) . '</a> ';
		echo "</form>\n";

		// Slice the list a bit: limit last results.
		/**
		* How many IP max to display
		*
		* @param (int) $limit 50 by default
		*/
		$limit     = apply_filters( 'movelogin.ip_list.limit_max', 50 );
		$count_ips = count( $ban_ips );
		if ( $count_ips > $limit ) {
			$ban_ips = array_slice( $ban_ips, - $limit );
			echo '<p>' . sprintf( __( 'Last %1$s/%2$s allowed IPs:', 'movelogin' ), number_format_i18n( $limit ), $count_ips ) . '</p>' . "\n";
		}

		// Display the list.
		echo '<ul id="movelogin-whitelist-ips-list" class="movelogin-boxed-group">';
		if ( $ban_ips ) {
			foreach ( $ban_ips as $ip => $time ) {
				echo '<li class="movelogin-large-row" data-ip="' . esc_attr( $ip ) . '">';
					$href   = wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-unwhitelist-ip&ip=' . esc_attr( $ip ) . $referer_arg ), 'movelogin-unwhitelist-ip_' . $ip );

					printf( '<strong>%s</strong>', esc_html( $ip ) );
					printf( '<span><a class="a-unwhitelist-ip" href="%s">%s</a> <span class="spinner movelogin-inline-spinner hide-if-no-js"></span></span>', esc_url( $href ), __( 'Remove', 'movelogin' ) );
				echo "</li>\n";
			}
			if ( $count_ips > $limit ) {
				echo '<li>' . __( 'Do a search to find more.', 'movelogin' ) . '</li>';
			}
			unset( $count_ips );
		} else {
			echo '<li id="no-whitelist-ips">' . $empty_list_message . '</li>';
		}
		echo "</ul>\n";

		// Actions.
		echo '<p id="movelogin-whitelist-ips-actions">';
			// Display a button to unban all IPs.
			$clear_href = wp_nonce_url( admin_url( 'admin-post.php?action=movelogin-clear-whitelist-ips' . $referer_arg ), 'movelogin-clear-whitelist-ips' );
			echo '<a class="movelogin-button movelogin-button-secondary' . ( $ban_ips || $is_search ? '' : ' hidden' ) . '" id="movelogin-clear-whitelist-ips-button" href="' . esc_url( $clear_href ) . '" data-loading-i18n="' . esc_attr__( 'Clearing&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr__( 'Clear all IPs', 'movelogin' ) . '">' . __( 'Clear all IPs', 'movelogin' ) . "</a>\n";
			echo '<span class="spinner movelogin-inline-spinner' . ( $ban_ips || $is_search ? ' hide-if-no-js' : ' hidden' ) . '"></span>';
			// For JS: ban a IP.
			echo '<button type="button" class="movelogin-button movelogin-button-primary hide-if-no-js" id="movelogin-whitelist-ip-button" data-loading-i18n="' . esc_attr__( 'Adding to allowed list&hellip;', 'movelogin' ) . '" data-original-i18n="' . esc_attr_x( 'Allow', 'verb', 'movelogin' ) . '">' . _x( 'Allow', 'verb', 'movelogin' ) . "</button>\n";
			echo '<span class="spinner movelogin-inline-spinner hide-if-no-js"></span>';
		echo "</p>\n";
	}


	/**
	 * Displays the restrictions for HTTP Log
	 *
	 * @since 2.1
	 */
	protected function http_logs_restrictions() {
		$http_log_settings = get_option( MOVELOGIN_HTTP_LOGS );
		if ( ! $http_log_settings ) {
			return;
		}
		?>
		<style>

		.movelogin-http-settings th.column-hits {
			width: 45px;
		}
		.movelogin-http-settings .since {
			color:#50575e;
		}
		.movelogin-http-settings .square-box {
			display: flex;
			align-items: center;
			justify-content: center;
			box-sizing: content-box;
		}
		.movelogin-http-settings .square-box-external {
			width: 32px;
			height: 32px;
			border-radius: 5px;
		}
		.movelogin-http-settings .square-box-internal {
			width: 16px;
			height: 16px;
			border-radius: 50%;
			border: 1.5px solid #fff;
		}
		.movelogin-http-settings .square-box-percent {
			width: 13px;
			height: 13px;
			border-radius: 50%;
			box-sizing: border-box;
		}


		</style>
		<h2>Stuff</h2>
		<p class="description">Stuff Stuff Stuff Stuff</p>

		<table class="movelogin-http-settings wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="logs-select-all-1">Select All</label>
						<input id="logs-select-all-1" type="checkbox">
					</td>
					<th scope="col" class="manage-column column-title column-primary">URL</th>
					<th scope="col" class="manage-column column-max-calls">Max Calls</th>
					<th scope="col" class="manage-column column-options">Options</th>
					<th scope="col" class="manage-column column-hits">Hits</th>
				</tr>
			</thead>

			<tbody id="the-list">

			<?php
			$last_host = '';
			$max_index = count( movelogin_get_http_logs_limits() ) - 1;
			function movelogin_get_square_box_background_color( $index ) {
				$max     = count( movelogin_get_http_logs_limits() ) - 1;
				$percent = 100 - ( $index * 100 / $max );
				$from    = [ 237, 95, 116 ]; // red RGB
				$to      = [ 51, 194, 127 ]; // green RGB
				$diff    = [ $to[0] - $from[0], $to[1] - $from[1], $to[2] - $from[2] ];
				$color   = [];
				for ( $i=0; $i < 3; $i++ ) {
					$color[] = round( $from[ $i ] + ( $percent * $diff[ $i ] / 100 ) );
				}
				return implode( ',', $color );
			}
			foreach( $http_log_settings as $url => $setting ) {
				$url_host = wp_parse_url( $url, PHP_URL_HOST );
				$prefix   = $url_host === $last_host ? '— ' : '';
				$level    = (int) ! empty( $prefix );
			?>
				<tr id="http-setting-<?php echo sanitize_html_class( $url ); ?>" class="level-<?php echo $level; ?>">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-1">Select <?php echo esc_html( $url ); ?></label>
						<input type="checkbox" name="http-setting[]" value="<?php echo esc_attr( $url ); ?>">
					</th>
					<td class="column-title has-row-actions column-primary">
						<strong>
							<?php
							$last_host = $url_host;
							$url       = strlen( $url ) > 160 ? substr( $url, 0, 158 ) . '<abbr title="' . esc_attr( $url ) . '">&hellip;</abbr>' : esc_html( $url );
							echo $prefix . $url;
							?>
						</strong>

						<div class="row-actions">
							<span class="since">Since: <abbr title="2019/08/22 9:00:46 am">22 April 2021</abbr></span> |
							<span class="delete"><a href="#" class="submitdelete" aria-label="Delete “<?php echo esc_attr( $url ); ?>”">Delete</a></span>
						</div>
					</td>
					<td class="column-max-calls">
					<?php
					$percent = 100 - round( $setting['index'] * 100 / $max_index, 2 );
					$color   = movelogin_get_square_box_background_color( $setting['index'] );
					?>
					<div class="square-box" title="<?php echo esc_attr( movelogin_get_http_logs_limits()[ $setting['index'] ] ); ?>">
						<div class="square-box square-box-external" style="background-color: rgb(<?php echo $color; ?>)">
							<div class="square-box square-box-internal">
								<div class="square-box square-box-percent" style="background: conic-gradient(#FFF <?php echo $percent; ?>%, #0000 0%);"></div>
							</div>
						</div>
					</div>

					</td>
					<td class="column-options">
						<?php
						if ( isset( $setting['options']['ignore-param'] ) ) {
							echo '<p>Parameters: <code>';
							echo implode( '</code>, <code>', $setting['options']['ignore-param'] );
							echo '</code>.</p>';
						}
						if ( isset( $setting['options']['block-method'] ) ) {
							echo '<p>Blocked Methods: <code>';
							echo implode( '</code>, <code>', $setting['options']['block-method'] );
							echo '</code>.</p>';
						}
						if ( ! isset( $setting['options']['ignore-param'] ) && ! isset( $setting['options']['block-method'] ) ) {
							echo '–';
						}
						$last    = isset( $setting['last'] ) && $setting['last'] > 0 ? sprintf( __( 'Last hit: %s', 'movelogin' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $setting['last'] ) ) : '';
						$hits    = isset( $setting['hits'] ) ? number_format_i18n( (int) $setting['hits'] ) : '–';
						$hit_fmt = ! empty( $last ) ? sprintf( '<span class="update-plugins"><span class="update-count"><abbr title="%1$s">%2$s</abbr></span></span>', esc_attr( $last ), esc_html( $hits ) ) : '–';
						?>
					</td>
					<td class="column-hits">
						<?php echo $hit_fmt; ?>
					</td>
				</tr>
			<?php
			}
			?>

			</tbody>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="logs-select-all-2">Select All</label>
						<input id="logs-select-all-2" type="checkbox">
					</td>
					<th scope="col" class="manage-column column-title column-primary">URL</th>
					<th scope="col" class="manage-column column-max-calls">Max Calls</th>
					<th scope="col" class="manage-column column-options">Options</th>
					<th scope="col" class="manage-column column-hits">Hits</th>
				</tr>
			</tfoot>
		</table>
		<?php
	}



	/**
	 * Displays the old backups.
	 *
	 * @since 1.0
	 */
	protected function backup_history() {
		?>
		<p id="movelogin-no-backups"><em><?php _e( 'No backups found yet.', 'movelogin' ); ?></em></p>
		<?php
	}


	/**
	 * Displays the tables to launch a backup
	 *
	 * @since 1.0
	 */
	protected function backup_db() {
		?>
		<p class="submit">
			<button type="button" disabled="disabled" class="movelogin-button">
				<span class="icon">
					<i class="movelogin-icon-download"></i>
				</span>
				<span class="text">
					<?php esc_html_e( 'Backup my database', 'movelogin' ); ?>
				</span>
			</button>
		</p>
		<?php
	}


	/**
	 * Displays the files backups and the button to launch one.
	 *
	 * @since 1.0
	 */
	protected function backup_files() {
		?>
		<p class="submit">
			<button type="button" disabled="disabled" class="movelogin-button">
				<span class="icon">
					<i class="movelogin-icon-download"></i>
				</span>
				<span class="text">
					<?php esc_html_e( 'Backup my files', 'movelogin' ); ?>
				</span>
			</button>
		</p>
		<?php
	}


	/**
	 * Scan the installation and search for modified/malicious files
	 *
	 * @since 1.0
	 */
	protected function file_scanner() {
		?>
		<p class="submit">
			<button type="button" disabled="disabled" class="movelogin-button">
				<?php _e( 'Search for malwares', 'movelogin' ); ?>
			</button>
		</p>
		<?php
	}


	/** Includes ================================================================================ */

	/**
	 * Include the current module settings file.
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	final protected function load_module_settings() {
		// Use specific settings.php for users-login and services, otherwise use generic settings
		if ( 'users-login' === $this->modulenow || 'services' === $this->modulenow ) {
			$module_file = MOVELOGIN_MODULES_PATH . $this->modulenow . '/settings.php';
		} else {
			// Use generic settings file for all other modules
			$module_file = MOVELOGIN_MODULES_PATH . 'generic-settings.php';
		}

		if ( file_exists( $module_file ) ) {
			require_once( $module_file );
		}

		return $this;
	}


	/**
	 * Include a plugin settings file. Also, automatically set the current module and print the sections.
	 *
	 * @since 1.0
	 *
	 * @param (string) $plugin The plugin.
	 *
	 * @return (object) The class instance.
	 */
	final protected function load_plugin_settings( $plugin ) {
		/**
		 * Give the possibility to hide a full block of options
		 *
		 * @since 1.4
		 *
		 * @param (bool) false by default
		 */

		if ( false !== apply_filters( 'movelogin.settings.load_plugin.' . $plugin, false ) ) {
			return;
		}
		$plugin_file = MOVELOGIN_MODULES_PATH . $this->modulenow . '/settings/' . $plugin . '.php';

		return $this->require_settings_file( $plugin_file, $plugin );
	}


	/** Other =================================================================================== */

	/**
	 * Filter the arguments passed to the section submit button and disable it.
	 *
	 * @since 1.0.6
	 * @author Grégory Viguier
	 *
	 * @param (array) $args An array of arguments passed to the `submit_button()` method.
	 *
	 * @return (array)
	 */
	public function disable_sumit_buttons( $args ) {
		$wrap = isset( $args['wrap'] ) ? $args['wrap'] : true;
		$atts = array();
		$atts = isset( $args['other_attributes'] ) && is_array( $args['other_attributes'] ) ? $args['other_attributes'] : array();
		$atts = array_merge( $atts, array(
			'disabled'      => 'disabled',
			'aria-disabled' => 'true',
		) );

		return array_merge( $args, array(
			'wrap'             => $wrap,
			'other_attributes' => $atts,
		) );
	}
}
