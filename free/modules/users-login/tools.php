<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

/**
 * Get the Disallowed usernames.
 *
 * @author Julio Potier
 * @since 2.2.6
 * @author Grégory Viguier
 * @since 1.0
 *
 * @return (array)
 */
function movelogin_get_blacklisted_usernames() {
	// Disallowed usernames.
	// usernames with "*" are basically from malwares where the joker "*" is a random number
	$filename = movelogin_get_data_file_path( 'disallowed_logins_list' );
	$list     = [];
	if ( $filename ) {
		$list = explode( ',', file_get_contents( $filename ) );
	}
	/**
	 * Filter the list of Disallowed usernames.
	 *
	 * @since 2.0
	 *
	 * @param (array) $list List of usernames.
	 */
	$list = apply_filters( 'movelogin.plugin.disallowed_logins_list', $list );
	if ( has_filter( 'movelogin.plugin.disallowed_logins_list' ) ) {
   		_deprecated_hook( 'movelogin.plugin.disallowed_logins_list', '2.2.6', 'movelogin.plugins.disallowed_logins_list' );
	}
	$list = apply_filters( 'movelogin.plugins.disallowed_logins_list', $list );

	// Temporarily allow some Disallowed usernames.
	$allowed = (array) movelogin_cache_data( 'allowed_usernames' );
	if ( $allowed ) {
		$list = array_diff( $list, $allowed );
		movelogin_cache_data( 'allowed_usernames', array() );
	}

	return $list;
}

/**
 * Return an array of forbidden roles
 *
 * @since 2.0
 * @author Julio Potier
 *
 * @see roles_radios
 *
 * @return (array) $roles
 **/
function movelogin_get_forbidden_default_roles() {
	$roles = [ 'administrator' => true ];
	$roles = apply_filters( 'movelogin.plugin.default_role.forbidden', $roles );
	if ( has_filter( 'movelogin.plugin.default_role.forbidden' ) ) {
		_deprecated_hook( 'movelogin.plugin.default_role.forbidden', '2.2.6', 'movelogin.plugins.default_role.forbidden' );
	}
	/**
	* Filter the forbidden roles
	* @param (array) $roles, format 'role' => true
	*/
	$roles = apply_filters( 'movelogin.plugins.default_role.forbidden', $roles );

	return $roles;
}


/**
 * Return the distance between 2 strings from 0 (same) to 1 (different)
 *
 * @since 2.2.6
 * @author Julio Potier
 *
 * @param (string) $str1 The first string to compare
 * @param (string) $str2 The second string to compare
 *
 * @return (float)
 **/
function movelogin_levenshtein( $str1, $str2 ) {
    $distance = levenshtein( $str1, $str2 );
    $maxlen   = max( strlen( $str1 ), strlen( $str2 ) );
    return 1 - ( $distance / $maxlen );
}

/**
 * Detect if the user has a mobile session
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @param (int)   $user_id
 * @return (bool) True if the user_id has at least one mobile session
 **/
function movelogin_user_has_mobile_session( $user_id ) {
	$sessions_inst = WP_Session_Tokens::get_instance( $user_id );
	$all_sessions  = $sessions_inst->get_all();
	if ( empty( $all_sessions ) ) {
		return false;
	}
	return (bool) count( array_filter( wp_list_pluck( $all_sessions, 'ua' ), 'movelogin_is_mobile' ) );
}

/**
 * Get the roles of a user.
 * 
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @param int $user_id The user ID.
 * @return array The user roles.
 */
function movelogin_get_user_roles( $user_id ) {
	$user = get_user_by( 'ID', $user_id );

	return isset( $user->roles ) ? $user->roles : [];
}

/**
 * Get user IDs with active sessions.
 *
 * @since 2.2.6
 * @author Julio Potier
 *
 * @return (array) User IDs with active sessions.
 */
function movelogin_get_connected_user_ids() {
	static $user_ids = [];
	$user_ids = $user_ids ?? [];

	if ( ! empty( $user_ids ) ) {
		return $user_ids;
	}

	// Get all user IDs
	$users = get_users( [
		'meta_key'     => 'session_tokens',
		'meta_compare' => 'EXISTS'
	] );

	foreach ($users as $user) {
		$instance = WP_Session_Tokens::get_instance( $user->ID );
		$sessions = $instance->get_all();
		foreach ( $sessions as $session ) {
			if ( $session['expiration'] > time() ) {
				$user_ids[] = $user->ID;
				break; // No need to check further tokens for this user
			}
		}
	}

	return $user_ids;
}

