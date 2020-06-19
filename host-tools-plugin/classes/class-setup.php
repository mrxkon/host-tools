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

class Setup {
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
		// Shortcodes.
		add_shortcode( 'host-tools-dns-checkup', array( '\\Host_Tools\\DNS_Checker', 'shortcode' ) );
		add_shortcode( 'host-tools-php-info', array( '\\Host_Tools\\PHP_Info', 'shortcode' ) );
		add_shortcode( 'host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'shortcode' ) );
		add_shortcode( 'host-tools-cert-decoder', array( '\\Host_Tools\\Cert_Decoder', 'shortcode' ) );
		add_shortcode( 'host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'shortcode' ) );

		// REST
		add_action( 'rest_api_init', array( '\\Host_Tools\\Setup', 'rest_routes' ) );

		// General Scripts & Styles.
		add_action( 'wp_footer', array( '\\Host_Tools\\Setup', 'scripts_styles' ), 999 );

		// Ping & TTFB.
		add_action( 'wp_ajax_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );

		// Cert Decoder.
		add_action( 'wp_ajax_host-tools-cert-decoder', array( '\\Host_Tools\\Cert_Decoder', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-cert-decoder', array( '\\Host_Tools\\Cert_Decoder', 'run_test' ) );

		// CSR Decoder.
		add_action( 'wp_ajax_host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'run_test' ) );
	}

	/**
	 * Rest routes.
	 */
	public static function rest_routes() {
		register_rest_route(
			'dns',
			'/(?P<domain>.+)',
			array(
				'methods'  => 'GET',
				'callback' => array( '\\Host_Tools\\DNS_Checker', 'return_json' ),
			)
		);
	}

	/**
	 * Scripts & Styles.
	 */
	public static function scripts_styles() {
		global $post;

		$scripts_styles = '<script>var host_tools_ajax_url = \'' . admin_url( 'admin-ajax.php' ) . '\';</script>';

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-dns-checkup' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#domainInput' ).focus();
				});

				$( '#host-tools-dns-checker-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-dns-checker-form #domainInput' ).val(),
						};

					$.get( 'https://tools.wpmudev.host/wp-json/dns/' + data.domain, function( r ) {
						body_string  = '<h2>Results for: ' + r.domain + '</h2>';
						body_string += '<div class="uk-grid-small uk-child-width-1-1@s uk-child-width-1-3@m" uk-grid>';
						$.each( r.dns , function( ns, records ) {
								body_string += '<div>';
								body_string += '<table class="uk-table uk-table-striped uk-table-hover">';
								body_string += '<tr>';
								body_string += '<td colspan="2">';
								body_string += '<h3>DNS ' + ns + '</h3>';
								body_string += '</td>';
								body_string += '</tr>';

								$.each( records, function( record, entry ) {
									body_string += '<tr>';
									body_string += '<td><p>' + record + '</p></td>';
									body_string += '<td>';
									$.each( entry, function( key, value ) {
										body_string += '<p>' + value + '<p/>';
									});
									body_string += '</td>'
									body_string += '</tr>';
								});

								body_string += '</table>';
								body_string += '</div>';
						});

						body_string += '</div>';
						$( '#host-test-results' ).html( body_string );
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$dns_check = ob_get_clean();

			$scripts_styles .= $dns_check;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-ping-ttfb' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#domainInput' ).focus();
				});

				$( '#host-tools-ping-ttfb-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-ping-ttfb-form #domainInput' ).val(),
							'action': 'host-tools-ping-ttfb',
							'htnonce': $( '#host-tools-ping-ttfb-form #htnonce' ).val(),
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
			$ping_ttfb = ob_get_clean();

			$scripts_styles .= $ping_ttfb;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-cert-decoder' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#certInput' ).focus();
				});

				$( '#host-tools-cert-decoder-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'cert': $( '#host-tools-cert-decoder-form #certInput' ).val(),
							'key': $( '#host-tools-cert-decoder-form #keyInput' ).val(),
							'action': 'host-tools-cert-decoder',
							'htnonce': $( '#host-tools-cert-decoder-form #htnonce' ).val(),
						};

					$.post( host_tools_ajax_url, data, function( r ) {
						if ( r.success ) {
							var body_string,
								date_from = new Date( r.data.validFrom_time_t * 1000 ).toDateString(),
								date_to   = new Date( r.data.validTo_time_t * 1000 ).toDateString();

							body_string  = '<p><strong>Domain:</strong> ' + r.data.subject.CN;
							body_string += '<p><strong>Issuer:</strong> ' + r.data.issuer.O + ' (' + r.data.issuer.CN + ')';
							body_string += '<p><strong>Signature Type:</strong> ' + r.data.signatureTypeSN;
							body_string += '<p><strong>Valid From:</strong> ' + date_from;
							body_string += '<p><strong>Valid Until:</strong> ' + date_to;
							body_string += '<p><strong>Serial Number:</strong> ' + r.data.serialNumberHex;

							if ( r.data.privKeyMatch ) {
								body_string += '<p><strong>Private Key Match:</strong> ' + r.data.privKeyMatch;
							}

							$( '#host-test-results' ).html( body_string );
						} else {
							$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
						}
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$cert_decoder = ob_get_clean();

			$scripts_styles .= $cert_decoder;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-csr-decoder' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#csrInput' ).focus();
				});

				$( '#host-tools-csr-decoder-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'csr': $( '#host-tools-csr-decoder-form #csrInput' ).val(),
							'action': 'host-tools-csr-decoder',
							'htnonce': $( '#host-tools-csr-decoder-form #htnonce' ).val(),
						};

					$.post( host_tools_ajax_url, data, function( r ) {
						if ( r.success ) {
							var body_string;

							body_string  = '<p><strong>Domain:</strong> ' + r.data.CN + '<br/>';
							body_string += '<p><strong>Organization:</strong> ' + r.data.O + '<br/>';
							body_string += '<p><strong>Department:</strong> ' + r.data.OU + '<br/>';
							body_string += '<p><strong>City:</strong> ' + r.data.L + '<br/>';
							body_string += '<p><strong>State:</strong> ' + r.data.ST + '<br/>';
							body_string += '<p><strong>Email:</strong> ' + r.data.emailAddress + '<br/>';
							body_string += '<p><strong>Country:</strong> ' + r.data.C + '<br/>';

							$( '#host-test-results' ).html( body_string );
						} else {
							$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
						}
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$csr_decoder = ob_get_clean();

			$scripts_styles .= $csr_decoder;
		}

		echo $scripts_styles;
	}
}


