<?php
/**
 * Place order form.
 *
 *
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 * @author
 */
class Wp_Travel_Engine_Thank_You
{
  /**
	* Initialize the thank you form shortcode.
	* @since 1.0.0
	*/
	function init()
	{
    	add_shortcode( 'WP_TRAVEL_ENGINE_THANK_YOU', array( $this, 'wp_travel_engine_thank_you_shortcodes_callback' ) );
    	add_filter( 'body_class', array( $this, 'add_thankyou_body_class' ) );
	}

	function add_thankyou_body_class($classes){
        global $post;
        if ( is_object( $post ) )
        {
            if ( has_shortcode( $post->post_content, 'WP_TRAVEL_ENGINE_THANK_YOU' ) ) {
                $classes[] = 'thank-you';
            }
        }
        
        return $classes;
    }

	/**
	* Place order form shortcode callback function.
	* @since 1.0.0
	*/
	function wp_travel_engine_thank_you_shortcodes_callback()
	{
		if (is_admin())
			return;

		ob_start();

		if ( ( defined( 'WTE_USE_OLD_BOOKING_PROCESS' ) && ! WTE_USE_OLD_BOOKING_PROCESS ) || ( ! defined( 'WTE_USE_OLD_BOOKING_PROCESS' ) ) ) :

			wte_get_template( 'thank-you/thank-you.php' );

			$data = ob_get_clean();

			return $data;

		endif;


		if( isset( $_SESSION['fdd-id'] ) )
		{
			$obj = new Wp_Travel_Engine_Functions();
			$did = esc_attr( $_SESSION['fdd-id'] );
			$pid = $_SESSION['trip-id'];
			$pno = $_SESSION['travelers'];
			$wp_travel_engine_departure_settings = get_post_meta( $pid, 'WTE_Fixed_Starting_Dates_setting', true );
			$wp_travel_engine_trip_setting = get_post_meta( $pid, 'wp_travel_engine_setting', true );
			if( isset( $wp_travel_engine_departure_settings['departure_dates']['seats_available'][$did]) && $wp_travel_engine_departure_settings['departure_dates']['seats_available'][$did]!='' )
			{
				$new_did = $wp_travel_engine_departure_settings['departure_dates']['seats_available'][$did]-$pno;
				$wp_travel_engine_departure_settings['departure_dates']['seats_available'][$did] = $new_did;
				update_post_meta( $pid, 'WTE_Fixed_Starting_Dates_setting', $wp_travel_engine_departure_settings );
			}
		}

		if( isset( $_POST['wp-travel-engine-confirmation-submit'] ) && isset( $_SESSION['trip-id'] ) )
		{
			if ( ! isset( $_POST['nonce'] ) || $_POST['nonce']=='' || ! wp_verify_nonce( $_POST['nonce'],'wp_travel_engine_final_confirmation_nonce') || !isset($_SESSION['trip-id'])) {
		     		$thank_page_msg = __('Sorry, you may not have confirmed your booking. Please fill up the form and confirm your booking. Thank you.','wp-travel-engine');
		     		$thank_page_error = apply_filters('wp_travel_engine_thankyou_page_error_msg',$thank_page_msg);
		           	return $thank_page_error;

	        }
		    //for payfast
	        $meta_id = get_post_meta($_SESSION['trip-id'],$_SESSION['trip-date'].'_bid');

		    $tid = isset( $_SESSION['tid'] ) ? $_SESSION['tid']: $meta_id[0] ;
		    $post = get_post( $_SESSION['trip-id'] );
		    $tname =  $post->post_title;
		    $order_metas = $_POST['wp_travel_engine_placeorder_setting'];
		    update_post_meta( $tid, 'wp_travel_engine_placeorder_setting', $order_metas );

		    //for payfast
		    if(get_post_meta($_SESSION['trip-id'],$_SESSION['trip-date'].'_bid'))
		    {
			    delete_post_meta( $_SESSION['trip-id'],$_SESSION['trip-date'].'_bid',$meta_id );
		    }

		    $obj = new Wp_Travel_Engine_Functions();
			$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
			$wp_travel_engine_trip_setting = get_post_meta( $_SESSION['trip-id'], 'wp_travel_engine_setting', true );
		    
		    //for code and currency code
			$code = 'USD';
            if( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code']!= '' )
            {
                $code = $wp_travel_engine_settings['currency_code'];
            }
        	$currency = $obj->wp_travel_engine_currencies_symbol( $code );

		    if ( isset( $wp_travel_engine_settings['confirmation_msg'] ) && $wp_travel_engine_settings['confirmation_msg']!='' )
		    {
			    $thankyou = $wp_travel_engine_settings['confirmation_msg'];
		   	}
			else{
				$thankyou  = __('Thank you for booking the trip. Please check your email for confirmation.','wp-travel-engine');
			    $thankyou .= __(' Below is your booking detail:','wp-travel-engine');
			    $thankyou .= '<br>';
			}
			    echo wp_kses_post( $thankyou );
		    ?>
		    <div class="thank-you-container">
				<h3 class="trip-details"><?php _e('Trip Details:','wp-travel-engine');?></h3>
				<div class="detail-container">
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Trip ID:','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo esc_attr( $_SESSION['trip-id'] );?></span>
					</div>
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Trip Name:','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo esc_attr( $tname );?></span>
					</div>
					<?php
					if(isset($_SESSION['due']) && $_SESSION['due']!='' ){ ?>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Total Paid:','wp-travel-engine' ); ?></strong>
							<?php
		                	$cost = str_replace( ',','',$_SESSION['trip-cost'] );?>
							<span class="value"><?php echo esc_attr($currency.$obj->wp_travel_engine_price_format( $cost ).' '.$code);?></span>
						</div>
					<?php
					} else{
					?>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Total Cost:','wp-travel-engine' ); ?></strong>
							<?php
		                	$cost = str_replace( ',','',$_SESSION['trip-cost'] );?>
							<span class="value"><?php echo esc_attr($currency.$obj->wp_travel_engine_price_format( $cost ).' '.$code);?></span>
						</div>
					<?php }?>
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Remaining Payment:','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo isset( $_SESSION['due'] ) ? esc_attr( $currency.$_SESSION['due'].' '.$code ):'-';?></span>
					</div>
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Trip Start Date:','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo esc_attr( $_SESSION['trip-date'] );?></span>
					</div>
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Number of Traveler(s):','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo esc_attr( $_SESSION['travelers'] );?></span>
					</div>
					<div class="detail-item">
						<strong class="item-label"><?php _e( 'Number of Child Traveler(s):','wp-travel-engine' ); ?></strong>
						<span class="value"><?php echo isset( $_SESSION['child-travelers'] ) ? esc_attr( $_SESSION['child-travelers'] ):'';?></span>
					</div>
					<?php

					$valid_exs = false;

					if( isset( $_SESSION['extra_service'] ) && $_SESSION['extra_service']!='' )
					{
						foreach( $_SESSION['extra_service'] as $key => $value ) {
							if ( '0' !== $value ) {
								$valid_exs = true;
							}
						}
						if ( $valid_exs ) :
						?>
							<div class="detail-item">
								<strong class="item-label"><?php _e( 'Extra Service(s):','wp-travel-engine' ); ?></strong>
								<span class="value">
									<?php
									foreach ( $_SESSION['extra_service'] as $key => $value )
									{
										if( isset( $wp_travel_engine_trip_setting['extra_service'][$key] ) && $wp_travel_engine_trip_setting['extra_service'][$key]!='' && isset( $_SESSION['extra_service'][$key] ) && $_SESSION['extra_service'][$key]!='0' ){

											if ( '0' === $_SESSION['extra_service'][$key] )
												continue;

												echo '<span>';
												echo isset( $wp_travel_engine_trip_setting['extra_service'][$key] ) ? esc_html($wp_travel_engine_trip_setting['extra_service'][$key]) . ' : ' : 'N/A';
												$cost = floatval( $wp_travel_engine_trip_setting['extra_service_cost'][$key] ) * floatval( $_SESSION['extra_service'][$key] );
												echo $_SESSION['extra_service'][$key] . ' X ' . esc_attr($currency) . esc_attr( $obj->wp_travel_engine_price_format( $_SESSION['extra_service_name'][$key] ) ). ' = ' . esc_attr($currency) . esc_html( strval( $cost ) ) . ' ' . esc_attr($code);
												echo '</span>';

										}
									}
									?>
								</span>
							</div>
						<?php
						endif;
					}
					?>
				</div>
			</div>
			<?php
			if (session_id() ) {
        		session_destroy();
     		}
		}
		else{
				if(isset($_SESSION['custom']))
				{
					$nonce = substr($_SESSION['custom'], 0, strpos($_SESSION['custom'], '!'));
				}
				if(isset($_SESSION['nonce']))
				{
					$nonce = substr($_SESSION['nonce'], 0, strpos($_SESSION['nonce'], '!'));
				}
				if(isset($_POST['nonce']))
				{
					$nonce = $_POST['nonce'];
				}
				if(isset($_POST['custom']))
				{
					$nonce = substr($_POST['custom'], 0, strpos($_POST['custom'], '!'));
				}
				if ( !isset($_SESSION['trip-id']))
				{
		     		$thank_page_msg = __('Sorry, you may not have confirmed your booking. Please fill up the form and confirm your booking. Thank you.','wp-travel-engine');
		     		$thank_page_error = apply_filters('wp_travel_engine_thankyou_page_error_msg',$thank_page_msg);
		     		return $thank_page_error;
		        }
			    $tid = esc_attr( $_SESSION['tid'] );
			    $post = get_post( $_SESSION['trip-id'] );
			    $tname =  $post->post_title;
			    $obj = new Wp_Travel_Engine_Functions();
				$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
				$wp_travel_engine_trip_setting = get_post_meta( $_SESSION['trip-id'], 'wp_travel_engine_setting', true );
			    
			    //for code and currency code
				$code = 'USD';
	            if( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code']!= '' )
	            {
	                $code = $wp_travel_engine_settings['currency_code'];
	            }
	        	$currency = $obj->wp_travel_engine_currencies_symbol( $code );
			    if ( isset( $wp_travel_engine_settings['confirmation_msg'] ) && $wp_travel_engine_settings['confirmation_msg']!='' )
			    {
				    $thankyou = $wp_travel_engine_settings['confirmation_msg'];
			   	}
				else{
					$thankyou  = __('Thank you for booking the trip. Please check your email for confirmation.','wp-travel-engine');
				    $thankyou .= __(' Below is your booking detail:','wp-travel-engine');
				    $thankyou .= '<br>';
				}
				    echo  wp_kses_post( $thankyou );
			    ?>
			    <div class="thank-you-container">
					<h3 class="trip-details"><?php _e('Trip Details:','wp-travel-engine');?></h3>
					<div class="detail-container">
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Trip ID:','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo esc_attr( $_SESSION['trip-id'] );?></span>
						</div>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Trip Name:','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo esc_attr( $tname );?></span>
						</div>
						<?php
						if(isset($_SESSION['due']) && $_SESSION['due']!='' ){ ?>
							<div class="detail-item">
								<strong class="item-label"><?php _e( 'Total Paid:','wp-travel-engine' ); ?></strong>
								<?php
			                	$cost = str_replace( ',','',$_SESSION['trip-cost'] );?>
								<span class="value"><?php echo esc_attr($currency.$obj->wp_travel_engine_price_format( $cost ).' '.$code);?></span>
							</div>
						<?php
						} else{
						?>
							<div class="detail-item">
								<strong class="item-label"><?php _e( 'Total Cost:','wp-travel-engine' ); ?></strong>
								<?php
			                	$cost = str_replace( ',','',$_SESSION['trip-cost'] );?>
								<span class="value"><?php echo esc_attr($currency.$obj->wp_travel_engine_price_format( $cost ).' '.$code);?></span>
							</div>
						<?php }?>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Remaining Payment:','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo isset( $_SESSION['due'] ) ? esc_attr( $currency.$_SESSION['due'].' '.$code ):'-';?></span>
						</div>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Trip Start Date:','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo esc_attr( $_SESSION['trip-date'] );?></span>
						</div>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Number of Traveler(s):','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo esc_attr( $_SESSION['travelers'] );?></span>
						</div>
						<div class="detail-item">
							<strong class="item-label"><?php _e( 'Number of Child Traveler(s):','wp-travel-engine' ); ?></strong>
							<span class="value"><?php echo isset( $_SESSION['child-travelers'] ) ? esc_attr( $_SESSION['child-travelers'] ):'';?></span>
						</div>
						<?php

						$valid_exs = false;

						if( isset( $_SESSION['extra_service'] ) && $_SESSION['extra_service']!='' )
						{
							foreach( $_SESSION['extra_service'] as $key => $value ) {
								if ( '0' !== $value ) {
									$valid_exs = true;
								}
							}
							if ( $valid_exs ) :
							?>
								<div class="detail-item">
									<strong class="item-label"><?php _e( 'Extra Service(s):','wp-travel-engine' ); ?></strong>
									<span class="value">
										<?php
										foreach ( $_SESSION['extra_service'] as $key => $value )
										{
											if( isset( $wp_travel_engine_trip_setting['extra_service'][$key] ) && $wp_travel_engine_trip_setting['extra_service'][$key]!='' && isset( $_SESSION['extra_service'][$key] ) && $_SESSION['extra_service'][$key]!='0' ){

												if ( '0' === $_SESSION['extra_service'][$key] )
													continue;

													echo '<span>';
													echo isset( $wp_travel_engine_trip_setting['extra_service'][$key] ) ? esc_html($wp_travel_engine_trip_setting['extra_service'][$key]) . ' : ' : 'N/A';
													$cost = floatval( $wp_travel_engine_trip_setting['extra_service_cost'][$key] ) * floatval( $_SESSION['extra_service'][$key] );
													echo $_SESSION['extra_service'][$key] . ' X ' . esc_attr($currency) . esc_attr( $obj->wp_travel_engine_price_format( $_SESSION['extra_service_name'][$key] ) ). ' = ' . esc_attr($currency) . esc_html( strval( $cost ) ) . ' ' . esc_attr($code);
													echo '</span>';

											}
										}
										?>
									</span>
								</div>
							<?php
							endif;
						}
						?>
					</div>
				</div>
				<?php
					if (session_id() ) {
		        		session_destroy();
		     		}
				}
			$data = ob_get_clean();

			return $data;
		}
	}