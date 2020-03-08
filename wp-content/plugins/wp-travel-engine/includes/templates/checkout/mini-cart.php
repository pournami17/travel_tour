<?php
/**
 * Mini cart template.
 *
 * @package WP Travel Engine.
 */
global $wte_cart;

$cart_items   = $wte_cart->getItems();
$date_format  = get_option( 'date_format' );
$cart_totals  = $wte_cart->get_total();
$wte_settings = get_option( 'wp_travel_engine_settings' );
$extra_service_title = isset( $wte_settings['extra_service_title'] ) ? $wte_settings['extra_service_title'] : __( 'Extra Services', 'wp-travel-engine' );

if ( ! empty( $cart_items ) ) :
?>
    <div class="wpte-bf-book-summary">
        <div class="wpte-bf-summary-wrap">
            <div class="wpte-bf-title"><?php esc_html_e( 'Booking Summary', 'wp-travel-engine' ); ?></div>

            <?php foreach( $cart_items as $key => $cart_item ) : ?>
                <div class="wpte-bf-trip-name-wrap">
                    <div class="wpte-bf-trip-name"><?php echo esc_html( get_the_title( $cart_item['trip_id'] ) ); ?></div>
                    <span class="wpte-bf-trip-date"><?php echo esc_html( sprintf( __( 'Starting Date: %1$s', 'wp-travel-engine' ), date_i18n( $date_format, strtotime( $cart_item['trip_date'] ) ) ) ); ?></span>
                </div>
                <table class="wpte-bf-summary-table">
                    <tbody>
                    <?php foreach( $cart_item['pax'] as $pax_label => $pax ) :
                        if ( $pax == '0' ) continue;

                        $pax_label_disp = $pax_label;

                        if ( isset( $cart_item['multi_pricing_used'] ) && $cart_item['multi_pricing_used'] ) :
                            $pax_label_disp = wte_get_pricing_label_by_key( $cart_item['trip_id'], $pax_label );
                        endif;
                    ?>
                        <tr>
                            <td><span><?php printf( __( '%1$s %2$s', 'wp-travel-engine' ), number_format_i18n( $pax ), ucfirst( $pax_label_disp ) ); ?></span></td>
                            <td><b><?php echo esc_html( wp_travel_engine_get_formated_price_with_currency_code( $cart_item['pax_cost'][ $pax_label ] ) ); ?></b></td>
                        </tr>
					<?php endforeach; ?>

				<!-- Extra Services -->
				<?php if ( isset( $cart_item['trip_extras'] ) && ! empty( $cart_item['trip_extras'] ) ) : ?>
						<tr>
							<td colspan="2"><?php echo esc_html( $extra_service_title ); ?></td>
						</tr>
					<?php foreach( $cart_item["trip_extras"] as $trip_extra ) : ?>
						<tr>
							<td><span><?php echo esc_html( $trip_extra['qty'] ); ?> x <?php echo esc_html( $trip_extra['extra_service'] ); ?></span></td>
							<td><b><?php echo esc_html( wp_travel_engine_get_formated_price_with_currency_code( $trip_extra['qty'] * $trip_extra['price'] ) ); ?></b></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<!-- ./ Extra Services -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <span class="wpte-bf-total-txt"><?php esc_html_e( 'Total :', 'wp-travel-engine' ); ?></span>
                                <span class="wpte-bf-currency"><?php echo wp_travel_engine_get_currency_code(); ?></span>
                                <span class="wpte-bf-price-amt"><?php echo wp_travel_engine_get_formated_price_separator( $cart_totals['sub_total'] ); ?></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php if ( wp_travel_engine_is_trip_partially_payable( $cart_item['trip_id'] ) ) : ?>
                    <table class="wpte-bf-extra-info-table">
                        <tbody>
                            <tr>
                                <td><span><?php echo esc_html__( 'Down Payment', 'wp-travel-engine' ); ?></span></td>
                                <td><b><?php echo wp_travel_engine_get_formated_price_with_currency_code( $cart_totals['total_partial'] ); ?></b></td>
                            </tr>
                            <tr>
                                <td><span><?php esc_html_e( 'Remaining Payment', 'wp-travel-engine' ); ?></span></td>
                                <td><b><?php echo wp_travel_engine_get_formated_price_with_currency_code( ( $cart_totals['sub_total'] - $cart_totals['total_partial'] ) ); ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="wpte-bf-summary-total">
        <?php
            $payable_now = wp_travel_engine_is_trip_partially_payable( $cart_item['trip_id'] ) ? $cart_totals['total_partial'] : $cart_totals['cart_total'];
        ?>
            <div class="wpte-bf-total-price">
                <span class="wpte-bf-total-txt"><?php esc_html_e( 'Total Payable Now :', 'wp-travel-engine' ); ?></span>
                <span class="wpte-bf-currency"><?php echo wp_travel_engine_get_currency_code(); ?></span>
                <span class="wpte-bf-price-amt"><?php echo wp_travel_engine_get_formated_price_separator( $payable_now ); ?></span>
            </div>
        </div><!-- .wpte-bf-summary-total -->
    </div><!-- .wpte-bf-book-summary -->
<?php
endif;
