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

		$html = Helpers::domain_form();

		return $html;
	}

	/**
	 * Run test.
	 */
	public static function run_test() {
		if (
			isset( $_POST['htnonce'] ) &&
			wp_verify_nonce( $_POST['htnonce'], 'host_tools_test_nonce' ) &&
			isset( $_POST['domain'] ) &&
			! empty( $_POST['domain'] ) &&
			Helpers::is_domain_valid( $_POST['domain'] )
		) {
			$domain = str_replace( array( 'https', 'http', ':', '/' ), '', $_POST['domain'] );

			$ping = shell_exec( 'ping -c 5 ' . $domain );

			$ttfb = shell_exec( 'curl -i -s -w "\nTTFB: %{time_starttransfer}\n" https://' . $domain . ' | egrep "hummingbird-cache|x-cache|TTFB"' );

			$result .= '<p>';
			$result .= str_replace( PHP_EOL, '<br/>', $ping );
			$result .= '</p>';

			$result .= '<p>';
			$result .= str_replace( PHP_EOL, '<br/>', $ttfb );
			$result .= '</p>';

			wp_send_json_success( $result );
		} else {
			wp_send_json_error( 'Please enter a valid domain.' );
		}
	}

	/**
	 * Scripts.
	 */
	public static function scripts() {
		ob_start();
		?>
		<script>
		( function( $ ) {

			$( '#host-tools-domain-form' ).on ( 'submit', function( e ) {
				e.preventDefault();

				$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

				var data = {
						'domain': $( '#host-tools-domain-form #domainInput' ).val(),
						'action': 'host-tools-ping-ttfb',
						'htnonce': $( '#host-tools-domain-form #htnonce' ).val(),
					};

				$.post( host_tools_ajax_url, data, function( r ) {
					if ( r.success ) {
						$( '#host-test-results' ).html( r.data );
					} else {
						$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
					}
				});
			});

		} ( jQuery ) );
		</script>
		<?php
		$scripts = ob_get_clean();

		echo $scripts;
	}
}
