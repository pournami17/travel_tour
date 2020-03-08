<?php
/**
 * Helper functions for WP Travel Engine.
 *
 * @package WP_Travel_Engine
 */
require sprintf( '%s/includes/helpers/helpers-prices.php', WP_TRAVEL_ENGINE_ABSPATH );
/**
 * Return array list of all trips.
 *
 * @return Array
 */
function wp_travel_engine_get_trips_array( $use_titles = false ) {
	$args = array(
		'post_type'   => 'trip',
		'numberposts' => -1,
	);

	$trips = get_posts( $args );

	$trips_array = array();
	foreach ( $trips as $trip ) {
		if ( $use_titles ) {
			$trips_array[ $trip->post_title ] = $trip->post_title;
		} else {
			$trips_array[ $trip->ID ] = $trip->post_title;
		}
	}
	return apply_filters( 'wp_travel_engine_trips_array', $trips_array, $args );
}

/**
 * Get permalink settings for WP Travel Engine.
 *
 * @since  2.2.4
 * @return array
 */
function wp_travel_engine_get_permalink_structure() {

	$permalinks = wp_parse_args(
		(array) get_option( 'wp_travel_engine_permalinks', array() ),
		array(
			'wp_travel_engine_trip_base'        => '',
			'wp_travel_engine_trip_type_base'   => '',
			'wp_travel_engine_destination_base' => '',
			'wp_travel_engine_activity_base'    => '',
		)
	);

	$permalinks['wp_travel_engine_trip_base']        = untrailingslashit( empty( $permalinks['wp_travel_engine_trip_base'] ) ? 'trip' : $permalinks['wp_travel_engine_trip_base'] );
	$permalinks['wp_travel_engine_trip_type_base']   = untrailingslashit( empty( $permalinks['wp_travel_engine_trip_type_base'] ) ? 'trip-types' : $permalinks['wp_travel_engine_trip_type_base'] );
	$permalinks['wp_travel_engine_destination_base'] = untrailingslashit( empty( $permalinks['wp_travel_engine_destination_base'] ) ? 'destinations' : $permalinks['wp_travel_engine_destination_base'] );
	$permalinks['wp_travel_engine_activity_base']    = untrailingslashit( empty( $permalinks['wp_travel_engine_activity_base'] ) ? 'activities' : $permalinks['wp_travel_engine_activity_base'] );

	return $permalinks;
}

/**
 * Get trip settings meta.
 *
 * @param int $trip_id
 * @return mixed $trip_settings | false
 * @since 2.2.4
 */
function wp_travel_engine_get_trip_metas( $trip_id ) {

	if ( ! $trip_id )
		return false;

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	return ! empty( $trip_settings ) ? $trip_settings : false;


}

/**
 * Get trip preview price ( Before sale )
 *
 * @param int $trip_id
 * @return int $prev_price
 * @since 2.2.4
 */
function wp_travel_engine_get_prev_price( $trip_id ) {

	if ( ! $trip_id )
		return 0;

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$prev_price    = '';

	if( $trip_settings ) {
		$prev_price = isset( $trip_settings['trip_prev_price'] ) ? $trip_settings['trip_prev_price'] : '';
	}

	return apply_filters( 'wp_travel_engine_trip_prev_price', $prev_price, $trip_id );

}

/**
 * Get trip sale price
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_sale_price( $trip_id ) {

	if ( ! $trip_id )
		return 0;

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );
	$sale_price    = '';

	if( $trip_settings ) {
		$sale_price = isset( $trip_settings['trip_price'] ) ? $trip_settings['trip_price'] : '';
	}

	return apply_filters( 'wp_travel_engine_trip_sale_price', $sale_price, $trip_id );

}

/**
 * Check if the trip is on sale
 *
 * @param int $trip_id
 * @return bool
 */
function wp_travel_engine_is_trip_on_sale( $trip_id ) {

	if ( ! $trip_id  )
		return false;

	$trip_settings = wp_travel_engine_get_trip_metas( $trip_id );

	if ( ! $trip_settings )
		return false;

	$trip_on_sale = isset( $trip_settings['sale'] ) ? true : false;

	return apply_filters( 'wp_travel_engine_is_trip_on_sale', $trip_on_sale, $trip_id );

}

/**
 * Get actual trip price.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_actual_trip_price( $trip_id ) {

	if ( ! $trip_id )
		return 0;

	$on_sale = wp_travel_engine_is_trip_on_sale( $trip_id );

	$trip_actual_price = $on_sale ? wp_travel_engine_get_sale_price( $trip_id ) : wp_travel_engine_get_prev_price( $trip_id );

	return apply_filters( 'wp_travel_engine_actual_trip_price', $trip_actual_price, $trip_id );

}

/**
 * Get currenct code.
 *
 * @return void
 */