/**
 * Check if a user is a fake user.
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @param int $user_id The user ID.
 * @return (string|bool) Descriptive word if the user is a fake user, false otherwise.
 */
function movelogin_is_fake_user( $user_id ) {
	$_user = new WP_User( $user_id );

	if ( ! $_user->exists() ) {
		return 'not_exists';
	}

	if ( 32 >= strlen( $_user->user_pass ) ) {
		return 'wrong_passwordhash';
	}

	if ( '0000-00-00 00:00:00' === $_user->user_registered ) {
		return 'no_date';
	}

	if ( empty( $_user->user_nicename ) ) {
		return 'no_nicename';
	}

	$_meta = get_user_meta( $user_id, MOVELOGIN_USER_PROTECTION, true );
	if ( ! $_meta ) {
		return 'no_metadata';
	}

	$modulo = movelogin_get_option( 'movelogin_user_protection_modulo' );
	$seed   = movelogin_get_option( 'movelogin_user_protection_seed' );

	if ( $_meta != ( $user_id * $seed ) % $modulo ) { // do not use !==, do not cast $_meta as int to do so.
		return 'no_modulo';
	}

	if ( ! is_email( $_user->user_email ) ) {
		return 'wrong_email_dom';
	}

	if ( movelogin_is_submodule_active( 'users-login', 'same-email-domain' ) && movelogin_email_domain_is_same( $_user->user_email ) ) {
		return 'same_email';
	}

	if ( movelogin_is_submodule_active( 'users-login', 'bad-email-domains' ) ) {
		// special cache system here, this cost a lot and takes a long time to check.
		if ( get_user_meta( $_user->ID, 'movelogin-bad-mx-' . md5( $_user->user_email ), true ) ) {
			return 'wrong_email_mx';
		} else if ( movelogin_pro_bad_email_domain_is_bad( $_user->user_email ) ) {
			update_user_meta( $_user->ID, 'movelogin-bad-mx-' . md5( $_user->user_email ), 1 );
			return 'wrong_email_mx';
		}
	}

	$admins = get_option( MOVELOGIN_ADMIN_IDS );
	if ( $admins && user_can( $_user, 'administrator' ) && ! in_array( $user_id, $admins ) ) {
		return 'not_admin';
	}

	return false; // This is a correct user
}

/**
 * Get the fake users
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @return array The fake users.
 */
