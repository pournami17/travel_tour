<?php
	$wp_travel_engine_tabs = get_option( 'wp_travel_engine_settings' );
?>
<div class="email-content">
	<h4 class="title">Email Settings</h4>
	<div class="email-template">
		<label for="wp_travel_engine_settings[email][template]"><?php _e( 'Email Template:','wp-travel-engine' ); ?>: <span class="tooltip" title="Choose a template. Click Save Changes then Preview Purchase Receipt to see the new template."><i class="fas fa-question-circle"></i></span></label>
		<div class="select-holder">
			<select id="wp_travel_engine_settings[email][template]" name="wp_travel_engine_settings[email][template]" data-placeholder="<?php esc_attr_e( 'Choose a template type&hellip;', 'wp-travel-engine' ); ?>" class="wc-enhanced-select">
				<?php
				$template = 'default-template';
				if(isset($wp_travel_engine_tabs['email']['template']))
				{
					$template = esc_attr( $wp_travel_engine_tabs['email']['template'] );
				}
				$obj = new Wp_Travel_Engine_Functions();
				$wp_travel_engine_template_options = $obj->wp_travel_engine_template_options();
				foreach ( $wp_travel_engine_template_options as $key => $val ) {
					echo '<option value="' .( !empty($key)?esc_attr( $key ):"Please select")  . '" ' . selected( $template, $key, false ) . '>' . esc_html( $val ) . '</option>';
				}
				?>
			</select>
		</div>
	</div>
</div>