function wp_travel_engine_get_currency_code( $use_default_currency_code = false ) {

	$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );

	$code = 'USD';

    if( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code']!= '' ){
        $code = $wp_travel_engine_settings['currency_code'];
	}

	return apply_filters( 'wp_travel_engine_currency_code', $code, $use_default_currency_code );

}

/**
 * Get currency symbol
 *
 * @param string $code
 * @return void
 */
function wp_travel_engine_get_currency_symbol( $code = 'USD') {

	$wte      = new Wp_Travel_Engine_Functions();
	$currency = $wte->wp_travel_engine_currencies_symbol( $code );

	return $currency;

}

/**
 * Get fixed departure dates array.
 *
 * @param [type] $trip_id
 * @return void
 */
function wp_travel_engine_get_fixed_departure_dates( $trip_id ) {

	$obj = new Wp_Travel_Engine_Functions();

	$valid_departure_dates_array = array();

	if ( ! $trip_id )
		return $valid_departure_dates_array;

	if( class_exists('WTE_Fixed_Starting_Dates') ){

		$starting_dates = get_post_meta( $trip_id, 'WTE_Fixed_Starting_Dates_setting', true );

		global $post;

		$WTE_Fixed_Starting_Dates_setting        = get_post_meta( $trip_id, 'WTE_Fixed_Starting_Dates_setting', true);
		$wp_travel_engine_setting_option_setting = get_option('wp_travel_engine_settings', true);
		$sortable_settings                       = get_post_meta( $trip_id, 'list_serialized', true);
		$wp_travel_engine_setting                = get_post_meta( $trip_id,'wp_travel_engine_setting',true );

		if(!is_array($sortable_settings))
		{
			$sortable_settings = json_decode($sortable_settings);
		}


		$valid_departure_dates = isset( $starting_dates['departure_dates'] ) && ! empty( $starting_dates['departure_dates'] ) ? $obj->wte_is_fixed_starting_dates_valid( $WTE_Fixed_Starting_Dates_setting, $sortable_settings ) : false;

		if( isset( $starting_dates['departure_dates'] ) && ! empty( $starting_dates['departure_dates'] ) && isset($starting_dates['departure_dates']['sdate']) && $valid_departure_dates ){

			$today = strtotime(date("Y-m-d"))*1000;
			$i = 0;
			foreach( $sortable_settings as $content )
			{
				$new_date = substr( $WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id], 0, 7 );
				if( $today <= strtotime($WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id])*1000 )
				{

					$num = isset($wp_travel_engine_setting_option_setting['trip_dates']['number']) ? $wp_travel_engine_setting_option_setting['trip_dates']['number'] : 3;
					if($i < $num)
					{
						if( isset( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) )
						{

							$valid_departure_dates_array[$i] = array(
								'starting_date'   => date_i18n( get_option( 'date_format' ), strtotime( $WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id] ) ),
								'remaining_seats' => $remaining = isset( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) && ! empty( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) ?  $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] . ' ' . __( 'spaces left', 'wp-travel-engine' ) : __( '0 space left', 'wp-travel-engine' ),
							);
						}

					}
				$i++;
				}
			}
		}
	}

	return $valid_departure_dates_array;

}


/**
 * Get checkout page URL
 *
 * @return void
 */
function wp_travel_engine_get_checkout_url() {

	$wte_global_options          = get_option('wp_travel_engine_settings', true);
	$wp_travel_engine_placeorder = isset($wte_global_options['pages']['wp_travel_engine_place_order']) ? esc_attr($wte_global_options['pages']['wp_travel_engine_place_order']) : '';

	return ! empty( $wp_travel_engine_placeorder ) ? esc_url( get_permalink( $wp_travel_engine_placeorder ) ) : esc_url( home_url( '/' ) );

}

/**
 * Sorted extras
 *
 * @param [type] $trip_id
 * @param array $extra_services
 * @return void
 */
function wp_travel_engine_sort_extra_services( $trip_id, $extra_services = array() )  {

	$sorted_extras = array();

	if ( ! $trip_id ) {

		return $sorted_extras;

	}

	$wp_travel_engine_setting = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

	foreach ($extra_services as $key => $value) {
		if( isset( $extra_services[$key] ) && $extra_services[$key]!='' && isset( $_POST['extra_service_name'][$key] ) && $_POST['extra_service_name'][$key]!= '' && '0' !== $extra_services[$key] )
		{
			$sorted_extras[$key] = array(
				'extra_service' => $wp_travel_engine_setting['extra_service'][$key],
				'qty'           => $extra_services[$key],
				'price'         => wp_travel_engine_get_formated_price( $_POST['extra_service_name'][$key] ),
			);
		}
	}

	return $sorted_extras;

}
/**
 * Get trip duration [ formatted ]
 */