function movelogin_get_fake_users() {
	global $wpdb;
	static $fake_users;

	if ( isset( $fake_users ) ) {
		return $fake_users;
	}

	$temp_users = [];
	$fake_users = [];

	if ( class_exists( 'SecuPress_User_Protection' ) ) {
	    remove_action( 'pre_get_users', array( $GLOBALS['SecuPress_User_Protection'], 'filter_fake_users' ) );
	}
	// #1 Get fake users without metadata
	$modulo = movelogin_get_option( 'movelogin_user_protection_modulo' );
	$seed   = movelogin_get_option( 'movelogin_user_protection_seed' );

	// #2 Get fake users with wrong metadata
	$temp_users = $wpdb->prepare("
		SELECT u.ID
		FROM {$wpdb->users} AS u
		LEFT JOIN {$wpdb->usermeta} AS um ON u.ID = um.user_id AND um.meta_key = %s
		WHERE um.meta_key IS NULL OR um.meta_value != (u.ID * %d) %% %d;
	", MOVELOGIN_USER_PROTECTION, $seed, $modulo);
	$temp_users = $wpdb->get_col( $temp_users );
	$fake_users = array_merge( $temp_users, $fake_users );

	// #3 Get fake users with a 0000-00-00 00:00:00 registered date
	$temp_users = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->users} WHERE user_registered = '0000-00-00 00:00:00'",
	);
	$fake_users = array_merge( $temp_users, $fake_users );

	// #4 Get fake users with a 32-character password length
	$temp_users = $wpdb->get_col( "SELECT ID FROM {$wpdb->users} WHERE LENGTH(user_pass) <= 32" ); // md5 length, do not change
	$fake_users = array_merge( $temp_users, $fake_users );

	// #5 Get fake users with no nicename
	$temp_users = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->users} WHERE LENGTH(user_nicename) = 0",
	);
	$fake_users = array_merge( $temp_users, $fake_users );

	// #6 Get users with bad domains MX
	if ( movelogin_is_submodule_active( 'users-login', 'bad-email-domains' ) ) {
		$domain_conditions = implode( '|', movelogin_pro_bad_email_domain_get_bad_tld_for_email() );
		$temp_users = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_email REGEXP CONCAT('.(', %s, ')$')", $domain_conditions ) );
		$fake_users = array_merge( $temp_users, $fake_users );
	}

	// #7 Get users with same domain name
	if ( movelogin_is_submodule_active( 'users-login', 'same-email-domain' ) ) {
		$website_domain = movelogin_get_current_url( 'domain' );
		$temp_users     = $wpdb->get_col( $wpdb->prepare(
			 "SELECT ID FROM {$wpdb->users} WHERE user_email LIKE %s", '%' . $wpdb->esc_like( $website_domain )
			)
		);
		$fake_users = array_merge( $temp_users, $fake_users );
	}

	// #8 Get fake users with wrong email address
	$temp_users = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->users} WHERE user_email NOT LIKE '%_@_%.__%'" // very basic but will works for the malwares, not for real case, do not use this for real verification.
	);
	$fake_users = array_merge( $temp_users, $fake_users );

	// #9 Get admin users granted directly in DB (not in MOVELOGIN_ADMIN_IDS option)
	$_tmp_pends = movelogin_get_pending_user_ids();
	$temp_users = get_users( [ 'role' => 'administrator', 'exclude' => array_merge( $_tmp_pends, get_option( MOVELOGIN_ADMIN_IDS, [] ) ), 'number' => -1, 'fields' => 'ids' ] );
	$fake_users = array_merge( $temp_users, $fake_users );

	$fake_users = array_unique( $fake_users );
	$fake_users = array_map( 'get_user_by', array_fill( 0, count( $fake_users ), 'ID' ), $fake_users );

	if ( class_exists( 'SecuPress_User_Protection' ) ) {
		add_action( 'pre_get_users', array( $GLOBALS['SecuPress_User_Protection'], 'filter_fake_users' ) );
	}
	return $fake_users;
}

/**
 * Returns pending users (user_status=2) from custom query to prevent any filter
 *
 * @since 2.2.6
 * @author Julio Potier
 * 
 * @return (array)
 */
function movelogin_get_pending_user_ids() { // do not add this in the class, we need it outside in movelogin_get_fake_users()
	global $wpdb;
	static $ids;

	if ( isset( $ids ) ) {
		return $ids;
	}

	$query = "SELECT ID FROM $wpdb->users WHERE user_status = 2";
	$ids   = $wpdb->get_col( $query );

	return $ids;
}

/**
 * Returns true if the wp_users table contains any duplicated user_pass (which is 100% malicious)
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @return (bool)
 **/
function movelogin_users_contains_duplicated_hashes() {
	global $wpdb;
	static $res;

	if ( isset( $res ) ) {
		return $res;
	}

	$query = "SELECT CASE WHEN count(distinct user_pass) = count(id) THEN 'false' ELSE 'true' END FROM $wpdb->users";
	$res   = $wpdb->get_var( $query ) === 'true';

	return $res;
}

/**
 * Returns administrators from custom query to prevent any filter
 *
 * @author Julio Potier
 * @since 2.2.6
 * 
 * @return (array)
 **/
function movelogin_get_admin_ids_by_capa() {
	global $wpdb;
	static $ids;

	if ( isset( $ids ) ) {
		return $ids;
	}
	$query = "SELECT ID FROM $wpdb->users u INNER JOIN $wpdb->usermeta um ON u.ID = um.user_id WHERE um.meta_key LIKE '{$wpdb->prefix}capabilities' AND ( um.meta_value LIKE '%\"administrator\"%' OR um.meta_value LIKE '%\'administrator\'%');";
	$ids   = $wpdb->get_col( $query );

	return $ids;
}

