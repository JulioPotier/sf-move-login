<?php
/**
 * Uninstall Script
 * // DO NOT USE ANY MOVELOGIN FUNCTIONS/CONSTANTS, WE MAY BE DEACTIVATED.
 * @version 2.6
*/

defined( 'WP_UNINSTALL_PLUGIN' ) or die( 'Something went wrong.' );

delete_option( 'movelogin_settings' );
delete_option( 'movelogin_users-login_settings' );
delete_option( 'movelogin_active_submodule_move-login' );