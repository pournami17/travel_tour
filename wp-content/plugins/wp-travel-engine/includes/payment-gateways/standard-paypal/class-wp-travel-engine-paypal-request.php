<?php
/**
 * Paypal payment gateway.
 *
 * @package WP_Travel_Engine/includes/payment-gateways
 * @author WP Travel Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * class paypal payment gateway
 * 
 * @since 2.2.8
 */
class WTE_Payment_Gateway_Paypal {
	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'wp_travel_engine_after_booking_process_completed', array( $this, 'process' ) );
		// TODO Implement partial paymnet support.
	}

	/**
	 * Paypal Process.
	 *
	 * @param int $booking_id Booking ID.
	 * @return void
	 */
	public function process( $booking_id, $partial_payment = false ) {

		if ( ! $booking_id ) {
			return;
		}
		
		// Is partial payment check.
		if ( ! $partial_payment ) {
			do_action( 'wte_payment_process', $booking_id );
		}

		// Check if paypal is selected.
		if ( ! isset( $_POST['wpte_checkout_paymnet_method'] ) || 'paypal_payment' !== $_POST['wpte_checkout_paymnet_method'] ) {
			return;
		}

		$args         = $this->get_args( $booking_id, $partial_payment );
		$redirect_uri = esc_url( home_url( '/' ) );

		if ( $args ) {
			$paypal_request_args  = http_build_query( $args, '', '&' );
			$redirect_uri         = esc_url( wte_get_paypal_redirect_url() ) . '?' . $paypal_request_args;
		}

		wp_redirect( $redirect_uri );

		exit;
	}
	/**
	 * Get Paypal Arguments.
	 *
	 * @param number $booking_id Booking ID.
	 * @return Array
	 */
	private function get_args( $booking_id, $partial_payment = false ) {

		// Get settings.
		$wte_settings = get_option( 'wp_travel_engine_settings', true );

		// Check if paypal email is set.
		if ( ! isset( $wte_settings['paypal_id'] ) || '' === $wte_settings['paypal_id'] ) {
			return false;
		}

		$paypal_id     = is_email( $wte_settings['paypal_id'] ) ? sanitize_email( $wte_settings['paypal_id'] ) : $wte_settings['paypal_id'];
		$currency_code = wp_travel_engine_get_currency_code();
		$payment_mode  = isset( $_POST['wp_travel_engine_payment_mode'] ) ? $_POST['wp_travel_engine_payment_mode'] : '';

		global $wte_cart;
		
		$items       = $wte_cart->getItems();
		$return_url  = wp_travel_engine_get_booking_confirm_url();

		if ( $items ) {

			$cart_amounts = $wte_cart->get_total();
			$discount     = isset( $cart_amounts['discount'] ) ? wp_travel_engine_get_formated_price( $cart_amounts['discount'] ) : 0;
			$tax          = 0;

			if ( 'partial' === $payment_mode ) {
				$discount = isset( $cart_amounts['discount_partial'] ) ? wp_travel_engine_get_formated_price( $cart_amounts['discount_partial'] ) : 0;
			}

			$args['amount'] = wp_travel_engine_is_cart_partially_payable() ? $cart_amounts['total_partial'] : $cart_amounts['total'];

			$args['cmd']                  = '_cart';
			$args['upload']               = '1';
			$args['currency_code']        = sanitize_text_field( $currency_code );
			$args['business']             = $paypal_id;
			$args['bn']                   = '';
			$args['rm']                   = '2';
			$args['discount_amount_cart'] = $discount;
			$args['tax_cart']             = $tax;
			$args['charset']              = get_bloginfo( 'charset' );
			$args['cbt']                  = get_bloginfo( 'name' );
			$args['return']               = add_query_arg(
				array(
					'booking_id'  => $booking_id,
					'booked'      => true,
					'status'      => 'success',
					'wte_gateway' => 'paypal',
				),
				$return_url
			);
			$args['cancel']               = add_query_arg(
				array(
					'booking_id' => $booking_id,
					'booked'     => true,
					'status'     => 'cancel',
				),
				$return_url
			);
			$args['handling']             = 0;
			$args['handling_cart']        = 0;
			$args['no_shipping']          = 0;
			$args['notify_url']           = esc_url( add_query_arg( 'wp_travel_engine_ipn_listener', 'IPN', home_url( 'index.php' ) ) );

			$agrs_index = 1;

			// Add cart items to paypal args.
			foreach ( $items as $cart_id => $item ) {
				
				$trip_id    = $item['trip_id'];
				$pax        = 1;
				// $trip_price = $item['trip_price'];
				$trip_price = $cart_amounts['total'];

				$item_name = html_entity_decode( get_the_title( $trip_id ) );

				$payment_amount = wp_travel_engine_get_formated_price( $trip_price );

				if ( 'partial' === $payment_mode && wp_travel_engine_is_trip_partially_payable( $trip_id ) ) :

					// $payment_amount = wp_travel_engine_get_formated_price( $item['trip_price_partial'] );
					$payment_amount = wp_travel_engine_get_formated_price( $cart_amounts['total_partial'] );

				endif;

				$args[ 'item_name_' . $agrs_index ]   = $item_name;
				$args[ 'quantity_' . $agrs_index ]    = $pax;
				$args[ 'amount_' . $agrs_index ]      = $payment_amount;
				$args[ 'item_number_' . $agrs_index ] = $trip_id;
				$args[ 'on2_' . $agrs_index ]         = __( 'Total Price', 'wp-travel-engine' );
				$args[ 'os2_' . $agrs_index ]         = $trip_price;

				// $args = apply_filters( 'wp_travel_engine_tour_extras_paypal_request_args', $args, $item, $cart_id, $agrs_index );

				// TODO paypal args add for trip extras.

				$agrs_index++;
			}
		} else {
			return;
		}

		$args['option_index_0'] = $agrs_index;
		$args['custom'] = $booking_id;

		return apply_filters( 'wp_travel_engine_paypal_request_args', $args );
	}
}

new WTE_Payment_Gateway_Paypal();