/**
 * Check if the email has the same domain as this website
 *
 * @since 2.2.6
 * @author Julio Potier
 * 
 * @param (string) $email
 * @return (bool)
 **/
function movelogin_email_domain_is_same( $email ) {
	static $website_domain;

	$domain  = substr( strrchr( $email, '@' ), 1 );

	// Check if the user email domain matches the website domain
	if ( ! isset( $website_domain ) ) {
		$website_domain = movelogin_get_current_url( 'domain' );
	}
	if ( strcasecmp( $domain, $website_domain ) === 0 ) {
		return true;
	}

	return false;
}

/**
 * Select an emoji set for the captcha module
 *
 * @since 2.2.6
 * @author Julio Potier
 * 
 * @param (string) $set 'all' means return everything, 'random' means anything, or a key from $sets
 * 
 * @return (array) $sets
 **/
function movelogin_get_emojiset( $set = 'random' ) {
	$sets              = [];

	$sets['numbers']   = [ '1️⃣' => _x( 'One', 'emoji', 'movelogin' ),   '2️⃣' => _x( 'Two', 'emoji', 'movelogin' ),    '3️⃣' => _x( 'Three', 'emoji', 'movelogin' ),  '4️⃣' => _x( 'Four', 'emoji', 'movelogin' ),    '5️⃣' => _x( 'Five', 'emoji', 'movelogin' )   ];
	$sets['maths']     = [ '➕' => _x( 'Plus', 'emoji', 'movelogin' ),  '➖' => _x( 'Minus', 'emoji', 'movelogin' ),  '✖️' => _x( 'Times', 'emoji', 'movelogin' ),  '➗' => _x( 'Divided', 'emoji', 'movelogin' ), '🟰' => _x( 'Equal', 'emoji', 'movelogin' )  ];
	$sets['game']      = [ '♠️' => _x( 'Spade', 'emoji', 'movelogin' ), '♣️' => _x( 'Clover', 'emoji', 'movelogin' ), '♥️' => _x( 'Heart', 'emoji', 'movelogin' ),  '♦️' => _x( 'Diamond', 'emoji', 'movelogin' ), '◼️' => _x( 'Square', 'emoji', 'movelogin' ) ];
	$sets['animals']   = [ '🐶' => _x( 'Dog', 'emoji', 'movelogin' ),   '🐱' => _x( 'Cat', 'emoji', 'movelogin' ),    '🐵' => _x( 'Monkey', 'emoji', 'movelogin' ), '🐷' => _x( 'Pig', 'emoji', 'movelogin' ),     '🦁' => _x( 'Lion', 'emoji', 'movelogin' )   ];
	$sets['nature']    = [ '🌳' => _x( 'Tree', 'emoji', 'movelogin' ),  '🪵' => _x( 'Logs', 'emoji', 'movelogin' ),   '🍀' => _x( 'Clover', 'emoji', 'movelogin' ), '🍁' => _x( 'Leaf', 'emoji', 'movelogin' ),    '🌸' => _x( 'Flower', 'emoji', 'movelogin' ) ];
	$sets['fruits']    = [ '🍎' => _x( 'Apple', 'emoji', 'movelogin' ), '🍌' => _x( 'Banana', 'emoji', 'movelogin' ), '🍋' => _x( 'Lemon', 'emoji', 'movelogin' ),  '🍇' => _x( 'Grapes', 'emoji', 'movelogin' ),  '🥝' => _x( 'Kiwi', 'emoji', 'movelogin' )   ];
	$sets['vegeta']    = [ '🌶️' => _x( 'Chili', 'emoji', 'movelogin' ), '🥕' => _x( 'Carrot', 'emoji', 'movelogin' ), '🌽' => _x( 'Corn', 'emoji', 'movelogin' ),   '🥑' => _x( 'Avocado', 'emoji', 'movelogin' ), '🍅' => _x( 'Tomato', 'emoji', 'movelogin' ) ];
	$sets['chars']     = [ '🤖' => _x( 'Robot', 'emoji', 'movelogin' ), '🤡' => _x( 'Clown', 'emoji', 'movelogin' ),  '👻' => _x( 'Ghost', 'emoji', 'movelogin' ),  '👽' => _x( 'Alien', 'emoji', 'movelogin' ),   '💩' => _x( 'Poo', 'emoji', 'movelogin' )    ];
	$sets['food']      = [ '🍞' => _x( 'Bread', 'emoji', 'movelogin' ), '🧀' => _x( 'Cheese', 'emoji', 'movelogin' ), '🥩' => _x( 'Steak', 'emoji', 'movelogin' ),  '🧈' => _x( 'Butter', 'emoji', 'movelogin' ),  '🥗' => _x( 'Salad', 'emoji', 'movelogin' )  ];
	$sets['ffood']     = [ '🌮' => _x( 'Taco', 'emoji', 'movelogin' ),  '🌭' => _x( 'Hotdog', 'emoji', 'movelogin' ), '🍕' => _x( 'Pizza', 'emoji', 'movelogin' ),  '🍔' => _x( 'Burger', 'emoji', 'movelogin' ),  '🍟' => _x( 'Fries', 'emoji', 'movelogin' )  ];
	$sets['space']     = [ '🌍' => _x( 'Earth', 'emoji', 'movelogin' ), '✨' => _x( 'Stars', 'emoji', 'movelogin' ),  '🌜' => _x( 'Moon', 'emoji', 'movelogin' ),   '☀️' => _x( 'Sun', 'emoji', 'movelogin' ),     '☄️' => _x( 'Comet', 'emoji', 'movelogin' )  ];
	$sets['objects']   = [ '🎩' => _x( 'Hat', 'emoji', 'movelogin' ),   '👋' => _x( 'Hand', 'emoji', 'movelogin' ),   '👁️' => _x( 'Eye', 'emoji', 'movelogin' ),    '👓' => _x( 'Glasses', 'emoji', 'movelogin' ), '🚗' => _x( 'Car', 'emoji', 'movelogin' )    ];
	$sets['objects2']  = [ '🏠' => _x( 'House', 'emoji', 'movelogin' ), '🎹' => _x( 'Piano', 'emoji', 'movelogin' ),  '⚽️' => _x( 'Ball', 'emoji', 'movelogin' ),   '🍪' => _x( 'Cookie', 'emoji', 'movelogin' ),  '⭐️' => _x( 'Star', 'emoji', 'movelogin' )   ];
	
	$sets  = apply_filters( 'movelogin.plugins.emojisets', $sets );

	unset( $sets['all'], $sets['random'] ); // Don't set those names, see docblock.

	if ( 'all' === $set ) {
		return $sets;
	}

	if ( 'random' === $set ) {
		$sets = movelogin_shuffle_assoc( $sets );
		return reset( $sets );
	}

	if ( ! isset( $sets[ $set ] ) ) {
		return reset( $sets );
	}

	return $sets[ $set ];
}

