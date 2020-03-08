<?php
/**
 * Featured Trip Section
 * 
 * @package WP_Travel_Engine
 */
$defaults      = new Travel_Booking_Toolkit_Dummy_Array;
$obj           = new Travel_Booking_Toolkit_Functions;
$ed_demo       = get_theme_mod( 'ed_featured_demo', true );
$title         = get_theme_mod( 'feature_title', __( 'Featured Trip', 'travel-booking-toolkit' ) );
$content       = get_theme_mod( 'feature_desc', __( 'This is the best place to show your other travel packages. You can modify this section from Appearance > Customize > Front Page Settings > Featured Section.', 'travel-booking-toolkit' ) );
$trip_type     = get_theme_mod( 'trip_type', 'select_cat' ); 
$trip_cat      = get_theme_mod( 'featured_cat' );
$no_of_trip    = (int) get_theme_mod( 'no_of_trips', '6' );
$view_detail   = get_theme_mod( 'featured_readmore', __( 'View Detail', 'travel-booking-toolkit' ) );
$view_all      = get_theme_mod( 'featured_view_all', __( 'View All Packages', 'travel-booking-toolkit' ) );
$view_all_link = get_theme_mod( 'featured_view_all_link', '#' );


if( $title || $content || $obj->travel_booking_toolkit_is_wpte_activated() ){ ?>
    <section id="featured-trip-section" class="featured-trip">
    	<div class="container">
    		
            <?php if( $title || $content ){ ?>
            <header class="section-header">
    			<?php 
                    if( $title ) echo '<h2 class="section-title">' . esc_html( travel_booking_toolkit_get_featured_title() ) . '</h2>';
                    if( $content ) echo '<div class="section-content">' . wp_kses_post( travel_booking_toolkit_get_featured_content() ) . '</div>'; 
                ?>
    		</header>
            <?php }

            for( $i=1; $i<= $no_of_trip; $i++ ){
                    $trip_posts[]  = get_theme_mod( 'choose_trip_'.$i );
            }

            $args = array(); // Initialize an empty array 

            if( $trip_type == 'select_cat' && $trip_cat ) {
                $args = array( 
                    'post_type'       => 'trip',
                    'post_status'     => 'publish',
                    'posts_per_page'  => $no_of_trip,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'activities',
                            'terms' => $trip_cat,
                        )    
                    ), 
                );
            }elseif(  $trip_type == 'select_trips' && ! empty( $trip_posts ) ){
                $args = array( 
                    'post_type'       => 'trip',
                    'post__in'        => $trip_posts,
                    'post_status'     => 'publish',
                    'posts_per_page'  => count( $trip_posts ) 
                );
            } 
            global $post;
            $qry = new WP_Query( $args ); 

            if( $obj->travel_booking_toolkit_is_wpte_activated() && $qry->have_posts() ){ 
                $currency = $obj->travel_booking_toolkit_get_trip_currency();
                $new_obj  = new Wp_Travel_Engine_Functions(); 
                ?>
                <div class="grid">
        			<?php 
                        while( $qry->have_posts() ){ 
                            $qry->the_post(); 
                            $code = $new_obj->trip_currency_code( get_post() );
                            $meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true ); ?>			
                			<div class="col">            				
                                <div class="img-holder">
                					<a href="<?php the_permalink(); ?>">
                                        <?php
                                            $featured_trip_image_size =  apply_filters( 'tbt_featured_trip_image_size', 'thumbnail' ); 
                                            if( has_post_thumbnail() ){
                                                the_post_thumbnail( $featured_trip_image_size );                        
                                            }else{ ?>
                                                <img src="<?php echo esc_url( TBT_FILE_URL . '/includes/images/popular-package-image-size.jpg' ); ?>" alt="<?php the_title_attribute(); ?>" />    
                                                <?php 
                                            } 
                                        ?>
                                    </a>
                                    <?php 
                                        if( ( isset( $meta['trip_prev_price'] ) && $meta['trip_prev_price'] ) && ( isset( $meta['sale'] ) && $meta['sale'] ) && ( isset( $meta['trip_price'] ) && $meta['trip_price'] ) ){ 
                                            $diff = (int)( $meta['trip_prev_price'] - $meta['trip_price'] );
                                            $perc = (float)( ( $diff / $meta['trip_prev_price'] ) * 100 );  
                                            echo '<div class="discount-amount">';
                                            printf( __( '%1$s&percnt; Off', 'travel-booking-toolkit' ), round( $perc ) ); 
                                            echo '</div>';  
                                        }
                                    ?>
                                </div>
                                <div class="text-holder">
                                    <div class="price-info">
                                    <?php 
                                        $obj->travel_booking_toolkit_trip_symbol_options( get_the_ID(), $code, $currency );

                                        if( $obj->travel_booking_toolkit_is_wpte_gd_activated() && isset( $meta['group']['discount'] ) && isset( $meta['group']['traveler'] ) && ! empty( $meta['group']['traveler'] ) ){ ?>
                                            <span class="group-discount"><span class="tooltip"><?php _e( 'You have group discount in this trip.', 'travel-booking-toolkit' ) ?></span><?php _e( 'Group Discount', 'travel-booking-toolkit' ) ?></span>
                                            <?php
                                        }
                                    ?>
                                    </div>

                                    <div class="trip-info">
                                        <?php
                                        if( $obj->travel_booking_toolkit_is_wpte_tr_activated() )
                                        { ?>
                                        <div class="star-holder">
                                        <?php
                                            global $post;
                                            $comments = get_comments( array(
                                                'post_id' => $post->ID,
                                                'status' => 'approve',
                                            ) );
                                            if ( !empty( $comments ) ){
                                                echo '<div class="review-wrap"><div class="average-rating">';
                                                $sum = 0;
                                                $i = 0;
                                                foreach($comments as $comment) {
                                                    $rating = get_comment_meta( $comment->comment_ID, 'stars', true );
                                                    $sum = $sum+$rating;
                                                    $i++;
                                                }
                                                $aggregate = $sum/$i;
                                                $aggregate = round($aggregate,2);

                                                echo 
                                                '<script>
                                                    jQuery(document).ready(function($){
                                                        $("#feat-agg-rating-'.get_the_ID().'").rateYo({
                                                            rating: '.floatval($aggregate).'
                                                        });
                                                    });
                                                </script>';
                                                echo '<div id="feat-agg-rating-'.get_the_ID().'" class="agg-rating"></div><div class="aggregate-rating">
                                                <span class="rating-star">'.$aggregate.'</span><span>'.$i.'</span> '. esc_html( _nx( 'review', 'reviews', $i, 'reviews count', 'travel-booking-toolkit' ) ) .'</div>';
                                                echo '</div></div><!-- .review-wrap -->';
                                            }
                                        ?>  
                                        </div>
                                        <?php } ?>
                                        <h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                        <div class="meta-info">
                                            <?php 
                                                $destinations = wp_get_post_terms( get_the_ID(), 'destination' );

                                                if( ! empty( $destinations ) ){
                                                    foreach ($destinations as $destination ){
                                                        echo '<span class="place"><i class="fa fa-map-marker"></i>'. esc_html( $destination->name ) .'</span>';
                                                    }
                                                }
                                                if( isset( $meta['trip_duration'] ) && '' != $meta['trip_duration'] ){
                                                    echo '<span class="time"><i class="fa fa-clock-o"></i>';
                                                    printf( esc_html( _nx( '%1$s Day', '%1$s Days', absint( $meta['trip_duration'] ), 'trip duration', 'travel-booking-toolkit' ) ), absint( $meta['trip_duration'] ) );
                                                    echo '</span>';
                                                }  ?>
                                        </div>
                                    </div>

                                    <?php 
                                        if( $obj->travel_booking_toolkit_is_wpte_fsd_activated() ){ 
                                            $starting_dates = get_post_meta( get_the_ID(), 'WTE_Fixed_Starting_Dates_setting', true );

                                            if( isset( $starting_dates['departure_dates'] ) && ! empty( $starting_dates['departure_dates'] ) && isset($starting_dates['departure_dates']['sdate']) ){ ?>
                                                <div class="next-trip-info">
                                                    <h3><?php esc_html_e( 'Next Departure', 'travel-booking-toolkit' ); ?></h3>
                                                    <ul class="next-departure-list">
                                                        <?php
                                                            $wte_option_settings = get_option('wp_travel_engine_settings', true);
                                                            $sortable_settings   = get_post_meta( get_the_ID(), 'list_serialized', true);

                                                            if(!is_array($sortable_settings))
                                                            {
                                                              $sortable_settings = json_decode($sortable_settings);
                                                            }
                                                            $today = strtotime(date("Y-m-d"))*1000;
                                                            $i = 0;
                                                            foreach( $sortable_settings as $content )
                                                            {
                                                                $new_date = substr( $starting_dates['departure_dates']['sdate'][$content->id], 0, 7 );
                                                                if( $today <= strtotime($starting_dates['departure_dates']['sdate'][$content->id])*1000 )
                                                                {
                                                                    
                                                                    $num = isset($wte_option_settings['trip_dates']['number']) ? $wte_option_settings['trip_dates']['number']:5;
                                                                    if($i < $num)
                                                                    {
                                                                        if( isset( $starting_dates['departure_dates']['seats_available'][$content->id] ) )
                                                                        {
                                                                            $remaining = isset( $starting_dates['departure_dates']['seats_available'][$content->id] ) && ! empty( $starting_dates['departure_dates']['seats_available'][$content->id] ) ?  $starting_dates['departure_dates']['seats_available'][$content->id] . ' ' . __( 'spaces left', 'travel-booking-toolkit' ) : __( '0 space left', 'travel-booking-toolkit' );
                                                                            echo '<li><span class="left"><i class="fa fa-clock-o"></i>'. date_i18n( get_option( 'date_format' ), strtotime( $starting_dates['departure_dates']['sdate'][$content->id] ) ).'</span><span class="right">'. esc_html( $remaining) .'</span></li>';
                                                                        }
                                                                    
                                                                    }
                                                                $i++;
                                                                }
                                                            }
                                                        ?>
                                                    </ul>
                                                </div>
                                            <?php } 
                                        }

                                        if( ! empty( $view_detail ) ){ ?>
                                            <div class="btn-holder">
                                                <a href="<?php the_permalink(); ?>" class="primary-btn readmore-btn"><?php echo esc_html( $view_detail ); ?></a>
                                            </div>
                                    <?php } ?>
                                </div>
                			</div>
                			<?php 
                        }
                        wp_reset_postdata();
                    ?>
        		</div>
            <?php
            }elseif( $ed_demo ){
                //Default
                $i = 1;
                $featured = $defaults->travel_booking_toolkit_default_trip_featured_posts(); ?>
                <div class="grid">
                    <?php foreach( $featured as $v ){ ?>
                    <div class="col">
                        <div class="img-holder">
                            <a href="#"><img src="<?php echo esc_url( $v['img'] ); ?>" alt="<?php echo esc_attr( $v['title'] ) ?>"></a>
                            <div class="discount-amount"><?php echo esc_html( $v['discount'] ) ?></div>
                        </div>
                        <div class="text-holder">
                            <div class="price-info">
                                <span class="price-holder">
                                    <span class="old-price"><?php echo esc_html( $v['old_price'] ) ?></span>
                                    <span class="new-price"><?php echo esc_html( $v['new_price'] ) ?></span>
                                </span>
                                <span class="group-discount"><span class="tooltip"><?php esc_html_e( 'You have group discount in this trip.', 'travel-booking-toolkit' ) ?></span><?php esc_html_e( 'Group Discount', 'travel-booking-toolkit' ) ?></span>
                            </div>
                            <div class="trip-info">
                                <div class="star-holder"><img src="<?php echo esc_url( $v['rating'] ) ?>" alt="<?php esc_html_e( '5 rating', 'travel-booking-toolkit' ) ?>"></div>
                                <h2 class="title"><a href="#"><?php echo esc_html( $v['title'] ) ?></a></h2>
                                <div class="meta-info">
                                    <span class="place"><i class="fa fa-map-marker"></i><?php echo esc_html( $v['destination'] ) ?></span>
                                    <span class="time"><i class="fa fa-clock-o"></i><?php echo esc_html( $v['days'] ) ?></span>
                                </div>
                            </div>
                            <div class="next-trip-info">
                                <h3><?php esc_html_e( 'Next Departure', 'travel-booking-toolkit' ) ?></h3>
                                <ul class="next-departure-list">
                                    <?php 
                                        foreach ( $v['next-trip-info'] as $value ) {
                                        echo '<li>
                                                <span class="left"><i class="fa fa-clock-o"></i>'. esc_html( $value['date'] ) .'</span>
                                                <span class="right">'. esc_html( $value['space_left']).'</span>
                                            </li>';
                                        }
                                    ?>
                                </ul>
                            </div>

                            <?php
                                if( ! empty( $view_detail ) ){ ?>
                                    <div class="btn-holder">
                                        <a href="#" class="primary-btn readmore-btn"><?php echo esc_html( $view_detail ); ?></a>
                                    </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if( $i == $no_of_trip ) break; $i++; } ?>
                </div><!-- .grid -->
                <?php
            }

            $term_link = ( $trip_type == 'select_cat' ) && ( ! empty( $trip_cat ) &&  $obj->travel_booking_toolkit_is_wpte_activated() ) ? get_term_link( absint( $trip_cat ), 'activities' ) : $view_all_link;

            if( $term_link && $view_all ){ ?>
                <div class="btn-holder">
                    <a href="<?php echo esc_url( $term_link ); ?>" class="primary-btn view-all-btn"><?php echo esc_html( travel_booking_toolkit_get_featured_view_all_label() ); ?></a>
                </div>
                <?php 
            }
            ?>
    	</div>
    </section>
<?php 
}