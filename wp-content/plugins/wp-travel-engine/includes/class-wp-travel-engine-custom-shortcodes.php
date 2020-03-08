<?php
/**

* Class for trip custom shortcodes.

*/

class WP_Travel_Engine_Custom_Shortcodes
{
    public function __construct() {

        add_shortcode( 'wte_trip', array( $this, 'wte_trip_shortcodes_callback' ) );
        add_shortcode( 'wte_trip_map', array( $this, 'wte_show_trip_map_shortcodes_callback' ) );
        add_shortcode( 'wte_trip_tax', array( $this, 'wte_trip_tax_shortcodes_callback' ) );
        add_action( 'wte_trip_content_action', array( $this, 'wte_trip_content' ) );
        add_filter( 'body_class', array( $this, 'wte_custom_shortcode_class' ) );

        /**
		 * Checkout Shortcodes.
		 *
		 * @since 2.2.6
		 * Shortcodes for new checkout process.
		 */
		$shortcodes = array(
			'wp_travel_engine_cart'      => __CLASS__ . '::cart',
			'wp_travel_engine_checkout'  => __CLASS__ . '::checkout',
			'wp_travel_engine_dashboard' => __CLASS__ . '::user_account',
		);

		$shortcode = apply_filters( 'wp_travel_engine_cart_shortcodes', $shortcodes );

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

    }

