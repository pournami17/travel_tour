<?php
/**
* The template for displaying trips archive page
*
* @package Wp_Travel_Engine
* @subpackage Wp_Travel_Engine/includes/templates
* @since 1.0.0
*/
get_header(); ?>
<div id="wte-crumbs">
    <?php
        do_action('wp_travel_engine_breadcrumb_holder');
    ?>
</div>
<div id="wp-travel-trip-wrapper" class="trip-content-area" itemscope itemtype="http://schema.org/ItemList">
    <div class="wp-travel-inner-wrapper">
        <div class="wp-travel-engine-archive-outer-wrap">
            <div class="details">
                <?php
                $wp_travel_engine_setting_option_setting = get_option( 'wp_travel_engine_settings', true );  
                $obj = new Wp_Travel_Engine_Functions();              
                $termID = get_queried_object()->term_id; // Parent A ID
                $term = get_term( $termID );
                $taxonomyName = $term->taxonomy;
                $terms = get_terms('activities');
                $act_terms = array();
                $count = '';
                $j = 1;
                if ( !empty( $terms ) && !is_wp_error( $terms ) ){
                    foreach ( $terms as $term ) {
                        $act_terms[] = $term->term_id;
                    }
                } 

                $order = apply_filters('wpte_activities_terms_order','ASC');
                $orderby = apply_filters('wpte_activities_terms_order_by','date');
                $terms = get_terms('activities', array('orderby' => $orderby, 'order' => $order));
                $wte_trip_cat_slug = get_queried_object()->slug;
                $wte_trip_cat_name = get_queried_object()->name;
                ?>
                    <div class="page-header">
                        <div id="wte-crumbs">
                            <?php
                        do_action('wp_travel_engine_beadcrumb_holder');
                        ?>
                        </div>
                        <h1 class="page-title" itemprop="name">
                            <?php echo esc_attr( $wte_trip_cat_name ); ?>
                        </h1>
                        <?php 
                        $image_id = get_term_meta ( $termID, 'category-image-id', true );
                        if(isset($image_id) && $image_id !='' && isset($wp_travel_engine_setting_option_setting['tax_images']) && $wp_travel_engine_setting_option_setting['tax_images']!='' )
                        {
                            $destination_banner_size = apply_filters('wp_travel_engine_template_banner_size', 'full');
                            echo wp_get_attachment_image ( $image_id, $destination_banner_size );
                        } ?>
                    </div>
                    <?php 
                    $term_description = term_description( $termID, 'destination' ); ?>
                    <div class="parent-desc" itemprop="description">
                        <p>
                            <?php echo isset( $term_description ) ?  $term_description:'';?>
                        </p>
                    </div>
                    <?php
                $default_posts_per_page = get_option( 'posts_per_page' );
                $wte_trip_cat_slug = get_queried_object()->slug;
                if( isset($terms) && $terms!='' && is_array($terms) )
                {
                    foreach( $terms as $term ) {
                        $args = array(
                            'post_type'      => 'trip',
                            'order'          => apply_filters('wpte_destination_trips_order','ASC'),
                            'orderby'        => apply_filters('wpte_destination_trips_order_by','date'),
                            'post_status'    => 'publish',
                            'posts_per_page' => $default_posts_per_page,
                            'tax_query'      => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy'    =>  $taxonomyName,
                                    'field'       => 'slug',
                                    'terms'       => $wte_trip_cat_slug
                                ),
                                array(
                                    'taxonomy'    => 'activities',
                                    'field'       => 'slug',
                                    'terms'       => array( $term->slug )
                                )
                            )
                        );
                        $my_query = new WP_Query($args);
                        $count = $my_query->found_posts;
                        if ($my_query->have_posts()) { ?>
                            <h2 class="activity-title"><span><?php echo esc_attr($term->name);?></span></h2>
                            <div class="wrap">
                                <div class="child-desc">
                                    <p>
                                        <?php echo html_entity_decode(term_description( $term->term_id, 'activities' ));?>
                                    </p>
                                </div>
                                <div class="grid <?php echo esc_attr($term->term_id);?>" data-id="<?php echo $my_query->max_num_pages; ?>">
                                    <?php
                                        while ($my_query->have_posts()) : $my_query->the_post(); 
                                            global $post;
                                            Wp_Travel_Engine_Functions::get_template( 'content-grid.php', array( 'j' => $j, 'post' => $post, 'destination' => true ));
                                            $j++;
                                            
                                        endwhile;
                                        if( $count > $default_posts_per_page )
                                        {
                                            echo '<div class="load-destination"><span>'.__('Load More Trips','wp-travel-engine').'</span></div>';
                                        }
                                        wp_reset_postdata();wp_reset_query();
                                        ?>
                                </div>
                            </div>

                            <?php
                        } // END if have_posts loop
                        ?>
                                <?php
                    //end
                    }
                }
                
                $args = array(
                    'post_type'      => 'trip',
                    'order'          => apply_filters('wpte_destination_trips_order','ASC'),
                    'orderby'        => apply_filters('wpte_destination_trips_order_by','date'),
                    'post_status'    => 'publish',
                    'posts_per_page' => $default_posts_per_page,
                    'tax_query'           => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy'    =>  $taxonomyName,
                            'field'       => 'slug',
                            'terms'       => $wte_trip_cat_slug
                        ),
                        array(
                            'taxonomy'    => 'activities',
                            'field'       => 'term_id',
                            'terms'       => $act_terms,
                            'operator'    => 'NOT IN'
                        )
                    )
                );
                $others_query = new WP_Query($args);
                if ($others_query->have_posts()) { ?>
                    <h2 class="activity-title"><span><?php 
                    $other_trips = apply_filters('wp_travel_engine_other_trips_title', __('Other Trips','wp-travel-engine') ); 
                    echo esc_html($other_trips);
                    ?></span></h2>
                    <div class="wrap">
                        <div class="child-desc">
                            <p>
                                <?php $other_trips_desc = apply_filters('wp_travel_engine_other_trips_desc',__('These are other trips.','wp-travel-engine') ); 
                                echo esc_html($other_trips_desc);
                                ?>
                            </p>
                        </div>
                        <div class="grid other" data-id="<?php echo $others_query->max_num_pages; ?>">
                            <?php
                                while ($others_query->have_posts()) : $others_query->the_post(); 
                                    global $post;
                                    Wp_Travel_Engine_Functions::get_template( 'content-grid.php', array( 'j' => $j, 'post' => $post, 'destination' => true ));
                                    $j++;
                                    
                                endwhile;
                                wp_reset_postdata();wp_reset_query();
                                if( $others_query->found_posts > $default_posts_per_page )
                                {
                                    echo '<div class="load-destination"><span>'.__('Load More Trips','wp-travel-engine').'</span></div>';
                                }
                                ?>
                        </div>
                    </div>
                    <?php
                } // END if have_posts loop
                ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();