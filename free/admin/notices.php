<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );



add_action( 'in_plugin_update_message-' . plugin_basename( MOVELOGIN_FILE ), 'movelogin_updates_message', 10, 2 );
/**
 * Display a message below our plugins to display the next update information if needed
 *
 * @since 1.1.1
 * @author Julio Potier
 *
 * @param (array) $plugin_data Contains the old plugin data from EDD or repository.
 * @param (array) $new_plugin_data Contains the new plugin data from EDD or repository.
 */
function movelogin_updates_message( $plugin_data, $new_plugin_data ) {
	// Get next version.
	if ( isset( $new_plugin_data->new_version ) ) {
		$remote_version = $new_plugin_data->new_version;
	}

	if ( ! isset( $remote_version ) ) {
		return;
	}

	$body = get_transient( 'movelogin_updates_message' );

	if ( ! isset( $body[ $remote_version ] ) ) {
		$url = 'https://plugins.svn.wordpress.org/sf-move-login/trunk/readme.txt';
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		set_transient( 'movelogin_updates_message' , array( $remote_version => $body ) );
	} else {
		$body = $body[ $remote_version ];
	}

	// Find the Notes for this version.
	$regexp = '#== Upgrade Notice ==.*= ' . preg_quote( $remote_version ) . ' =(.*)=#Us';

	if ( preg_match( $regexp, $body, $matches ) ) {

		$notes = (array) preg_split( '#[\r\n]+#', trim( $matches[1] ) );
		$date  = str_replace( '* ', '', wp_kses_post( array_shift( $notes ) ) );

		echo '<div>';
		/** Translators: %1$s is the version number, %2$s is a date. */
		echo '<strong>' . sprintf( __( 'Please read these special notes for this update, version %1$s - %2$s', 'movelogin' ), $remote_version, $date ) . '</strong>';
		echo '<ul style="list-style:square;margin-left:20px;line-height:1em">';
		foreach ( $notes as $note ) {
			echo '<li>' . str_replace( '* ', '', wp_kses_post( $note ) ) . '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
}
