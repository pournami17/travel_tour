<?php 
    /**
     * Traveller's Information Template.
     * 
     * @package WP_Travel_Engine.
     */
    global $wte_cart;

		$totals = $wte_cart->get_total();

		// Set trip cost from cart.
		$_SESSION['trip-cost'] = $totals['total'];

		if( !isset( $_SESSION['nonce'] ) || $_SESSION['nonce']=='' )
		{
			$confirm_page_msg = __('Sorry, you may not have confirmed your booking. Please fill up the form and confirm your booking. Thank you.','wp-travel-engine');
			$confirm_page_error = apply_filters('wp_travel_engine_confirm_page_error_msg',$confirm_page_msg);
			return esc_attr( $confirm_page_error );

		}
		if (isset($_POST['wp_travel_engine_booking_setting']['place_order']['booking']['subscribe']) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['subscribe']=='1' )
		{
			$myvar = $_POST;
			$obj = new Wte_Mailchimp_Main;
			$new = $obj->wte_mailchimp_action($myvar);
		}
		if (isset($_POST['wp_travel_engine_booking_setting']['place_order']['booking']['mailerlite']) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['mailerlite']=='1' )
		{
			$myvar = $_POST;
			$obj = new Wte_Mailerlite_Main;
			$new = $obj->wte_mailerlite_action($myvar);
		}
		if (isset($_POST['wp_travel_engine_booking_setting']['place_order']['booking']['convertkit']) && $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['convertkit']=='1' )
		{
			$myvar = $_POST;
			$obj = new Wte_Convertkit_Main;
			$new = $obj->wte_convertkit_action($myvar);
		}
		$options = get_option('wp_travel_engine_settings', true);
		$wp_travel_engine_thankyou = isset($options['pages']['wp_travel_engine_thank_you']) ? esc_attr($options['pages']['wp_travel_engine_thank_you']) : '';

		$wp_travel_engine_thankyou = ! empty( $wp_travel_engine_thankyou ) ? get_permalink( $wp_travel_engine_thankyou ) : home_url( '/' );

		if ( isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) ) :
			
			$wp_travel_engine_thankyou = add_query_arg( 'booking_id', $_GET['booking_id'], $wp_travel_engine_thankyou );

		endif;

		?>
		<form method="post" id="wp-travel-engine-order-form" action="<?php echo esc_url( $wp_travel_engine_thankyou )?>">
			<?php
			if( !empty( $_GET['paymentid'] ) && ! empty( $_GET['payerID'] ) && ! empty( $_GET['token'] ) && ! empty( $_GET['pid'] ) ) {

				$paymentID = $_GET['paymentid'];
				$payerID   = $_GET['payerID'];
				$token     = $_GET['token'];
				$pid       = $_GET['pid'];
				
				require WP_TRAVEL_ENGINE_PAYPAL_EXPRESS_BASE_PATH . '/paypalExpress.php';
				
				$obj         = new paypalExpress;
				$order_metas = $obj->paypalCheck($paymentID, $pid, $payerID, $token);
			}

			if( isset( $_GET['paymentid'] ) && $_GET['paymentid']!='' && !isset($_GET['payerID']) )
			{
				$order_metas =
				Array
				(
					'place_order' => Array
						(
							'traveler' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['traveler'],
							'cost' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['cost'],
							'due' 		=> isset($_SESSION['due']) ? esc_attr( $_SESSION['due'] ):'',
							'tid' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['tid'],
							'tname' 	=> get_the_title($_POST['wp_travel_engine_booking_setting']['place_order']['tid']),
							'datetime' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['datetime'],
							'booking' 	=> Array
							(
								'fname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['fname'] ),
								'lname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['lname'] ),
								'email' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['email'] ),
								'address' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['address'] ),
								'city' 		=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['city'] ),
								'country' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['country'] ),
							),
							'payment'	=> Array
							(
								'paymentid' 	 	=> $_GET['paymentid'],
								'payerid' 			=> $_GET['payerid'],
								'token' 			=> $_GET['token'],
							)
						)
				);
			}

			// if( isset( $_POST['mihpayid'] ) && $_POST['mihpayid']!='' )
			// {
			// 	$order_metas =
			// 	Array
			// 	(
			// 		'place_order' => Array
			// 			(
			// 				'traveler' 	=> $_POST['udf1'],
			// 				'cost' 		=> $_POST['amount'],
			// 				'due' 		=> '',
			// 				'tid' 		=> $_SESSION['trip-id'],
			// 				'tname' 	=> get_the_title($_SESSION['trip-id']),
			// 				'datetime' 	=> $_SESSION['trip-date'],
			// 				'booking' 	=> Array
			// 				(
			// 					'fname' 	=> $_POST['firstname'],
			// 					'lname' 	=> $_POST['lastname'],
			// 					'email' 	=> $_POST['email'],
			// 					'address' 	=> $_POST['address1'],
			// 					'city' 		=> $_POST['city'],
			// 					'country' 	=> $_POST['country'],
			// 				),
			// 				'payment'	=> Array
			// 				(
			// 					'mihpayid' 	 		=> $_POST['mihpayid'],
			// 					'txnid' 			=> $_POST['txnid'],
			// 					'status' 			=> $_POST['status'],
			// 				)
			// 			)
			// 	);
			// }

			if(isset($_POST['stripeTokenType']))
			{
				do_action('stripe_payment_process',$_SESSION);
				$pno = $_POST['wp_travel_engine_booking_setting']['place_order']['traveler'];
				$order_metas =
				Array
				(
					'place_order' => Array
						(
							'traveler' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['traveler'],
							'cost' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['cost'],
							'due' 		=> isset($_SESSION['due']) ? esc_attr( $_SESSION['due'] ):'',
							'tid' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['tid'],
							'tname' 	=> get_the_title($_POST['wp_travel_engine_booking_setting']['place_order']['tid']),
							'datetime' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['datetime'],
							'booking' 	=> Array
							(
								'fname' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['fname'],
								'lname' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['lname'],
								'email' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['email'],
								'address' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['address'],
								'city' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['city'],
								'country' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['country'],
								'survey'	=> isset($_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey']) ? esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ):'',
							),
							'payment'	=> Array
							(
								'stripeToken' 		=> $_POST['stripeToken'],
								'stripeTokenType' 	=> $_POST['stripeTokenType'],
								'stripeEmail' 	 	=> $_POST['stripeEmail'],
								'payment_gateway'	=> 'stripe'
							)
						)
				);
			}
			if( isset( $_SESSION['payment'] ) && $_SESSION['payment'] == 'Authorize.net' ) {
				$cost = $_SESSION['trip-cost'];
				$cost = str_replace(',', '', $cost);
				$order_metas =
					Array
					(
						'place_order' => Array
							(
								'traveler' 	=> $_SESSION['travelers'],
								'cost' 		=> $cost,
								'tid' 		=> $_SESSION['trip-id'],
								'tname' 	=> get_the_title($_SESSION['trip-id']),
								'datetime' 	=> esc_attr( $_SESSION['trip-date'] ),
								'booking' 	=> Array
								(
									'fname' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['fname'],
									'lname' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['lname'],
									'email' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['email'],
									'address' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['address'],
									'city' 		=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['city'],
									'country' 	=> $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['country'],
									'survey'	=> isset($_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey']) ? esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ):'',
								),
								'payment'	=> Array
								(
									'acode' 		=> $_SESSION['acode'],
									'atid' 			=> $_SESSION['atid'],
									'atype' 	 	=> $_SESSION['atype'],
									'amethod'		=> $_SESSION['amethod'],
									'aemail'		=> $_SESSION['aemail']
							),
						)
					);
			}


			if( isset( $_POST['wp-travel-engine-submit'] ) && isset( $_POST['wte_payment_options'] ) && $_POST['wte_payment_options'] == 'Test Payment' )
			{ 
				$post = get_post( $_SESSION['trip-id'] ); 
				$slug = $post->post_title;
				$order_metas =
					Array
					(
						'place_order' => Array
							(
								'traveler' 	=> esc_attr( $_SESSION['travelers'] ),
								'cost' 		=> esc_attr( $_SESSION['trip-cost'] ),
								'due' 		=> isset( $_SESSION['due'] ) ? $_SESSION['due']:'',
								'tid' 		=> esc_attr( $_SESSION['trip-id'] ),
								'tname' 	=> esc_attr( $slug ),
								'datetime' 	=> esc_attr( $_SESSION['trip-date'] ),
								'booking' 	=> Array
								(
									'fname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['fname'] ),
									'lname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['lname'] ),
									'email' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['email'] ),
									'address' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['address'] ),
									'city' 		=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['city'] ),
									'country' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['country'] ),
									'survey'	=> isset( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ) ? esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ):'',
								),
							)
					);
			}

			if( isset( $_POST['wp-travel-engine-submit'] ) && !isset( $_POST['wte_payment_options'] ) )
			{ 
				$post = get_post( $_SESSION['trip-id'] ); 
				$slug = $post->post_title;
				$order_metas =
					Array
					(
						'place_order' => Array
						(
							'traveler' 	=> esc_attr( $_SESSION['travelers'] ),
							'cost' 		=> esc_attr( $_SESSION['trip-cost'] ),
							'due' 		=> isset( $_SESSION['due'] ) ? $_SESSION['due']:'',
							'tid' 		=> esc_attr( $_SESSION['trip-id'] ),
							'tname' 	=> esc_attr( $slug ),
							'datetime' 	=> esc_attr( $_SESSION['trip-date'] ),
							'booking' 	=> Array
							(
								'fname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['fname'] ),
								'lname' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['lname'] ),
								'email' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['email'] ),
								'address' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['address'] ),
								'city' 		=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['city'] ),
								'country' 	=> esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['country'] ),
								'survey'	=> isset( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ) ? esc_attr( $_POST['wp_travel_engine_booking_setting']['place_order']['booking']['survey'] ):'',
							),
						)
					);
			}
			if( isset( $_GET['wte_gateway'] ) && 'paypal' === $_GET['wte_gateway'] ) {

				do_action( 'wp_travel_engine_verify_paypal_ipn' );

			}

			if ( isset( $_SESSION['travelers'] ) && $_SESSION['travelers']!='' )
			{
				$pno = esc_attr( $_SESSION['travelers'] );
			} 

			if( isset($order_metas) && is_array($order_metas) )
			{
				
				global $wpdb;
				$new_post = array(
					'post_status' => 'publish',
					'post_type' 	=> 'booking',
					'post_title' 	=> 'booking',
					);
				$post_id = wp_insert_post( $new_post );
				
				if( ! is_wp_error( $post_id ) ) :

					/**
					 * @action_hook wte_created_user_booking
					 * 
					 * @since 2.2.0
					 */
					do_action( 'wte_after_booking_created', $post_id );

				endif;

				$book_post = array(
						'ID'           => $post_id,
						'post_title'   => 'booking '.$post_id,
					);
				// Update the post into the database
				$updated = wp_update_post( $book_post );

				$bid[] = $post_id;

				$order_metas = array_merge_recursive( $order_metas, $bid );
            update_post_meta( $post_id, 'wp_travel_engine_booking_setting', $order_metas );
            
            $info_class = new Wp_Travel_Engine_Order_Confirmation();
				
			$info_class->insert_customer( $order_metas );

				if ( false === $updated ) {
					_e( 'There was an error on update.','wp-travel-engine' );
				}
				$class = 'Wp_Travel_Engine_Mail_Template';
				$obj = apply_filters('mail_template_class', $class);
				$obj = new $obj;
				$obj->mail_editor( $order_metas,$post_id );
				
				$obj = new Wp_Travel_Engine_Functions();
				$personal_options = $obj->order_form_personal_options();
				$relation_options = $obj->order_form_relation_options();
				$_SESSION['tid'] = esc_attr( $post_id );
			}

			if(isset($options['travelers_information']))
			{
				if (isset($_POST))
				{
					$error_found = FALSE;

				    //  Some input field checking
					if ( $error_found == FALSE ) {
				        //  Use the wp redirect function
						wp_redirect( $wp_travel_engine_thankyou );
					}
					else {
						//  Some errors were found, so let's output the header since we are staying on this page
						if (isset($_GET['noheader']))
							require_once(ABSPATH . 'wp-admin/admin-header.php');
					}
				}
			}
			
			include_once WP_TRAVEL_ENGINE_ABSPATH . '/includes/lib/wte-form-framework/class-wte-form.php';

			$total_pax = 0;
			$cart_items = $wte_cart->getItems();

			foreach( $cart_items as $key => $item ) {
				$pax       = array_sum( $item['pax'] );
				$total_pax = absint( $total_pax + $pax );
			}

			$form_fields      = new WP_Travel_Engine_Form_Field();

			$traveller_fields   = WTE_Default_Form_Fields::traveller_information();
			$traveller_fields   = apply_filters( 'wp_travel_engine_traveller_info_fields_display', $traveller_fields );
			
			$emergency_contact_fields = WTE_Default_Form_Fields::emergency_contact();
			$emergency_contact_fields = apply_filters( 'wp_travel_engine_emergency_contact_fields_display', $emergency_contact_fields );

			$wp_travel_engine_settings_options = get_option( 'wp_travel_engine_settings', true );

			for( $i = 1; $i <= $total_pax; $i++ ) {
				echo '<div class="relation-options-title">'. sprintf( __( 'Personal details for Traveler: #%1$s', 'wp-travel-engine' ), $i ) .'</div>';

				$modified_traveller_fields = array_map( function( $field ) use ( $i ) {
					if (strpos($field['name'], 'wp_travel_engine_placeorder_setting[place_order][travelers]') !== false) {
						$field['name'] = sprintf( '%s[%d]', $field['name'], $i );
					} else {
						$field['name'] = sprintf( 'wp_travel_engine_placeorder_setting[place_order][travelers][%s][%d]', $field['name'], $i );
					}
					$field['id']   = sprintf( '%s[%d]', $field['id'], $i );
					$field['wrapper_class'] = 'wp-travel-engine-personal-details';
					return $field;
				}, $traveller_fields );
				
				$form_fields->init( $modified_traveller_fields )->render();

				if ( ! isset( $wp_travel_engine_settings_options['emergency'] ) ) {
					echo '<div class="relation-options-title">'. sprintf( __( 'Emergency contact details for Traveler: #%1$s', 'wp-travel-engine' ), $i ) .'</div>';

					$modified_emergency_contact_fields = array_map( function( $field ) use( $i ) {
						if (strpos($field['name'], 'wp_travel_engine_placeorder_setting[place_order][relation]') !== false) {
							$field['name'] = sprintf( '%s[%d]', $field['name'], $i );
						} else {
							$field['name'] = sprintf( 'wp_travel_engine_placeorder_setting[place_order][relation][%s][%d]', $field['name'], $i );
						}
						$field['id']   = sprintf( '%s[%d]', $field['id'], $i );
						$field['wrapper_class'] = 'wp-travel-engine-personal-details';
						return $field;
					}, $emergency_contact_fields );

					$form_fields->init( $modified_emergency_contact_fields )->render();
				}
			}
			$nonce = wp_create_nonce('wp_travel_engine_final_confirmation_nonce');
			?>
			<input type="hidden" name="nonce" value="<?php echo $nonce;?>">
			<input type="submit" name="wp-travel-engine-confirmation-submit" value="<?php _e('Confirm Booking','wp-travel-engine');?>">
		</form>
<?php