function wp_travel_engine_get_trip_duration( $trip_id ) {

	if ( ! $trip_id ) {
		return false;
	}

	$trip_settings = get_post_meta( $trip_id,'wp_travel_engine_setting',true );

	return sprintf( _nx( '%s Day', '%s Days', $trip_settings['trip_duration'], 'trip duration days', 'wp-travel-engine' ), number_format_i18n( $trip_settings['trip_duration'] ) ) . ' ' . sprintf( _nx( '%s Night', '%s Nights', $trip_settings['trip_duration_nights'], 'trip duration nights', 'wp-travel-engine' ), number_format_i18n( $trip_settings['trip_duration_nights'] ) ) ;

}

add_action( 'wp_travel_engine_proceed_booking_btn', 'wp_travel_engine_default_booking_proceed' );

/**
 * Default proceed booking button.
 *
 * @return void
 */
function wp_travel_engine_default_booking_proceed() {

	$wp_travel_engine_setting_option_setting = get_option('wp_travel_engine_settings', true);

	global $post;

	ob_start();

	?>
		<button class="check-availability"><?php $button_txt =  __('Check Availability','wp-travel-engine'); echo apply_filters( 'wp_travel_engine_check_availability_button_text', $button_txt );?></button>
		<?php
		$btn_txt = __('Book Now','wp-travel-engine');
		if( isset( $wp_travel_engine_setting_option_setting['book_btn_txt'] ) && $wp_travel_engine_setting_option_setting['book_btn_txt']!='')
		{
			$btn_txt = $wp_travel_engine_setting_option_setting['book_btn_txt'];
		} ?>
		<input name="booking_btn" data-formid="booking-frm-<?php echo esc_attr( $post->ID ); ?>" type="submit" class="book-submit" value="<?php echo esc_attr( $btn_txt ); ?>">
	<?php

	$data = ob_get_clean();

	echo apply_filters( 'wp_travel_engine_booking_process_btn_html', $data );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string Template path.
 */
function wte_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = WP_TRAVEL_ENGINE_TEMPLATE_PATH;
	}

	if ( ! $default_path ) {
		$default_path = WP_TRAVEL_ENGINE_BASE_PATH . '/includes/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit(  $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template.
	if ( ! $template || WTE_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'wte_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. article attributes) passing attributes and including the file.
 *
 * @since 1.0.0
 *
 * @param string $template_name   Template name.
 * @param array  $args            Arguments. (default: array).
 * @param string $template_path   Template path. (default: '').
 * @param string $default_path    Default path. (default: '').
 */
function wte_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key ( implode( '-', array( 'template', $template_name, $template_path, $default_path, WP_TRAVEL_ENGINE_VERSION ) ) );
	$template = (string) wp_cache_get( $cache_key, 'wte-form-editor' );

	if ( ! $template ) {
		$template = wte_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'wte-form-editor' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wte_get_template', $template, $template_name, $args, $template_path, $default_path );

	if( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			wte_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'wp-travel-engine' ), '<code>' . $template . '</code>' ), '1.0.0' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			wte_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling wte_get_template.', 'wp-travel-engine' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args );
	}

	do_action( 'wte_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'wte_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}


/**
 * Like wte_get_template, but return the HTML instaed of outputting.
 *
 * @see wte_get_template
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param array $args           Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string.
 */
function wte_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
		wte_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Get list of all available paymanet gateways.
 *
 * @return void
 */
function wp_travel_engine_get_available_payment_gateways( $gateways_list = array() ) {

	$gateways_list = array(
		'booking_only'    => array(
			'label'       => __( 'Booking Only', 'wp-travel-engine' ),
			'input_class' => '',
			'public_label' => '',
			'icon_url'     => '',
			'info_text'   => __( 'If checked, no paymnet gateways will be used in checkout. The booking process will be completed and booking will be saved without payment.', 'wp-travel-engine' ),
		),
		'test_payment'    => array(
			'label'       => __( 'Test Payment', 'wp-travel-engine' ),
			'input_class' => '',
			'public_label' => '',
			'icon_url'     => '',
			'info_text'   => __( 'If checked, payment gateways will be disabled and booking will be done on a test mode. However, booking email will be received and booking will be successfully completed.', 'wp-travel-engine' ),
		),
		'paypal_payment'  => array(
			'label'       => __( 'Paypal Standard', 'wp-travel-engine' ),
			'input_class' => 'paypal-payment',
			'public_label' => '',
			'icon_url'     => WP_TRAVEL_ENGINE_URL . '/public/css/icons/paypal-payment.png',
			'info_text'   => __( 'Please check this to enable Paypal Standard booking system for trip booking and fill the account info below.', 'wp-travel-engine' ),
		),
	);

	return apply_filters( 'wp_travel_engine_available_payment_gateways', $gateways_list );

}

/**
 * Get sorted payment gateway list array
 *
 * @return void
 */
function wp_travel_engine_get_sorted_payment_gateways () {

	$wpte_settings      = get_option( 'wp_travel_engine_settings' );
	$available_gateways = wp_travel_engine_get_available_payment_gateways();

	$payment_gateway_sorted_settings = isset( $wpte_settings['sorted_payment_gateways'] ) && ! empty( $wpte_settings['sorted_payment_gateways'] ) ? $wpte_settings['sorted_payment_gateways'] : array_keys( $available_gateways );

	$sorted_payment_gateways = array();

	foreach ( $payment_gateway_sorted_settings as $key ) :

		if ( array_key_exists( $key, $available_gateways ) ) :

            $sorted_payment_gateways[$key] = $available_gateways[$key];

			unset( $available_gateways[$key] );

		endif;

	endforeach;

    return $sorted_payment_gateways + $available_gateways;

}

/**
 * return active payment gateways.
 *
 * @return void
 */
function wp_travel_engine_get_active_payment_gateways () {

	$available_sorted_gateways = wp_travel_engine_get_sorted_payment_gateways();
	$wpte_settings             = get_option( 'wp_travel_engine_settings' );

	$available_sorted_gateways = array_filter( $available_sorted_gateways, function( $gateway_key ) use ( $wpte_settings ) {
		return isset( $wpte_settings[$gateway_key] ) && ! empty( $wpte_settings[$gateway_key] );
	}, ARRAY_FILTER_USE_KEY );

	return $available_sorted_gateways;

}

/**
 * Get booking confirmation page URL
 *
 * @return url Confirmation page url.
 */
function wp_travel_engine_get_booking_confirm_url () {

	$wte_settings = get_option( 'wp_travel_engine_settings', true );

	$wte_confirm  = isset($wte_settings['pages']['wp_travel_engine_confirmation_page']) ? esc_attr($wte_settings['pages']['wp_travel_engine_confirmation_page']) : '';

	if ( empty( $wte_confirm ) ) :
		$wte_confirm  = esc_url( home_url( '/' ) );
	else :
		$wte_confirm  = get_permalink( $wte_confirm );
	endif;

	return $wte_confirm;

}
/*
 * Delete all the transients with a prefix.
 */
function wte_purge_transients( $prefix ) {
	global $wpdb;

	$prefix = esc_sql( $prefix );

	$options = $wpdb->options;

	$t  = esc_sql( "_transient_timeout_{$prefix}%" );

	$sql = $wpdb -> prepare (
	  "
		SELECT option_name
		FROM $options
		WHERE option_name LIKE '%s'
	  ",
	  $t
	);

	$transients = $wpdb -> get_col( $sql );

	// For each transient...
	foreach( $transients as $transient ) {

	  // Strip away the WordPress prefix in order to arrive at the transient key.
	  $key = str_replace( '_transient_timeout_', '', $transient );

	  // Now that we have the key, use WordPress core to the delete the transient.
	  delete_transient( $key );

	}

	// But guess what?  Sometimes transients are not in the DB, so we have to do this too:
	wp_cache_flush();
}

/**
 * Get view mode
 *
 * @return string $view_mode
 */
function wp_travel_engine_get_archive_view_mode() {
	$default   = 'list';
	$default   = apply_filters( 'wp_travel_engine_default_archive_view_mode', $default );
	$view_mode = $default;

	if ( isset( $_GET['view_mode'] ) && ( 'grid' === $_GET['view_mode'] || 'list' === $_GET['view_mode'] ) ) {
		$view_mode = $_GET['view_mode'];
	}

	return $view_mode;
}

/**
 * Outputs hidden form inputs for each query string variable.
 *
 * @since 3.0.6
 * @param string|array $values Name value pairs, or a URL to parse.
 * @param array        $exclude Keys to exclude.
 * @param string       $current_key Current key we are outputting.
 * @param bool         $return Whether to return.
 * @return string
 */
function wte_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
	if ( is_null( $values ) ) {
		$values = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( is_string( $values ) ) {
		$url_parts = wp_parse_url( $values );
		$values    = array();

		if ( ! empty( $url_parts['query'] ) ) {
			// This is to preserve full-stops, pluses and spaces in the query string when ran through parse_str.
			$replace_chars = array(
				'.' => '{dot}',
				'+' => '{plus}',
			);

			$query_string = str_replace( array_keys( $replace_chars ), array_values( $replace_chars ), $url_parts['query'] );

			// Parse the string.
			parse_str( $query_string, $parsed_query_string );

			// Convert the full-stops, pluses and spaces back and add to values array.
			foreach ( $parsed_query_string as $key => $value ) {
				$new_key            = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $key );
				$new_value          = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $value );
				$values[ $new_key ] = $new_value;
			}
		}
	}
	$html = '';

	foreach ( $values as $key => $value ) {
		if ( in_array( $key, $exclude, true ) ) {
			continue;
		}
		if ( $current_key ) {
			$key = $current_key . '[' . $key . ']';
		}
		if ( is_array( $value ) ) {
			$html .= wte_query_string_form_fields( $value, $exclude, $key, true );
		} else {
			$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
		}
	}

	if ( $return ) {
		return $html;
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get Enquiry form field by name.
 */
function wp_travel_engine_get_enquiry_field_label_by_name( $name = false ) {
	if ( ! $name ){
		return false;
	}
	$enquiry_form_fields   = WTE_Default_Form_Fields::enquiry();
	$enquiry_form_fields   = apply_filters( 'wp_travel_engine_enquiry_fields_display', $enquiry_form_fields );

	$field_label = isset( $enquiry_form_fields[$name] ) && isset( $enquiry_form_fields[$name]['field_label'] ) ? $enquiry_form_fields[$name]['field_label'] : $name;

	return $field_label;
}

/**
 * Get Booking form field by name.
 */
function wp_travel_engine_get_booking_field_label_by_name( $name = false ) {
	if ( ! $name ){
		return false;
	}
	$booking_form_fields   = WTE_Default_Form_Fields::booking();
	$booking_form_fields   = apply_filters( 'wp_travel_engine_booking_fields_display', $booking_form_fields );

	$field_label = isset( $booking_form_fields[$name] ) && isset( $booking_form_fields[$name]['field_label'] ) ? $booking_form_fields[$name]['field_label'] : $name;

	return $field_label;
}

/**
 * Get ller Info form field by name.
 */
function wp_travel_engine_get_traveler_info_field_label_by_name( $name = false ) {
	if ( ! $name ){
		return false;
	}
	$traveller_info_form_fields   = WTE_Default_Form_Fields::traveller_information();
	$traveller_info_form_fields   = apply_filters( 'wp_travel_engine_traveller_info_fields_display', $traveller_info_form_fields );

	$field_label = isset( $traveller_info_form_fields[$name] ) && isset( $traveller_info_form_fields[$name]['field_label'] ) ? $traveller_info_form_fields[$name]['field_label'] : $name;

	return $field_label;
}

/**
 * Get ller Info form field by name.
 */
function wp_travel_engine_get_relationship_field_label_by_name( $name = false ) {
	if ( ! $name ){
		return false;
	}
	$emergency_contact_form_fields   = WTE_Default_Form_Fields::emergency_contact();
	$emergency_contact_form_fields   = apply_filters( 'wp_travel_engine_emergency_contact_fields_display', $emergency_contact_form_fields );

	$field_label = isset( $emergency_contact_form_fields[$name] ) && isset( $emergency_contact_form_fields[$name]['field_label'] ) ? $emergency_contact_form_fields[$name]['field_label'] : $name;

	return $field_label;
}

/**
 * Get Default Settings Tab
 */
function wte_get_default_settings_tab() {
	$default_tabs = array(
		'name' => array
			(
				'1' => 'Overview',
				'2' => 'Itinerary',
				'3' => 'Cost',
				'4' => 'Faqs',
				'5' => 'Map'
			),

		'field' => array
			(
				'1' => 'wp_editor',
				'2' => 'itinerary',
				'3' => 'cost',
				'4' => 'faqs',
				'5' => 'map'
			),
		'id'	=> array
			(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5'
			)
	);

	return $default_tabs;
}

/**
 * Get From Email Address
 */
function wte_get_from_email() {
	$admin_email = get_option( 'admin_email' );
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );

	if ( in_array( $sitename, array( 'localhost', '127.0.0.1' ) ) ) {
		return $admin_email;
	}

	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	if ( strpbrk( $admin_email, '@' ) == '@' . $sitename ) {
		return $admin_email;
	}

	return 'wordpress@' . $sitename;
}
