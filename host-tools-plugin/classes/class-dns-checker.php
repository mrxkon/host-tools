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
		$the_domain = '';

		if ( isset( $_POST['domain'] ) ) {
			$the_domain = $_POST['domain'];
		} elseif ( isset( $_GET['domain'] ) ) {
			$the_domain = $_GET['domain'];
		}

		$result = Helpers::domain_form();

		if ( ! empty( $the_domain ) && Helpers::is_domain_valid( $the_domain ) ) {
			$domain = str_replace( array( 'https', 'http', ':', '/' ), '', $the_domain );

			$result .= '<div class="host-tools-dns-results">';

			foreach ( self::$dns_list as $dns ) {
				$result .= '<table class="host-tools-table">';
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
						$result .= '<td>' . $record['value'] . '</td>';
					}
					$result .= '</tr>';
				}

				$result .= '</table>';
			}

			$result .= '</div>';
		} else {
			$result .= '<h3>Please enter a domain.</h3>';
		}

		return $result;
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
