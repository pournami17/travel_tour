<?php
/**
 * Price and currency helpers
 *
 * @package WP_Tarvel_Engine
 */

/**
 * Used For Calculation purpose. for display purpose use wp_travel_engine_get_formated_price_with_currency.
 *
 * @param int $price Amount to be formatted.
 * @param bool $format If true should be formatted according to the WP Travel Number fomatting Setting @since WP Travel v3.0.4
 * @param int $number_of_decimals Number after decimal .00.
 */

/**
 * Currency code in db
 *
 * @since 3.0.2
 *
 * @return string Currency code stored in db.
 */

/**
 * Currency code in db.
 *
 * @return string Return currency code in db.
 */
 function wte_currency_code_in_db() {
	// If the currency stored in the database is not in the
	// currency converter list, append it as well.
	$wte_settings = get_option( 'wp_travel_engine_settings' );
	if ( ! isset( $wte_settings['currency_code'] ) ) {
		$wte_settings['currency_code'] = 'USD';
	}
	$code_in_db = $wte_settings['currency_code'];

	return $code_in_db;
}

function wp_travel_engine_get_formated_price( $price, $format = true, $number_of_decimals = 2 ) {
	if ( is_string( $price ) ) {
		$price = floatval( $price );
	}

	if ( ! $price ) {
		return 0;
	}

	if ( ! $format ) {
		return $price;
	}

	return floor( $price ) == $price ? number_format( $price, 0, '.', '' ) : number_format( $price, $number_of_decimals, '.', '' );
}

/**
 * Undocumented function
 *
 * @param [type] $cost
 * @return void
 */
function wp_travel_engine_get_formated_price_separator( $cost, $trip_id = false, $use_default_currency_code = false ) {
	if ( is_string( $cost ) ) {
		$cost = floatval( $cost );
	}

	$cost = apply_filters( 'wp_travel_engine_before_get_formatted_price_separator', $cost, $trip_id, $use_default_currency_code );

	$wte = new Wp_Travel_Engine_Functions();

	$settings            = get_option( 'wp_travel_engine_settings' );
	$thousands_separator = isset( $settings['thousands_separator'] ) && $settings['thousands_separator']!='' ? esc_attr( $settings['thousands_separator'] ) : ',';

	$formatted_cost = ( floor( $cost ) == $cost ) ? number_format( $cost, 0, '.', apply_filters('wp_travel_engine_default_separator', $thousands_separator ) ) : number_format( $cost, 2, '.', apply_filters('wp_travel_engine_default_separator', $thousands_separator ) );

	// TODO : Move to filter.
	if( class_exists( 'Wte_Trip_Currency_Converter_Init' ) && $trip_id ) {

		$trip           = get_post( $trip_id );
		$formatted_cost = $wte->convert_trip_price( $trip, $formatted_cost );

	}

	return apply_filters( 'wp_travel_engine_get_formatted_price_separator', $formatted_cost, $trip_id, $use_default_currency_code );

}

/**
 * Get formatted price with currency for output.
 */
function wp_travel_engine_get_formated_price_with_currency( $price, $trip_id = null, $use_default_currency_code = false ) {
	if ( is_string( $price ) ) {
		$price = floatval( $price );
	}

	$currency_code   = wp_travel_engine_get_currency_code( $use_default_currency_code );
	$currency_symbol = wp_travel_engine_get_currency_symbol( $currency_code );

	$price_html = sprintf( '%1$s%2$s %3$s', $currency_symbol, wp_travel_engine_get_formated_price_separator( $price ), $currency_code );

	return apply_filters( 'wp_travel_engine_formated_price_currency', $price_html, $trip_id, $use_default_currency_code );

}

/**
 * Get formatted price with currency for output.
 */
function wp_travel_engine_get_formated_price_with_currency_symbol( $price, $trip_id = null, $use_default_currency_code = false ) {
	if ( is_string( $price ) ) {
		$price = floatval( $price );
	}

	$currency_code   = wp_travel_engine_get_currency_code( $use_default_currency_code );
	$currency_symbol = wp_travel_engine_get_currency_symbol( $currency_code );

	$price_html = sprintf( '%1$s%2$s', $currency_symbol, wp_travel_engine_get_formated_price_separator( $price ) );

	return apply_filters( 'wp_travel_engine_formated_price_currency', $price_html, $trip_id, $use_default_currency_code );

}

