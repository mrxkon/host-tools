<?php //phpcs:ignore -- \r\n notice.

/**
 * This file comes with "host-tools".
 *
 * Author:      Konstantinos Xenos
 * Author URI:  https://xkon.gr
 * Repo URI:    https://github.com/mrxkon/host-tools/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Host_Tools;

class Ping_TTFB {
	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Return class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Shortcode.
	 */
	public static function shortcode() {
		$the_domain = '';

		if ( isset( $_POST['domain'] ) ) {
			$the_domain = $_POST['domain'];
		} elseif ( isset( $_GET['domain'] ) ) {
			$the_domain = $_GET['domain'];
		}

		$html .= '<form class="uk-grid-small" uk-grid id="host-tools-ping-ttfb-form">';
		$html .= '<div class="uk-width-auto@s">';
		$html .= '<label for="domainInput" style="display:block;position:relative;padding-top:9px;">Domain:</label>';
		$html .= '</div>';
		$html .= '<div class="uk-width-expand@s">';
		$html .= '<input class="uk-input" type="text" placeholder="example.com" id="domainInput" name="domain" value="' . $the_domain . '"/>';
		$html .= '</div>';
		$html .= '<div class="uk-width-auto@s">';
		$html .= wp_nonce_field( 'host_tools_ping_ttfb_test_nonce', 'htnonce' );
		$html .= '<input class="uk-button uk-button-default" type="submit" value="Submit" />';
		$html .= '</div>';
		$html .= '</form>';

		$html .= '<div class="uk-section">';
		$html .= '<div id="host-test-results">';
		$html .= '<p class="uk-text-warning">Please enter a domain.</p>';
		$html .= '</div>';
		$html .= '</div>';

		if ( isset( $_GET['domain'] ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#host-tools-domain-form input[type=submit]' ).click();
				});
			} ( jQuery ) );
			</script>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}

	/**
	 * Run test.
	 */
	public static function run_test() {
		if ( ! isset( $_POST['htnonce'] ) || ! wp_verify_nonce( $_POST['htnonce'], 'host_tools_ping_ttfb_test_nonce' ) ) {
			wp_send_json_error( 'Could not verify nonce.' );
		}

		if ( ! isset( $_POST['domain'] ) || empty( $_POST['domain'] ) ) {
			wp_send_json_error( 'Please enter a domain.' );
		}

		$domain = str_replace( array( 'https', 'http', ':', '/', '<', '>', '(', ')' ), '', $_POST['domain'] );

		if ( ! Helpers::is_domain_valid( $domain ) ) {
			wp_send_json_error( 'Please enter a valid domain.' );
		}

		$ping = shell_exec( 'ping -c 5 ' . $domain );

		$ttfb = shell_exec( 'curl -i -s -w "\nTTFB: %{time_starttransfer}\n" https://' . $domain . ' | egrep "hummingbird-cache|x-cache|TTFB"' );

		$result .= '<h2>Results for: ' . $domain . '</h2>';

		$result .= '<p>';
		$result .= str_replace( PHP_EOL, '<br/>', $ping );
		$result .= '</p>';

		$result .= '<p>';
		$result .= str_replace( PHP_EOL, '<br/>', $ttfb );
		$result .= '</p>';

		wp_send_json_success( $result );
	}
}
