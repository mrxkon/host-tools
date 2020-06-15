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

class DNS_Checker {
	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * DNS
	 */
	private static $dns_list = array(
		'ns1.wpdns.host',
		'1.1.1.1',
		'8.8.8.8',
	);

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
		$result = Helpers::domain_form();

		return $result;
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

			$result .= '<div class="uk-grid-small uk-child-width-1-1@s uk-child-width-1-3@m" uk-grid>';

			foreach ( self::$dns_list as $dns ) {
				$result .= '<div>';
				$result .= '<table class="uk-table uk-table-striped uk-table-hover uk-table-middle">';
				$result .= '<tr>';
				$result .= '<td colspan="2">';
				$result .= '<h3>DNS ' . $dns . '</h3>';
				$result .= '</td>';
				$result .= '</tr>';

				$records = self::get_records( $domain, $dns );
				foreach ( $records as $record ) {
					$result .= '<tr>';
					$result .= '<td>' . $record['name'] . '</td>';
					if ( in_array( $record['name'], array( 'A', 'AAAA', 'CNAME', 'NS', 'MX' ), true ) ) {
						$result .= '<td>' . str_replace( PHP_EOL, '<br/>', $record['value'] ) . '</td>';
					} else {
						$result .= '<td style="word-break:break-all;">' . $record['value'] . '</td>';
					}
					$result .= '</tr>';
				}

				$result .= '</table>';
				$result .= '</div>';
			}

			$result .= '</div>';

			wp_send_json_success( $result );
		} else {
			wp_send_json_error( 'Please enter a valid domain.' );
		}
	}

	/**
	 * Grab the results.
	 */
	private static function get_records( $domain, $dns ) {
		$ns = '@' . $dns;

		return array(
			'a'     => array(
				'name'  => 'A',
				'value' => shell_exec( 'dig +short A ' . $domain . ' ' . $ns ),
			),
			'aaaa'  => array(
				'name'  => 'AAAA',
				'value' => shell_exec( 'dig +short AAAA ' . $domain . ' ' . $ns ),
			),
			'cname' => array(
				'name'  => 'CNAME',
				'value' => shell_exec( 'dig +short CNAME ' . $domain . ' ' . $ns ),
			),
			'ns'    => array(
				'name'  => 'NS',
				'value' => shell_exec( 'dig +short NS ' . $domain . ' ' . $ns ),
			),
			'acme'  => array(
				'name'  => 'ACME',
				'value' => shell_exec( 'dig +short CNAME _acme-challenge.' . $domain . ' ' . $ns ),
			),
			'mx'    => array(
				'name'  => 'MX',
				'value' => shell_exec( 'dig +short MX ' . $domain . ' ' . $ns ),
			),
			'txt'   => array(
				'name'  => 'TXT',
				'value' => shell_exec( 'dig +short TXT ' . $domain . ' ' . $ns ),
			),
			'dmarc' => array(
				'name'  => 'DMARC',
				'value' => shell_exec( 'dig +short TXT _dmarc.' . $domain . ' ' . $ns ),
			),
			'dkim'  => array(
				'name'  => 'DKIM',
				'value' => shell_exec( 'dig +short TXT dkim._domainkey.' . $domain . ' ' . $ns ),
			),
		);
	}
}
