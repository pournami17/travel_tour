<?php
/**
 * WP Travel Engine Template Hooks
 *
 * @package WP_Travel_Engine
 */
class WP_Travel_Engine_Template_Hooks {

    public function __construct() {

        $this->init_hooks();

    }

    /**
     * Initialization hooks.
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'wte_bf_travellers_input_fields', array( $this, 'booking_form_traveller_inputs' ) );
		add_action( 'wte_after_price_info_list', array( $this, 'display_multi_pricing_info' ) );
        add_action('wp_travel_engine_trip_itinerary_template', array($this, 'wte_itinerary_template'));

    }

    /**
     * Booking form traveller input fields.
     *
     * @return void
     */
    public function booking_form_traveller_inputs() {

        global $post;

        $trip_id = $post->ID;

        $post_meta    = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

        // Get trip price.
		$is_sale_price_enabled = wp_travel_engine_is_trip_on_sale( $post->ID );
		$sale_price            = wp_travel_engine_get_sale_price( $post->ID );
		$regular_price         = wp_travel_engine_get_prev_price( $post->ID );
		$price                 = wp_travel_engine_get_actual_trip_price( $post->ID );

        $this->booking_form_multiple_pricing_inputs( $trip_id, $price );

	}

	public function display_multi_pricing_info() {
        $wte_options = get_option( 'wp_travel_engine_settings', true );

        // Bail if disabled.
        if ( ! isset( $wte_options['show_multiple_pricing_list_disp'] ) || '1' != $wte_options['show_multiple_pricing_list_disp'] ) return;

        global $post;
		// Don't show the child price info, if the multi pricing is for child is set.
		$trip_settings = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
		$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;
		if ( $multiple_pricing_options ) :
			foreach( $multiple_pricing_options as $multiple_pricing ) :
				if ( 'Adult' === $multiple_pricing['label'] || '' === $multiple_pricing["price"] ) continue;

				$is_sale = false;
				if ( isset( $multiple_pricing['enable_sale'] ) && '1' === $multiple_pricing['enable_sale'] ) {
					$is_sale = true;
				}

				if ( isset( $multiple_pricing['sale_price'] ) ) {
					$sale_price = apply_filters( 'wte_multi_pricing', $multiple_pricing['sale_price'], $post->ID );
				}

				if ( isset( $multiple_pricing["price"] ) ) {
					$regular_price = apply_filters( 'wte_multi_pricing', $multiple_pricing["price"], $post->ID );
				}

				$price = $regular_price;
				if ( $is_sale ) {
					$price = $sale_price;
				}
			?>
			<?php $a = 1; ?>
			<div class="wpte-bf-price">
				<?php if ( $is_sale ) : ?>
					<del>
					<?php echo wp_travel_engine_get_formated_price_with_currency_code( $regular_price ); ?>
					</del>
				<?php endif; ?>
					<ins>
				<?php echo wp_travel_engine_get_currency_code(); ?><b> <?php echo wp_travel_engine_get_formated_price_separator( $price ); ?></b>
			</ins>
			<span class="wpte-bf-pqty">Per <?php echo esc_html( $multiple_pricing['label'] ); ?></span>
		</div>

	<?php
			endforeach;
		endif;
	}

    /**
     * Load booking form input fields
     *
     * @return void
     */
    public function booking_form_default_traveller_inputs( $price ) {

        ?>
            <div class="wpte-bf-traveler-block">
                <div class="wpte-bf-traveler">
                    <div class="wpte-bf-number-field">
                        <input type="text" name="add-member" value="1" min="1" max="99999999999999"
                            disabled
                            data-cart-field = "travelers"
                            data-cost-field = 'travelers-cost'
                            data-type = '<?php echo apply_filters( 'wte_default_traveller_type', __( 'Person', 'wp-travel-engine' ) ); ?>'
                            data-cost="<?php echo esc_attr( $price ); ?>" />
                        <button class="wpte-bf-plus">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
                        </button>
                        <button class="wpte-bf-minus">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
                        </button>
                    </div>
                    <span><?php echo apply_filters( 'wte_default_traveller_type', __( 'Person', 'wp-travel-engine' ) ); ?></span>
                </div>
                <div class="wpte-bf-price">
                    <ins>
                        <?php echo wp_travel_engine_get_currency_code(); ?><b> <?php echo wp_travel_engine_get_formated_price_separator( $price ); ?></b>
                    </ins>
                    <span class="wpte-bf-pqty"><?php echo apply_filters( 'wte_default_traveller_unit', __( 'Per Person', 'wp-travel-engine' ) ); ?></span>
                </div>
            </div>
        <?php do_action( 'wpte_after_travellers_input' );

    }