/**
 * Get formatted price with currency for output with currency code.
 */
function wp_travel_engine_get_formated_price_with_currency_code( $price, $trip_id = null, $use_default_currency_code = false ) {
	if ( is_string( $price ) ) {
		$price = floatval( $price );
	}

	$currency_code   = wp_travel_engine_get_currency_code( $use_default_currency_code );

	$price_html = sprintf( '%1$s %2$s', $currency_code, wp_travel_engine_get_formated_price_separator( $price ) );

	return apply_filters( 'wp_travel_engine_formated_price_currency_code', $price_html, $trip_id, $use_default_currency_code );
}

/**
 * Get formatted price with currency for output.
 */
function wp_travel_engine_get_formated_price_with_currency_code_symbol( $price, $trip_id = null, $use_default_currency_code = false ) {
	if ( is_string( $price ) ) {
		$price = floatval( $price );
	}

	$currency_code   = wp_travel_engine_get_currency_code( $use_default_currency_code );
	$currency_symbol = wp_travel_engine_get_currency_symbol( $currency_code );

	$price_html = sprintf( '%1$s %2$s%3$s', $currency_code , $currency_symbol, wp_travel_engine_get_formated_price_separator( $price ) );

	return apply_filters( 'wp_travel_engine_formated_price_currency_code_symbol', $price_html, $trip_id, $use_default_currency_code );

}

/**
 * Get price by key.
 *
 * @param boolean $pricing_key
 * @return void
 */
function wp_travel_engine_get_price_by_pricing_key( $trip_id, $pricing_key = false ) {

	$price = 0;

	// If no trip ID supplied.
	if ( ! $trip_id ) return $price;

	if ( ! $pricing_key ) :

		return wp_travel_engine_get_actual_trip_price( $trip_id );

	endif;

	return $price;

}

/**
 * Is partially payable for trip id.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_is_trip_partially_payable( $trip_id ) {

	if ( ! $trip_id )
		return false;

	$wte_options                  = get_option( 'wp_travel_engine_settings', true );
	$wte_trip_metas               = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
	$trip_partial_payment_enabled = isset( $wte_trip_metas['partial_payment_enable'] ) ? true : false;

	return class_exists( 'Wte_Partial_Payment_Admin' ) && isset( $wte_options['partial_payment_enable'] ) && $trip_partial_payment_enabled;

}

/**
 * Get partial payment data for trip.
 *
 * @return void
 */
function wp_travel_engine_get_trip_partial_payment_data( $trip_id ) {

	$partial_payment    = array();
	$trip_price_partial = 0;
	$wte_options        = get_option( 'wp_travel_engine_settings', true );
	$wte_trip_metas     = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );


	if ( ! $trip_id )
		return $partial_payment;

	if ( wp_travel_engine_is_trip_partially_payable( $trip_id ) ) :

		$partial_type = $wte_options['partial_payment_option'];

		if ( 'amount' === $partial_type ) :

			$trip_price_partial = isset( $wte_options['partial_payment_amount'] ) && ! empty( $wte_options['partial_payment_amount'] ) ? $wte_options['partial_payment_amount'] : 0;

			$trip_price_partial = isset( $wte_trip_metas['partial_payment_amount'] ) && ! empty( $wte_trip_metas['partial_payment_amount'] ) ? $wte_trip_metas['partial_payment_amount'] : $trip_price_partial;

			$partial_payment = array(
				'type'  => 'amount',
				'value' => $trip_price_partial,
			);

		elseif( 'percent' === $partial_type ) :

			$trip_partial_percentage = isset( $wte_options['partial_payment_percent'] ) && ! empty( $wte_options['partial_payment_percent'] ) ? $wte_options['partial_payment_percent'] : 0;

			$trip_partial_percentage = isset( $wte_trip_metas['partial_payment_percent'] ) && ! empty( $wte_trip_metas['partial_payment_percent'] ) ? $wte_trip_metas['partial_payment_percent'] : $trip_partial_percentage;

			$partial_payment = array(
				'type'  => 'percentage',
				'value' => $trip_partial_percentage,
			);

		endif;

	endif;

	return $partial_payment;

}

