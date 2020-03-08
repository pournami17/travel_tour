<div class="wpte-row">
	<h3 class="title">Trip Info</h3>
	<div class="trip-info-title-wrapper">
		<div class="trip-info-title">
			<h4>Field Name <span class="required">*</span> <span class="tooltip" title="Field Name is the unique id of the input field."><i class="fas fa-question-circle"></i></span></h4>
			<h4>Field Icon <span class="required">*</span> <span class="tooltip" title="Choose icon for the tab. Leave blank if no icon is required."><i class="fas fa-question-circle"></i></span></h4>
			<h4>Field Type <span class="required">*</span> <span class="tooltip" title="Field type is the input types."><i class="fas fa-question-circle"></i></span></h4>
			<h4>Field Placeholder <span class="required">*</span> <span class="tooltip" title="Placeholder for the input field."><i class="fas fa-question-circle"></i></span></h4>
		</div>
	</div>	
	<ul class="fields-accordion">
		<?php $wp_travel_engine_settings = get_option( 'wp_travel_engine_settings',true );
		if( isset( $wp_travel_engine_settings['trip_facts'] ) ) {
			$trip_facts = $wp_travel_engine_settings['trip_facts'];
			$arr_keys = array_keys( $trip_facts['field_id'] );
			$len = sizeof( $wp_travel_engine_settings['trip_facts']['field_id'] );
			$i=1;
			foreach ( $arr_keys as $key => $value ) { ?>		
				<li id="trip_facts_template-<?php echo $value;?>" data-id="<?php echo esc_attr($value);?>" class="trip_facts">
				 	<span class="tabs-handle">
						<span></span>
					</span>
					<div class="form-builder">
						<div class="fid">
							<label for="wp_travel_engine_settings[trip_facts][fid][<?php echo $value;?>]"></label> 
							<input type="hidden" name="wp_travel_engine_settings[trip_facts][fid][<?php echo $value;?>]" value="<?php echo isset($wp_travel_engine_settings['trip_facts']['fid'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['fid'][$value] ): '';?>" required>
						</div>
						<div class="field-id"> 
							<input type="text" name="wp_travel_engine_settings[trip_facts][field_id][<?php echo $value;?>]" value="<?php echo isset($wp_travel_engine_settings['trip_facts']['field_id'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['field_id'][$value] ): '';?>" required>
						</div>
						<div class="field-icon">
							<input class="trip-tabs-icon" type="text" name="wp_travel_engine_settings[trip_facts][field_icon][<?php echo $value;?>]" value="<?php echo isset($wp_travel_engine_settings['trip_facts']['field_icon'][$value]) ? esc_attr( $wp_travel_engine_settings['trip_facts']['field_icon'][$value] ): '';?>">
						</div>
						<div class="field-type">
							<div class="select-holder">
								<select id="wp_travel_engine_settings[trip_facts][field_type][<?php echo $value;?>]" name="wp_travel_engine_settings[trip_facts][field_type][<?php echo $value;?>]" data-placeholder="<?php esc_attr_e( 'Choose a field type&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
										<option value=" "><?php _e( 'Choose input type&hellip;', 'wp-travel-engine' ); ?></option>
									<?php
										$obj = new Wp_Travel_Engine_Functions();
										$fields = $obj->trip_facts_field_options();
										$selected_field = esc_attr( $wp_travel_engine_settings['trip_facts']['field_type'][$value] );
										foreach ( $fields as $key => $val ) {
										echo '<option value="' .( !empty($key)?esc_attr( $key ):"Please select")  . '" ' . selected( $selected_field, $val, false ) . '>' . esc_html( $key ) . '</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="select-options">
							<textarea id="wp_travel_engine_settings[trip_facts][select_options][<?php echo $value;?>]" name="wp_travel_engine_settings[trip_facts][select_options][<?php echo $value;?>]" rows="2" cols="25" required placeholder="<?php _e( 'Enter drop-down values separated by commas','wp-travel-engine' );?>"><?php echo isset( $wp_travel_engine_settings['trip_facts']['select_options'][$value] ) ? esc_attr( $wp_travel_engine_settings['trip_facts']['select_options'][$value] ): '';?></textarea>
						</div>
						<div class="input-placeholder"> 
							<input type="text" name="wp_travel_engine_settings[trip_facts][input_placeholder][<?php echo $value;?>]" value="<?php echo isset( $wp_travel_engine_settings['trip_facts']['input_placeholder'][$value] ) ? esc_attr( $wp_travel_engine_settings['trip_facts']['input_placeholder'][$value] ): '';?>">
						</div>
					</div>
					<a href="#" class="del-li"><i class="far fa-trash-alt"></i></a>
				</li>
			<?php
			$i++;
			}
		}
		?>
	<span id="writefacts"></span>
	</ul>	
</div>
<div id="add_remove_fields">
	<?php
	$other_attributes = array( 'id' => 'add_remove_field' );
	submit_button( 'Add Field', '', '', true, $other_attributes ); ?>
</div>