    /**
     * Multiple pricing input fields.
     *
     * @return void
     */
    public function booking_form_multiple_pricing_inputs( $trip_id, $default_price ) {

        $trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
        $multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;
        $multiple_pricing_is_adult_price_available = $this->multiple_pricing_is_adult_price_available( $trip_id );

        if ( $multiple_pricing_options && $multiple_pricing_is_adult_price_available ) :
            foreach( $multiple_pricing_options as $key => $pricing_option ) :
                $min_pax     = isset( $pricing_option['min_pax'] ) && ! empty( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : 0;
                $max_pax     = isset( $pricing_option['max_pax'] ) && ! empty( $pricing_option['max_pax'] ) ? $pricing_option['max_pax'] : 999999999;
                $enable_sale = isset( $pricing_option['enable_sale'] ) && '1' == $pricing_option['enable_sale'] ? true : false;

                $price         = $enable_sale && isset( $pricing_option['sale_price'] ) && ! empty( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : $pricing_option['price'];
                $pricing_label = isset( $pricing_option['label'] ) ? $pricing_option['label'] : ucfirst( $key );
                $value         = 'adult' === $key ? '1' : 0;
                $min_pax       = 'adult' === $key ? '1' : 0;

                $pricing_type = isset( $pricing_option['price_type'] ) && ! empty( $pricing_option['price_type'] ) ? $pricing_option['price_type'] : 'per-person';

				if ( '' === $price ) continue;

				$price = apply_filters( 'wte_multi_pricing', $price, $trip_id );
                ?>
                    <div class="wpte-bf-traveler-block">
                        <div class="wpte-bf-traveler">
                            <div class="wpte-bf-number-field">
                                <input type="text" name="add-member" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $min_pax ) ?>" max="<?php echo esc_attr( $max_pax ); ?>"
                                    disabled
                                    data-cart-field = "pricing_options[<?php echo esc_attr( $key ); ?>][pax]"
                                    data-cost-field = 'pricing_options[<?php echo esc_attr( $key ); ?>][cost]'
                                    data-type = '<?php echo esc_attr( $key ); ?>'
                                    data-cost="<?php echo esc_attr( $price ); ?>"
                                    data-pricing-type="<?php echo esc_attr( $pricing_type ); ?>"/>
                                <button class="wpte-bf-plus">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
                                </button>
                                <button class="wpte-bf-minus">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
                                </button>
                            </div>
                            <span><?php echo esc_html( $pricing_label ); ?></span>
                        </div>
                        <div class="wpte-bf-price">
                            <ins>
                                <?php echo wp_travel_engine_get_currency_code(); ?><b> <?php echo wp_travel_engine_get_formated_price_separator( $price ); ?></b>
                            </ins>
                            <span class="wpte-bf-pqty"><?php echo apply_filters( 'wte_default_pricing_option_unit_'. $key, sprintf( __( 'Per %1$s', 'wp-travel-engine' ), $pricing_label ) ); ?></span>
                        </div>
                    </div>
                <?php
			endforeach;
        else :
            $this->booking_form_default_traveller_inputs( $default_price );
        endif;

    }

    /**
     * Check if adult price available in multiple pricing
     *
     * @return void
     */
    public function multiple_pricing_is_adult_price_available( $trip_id ) {

        $trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
        $multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;

        if ( ! $multiple_pricing_options ) return false;

        if ( isset( $multiple_pricing_options['adult'] ) ) {

            $pricing_option = $multiple_pricing_options['adult'];
            $enable_sale    = isset( $pricing_option['enable_sale'] ) && '1' == $pricing_option['enable_sale'] ? true : false;
            $price         = $enable_sale && isset( $pricing_option['sale_price'] ) && ! empty( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : $pricing_option['price'];

            return ! empty( $price );

        }
        return false;
    }

    /**
     * Function to call Advanced itinerary template up on front or default parent template
     *
     * @return void
     */
    public function wte_itinerary_template() {
        $itinerary_template = apply_filters('wte_trip_itinerary_template_path', WP_TRAVEL_ENGINE_BASE_PATH . '/includes/templates/single-trip/trip-tabs/itinerary-tab.php');
        include $itinerary_template;
    }

}

new WP_Travel_Engine_Template_Hooks();