add_filter( 'authenticate', 'movelogin_force_strong_encryption_remove_blind_password', 0, 3 );
/**
 * Remove the suffix and set back the old password when the module "Prevent Other Encryption System to Log In" is not active
 * (Needed outside the module when deactivated)
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @see movelogin_force_strong_encryption_blind_password_set_password()
 * 
 * @param (WP_User|WP_Error) $user
 * @param (string) $username
 * @param (string) $password
 * 
 * @return (WP_User|WP_Error)
 **/
function movelogin_force_strong_encryption_remove_blind_password(
	$user,
	$username,
	#[\SensitiveParameter]
	$password
) {
	if ( movelogin_is_submodule_active( 'users-login', 'force-strong-encryption' ) ) {
		return $user;
	}

	$_user   = movelogin_get_user_by( $username );
	if ( ! movelogin_is_user( $_user ) ) {
		return $user;
	}

	$suffix  = get_user_meta( $_user->ID, 'movelogin-blind-password', true );
	if ( ! $suffix ) {
		return $user;
	}

	$hash    = movelogin_generate_key_for_object( $_user->ID );
	$passwor = $password . $hash;
	$valid   = wp_check_password( $passwor, $_user->user_pass, $_user->ID );
	
	if ( ! $valid ) {
		return $user;
	}

	// Do not warn, this is not a hack, nobody has to know that now.
	remove_all_actions( 'wp_set_password' );
	// Set back the old password
	wp_set_password( $password, $_user->ID );

	delete_user_meta( $_user->ID, 'movelogin-blind-password' );

	return $_user;
}

