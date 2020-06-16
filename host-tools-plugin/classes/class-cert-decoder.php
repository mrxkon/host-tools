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

class Cert_Decoder {
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
		$html .= '<div class="uk-grid-small uk-grid-divider" uk-grid>';

		$html .= '<div class="uk-width-1-2@s">';
		$html .= '<form id="host-tools-cert-decoder-form">';
		$html .= '<label for="certInput" style="display:block;position:relative;padding-top:9px;">Certificate:</label>';
		$html .= '<textarea style="font-family:monospace;font-size:14px;" class="uk-margin uk-textarea" rows="20" type="text" placeholder="" id="certInput" name="cert" /></textarea>';
		$html .= wp_nonce_field( 'host_tools_cert_decode_test_nonce', 'htnonce' );
		$html .= '<input class="uk-button uk-button-default" type="submit" value="Submit" />';
		$html .= '</form>';
		$html .= '</div>';

		$html .= '<div class="uk-width-1-2@s">';
		$html .= '<div id="host-test-results">';
		$html .= '<p class="uk-text-warning">Please enter a Certificate.</p>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Run test.
	 */
	public static function run_test() {
		if (
			isset( $_POST['htnonce'] ) &&
			wp_verify_nonce( $_POST['htnonce'], 'host_tools_cert_decode_test_nonce' ) &&
			isset( $_POST['cert'] ) &&
			Helpers::is_cert_valid( $_POST['cert'] )
		) {
			$cert = str_replace( array( '<', '>' ), '', $_POST['cert'] );
			$data = openssl_x509_parse( $_POST['cert'] );

			if ( empty( $data ) ) {
				wp_send_json_error( 'Please enter a valid Certificate.' );
			}

			wp_send_json_success( $data );
		} else {
			wp_send_json_error( 'Please enter a valid Certificate.' );
		}
	}

}
