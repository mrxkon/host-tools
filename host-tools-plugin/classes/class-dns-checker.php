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

		$html .= '<form class="uk-grid-small" uk-grid id="host-tools-dns-checker-form">';
		$html .= '<div class="uk-width-auto@s">';
		$html .= '<label for="domainInput" style="display:block;position:relative;padding-top:9px;">Domain:</label>';
		$html .= '</div>';
		$html .= '<div class="uk-width-expand@s">';
		$html .= '<input class="uk-input" type="text" placeholder="example.com" id="domainInput" name="domain" value="' . $the_domain . '"/>';
		$html .= '</div>';
		$html .= '<div class="uk-width-auto@s">';
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
					$( '#host-tools-dns-checker-form input[type=submit]' ).click();
				});
			} ( jQuery ) );
			</script>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}

	/**
	 * Return JSON.
	 */
	public static function return_json( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			error_log( $data['domain'] );
			$domain = Helpers::clean_domain( $data['domain'] );
		} else {
			$domain = Helpers::clean_domain( $data );
		}

		$result = array(
			'domain' => $domain,
		);

		foreach ( Helpers::return_dns_list() as $dns ) {
			$ns = '@' . $dns;

			$records = array(
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

			foreach ( $records as $record ) {
				$result['dns'][ $dns ][ $record['name'] ] = array_filter( explode( PHP_EOL, $record['value'] ) );
			}
		}

		wp_send_json( $result );
	}
}
