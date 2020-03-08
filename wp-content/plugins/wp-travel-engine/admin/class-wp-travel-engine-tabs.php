<?php
class Wp_Travel_Engine_Tabs{

	function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'wpte_add_trip_pricing_meta_boxes' ) );
	    add_action( 'add_meta_boxes', array( $this, 'wpte_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'wp_travel_engine_save_trip_price_meta_box_data' ) );
		add_action( 'save_post', array( $this, 'wp_travel_engine_save_meta_box_data' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpte_add_enquiry_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'wp_travel_engine_save_enquiry_meta_box_data' ) );
	}

	/**
	 * Adds metabox for trip pricing.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_trip_pricing_meta_boxes(){
		$screens = array( 'trip' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'trip_pricing_id',
				__( 'Trip Data', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_trip_price_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_trip_price_metabox_callback($tab_args){
		?>
		<div class="tabs">
	    	<ul>
		        <li>
		            <a href="#general"><?php _e('General','wp-travel-engine');?></a>
		        </li>
				<li>
		            <a href="#trip-prices"><?php _e('Prices','wp-travel-engine');?></a>
		        </li>
		        <li>
		            <a href="#trip-info"><?php _e('Trip Info','wp-travel-engine');?></a>
		        </li>
		        <li>
		            <a href="#trip-tabs"><?php _e('Trip Tabs','wp-travel-engine');?></a>
		        </li>
		        <li>
		            <a href="#trip-gallery"><?php _e('Gallery','wp-travel-engine');?></a>
		        </li>
		        <li>
		            <a href="#trip-map"><?php _e('Map','wp-travel-engine');?></a>
		        </li>
		        <?php do_action('add_extra_services_tab'); ?>
	        </ul>
		    <div class="post-trip-setting">
		        <?php
				include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/trip-price.php';
				include WP_TRAVEL_ENGINE_BASE_PATH . '/admin/meta-parts/trip-pricing-options.php';
				include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/trip-facts.php';
				include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/tabs.php';
				include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/galleries.php';
				include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/maps.php';
				do_action('add_extra_services_trips');
				?>
			</div>
    	</div>
	<?php
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_trip_price_meta_box_data( $post_id ) {
	    /*
	     * We need to verify this came from our screen and with proper authorization,
	     * because the save_post action can be triggered at other times.
	     */
	    // Sanitize user input.
	    if(isset($_POST['wp_travel_engine_setting']))
	    {
		    $obj = new Wp_Travel_Engine_Functions;
		    $settings = $obj->wte_sanitize_array( $_POST['wp_travel_engine_setting'] );
		    update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );

		    $cost = $settings['trip_price'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_price',$cost);

			$prev_cost = $settings['trip_prev_price'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_prev_price',$prev_cost);

			$duration = $settings['trip_duration'];
			update_post_meta($post_id,'wp_travel_engine_setting_trip_duration',$duration);

			if(isset($_POST['wpte_gallery_id'])) {
		      update_post_meta($post_id, 'wpte_gallery_id', $_POST['wpte_gallery_id']);
		    } else {
		      delete_post_meta($post_id, 'wpte_gallery_id');
		    }
	    }
	}

	/**
	 * Adds metabox for tabs.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_meta_boxes(){
		$screens = array( 'trip' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'trip_tab_id',
				__( '', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_metabox_callback($tab_args){
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_meta_box_data( $post_id ) {

	    /*
	     * We need to verify this came from our screen and with proper authorization,
	     * because the save_post action can be triggered at other times.
	     */
	    // Sanitize user input.
	    if(isset($_POST['wp_travel_engine_setting']))
	    {
		    $obj = new Wp_Travel_Engine_Functions;
		    $settings = $obj->wte_sanitize_array( $_POST['wp_travel_engine_setting'] );
		    update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );
	    }
	}


	/**
	 * Adds metabox for enquiries.
	 *
	 * @since 1.0.0
	 */
	function wpte_add_enquiry_meta_boxes(){
		$screens = array( 'enquiry' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'enquiry_tab_id',
				__( 'Enquiry details', 'wp-travel-engine' ),
				array($this,'wp_travel_engine_enquiry_metabox_callback'),
				$screen,
				'normal',
				'high'
			);
		}
	}

	// Tab for notice listing and settings
	public function wp_travel_engine_enquiry_metabox_callback($tab_args){
		include WP_TRAVEL_ENGINE_BASE_PATH.'/admin/meta-parts/enquiry.php';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wp_travel_engine_save_enquiry_meta_box_data( $post_id ) {

	    /*
	     * We need to verify this came from our screen and with proper authorization,
	     * because the save_post action can be triggered at other times.
	     */
	    // Sanitize user input.
	    if(isset($_POST['wp_travel_engine_setting']))
	    {
		    $obj = new Wp_Travel_Engine_Functions;
		    $settings = $obj->wte_sanitize_array( $_POST['wp_travel_engine_setting'] );
		    update_post_meta( $post_id, 'wp_travel_engine_setting', $settings );
	    }
	}
}

new Wp_Travel_Engine_Tabs();
