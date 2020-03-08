<?php
/**
 * Template part for displaying grid posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */
	$wp_travel_engine_setting = get_post_meta( $post->ID,'wp_travel_engine_setting',true );
	$wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );
    $obj = new Wp_Travel_Engine_Functions();?>

	<div class="col wp-travel-engine-archive-wrap" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <div class="holder">
            <div class="img-holder">
                <a href="<?php echo esc_url( get_the_permalink() );?>" class="trip-post-thumbnail"><?php
                $trip_feat_img_size = apply_filters('wp_travel_engine_archive_trip_feat_img_size','destination-thumb-trip-size');
                $feat_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $trip_feat_img_size );
                if(isset($feat_image_url[0]))
                { ?>
                    <img src="<?php echo esc_url( $feat_image_url[0] );?>">
                <?php
                }
                else{ ?>
                    <img src="<?php echo esc_url(  WP_TRAVEL_ENGINE_IMG_URL . '/public/css/images/trip-listing-fallback.jpg' );?>">
                <?php } ?>
                </a>
                  <?php
                    $code = wp_travel_engine_get_currency_code();
					$currency = wp_travel_engine_get_currency_symbol( $code );

                    $cost = wp_travel_engine_get_actual_trip_price( $post->ID );
                    // Don't show the price, if the price is not set.
                    if ( '' !== trim( $cost ) ) {
                        echo $obj->wte_trip_symbol_options($code, $currency, $cost);
                    }

					if( class_exists( 'Wp_Travel_Engine_Group_Discount' ) && isset( $wp_travel_engine_setting_option_setting['group']['discount'] ) ){

                        if( isset( $wp_travel_engine_setting['group']['discount'] ) && isset( $wp_travel_engine_setting['group']['traveler'] ) && ! empty( $wp_travel_engine_setting['group']['traveler'] ) ){ ?>
                            <span class="group-discount"><span class="tooltip"><?php _e( 'You have group discount in this trip.', 'wp-travel-engine' ) ?></span><?php _e( 'Group Discount', 'wp-travel-engine' ) ?></span>
                            <?php
                        }
                    }
                    ?>
            </div>
            <div class="text-holder">
                <?php
                if(class_exists('Wte_Trip_Review_Init'))
                {
                    $obj->wte_trip_review();
                }

                if( isset( $destination ) && $destination  ){
                    ?><h3 class="title" itemprop="name"><a itemprop="url" href="<?php echo esc_url( get_the_permalink() );?>"><?php the_title();?></a></h3><?php
                }
                else{
                    ?><h2 class="title" itemprop="name"><a itemprop="url" href="<?php echo esc_url( get_the_permalink() );?>"><?php the_title();?></a></h2><?php
                }

                if( ! empty( $j ) ){
                    ?>
                    <meta itemprop="position" content="<?php echo $j; ?>" />
                    <?php
                }
                $nonce = wp_create_nonce( 'wp-travel-engine-nonce' );

                $trip_duration        = isset( $wp_travel_engine_setting['trip_duration'] ) && ! empty( $wp_travel_engine_setting['trip_duration'] ) ? $wp_travel_engine_setting['trip_duration'] : false;
                $trip_duration_nights = isset( $wp_travel_engine_setting['trip_duration_nights'] ) && ! empty( $wp_travel_engine_setting['trip_duration_nights'] ) ? $wp_travel_engine_setting['trip_duration_nights'] : false;

                if( $trip_duration || $trip_duration_nights )
                { ?>
                    <div class="meta-info">
                        <?php
                            echo '<span class="time"><i class="fa fa-clock-o"></i>';
                            if( $trip_duration ){
                                printf( _nx( '%1$d Day', '%1$d Days', $trip_duration, 'trip duration', 'wp-travel-engine' ), $trip_duration );
                            }
                            if( $trip_duration_nights ) {
                                printf( _nx( ' - %1$d Night', ' - %1$d Nights', $trip_duration_nights, 'trip duration night', 'wp-travel-engine' ), $trip_duration_nights );
                            }
                            echo '</span>';
                        ?>
                    </div>
                <?php }

                $trip_starting_dates = wp_travel_engine_get_fixed_departure_dates( get_the_ID() );

                if ( ! empty( $trip_starting_dates ) && is_array( $trip_starting_dates ) ) :

                    echo '<div class="next-trip-info">';
                        echo '<div class="fsd-title">'.esc_html__( 'Next Departure', 'wp-travel-engine' ).'</div>';
                            echo '<ul class="next-departure-list">';

                                foreach( $trip_starting_dates as $key => $date ) :

                                    echo'<li><span class="left"><i class="fa fa-clock-o"></i>'. $date['starting_date'] .'</span><span class="right">' . $date['remaining_seats'] . '</span></li>';

                                endforeach;

                            echo '</ul>';
                    echo '</div>';

                endif;

                ?>
                <div class="btn-holder">
                    <a href="<?php echo esc_url( get_the_permalink() );?>" class="btn-more"><?php _e('View Detail','wp-travel-engine');?></a>
                </div>
            </div>
        </div>
	</div>
	<?php
