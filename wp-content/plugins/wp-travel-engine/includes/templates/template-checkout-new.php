<div class="wpte-bf-outer wpte-bf-checkout">
    <div class="wpte-bf-booking-steps">
        <?php
            // Get template for header crumbs.
            wte_get_template( 'checkout/header-steps.php' );

            $options                           = get_option('wp_travel_engine_settings', true);
            $wp_travel_engine_terms_conditions = isset( $options['pages']['wp_travel_engine_terms_and_conditions'] ) ? esc_attr( $options['pages']['wp_travel_engine_terms_and_conditions'] ) : '';

            // $checkout_default_fields = array(
            //     'datetime'     => array(
            //         'type'          => 'hidden',
            //         'wrapper_class' => 'wp-travel-engine-billing-details-field-wrap',
            //         'field_label'   => __( 'Date', 'wp-travel-engine' ),
            //         'name'          => 'wp_travel_engine_booking_setting[place_order][datetime]',
            //         'id'            => 'wp_travel_engine_booking_setting[place_order][datetime]',
            //         'validations'   => array(
            //             'required'  => true,
            //             'maxlength' => '50',
            //             'type'      => 'alphanum',
            //         ),
            //         'default'  => isset( $_SESSION['trip-date'] ) ? $_SESSION['trip-date'] : '',
            //         'priority' => 10,
            //     ),

            // );

            if( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ) {

                $privacy_policy_lbl = sprintf( __( 'Check the box to confirm you\'ve read and agree to our <a href="%1$s" id="terms-and-conditions" target="_blank"> Terms and Conditions</a> and <a href="%2$s" id="privacy-policy" target="_blank">Privacy Policy</a>.', 'wp-travel-engine'), esc_url( get_permalink( $wp_travel_engine_terms_conditions ) ), esc_url( get_privacy_policy_url()) );
        
                $checkout_default_fields['privacy_policy_info'] =  array(
                    'type'          => 'checkbox',
                    'options'       => array( '0' => $privacy_policy_lbl ),
                    'name'          => 'wp_travel_engine_booking_setting[terms_conditions]',
                    'wrapper_class' => 'wp-travel-engine-terms',
                    'id'            => 'wp_travel_engine_booking_setting[terms_conditions]',
                    'default'       => '',
                    'validations'   => array(
                        'required' => true,
                    ),
                    'option_attributes' => array(
                        'required' => true,
                        'data-msg' => __( 'Please make sure to check the privacy policy checkbox', 'wp-travel-engine' ),
                        'data-parsley-required-message' => __( 'Please make sure to check the privacy policy checkbox', 'wp-travel-engine' ),
                    ),
                    'priority' => 70,
                );
        
            }
            elseif ( current_user_can( 'edit_theme_options' ) ) {
        
                $privacy_policy_lbl = sprintf( __( '%1$sPrivacy Policy page not set or not published, please check Admin Dashboard > Settings > Privacy.%2$s', 'wp-travel-engine' ), '<p style="color:red;">', '</p>' );
        
                $checkout_default_fields['privacy_policy_info'] =  array(
                    'type'              => 'text_info',
                    // 'label'             => __( 'Privacy Policy', 'wp-travel-engine' ),
                    'id'                => 'wp-travel-engine-privacy-info',
                    'default'           => $privacy_policy_lbl,
                    'priority'          => 80,
                );
        
            }

            $checkout_fields   = WTE_Default_Form_Fields::booking();
            $checkout_fields   = apply_filters( 'wp_travel_engine_booking_fields_display', $checkout_fields );
            
            // $priority = array_column( $checkout_fields, 'priority' );
            // array_multisort( $priority, SORT_ASC, $checkout_fields );

        ?>
        <div class="wpte-bf-step-content-wrap">
            <?php  if ( ! empty( $checkout_fields ) && is_array( $checkout_fields ) ) : ?>
                <div class="wpte-bf-checkout-form">
                    <div class="wpte-bf-title"><?php echo apply_filters( 'wpte_billings_details_title', esc_html__( 'Billing Details', 'wp-travel-engine' ) ); ?></div>
                    <form id="wp-travel-engine-new-checkout-form" method="POST" name="wp_travel_engine_new_checkout_form" action="" enctype= "multipart/form-data">
                        <input type="hidden" name="action" value="wp_travel_engine_new_booking_process_action" >
                        <?php 
                            // Create booking process action nonce for security.
                            wp_nonce_field( 'wp_travel_engine_new_booking_process_nonce_action', 'wp_travel_engine_new_booking_process_nonce' );
                        ?>
                        <?php
                            // Include the form class - framework.
                            include_once WP_TRAVEL_ENGINE_ABSPATH . '/includes/lib/wte-form-framework/class-wte-form.php';

                            // form fields initialize.
                            $form_fields      = new WP_Travel_Engine_Form_Field();
                            
                            $checkout_fields  = array_map( function( $field ) {
                                $field['wrapper_class'] = 'wpte-bf-field wpte-cf-' . $field['type'];
                                return $field;
                            }, $checkout_fields );

                            // $privacy_fields = array();

                            // if ( isset( $checkout_fields['privacy_policy_info'] ) ) :
                            //     $privacy_fields['privacy_policy_info'] = $checkout_fields['privacy_policy_info'];
                            //     unset( $checkout_fields['privacy_policy_info'] );
                            // endif;

                            // Render form fields.
                            $form_fields->init( $checkout_fields )->render();

                            if( wp_travel_engine_is_cart_partially_payable() ) :
                                global $wte_cart;

                                $tripid = $wte_cart->get_cart_trip_ids();
                                $partial_payment_data = wp_travel_engine_get_trip_partial_payment_data( $tripid[0] );

                                if ( ! empty( $partial_payment_data ) ) :
                                    if( 'amount' === $partial_payment_data['type'] ) :
                                        $trip_price_partial = wp_travel_engine_get_formated_price_with_currency_code( $partial_payment_data['value'] );
                                    elseif( 'percentage' === $partial_payment_data['type'] ) :
                                        $trip_price_partial = sprintf( '%s%%', $partial_payment_data['value'] );
                                    endif;
                                endif;
                        ?>
                                <div class="wpte-bf-field wpte-bf-radio">
                                    <label for="" class="wpte-bf-label">
                                        <?php esc_html_e( 'Down payment options', 'wp-travel-engine' ); ?>
                                    </label>
                                    <div class="wpte-bf-radio-wrap">
                                        <input type="radio" name="wp_travel_engine_payment_mode" value="partial" id="wp_travel_engine_payment_mode-partial" checked >
                                        <label for="wp_travel_engine_payment_mode-partial"><?php echo sprintf( esc_html__( 'Down payment(%s)', 'wp-travel-engine' ), $trip_price_partial ); ?></label>
                                    </div>
                                    <div class="wpte-bf-radio-wrap">
                                        <input type="radio" name="wp_travel_engine_payment_mode" value="full_payment" id="wp_travel_engine_payment_mode-full">
                                        <label for="wp_travel_engine_payment_mode-full"><?php echo esc_html( 'Full payment(100%)', 'wp-travel-engine' ); ?></label>
                                    </div>
                                </div>
                        <?php
                            endif;
                            // Get active payment gateways to display publically.
                            $active_payment_methods = wp_travel_engine_get_active_payment_gateways();

                            if ( ! empty( $active_payment_methods ) ) :
                        ?>
                                <div class="wpte-bf-field wpte-bf-radio">
                                    <label for="" class="wpte-bf-label">
                                        <?php esc_html_e( 'Payment Method', 'wp-travel-engine' ); ?>
                                    </label>
                                    <?php
                                    $gateway_index = 1;
                                    foreach( $active_payment_methods as $key => $payment_method ) : ?>
                                        <div class="wpte-bf-radio-wrap">
                                            <input <?php checked( $gateway_index, 1 ); ?> type="radio" name="wpte_checkout_paymnet_method" value="<?php echo esc_attr( $key ); ?>" id="wpte-checkout-paymnet-method-<?php echo esc_attr( $key ); ?>">
                                            <label for="wpte-checkout-paymnet-method-<?php echo esc_attr( $key ); ?>">
                                                <?php 
                                                    if ( isset( $payment_method['icon_url'] ) && ! empty( $payment_method['icon_url'] ) ) : 
                                                ?>
                                                    <img src="<?php echo esc_url( $payment_method['icon_url'] ); ?>" alt="<?php echo esc_attr( $payment_method['label'] ); ?>">
                                                <?php else : 
                                                    echo esc_html( $payment_method['label'] );
                                                endif; ?>
                                            </label>
                                        </div>
                                    <?php 
                                    $gateway_index++;
                                    endforeach; ?>
                                </div>
                        <?php
                            endif; 
                            if ( ! empty( $checkout_default_fields ) ) :
                                $checkout_default_fields = apply_filters( 'wte_booking_privacy_fields', $checkout_default_fields );
                                $form_fields->init( $checkout_default_fields )->render();
                            endif;
                        ?>
                        <?php do_action( 'wte_booking_before_submit_button' ); ?>
                        <div class="wpte-bf-field wpte-bf-submit">
                            <input type="submit" name="wp_travel_engine_nw_bkg_submit" value="<?php esc_attr_e( 'Book Now', 'wp-travel-engine' ); ?>">
                        </div>
                        <?php do_action( 'wte_booking_after_submit_button' ); ?>
                    </form>
                </div><!-- .wpte-bf-checkout-form -->
            <?php endif; ?>
        <?php wte_get_template( 'checkout/mini-cart.php' );  ?>
        </div><!-- .wpte-bf-step-content-wrap -->
    </div><!-- .wpte-bf-booking-steps -->
</div><!-- .wpte-bf-outer -->
