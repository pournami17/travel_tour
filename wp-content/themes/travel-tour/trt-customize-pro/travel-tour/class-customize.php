<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Travel_Tour_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		require get_template_directory() . '/trt-customize-pro/travel-tour/section-pro.php';

		// Register custom section types.
		$manager->register_section_type( 'Travel_Tour_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Travel_Tour_Customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'title'    => esc_html__( 'Travel Tour', 'travel-tour' ),
					'pro_text' => esc_html__( 'Upgrade to Pro', 'travel-tour' ),
					'pro_url'  => esc_url( 'https://thebootstrapthemes.com/downloads/travel-tour-pro/' ),
					'priority' => 1
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'travel-tour-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'trt-customize-pro/travel-tour/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'travel-tour-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'trt-customize-pro/travel-tour/customize-controls.css' );
	}
}

// Doing this customizer thang!
Travel_Tour_Customize::get_instance();
