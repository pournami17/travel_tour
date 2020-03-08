<?php
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
?>
<h3><?php _e('Payment Debug','wp-travel-engine');?></h3>
<div class="wp-travel-engine-settings">
	<label for="wp_travel_engine_settings[payment_debug]"><?php _e('Enable Debug Mode : ','wp-travel-engine');?> <span class="tooltip" title="<?php esc_html_e( 'Check this option to enable debug mode for all active payment gateways. Enabling this option will use sandbox accounts( if available ) on the checkout page.', 'wp-travel-engine' ); ?>"><i class="fas fa-question-circle"></i></span></label>
	<input type="checkbox" id="wp_travel_engine_settings[payment_debug]" name="wp_travel_engine_settings[payment_debug]" value="1" <?php if(isset($wp_travel_engine_settings['payment_debug']) && $wp_travel_engine_settings['payment_debug']!='' ) echo 'checked'; ?>>
	<label for="wp_travel_engine_settings[payment_debug]" class="checkbox-label"></label>
</div>
<h3><?php _e('Enable Payment Gateways','wp-travel-engine');?></h3>
<div id="wte-available-payment-gateway-srtble">
	<?php 
		$payment_gateways_sorted = wp_travel_engine_get_sorted_payment_gateways();

		foreach( $payment_gateways_sorted as $key => $payment_gateway ) : ?>
			
			<div class="wte-<?php echo esc_attr( $key ) ?>-form wp-travel-engine-settings">
				
				<label for="wp_travel_engine_settings[<?php echo esc_attr( $key ); ?>]">
					<?php echo esc_html( $payment_gateway['label'] ); ?>
				</label>
				<input type="checkbox" 
					id    ="wp_travel_engine_settings[<?php echo esc_attr( $key ); ?>]" 
					class ="<?php echo esc_attr( $payment_gateway['input_class'] ) ?>" 
					name  ="wp_travel_engine_settings[<?php echo esc_attr( $key ); ?>]" 
					value ="1" 
					<?php 
					if( isset( $wp_travel_engine_settings[esc_attr( $key )] ) && $wp_travel_engine_settings[esc_attr( $key )] != '' ) echo 'checked'; 
				?>>

				<label for   ="wp_travel_engine_settings[<?php echo esc_attr( $key ); ?>]" 
					   class ="checkbox-label">
				</label>

				<div class ="settings-note">
					<?php echo esc_html( $payment_gateway['info_text'] ); ?>
				</div>

				<input 
					type  ="hidden" 
				    name  ="wp_travel_engine_settings[sorted_payment_gateways][]" 
					value ="<?php echo esc_attr( $key ); ?>">
			</div>

	<?php endforeach; ?>
</div>
<?php
if( has_action( 'wte_paypal_form' ) )
{ ?>
	<h3><?php _e('Payment Gateways','wp-travel-engine'); ?></h3>
<?php
}

/**
 * Hoo for payment gateways.
 * 
 * @hooked - payhere.
 */
do_action( 'wp_travel_engine_payment_gateways_settins' );

// Need to move to single hook.
do_action( 'wte_paypal_form' );
do_action( 'wte_stripe_form' );
do_action( 'wte_authorize_net_admin' );
do_action( 'wte_payu_settings' );
do_action( 'wte_payfast_settings' );
do_action( 'wte_paypalexpress_settings' );
do_action( 'wte_hbl_settings' );