/**
 * Get the best encryption algo for the installation
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @return (string)
 **/
function movelogin_get_best_encryption_system() {
	switch( true ) {
		case defined( 'PASSWORD_ARGON2ID' ):
			return PASSWORD_ARGON2ID;
		break;
		case defined( 'PASSWORD_ARGON2I' ):
			return PASSWORD_ARGON2I;
		break;
		case defined( 'PASSWORD_BCRYPT' ):
			return PASSWORD_BCRYPT;
		break;
		default:
			return PASSWORD_DEFAULT;
		break;
	}
}

/**
 * Get the name of the encryption algo
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @param (string) $system
 * 
 * @return (string)
 **/
function movelogin_get_encryption_name( $system ) {
	switch( $system ) {
		case '2y':
			return 'Bcrypt';
		break;
		case 'argon2i':
			return 'Argon2I';
		break;
		case 'argon2id':
			return 'Argon2ID';
		break;
		default:
			return ucfirst( $system );
		break;
	}
}

/**
 * Get the DB prefix for an algo
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @param (string) $system
 * 
 * @return (string)
 **/
function movelogin_get_encryption_prefix( $system ) {
	switch( $system ) {
		case '2y': // WP BCRYPT
			return '$wp$2y$';
		break;
		case 'argon2i':
			return '$argon2i$';
		break;
		case 'argon2id':
			return '$argon2id$';
		break;
		default: // DEF BCRYPT
			return '$2y$';
		break;
	}
}

/**
 * Get the best cost for an algo
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @param (string) $algo
 * 
 * @return 
 **/
function movelogin_get_best_cost_by_algo( $algo ) {
	$mem_max             = movelogin_get_max_memory();
	$args                = [];
	$args['algo']        = $algo;
	$args['memory_cost'] = $mem_max * 64; // 6.4% of max memory is enough
	$args['cost']        = 4;
	if ( '2y' === $algo ) {
		$via_php      = version_compare( PHP_VERSION, '8.4', '>=' ) ? 12 : 10;
		$thresholds   = [ 256, 512, 1024, 2048 ];
		$args['cost'] = $via_php;

		foreach ( $thresholds as $threshold ) {
			if ( $mem_max >= $threshold ) {
				++$args['cost'];
			}
		}
	} elseif ( false !== strpos( $algo, 'argon' ) ) {
		$args['cost']    = 3 + min( 4, max( 1, $mem_max === -1 ? 4 : (int) ( log( $mem_max ) / log( 2 ) - 6 ) ) ); // 6=128=2^7
	}
	$args                = apply_filters( 'movelogin.algo.args', $args, $algo );
	return $args;
}

/**
 * Add a rehash meta to any user who needs it
 * 
 * @internal Starts with "_": DO NOT USE ANYWHERE!
 * @see movelogin_password_policy_settings_callback()
 * 
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @return (bool) True if at least 1 user needed a rehash
 **/
function _movelogin_force_strong_encryption_set_rehash_meta() {
	global $wpdb;

	$prefix = movelogin_get_encryption_prefix( movelogin_get_best_encryption_system() );
	$sql    = $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->users . ' WHERE user_pass NOT LIKE %s', $prefix . '%' );
	$ids    = $wpdb->get_col( $sql );
	if ( ! $ids ) {
		return false;
	}
	foreach ( $ids as $_id ) {
		// @see movelogin_prevent_hash_reuse_password_needs_rehash()
		update_user_meta( $_id, 'movelogin-password-needs-rehash', 1 );
	}
	return true;
}

/**
 * Return the max memory on this server/host
 *
 * @since 2.3.21
 * @author Julio Potier
 * 
 * @return (int) $mem
 **/
function movelogin_get_max_memory() {
	$mem = (int) ini_get('memory_limit');
	if ( -1 === $mem ) {
		$mem = 2; // Gb ; No limit? Let's say 2 Gb then
	}
	if ( $mem < 16 ) { // Mb ; Less than 16Mb? Should be Gb then, multiply.
		$mem *= 1024;
	}
	return $mem;
}