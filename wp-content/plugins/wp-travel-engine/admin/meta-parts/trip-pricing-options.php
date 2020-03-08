<?php 
/**
 * Pricing Options Settings.
 */
$wte_trip_settings = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
?>
<div id="trip-prices">
	<div id="wte-pricing-options-holder">
		<?php
			global $post;
			// default options.
			$default_pricing_options = apply_filters( 'wte_default_pricing_options', 
				array( 
					'adult'  => __( 'Adult', 'wp-travel-engine' ), 
					'child'  => __( 'Child', 'wp-travel-engine' ), 
					'infant' => __( 'Infant', 'wp-travel-engine' ),
				) 
			);

			$saved_pricing_options = isset( $wte_trip_settings['multiple_pricing'] ) && ! empty( $wte_trip_settings['multiple_pricing'] ) ? $wte_trip_settings['multiple_pricing'] : $default_pricing_options;

			foreach( $saved_pricing_options as $option => $label ) :

				$trip_prev_price = '';
				$trip_sale_price = '';
				$trip_sale_enable = false;

				if ( 'adult' === $option && ! isset( $wte_trip_settings['multiple_pricing'] ) ) :

					$trip_prev_price = isset( $wte_trip_settings['trip_prev_price'] ) && ! empty( $wte_trip_settings['trip_prev_price'] ) ? $wte_trip_settings['trip_prev_price'] : '';
					$trip_sale_enable = isset( $wte_trip_settings['sale'] ) && '1' === $wte_trip_settings['sale'] ? true : false;
					$trip_sale_price = isset( $wte_trip_settings['trip_price'] ) && ! empty( $wte_trip_settings['trip_price'] ) ? $wte_trip_settings['trip_price'] : '';

				endif;

				if ( 'child' === $option && ! isset( $wte_trip_settings['multiple_pricing'] ) ) :

					$trip_prev_price = apply_filters( 'wte_apply_group_discount_default', $trip_prev_price );

				endif;

				$pricing_option_label = isset( $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['label'] ) ?  $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['label'] : ucfirst( $option );

				// $price =  $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['price'];

				$pricing_option_price = isset( $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['price'] ) ? $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['price'] : $trip_prev_price;

				$pricing_option_sale_price = isset( $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['sale_price'] ) ?  $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['sale_price'] : $trip_sale_price;

				$enable_sale_option = isset( $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['enable_sale'] ) && '1' === $wte_trip_settings['multiple_pricing'][esc_attr( $option )]['enable_sale'] ? true : $trip_sale_enable;

				$sale_display = $enable_sale_option ? 'block' : 'none';
		?>
				<div class="multiple-pricing-repeater">
					<?php
						/**
						 * Hook for pax limits and advanced options.
						 */
						do_action( 'wte_before_pricing_option_setting_fields' )
					?>
					<h3>
						<?php		
							$mp_label = ucfirst( $option );
							echo esc_html( sprintf( __( '%1$s Price', 'wp-travel-engine' ), $mp_label ) );
						?>
					</h3>
					<div class="repeater">
						<label for="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][label]">
							<?php _e( 'Pricing option label', 'wp-travel-engine' ); ?>
						</label>
						<input required type="text" name="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][label]" 
							id="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][label]" 
							value="<?php echo esc_attr( $pricing_option_label ); ?>"
							placeholder="<?php _e( 'Pricing option name', 'wp-travel-engine' ); ?>" />
					</div>
					<!-- Multiple Pricing cost -->
					<div class="repeater">
						<label for="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][price]">
							<?php _e( 'Price', 'wp-travel-engine' ); ?>
						</label>
						<div class="number-holder">
							<span class="currency-code">
							<?php echo esc_html( wp_travel_engine_get_currency_code() ); ?>    
							</span>
							<input type="number" name="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][price]" 
								id="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][price]" 
								value="<?php echo esc_attr( $pricing_option_price ); ?>"
								placeholder="<?php _e( 'Regular price', 'wp-travel-engine' ); ?>" />
						</div>
					</div>
					<!-- ./ Multiple Pricing cost -->
					<div class="repeater">
						
						<label 
							for="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][enable_sale]">
								<?php _e( 'Enable Sale', 'wp-travel-engine' ); ?>
						</label>
						<input 
							type    = "checkbox"
							class   = "wp-travel-engine-setting-enable-pricing-sale"
							id      = "wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][enable_sale]"
							name    = "wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][enable_sale]"
							value   = "1"
							<?php checked( $enable_sale_option, true ); ?>
						/>
						<label 
							for   = "wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][enable_sale]"
							class = "checkbox-label">
						</label>
					</div>
					<!-- Multiple Pricing sale cost -->
					<div class="repeater wp-travel-engine-pricing-sale" style="display:<?php echo esc_attr( $sale_display ); ?>">
						<label for="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][sale_price]">
							<?php _e( 'Sale Price', 'wp-travel-engine' ); ?>
						</label>
						<div class="number-holder">
							<span class="currency-code">
							<?php echo esc_html( wp_travel_engine_get_currency_code() ); ?>    
							</span>
							<input type="number" name="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][sale_price]" 
								id="wp_travel_engine_setting[multiple_pricing][<?php echo esc_attr( $option ); ?>][sale_price]" 
								value="<?php echo esc_attr( $pricing_option_sale_price ); ?>"
								placeholder="<?php _e( 'Sale price', 'wp-travel-engine' ); ?>" />
						</div>
					</div>
					<!-- ./ Multiple Pricing sale cost -->
					<?php
						/**
						 * Hook for pax limits and advanced options.
						 */
						do_action( 'wte_after_pricing_option_setting_fields' )
					?>
				</div>
		<?php 
			endforeach;
		?>
	</div>
	<?php 
		/**
		 * Action hook for custom multiple pricing options. 
		 */
		do_action( 'wte_after_multiple_pricing_options_settings' );
	?>
</div>
<?php