    /**
	 * Cart page shortcode.
	 *
	 * @return string
	 */
	public static function cart() {
		return self::shortcode_wrapper( array( 'WTE_Cart', 'output' ) );
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function checkout( $atts ) {
		return false;
	}
	/**
	 * Add user Account shortcode.
	 *
	 * @return string
	 */
	public static function user_account() {
		return false;
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function Callback function.
	 * @param array    $atts     Attributes. Default to empty array.
	 * @param array    $wrapper  Customer wrapper data.
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'wp-travel',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		// @codingStandardsIgnoreStart
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		// @codingStandardsIgnoreEnd

		return ob_get_clean();
	}

    function wte_custom_shortcode_class($classes){
        global $post;
        if ( is_object( $post ) )
        {
            if ( has_shortcode( $post->post_content, 'wte_trip_tax' ) || has_shortcode( $post->post_content, 'wte_trip' ) ) {
                $classes[] = 'archive';
            }
        }
        
        return $classes;
    }

    //function to display trips
    function wte_show_trip_map_shortcodes_callback($attr)
    {
        $attr = shortcode_atts( array(
          'id' => '',
          ), $attr, 'wte_trip_map' );
        $wp_travel_engine_setting = get_post_meta( $attr['id'],'wp_travel_engine_setting',true );
        ob_start();
        if( isset( $wp_travel_engine_setting['map']['image_url'] ) && $wp_travel_engine_setting['map']['image_url']!='' )
        {   
            $src = wp_get_attachment_image_src( $wp_travel_engine_setting['map']['image_url'],'full' );
            ?>
            <div class="trip-map image">
                <img src="<?php echo esc_url($src[0]); ?>">
            </div>
        <?php
        }

        else if( isset( $wp_travel_engine_setting['map']['image_url'] ) && $wp_travel_engine_setting['map']['image_url']!='' && isset( $wp_travel_engine_setting['map']['iframe'] ) && $wp_travel_engine_setting['map']['iframe']!='' )
        {   
            $src = wp_get_attachment_image_src( $wp_travel_engine_setting['map']['image_url'],'full' );
            ?>
            <div class="trip-map image">
                <img src="<?php echo esc_url($src[0]); ?>">
            </div>
        <?php
        }

        else{
            if( isset( $wp_travel_engine_setting['map']['iframe'] ) && $wp_travel_engine_setting['map']['iframe']!='' )
            {   
                ?>
                <div class="trip-map iframe">
                    <?php echo html_entity_decode($wp_travel_engine_setting['map']['iframe']); ?>
                </div>
            <?php
            }
        }
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    //function to generate shortcode
    function wte_trip_shortcodes_callback( $attr )
    { 
        $attr = shortcode_atts( array(
          'ids' => '',
          ), $attr, 'wte_trip' );

        if ( ! empty( $attr['ids'] ) ) {
            $ids = array();
            $ids = explode(",", $attr['ids']);
            $attr['ids'] = $ids;
        }

        ob_start();

        do_action( 'wte_trip_content_action', $attr );

        $output = ob_get_contents();
        ob_end_clean();

        if ( $output != '' ) {
            return $output;
        }
    }

    //function to generate shortcode
    function wte_trip_tax_shortcodes_callback( $attr )
    { 
        $attr = shortcode_atts( array(
            'activities'  => '',
            'destination' => '',
            'trip_types'  => '',
          ), $attr, 'wte_trip_tax' );

        if ( ! empty( $attr['activities'] ) ) {
            $activities         = array();
            $activities         = explode(",", $attr['activities']);
            $attr['activities'] = $activities;
        }

        if ( ! empty( $attr['destination'] ) ) {
            $destination         = array();
            $destination         = explode(",", $attr['destination']);
            $attr['destination'] = $destination;
        }

        if ( ! empty( $attr['trip_types'] ) ) {
            $trip_types         = array();
            $trip_types         = explode(",", $attr['trip_types']);
            $attr['trip_types'] = $trip_types;
        }

        ob_start();

        do_action( 'wte_trip_content_action', $attr );

        $output = ob_get_contents();
        ob_end_clean();

        if ( $output != '' ) {
            return $output;
        }
    }

    function wte_trip_content($atts)
    {
        if ( ! empty( $atts['ids'] ) ) {
            $args = array( 'post__in' => $atts['ids'], 'orderby' => 'post__in', 'post_type' => 'trip' );
            $query = new WP_Query( $args );
        }

        if(! empty( $atts['activities'] ) || ! empty( $atts['destination'] ) || ! empty( $atts['trip_types'] ))
        {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $default_posts_per_page = get_option( 'posts_per_page' );
            // Query arguments.
            $args = array(
                'post_type'                     => 'trip',
                'posts_per_page'                => $default_posts_per_page,
                'wpse_search_or_tax_query'      => true,
                'paged'                         => $paged
            );

            $taxquery = array('relation' => 'OR');
            if ( ! empty( $atts['activities'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'activities',
                        'field'            => 'term_id',
                        'terms'            => $atts['activities'],
                        'include_children' => false,
                    ));
            }
            if ( ! empty( $atts['destination'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'destination',
                        'field'            => 'term_id',
                        'terms'            => $atts['destination'],
                        'include_children' => false,
                    ));
            }
            if ( ! empty( $atts['trip_types'] ) ) {
                array_push($taxquery,array(
                        'taxonomy'         => 'trip_types',
                        'field'            => 'term_id',
                        'terms'            => $atts['trip_types'],
                        'include_children' => false,
                    ));
            }

            if(!empty($taxquery))
            {
                $args['tax_query'] = $taxquery;
            }
            $query = new WP_Query( $args );
        }

        if( $query->have_posts() ) :
            ?>
            <div class="trip-content-area">
                <div class="grid">
                    <?php
                    global $post;
                    while ( $query->have_posts() ) 
                    {
                        $query->the_post(); 
                        $wp_travel_engine_setting = get_post_meta( $post->ID,'wp_travel_engine_setting',true );
                        $wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );
                        $obj  = new Wp_Travel_Engine_Functions(); ?>                  
                        <div class="col">
                            <div class="holder">
                                <div class="img-holder">
                                    <a href="<?php echo esc_url( get_the_permalink() );?>"><?php
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
                                        $code = 'USD';
                                        $code = $obj->trip_currency_code( $post );
                                        $currency = $obj->wp_travel_engine_currencies_symbol( $code );
                                        $cost = isset( $wp_travel_engine_setting['trip_price'] ) ? $wp_travel_engine_setting['trip_price']: '';
                                        
                                        $prev_cost = isset( $wp_travel_engine_setting['trip_prev_price'] ) ? $wp_travel_engine_setting['trip_prev_price']: '';

                                        if( $cost!='' && isset($wp_travel_engine_setting['sale']) )
                                        {
                                            if( class_exists( 'Wte_Trip_Currency_Converter_Init' ) )
                                            { 
                                                $cost = $obj->convert_trip_price( $post, $cost );
                                            }
                                            echo $obj->wte_trip_symbol_options($code, $currency, $cost);
                                        }
                                        else{ 
                                            if( $prev_cost!='' )
                                            {
                                                if( class_exists( 'Wte_Trip_Currency_Converter_Init' ) )
                                                { 
                                                    $prev_cost = $obj->convert_trip_price( $post, $prev_cost );
                                                }
                                                echo $obj->wte_trip_symbol_options($code, $currency, $prev_cost);
                                            }
                                        }

                                        if (class_exists('Wp_Travel_Engine_Group_Discount') && isset($wp_travel_engine_setting_option_setting['group']['discount'])) {
                                        
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
                                    } ?>      
                                    <h2 class="title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?></a></h2>

                                    <?php
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
                                        <?php 
                                    }

                                    if( class_exists('WTE_Fixed_Starting_Dates') ){
                                        $starting_dates = get_post_meta( get_the_ID(), 'WTE_Fixed_Starting_Dates_setting',true );
                                        
                                        $WTE_Fixed_Starting_Dates_setting = get_post_meta( $post->ID, 'WTE_Fixed_Starting_Dates_setting', true);
                                        $wp_travel_engine_setting_option_setting = get_option('wp_travel_engine_settings', true);
                                        $sortable_settings = get_post_meta( $post->ID, 'list_serialized', true);
                                        $wp_travel_engine_setting = get_post_meta( $post->ID,'wp_travel_engine_setting',true );

                                        if(!is_array($sortable_settings))
                                        {
                                            $sortable_settings = json_decode($sortable_settings);
                                        }

                                        $valid_departure_dates = isset( $starting_dates['departure_dates'] ) && ! empty( $starting_dates['departure_dates'] ) ? $obj->wte_is_fixed_starting_dates_valid( $WTE_Fixed_Starting_Dates_setting, $sortable_settings ) : false;

                                        if( isset( $starting_dates['departure_dates'] ) && ! empty( $starting_dates['departure_dates'] ) && isset($starting_dates['departure_dates']['sdate']) && $valid_departure_dates ){ ?>
                                            <div class="next-trip-info">
                                                <?php echo '<div class="fsd-title">'.esc_html__( 'Next Departure', 'wp-travel-engine' ).'</div>'; ?>
                                                <ul class="next-departure-list">
                                                    <?php
                                                        $today = strtotime(date("Y-m-d"))*1000;
                                                        $i = 0;
                                                        foreach( $sortable_settings as $content )
                                                        {
                                                            $new_date = substr( $WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id], 0, 7 );
                                                            if( $today <= strtotime($WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id])*1000 )
                                                            {
                                                                
                                                                $num = isset($wp_travel_engine_setting_option_setting['trip_dates']['number']) ? $wp_travel_engine_setting_option_setting['trip_dates']['number']:5;
                                                                if($i < $num)
                                                                {
                                                                    if( isset( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) )
                                                                    {
                                                                        $remaining = isset( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) && ! empty( $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] ) ?  $WTE_Fixed_Starting_Dates_setting['departure_dates']['seats_available'][$content->id] . ' ' . __( 'spaces left', 'wp-travel-engine' ) : __( '0 space left', 'wp-travel-engine' );
                                                                        echo '<li><span class="left"><i class="fa fa-clock-o"></i>'. date_i18n( get_option( 'date_format' ), strtotime( $WTE_Fixed_Starting_Dates_setting['departure_dates']['sdate'][$content->id] ) ).'</span><span class="right">'. esc_html( $remaining) .'</span></li>';
                                                                    }
                                                                
                                                                }
                                                            $i++;
                                                            }
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        <?php 
                                        } 
                                    }
                                    ?>
                                    <div class="btn-holder">
                                        <a href="<?php echo esc_url( get_the_permalink() );?>" class="btn-more"><?php _e('View Details','wp-travel-engine');?></a>
                                    </div>   
                                    
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php 
            wp_reset_postdata();
        endif;
    }

}
new WP_Travel_Engine_Custom_Shortcodes;