/**
 * Check if cart is partially payable.
 *
 * @return void
 */
function wp_travel_engine_is_cart_partially_payable() {

	global $wte_cart;

	$cart_items = $wte_cart->getItems();

	if ( ! empty( $cart_items ) ) :

		$cart_items = array_filter( $cart_items, function( $item ) {
			return wp_travel_engine_is_trip_partially_payable( $item['trip_id'] );
		} );

		return ( ! empty( $cart_items ) );

	endif;

	return false;

}

/**
 * Get person format.
 *
 * @since 3.0.0
 *
 * @return string Person format
 */
function wte_get_person_format() {

	$wte_settings = get_option( 'wp_travel_engine_settings', true );

	$per_person = __( '/person', 'wp-travel-engine' );

	if ( $wte_settings ) :

		// Set default per person format.
		if ( ! isset( $wte_settings['person_format'] ) || empty( $wte_settings['person_format'] ) ) {
			$wte_settings['person_format'] = __( '/person', 'wp-travel-engine' );
		}
		$per_person = $wte_settings['person_format'];

	endif;

	return apply_filters( 'wte_person_format', $per_person );
}

/**
 * Get book now text.
 *
 * @since 3.0.0
 *
 * @return String book now text.
 */
function wte_get_book_now_text() {

	$wte_settings = get_option( 'wp_travel_engine_settings', true );

	$per_person = __( 'Book Now', 'wp-travel-engine' );

	if ( $wte_settings ) :

		if ( ! isset( $wte_settings['book_btn_txt'] ) || empty( $wte_settings['book_btn_txt'] ) ) {
			$wte_settings['book_btn_txt'] = __( 'Book Now', 'wp-travel-engine' );
		}
		$per_person = $wte_settings['book_btn_txt'];

	endif;

	return apply_filters( 'wte_book_now', $per_person );
}

/**
 * is multiple pricing enabled for the trip?
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_is_trip_multiple_pricing_enabled( $trip_id ) {

	if ( ! $trip_id ) return false;

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	return isset( $trip_settings['multiple_pricing_enable'] ) && '1' === $trip_settings['multiple_pricing_enable'];

}

/**
 * Undocumented function
 *
 * @param [type] $pricing_key
 * @return void
 */
function wte_get_pricing_label_by_key( $trip_id, $pricing_key ) {

	if ( ! $pricing_key || ! $trip_id ) return false;

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : array();

	if ( ! empty( $multiple_pricing_options ) && isset( $multiple_pricing_options[$pricing_key] ) ) :

		return isset( $multiple_pricing_options[$pricing_key]['label'] ) ? $multiple_pricing_options[$pricing_key]['label'] : $pricing_key;

	endif;

	return false;

}

/**
 * Undocumented function
 *
 * @param [type] $pricing_key
 * @return void
 */
function wte_get_pricing_label_by_key_invoices( $trip_id, $pricing_key, $pax ) {

	if ( ! $pricing_key || ! $trip_id ) return false;

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : array();

	if ( ! empty( $multiple_pricing_options ) && isset( $multiple_pricing_options[$pricing_key] ) ) :

		$pax_label_str = sprintf( _nx( 'Number of %1$s', 'Number of %1$s(s)', 'number of travellers', $pax, 'wp-travel-engine' ), ucfirst( $pricing_key ) );

		if ( 'child' === $pricing_key ) :

			$pax_label_str = sprintf( _nx( 'Number of %1$s', 'Number of Children', $pax, 'number of travellers', 'wp-travel-engine' ), ucfirst( $pricing_key ) );

		endif;

		// if ( 'group' === $pricing_key ) :

		// 	$pax_label_str = __( 'Number of pax in Group', 'wte' );

		// endif;

		// if ( isset( $multiple_pricing_options[$pricing_key]['label'] ) ) :

		// 	$pax_label_str = sprintf( _nx( 'Number of pax in %1$s', 'Number of pax in %1$s', $pax, 'number of travellers', 'wte' ), ucfirst( $multiple_pricing_options[$pricing_key]['label'] ) );

		// endif;

		return $pax_label_str;

	endif;

	return false;

}
