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

class CSR_Decoder {
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
		$html .= '<form id="host-tools-csr-decoder-form">';
		$html .= '<label for="csrInput" style="display:block;position:relative;padding-top:9px;">Certificate Signing Request:</label>';
		$html .= '<textarea style="font-family:monospace;font-size:14px;" class="uk-margin uk-textarea" rows="10" type="text" placeholder="" id="csrInput" name="csr" /></textarea>';
		$html .= wp_nonce_field( 'host_tools_csr_decode_test_nonce', 'htnonce' );
		$html .= '<input class="uk-button uk-button-default" type="submit" value="Submit" />';
		$html .= '</form>';
		$html .= '</div>';

		$html .= '<div class="uk-width-1-2@s">';
		$html .= '<div id="host-test-results">';
		$html .= '<p class="uk-text-warning">Please enter a CSR.</p>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Run test.
	 */
	public static function run_test() {
		if ( ! isset( $_POST['htnonce'] ) || ! wp_verify_nonce( $_POST['htnonce'], 'host_tools_csr_decode_test_nonce' ) ) {
			wp_send_json_error( 'Could not verify nonce.' );
		}

		if ( ! isset( $_POST['csr'] ) || empty( $_POST['csr'] ) ) {
			wp_send_json_error( 'Please enter a CSR.' );
		}

		$csr = str_replace( array( '<', '>' ), '', $_POST['csr'] );

		if ( ! Helpers::is_csr_valid( $csr ) ) {
			wp_send_json_error( 'Please enter a valid CSR.' );
		}

		$data = openssl_csr_get_subject( $csr );

		if ( empty( $data ) ) {
			wp_send_json_error( 'Could not parse the CSR.' );
		}

		wp_send_json_success( $data );
	}

}
