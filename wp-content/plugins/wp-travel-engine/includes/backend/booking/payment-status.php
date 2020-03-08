<?php
/**
 * Payment Status Metabox.
 * 
 * @package WP_Travel_Engine
 */
global $post;

$wp_travel_engine_postmeta_settings = get_post_meta( $post->ID, 'wp_travel_engine_booking_setting', true );

$payment_status  = get_post_meta( $post->ID, 'wp_travel_engine_booking_payment_status', true );
$payment_gateway = get_post_meta( $post->ID, 'wp_travel_engine_booking_payment_gateway', true );
$payment_details = get_post_meta( $post->ID, 'wp_travel_engine_booking_payment_details', true );

?>

    <table>

        <tr>
            <td><b><?php _e( 'Payment Status:', 'wp-travel-engine' ); ?></b></td>
            <td><?php echo esc_html( $payment_status ); ?></td>
        </tr>

        <tr>
            <td><b><?php _e( 'Payment Gateway:', 'wp-travel-engine' ); ?></b></td>
            <td><?php echo ! empty( $payment_gateway ) ? esc_html( $payment_gateway ) : __( 'N/A', 'wp-travel-engine' ); ?></td>
        </tr>

        <?php 
            if ( ! empty( $payment_details ) && is_array( $payment_details ) ) : 
                foreach( $payment_details as $key => $value ) :
        ?>
                    <tr>
                        <td><b><?php echo esc_html( $value['label'] ); ?>:</b></td>
                        <td><?php echo esc_html( $value['value'] ); ?></td>
                    </tr>
        <?php
                endforeach;
            endif; 
        ?>

    </table>

<?php
