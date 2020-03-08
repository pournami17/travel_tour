<?php
/**
 * Retrieve the correct PayPal Redirect based on http/s
 * and "live" or "test" mode, i.e., sandbox.
 *
 * @return PayPal URI
 */
function wte_get_paypal_redirect_url( $ssl_check = false ) {

	$protocol = is_ssl() || ! $ssl_check ? 'https://' : 'http://';

	if ( defined( 'WP_TRAVEL_ENGINE_PAYMENT_DEBUG' ) && WP_TRAVEL_ENGINE_PAYMENT_DEBUG ) {
		
		$paypal_uri = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';

	} else {
		
		$paypal_uri = $protocol . 'www.paypal.com/cgi-bin/webscr';
	
	}

	return $paypal_uri;
}


/**
 * Listen for a $_GET request from our PayPal IPN.
 * This would also do the "set-up" for an "alternate purchase verification"
 */
function wp_travel_engine_paypal_ipn_listner() {

	if ( isset( $_GET['wp_travel_engine_ipn_listener'] )
		&& $_GET['wp_travel_engine_ipn_listener'] == 'IPN'
		|| isset( $_GET['test'] )
		&& $_GET['test'] == true ) {
		do_action( 'wp_travel_engine_verify_paypal_ipn' );
	}

}
add_action( 'init', 'wp_travel_engine_paypal_ipn_listner' );


/**
 * When a payment is made PayPal will send us a response and this function is
 * called. From here we will confirm arguments that we sent to PayPal which
 * the ones PayPal is sending back to us.
 * This is the Pink Lilly of the whole operation.
 */
function wp_travel_engine_process_paypal_ipn() {
	/**
	 * Instantiate the IPNListener class
	 */
	include dirname( __FILE__ ) . '/php-paypal-ipn/IPNListener.php';
	$listener = new IPNListener();

	/**
	 * Set to PayPal sandbox or live mode
	 */
	$settings              = get_option( 'wp_travel_engine_settings', true );
	$listener->use_sandbox = ( defined( 'WP_TRAVEL_ENGINE_PAYMENT_DEBUG' ) ) ? WP_TRAVEL_ENGINE_PAYMENT_DEBUG : false;

	/**
	 * Check if IPN was successfully processed
	 */
	if ( $verified = $listener->processIpn() ) {

		/**
		 * Log successful purchases
		 */
		$transactionData = $listener->getPostData(); // POST data array
		file_put_contents( 'ipn_success.log', print_r( $transactionData, true ) . PHP_EOL, LOCK_EX | FILE_APPEND );

		$message = null;
		/**
		 * Verify seller PayPal email with PayPal email in settings
		 *
		 * Check if the seller email that was processed by the IPN matches what is saved as
		 * the seller email in our DB
		 */
		// if ( $_POST['receiver_email'] != $settings['paypal_id'] ) {
		// 	$message .= "\nEmail seller email does not match email in settings\n";
		// }

		/**
		 * Verify currency
		 *
		 * Check if the currency that was processed by the IPN matches what is saved as
		 * the currency setting
		 */
		if ( $_POST['mc_currency'] != wp_travel_engine_get_currency_code() ) {
			$message .= "\nCurrency does not match those assigned in settings\n";
		}

		/**
		 * Check if this payment was already processed
		 *
		 * PayPal transaction id (txn_id) is stored in the database, we check
		 * that against the txn_id returned.
		 */
		$booking_id    = isset( $_POST['custom'] ) ? $_POST['custom'] : 0;
		$booking_metas = get_post_meta( $booking_id, 'wp_travel_engine_booking_setting', true );

		$txn_id = isset( $booking_metas['place_order']['payment']['txn_id'] ) ? $booking_metas['place_order']['payment']['txn_id'] : '';

		if ( empty( $txn_id ) ) {

			$booking_metas['place_order']['payment']['txn_id'] = $_POST['txn_id'];

			update_post_meta( $booking_id, 'wp_travel_engine_booking_setting', $booking_metas );
		
		} else {
			
			$message .= "\nThis payment was already processed\n";
		
		}

		/**
		 * Verify the payment is set to "Completed".
		 *
		 * Create a new payment, send customer an email and empty the cart
		 */

		if ( ! empty( $_POST['payment_status'] ) && $_POST['payment_status'] == 'Completed' ) {
			
			// payment completed.
			// Update booking status and Payment args.
			$booking_metas['place_order']['payment']['payment_gateway'] = 'paypal';
			$booking_metas['place_order']['payment']['payment_status']  = $_POST['payment_status'];
			$booking_metas['place_order']['payment']['payer_status']    = $_POST['payer_status'];
			$booking_metas['place_order']['payment']['payment_type']    = $_POST['payment_type'];
			$booking_metas['place_order']['cost']                       = $_POST['mc_gross'];

			update_post_meta( $booking_id, 'wp_travel_engine_booking_setting', $booking_metas );

		} else {
			$message .= "\nPayment status not set to Completed\n";
		}

		/**
		 * Check if this is the test mode
		 *
		 * If this is the test mode we email the IPN text report.
		 * note about and box http://stackoverflow.com/questions/4298117/paypal-ipn-always-return-payment-status-pending-on-sandbox
		 */
		if ( defined( 'WP_TRAVEL_ENGINE_PAYMENT_DEBUG' ) && WP_TRAVEL_ENGINE_PAYMENT_DEBUG ) {

			$message .= "\nTest Mode\n";
			$email    = array(
				'to'      => get_option( 'admin_email' ),
				'subject' => __( 'Verified PayPal IPN', 'wp-travel-engine' ),
				'message' => $message . "\n" . $listener->getTextReport(),
			);

			wp_mail( $email['to'], $email['subject'], $email['message'] );

		}
	} else {

		/**
		 * Log errors
		 */
		$errors = $listener->getErrors();
		file_put_contents( 'ipn_errors.log', print_r( $errors, true ) . PHP_EOL, LOCK_EX | FILE_APPEND );

		/**
		 * An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
		 * a good idea to have a developer or sys admin manually investigate any
		 * invalid IPN.
		 */
		$from_email = isset( $settings['email']['from'] ) ? $settings['email']['from'] : '';
		if ( ! empty( $from_email ) ) {
			wp_mail( $settings['email']['from'], __( 'Invalid IPN', 'wp-travel-engine' ), $listener->getTextReport() );
		}
	}
}
add_action( 'wp_travel_engine_verify_paypal_ipn', 'wp_travel_engine_process_paypal_ipn' );
