<?php 
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true ); 
$feat_img = isset( $wp_travel_engine_settings['feat_img'] ) ? esc_attr($wp_travel_engine_settings['feat_img']):'0';
?>
<div class="wpte-row">
	<div class="wp-travel-engine-captcha">
		<label for="wp_travel_engine_settings[currency_code]"><?php _e('Currency code :','wp-travel-engine'); ?>
		</label>
		<div class="select-holder">
			<select id="wp_travel_engine_settings[currency_code]" name="wp_travel_engine_settings[currency_code]" data-placeholder="<?php esc_attr_e( 'Choose a currency&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
				<option value=""><?php _e( 'Choose a currency&hellip;', 'wp-travel-engine' ); ?></option>
				<?php
				$obj = new Wp_Travel_Engine_Functions();
				$currencies = $obj->wp_travel_engine_currencies();
				$code = 'USD';
	            if( isset( $wp_travel_engine_settings['currency_code'] ) && $wp_travel_engine_settings['currency_code']!= '' )
	            {
	                $code = $wp_travel_engine_settings['currency_code'];
	            } 
				$currency = $obj->wp_travel_engine_currencies_symbol( $code );
				foreach ( $currencies as $key => $name ) {
					echo '<option value="' .( !empty($key)?esc_attr( $key ):"USD")  . '" ' . selected( $code, $key, false ) . '>' . esc_html( $name . ' (' . $obj->wp_travel_engine_currencies_symbol( $key ) . ')' ) . '</option>';
				}
				?>
			</select>
		</div>

	</div>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[currency_option]"><?php _e('Display Currency Symbol or Code : ','wp-travel-engine');?> <span class="tooltip" title="Display Currency Symbol or Code in Trip Listing Templates."><i class="fas fa-question-circle"></i></span></label>
		<div class="select-holder">
			<select id="wp_travel_engine_settings[currency_option]" name="wp_travel_engine_settings[currency_option]" data-placeholder="<?php esc_attr_e( 'Choose a option&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
				<?php
				$options = array(
	            	'symbol' => 'Currency Symbol ( e.g. $ )',
	            	'code'=> 'Currency Code ( e.g. USD )'
	            );
				if(isset($wp_travel_engine_settings['currency_option']))
				{
					$option = esc_attr( $wp_travel_engine_settings['currency_option'] );
				}
				foreach ( $options as $key => $val ) {
					echo '<option value="' .( !empty($key) ? esc_attr( $key ) : "Please select")  . '" ' . selected( $option, $key, false ) . '>' . esc_html( $val ) . '</option>';
				}
				?>
			</select>
		</div>
	</div>

	<?php
		$thousands_separator = isset( $wp_travel_engine_settings['thousands_separator'] ) && $wp_travel_engine_settings['thousands_separator']!='' ? esc_attr( $wp_travel_engine_settings['thousands_separator'] ) : ',';
	?>
	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[thousands_separator]"><?php _e('Thousands Separator : ','wp-travel-engine');?> <span class="tooltip" title="Symbol to use for thousands separator in Trip Price."><i class="fas fa-question-circle"></i></span></label>
		<input type="text" id="wp_travel_engine_settings[thousands_separator]" name="wp_travel_engine_settings[thousands_separator]" value="<?php echo apply_filters('wp_travel_engine_default_separator', $thousands_separator);?>">
	</div>

	<div class="wp-travel-engine-captcha">
		<label for="wp_travel_engine_settings[book_btn_txt]"><?php _e('Book Now Button Text : ','wp-travel-engine');?></label> 
		<input type="text" id="wp_travel_engine_settings[book_btn_txt]" name="wp_travel_engine_settings[book_btn_txt]" value="<?php echo isset($wp_travel_engine_settings['book_btn_txt']) ? esc_attr( $wp_travel_engine_settings['book_btn_txt'] ): 'Book Now';?>" placeholder="<?php _e('Book now button label','wp-travel-engine');?>">
	</div>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[booking]"><?php _e('Hide Booking Form : ','wp-travel-engine');?> <span class="tooltip" title="If checked, booking form in the trip detail page will be disabled."><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[booking]" class="hide-booking" name="wp_travel_engine_settings[booking]" value="1" <?php if(isset($wp_travel_engine_settings['booking']) && $wp_travel_engine_settings['booking']!='' ) echo 'checked'; ?>>
		<label class="checkbox-label" for="wp_travel_engine_settings[booking]"></label>
	</div>
	
	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[enquiry]"><?php _e('Hide Enquiry Form : ','wp-travel-engine');?> <span class="tooltip" title="If checked, enquiry form in the trip detail page will be disabled."><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[enquiry]" class="hide-enquiry" name="wp_travel_engine_settings[enquiry]" value="1" <?php if(isset($wp_travel_engine_settings['enquiry']) && $wp_travel_engine_settings['enquiry']!='' ) echo 'checked'; ?>>
		<label class="checkbox-label" for="wp_travel_engine_settings[enquiry]"></label>
	</div>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[emergency]"><?php _e('Hide Emergency Contact Details : ','wp-travel-engine');?> <span class="tooltip" title="If checked, Emergency Contact Details of the travelers will be disabled from the Travelers Information Form."><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[emergency]" class="hide-emergency" name="wp_travel_engine_settings[emergency]" value="1" <?php if(isset($wp_travel_engine_settings['emergency']) && $wp_travel_engine_settings['emergency']!='' ) echo 'checked'; ?>>
		<label class="checkbox-label" for="wp_travel_engine_settings[emergency]"></label>
	</div>
	
	<div class="wp-travel-engine-settings enquiry-subject">
		<label for="wp_travel_engine_settings[query_subject]"><?php _e('Email Subject for Enquiry : ','wp-travel-engine');?> <span class="tooltip" title="Email subject for admin if a query is received. Supported Email tags - {enquirer_name}, {enquirer_email}"><i class="fas fa-question-circle"></i></span></label>
		<input type="text" id="wp_travel_engine_settings[query_subject]" name="wp_travel_engine_settings[query_subject]" value="<?php echo isset($wp_travel_engine_settings['query_subject']) ? esc_attr( $wp_travel_engine_settings['query_subject'] ): 'Enquiry received';?>">
	</div>

	<div class="wp_travel_engine_settings_pages thankyou-page">
		<label for="wp_travel_engine_settings[pages][enquiry]"><?php _e( 'Enquiry Thankyou Page:','wp-travel-engine' ); ?><span class="required">*</span> <span class="tooltip" title="This is the thankyou page where user will be redirected after successful enquiry."><i class="fas fa-question-circle"></i></span></label>
		<div class="select-holder">
			<?php 
			$enquiry = isset($wp_travel_engine_settings['pages']['enquiry']) ? esc_attr($wp_travel_engine_settings['pages']['enquiry']) : '';
		    wp_dropdown_pages(
		        array(
		             'name' => 'wp_travel_engine_settings[pages][enquiry]',
		             'echo' => 1,
		             'show_option_none' => __( '&mdash; Select &mdash;', 'wp-travel-engine' ),
		             'option_none_value' => '0',
		             'selected' => $enquiry,
		        )
		    );
		    ?>
		</div>
	</div>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[feat_img]"><?php _e('Hide Trip Featured Image : ','wp-travel-engine');?> <span class="tooltip" title="If checked, featured image in the trip detail page will be disabled."><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[feat_img]" name="wp_travel_engine_settings[feat_img]" value="1" <?php echo checked('1',$feat_img); ?>>
		<label for="wp_travel_engine_settings[feat_img]" class="checkbox-label"></label>
	</div>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[travelers_information]"><?php _e('Hide Travelers Information : ','wp-travel-engine');?> <span class="tooltip" title="If checked, information of all the travelers will be optional. After checkout, information of each of the travelers won't be asked to fill up."><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[travelers_information]" name="wp_travel_engine_settings[travelers_information]" value="1" <?php if(isset($wp_travel_engine_settings['travelers_information']) && $wp_travel_engine_settings['travelers_information']!='' ) echo 'checked'; ?>>
		<label for="wp_travel_engine_settings[travelers_information]" class="checkbox-label"></label>
	</div>
	
	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[tax_images]"><?php _e('Show Taxonomy Image : ','wp-travel-engine');?> <span class="tooltip" title="<?php _e('Enable to show taxonomy image in the taxonomy page.', 'wp-travel-engine'); ?>"><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[tax_images]" name="wp_travel_engine_settings[tax_images]" value="1" <?php if(isset($wp_travel_engine_settings['tax_images']) && $wp_travel_engine_settings['tax_images']!='' ) echo 'checked'; ?>>
		<label for="wp_travel_engine_settings[tax_images]" class="checkbox-label"></label>
	</div>

	<div class="wp-travel-engine-captcha">
		<label for="wp_travel_engine_settings[person_format]"><?php _e('Per Person Format : ','wp-travel-engine');?> <span class="tooltip" title="Per Person format in the trip booking form. Default is '/person'"><i class="fas fa-question-circle"></i></span></label> 
		<input type="text" id="wp_travel_engine_settings[person_format]" name="wp_travel_engine_settings[person_format]" value="<?php echo isset($wp_travel_engine_settings['person_format']) ? esc_attr( $wp_travel_engine_settings['person_format'] ): '/person';?>">
	</div>

	<div class="wp-travel-engine-captcha">
		<label for="wp_travel_engine_settings[confirmation_msg]"><?php _e('Booking Confirmation Message : ','wp-travel-engine');?></label> 
		<textarea rows="4" cols="30" id="wp_travel_engine_settings[confirmation_msg]" name="wp_travel_engine_settings[confirmation_msg]"><?php echo isset($wp_travel_engine_settings['confirmation_msg']) ? esc_attr( $wp_travel_engine_settings['confirmation_msg'] ): 'Thank you for booking the trip. Please check your email for confirmation. Below is your booking detail:';?></textarea>
	</div>

	<div class="wp-travel-engine-gdpr">
		<label for="wp_travel_engine_settings[gdpr_msg]"><?php _e('GDPR Message : ','wp-travel-engine');?></label> 
		<textarea rows="4" cols="30" id="wp_travel_engine_settings[gdpr_msg]" name="wp_travel_engine_settings[gdpr_msg]"><?php echo isset($wp_travel_engine_settings['gdpr_msg']) ? esc_attr( $wp_travel_engine_settings['gdpr_msg'] ): 'By contacting us, you agree to our ';?></textarea>
	</div>
	<?php 	
		do_action ('wp_travel_engine_settings_related_posts'); 
	?>

	<div class="wp-travel-engine-settings">
		<label for="wp_travel_engine_settings[show_multiple_pricing_list_disp]"><?php _e('Display multiple prices list : ','wp-travel-engine');?> <span class="tooltip" title="<?php __( 'If checked, multiple pricing options prices will be displayed on the trip page above the booking date selection area, if unchecked, Genereal or Adult prices will only be shown.', 'wp-travel-engine' ) ?>"><i class="fas fa-question-circle"></i></span></label>
		<input type="checkbox" id="wp_travel_engine_settings[show_multiple_pricing_list_disp]" name="wp_travel_engine_settings[show_multiple_pricing_list_disp]" value="1" <?php if(isset($wp_travel_engine_settings['show_multiple_pricing_list_disp']) && $wp_travel_engine_settings['show_multiple_pricing_list_disp']!='' ) echo 'checked'; ?>>
		<label for="wp_travel_engine_settings[show_multiple_pricing_list_disp]" class="checkbox-label"></label>
	</div>
</div>