<?php
/**
 *
 * This class defines all hooks for archive page of the trip.
 *
 * @since      1.0.0
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 * @author     WP Travel Engine <https://wptravelengine.com/>
 */
/**
* 
*/
class Wp_Travel_Engine_Archive_Hooks
{
	function __construct()
	{
		add_action( 'wp_travel_engine_trip_archive_outer_wrapper', array( $this, 'wp_travel_engine_trip_archive_wrapper' ) );
		
		add_action( 'wp_travel_engine_trip_archive_wrap', array( $this, 'wp_travel_engine_trip_archive_wrap' ) );
		
		add_action( 'wp_travel_engine_trip_archive_outer_wrapper_close', array( $this, 'wp_travel_engine_trip_archive_outer_wrapper_close' ) );
	}


	/**
     * Main wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_wrapper()
	{ ?>
		<div id="wte-crumbs">
            <?php
            do_action('wp_travel_engine_breadcrumb_holder');
            ?>
		</div>
		<div id="wp-travel-trip-wrapper" class="trip-content-area" itemscope itemtype="http://schema.org/ItemList">
            <div class="wp-travel-inner-wrapper">
	<?php
	}

	/**
     * Inner wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_wrap()
	{ ?>
		<div class="wp-travel-engine-archive-outer-wrap">
			<header class="page-header">
				<?php
					echo '<h1 class="page-title" itemprop="name">'.__('Trips','wp-travel-engine').'</h1>';
					the_archive_description( '<div class="taxonomy-description" itemprop="description">', '</div>' );
				?>
			</header><!-- .page-header -->
			<?php // do_action('wte_advanced_search'); ?>
			<div class="wp-travel-engine-archive-repeater-wrap">
			<?php
			global $post;
            $j = 1;
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $default_posts_per_page = get_option( 'posts_per_page' );
			$wte_doc_tax_post_args = array(
                'post_type'      => 'trip', // Your Post type Name that You Registered
                'posts_per_page' => $default_posts_per_page,
                'order'          => apply_filters('wpte_archive_trips_order','ASC'),
                'orderby'        => apply_filters('wpte_archive_trips_order_by','date'),
                'paged'          => $paged
			);
			$wte_doc_tax_post_qry = new WP_Query($wte_doc_tax_post_args);
		    if($wte_doc_tax_post_qry->have_posts()) :
		       while($wte_doc_tax_post_qry->have_posts()) :
		            $wte_doc_tax_post_qry->the_post(); 
			// Start the Loop.
			// while ( have_posts() ) : the_post();
				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
                Wp_Travel_Engine_Functions::get_template( 'content-grid.php', array( 'j' => $j, 'post' => $post ));
                $j++;

				endwhile; 

            endif;?>
			</div>
		</div>
        <div class="trip-pagination">
			<?php
			$obj = new Wp_Travel_Engine_Functions;
			$obj->pagination_bar( $wte_doc_tax_post_qry );
	        ?>
        </div>
    <?php
    }

	/**
     * Oter wrap of the archive.
     *
     * @since    1.0.0
     */
	function wp_travel_engine_trip_archive_outer_wrapper_close()
	{ ?>

		</div><!-- wp-travel-inner-wrapper -->
		</div><!-- .wp-travel-trip-wrapper -->
	<?php
	}
}
new Wp_Travel_Engine_Archive_Hooks();