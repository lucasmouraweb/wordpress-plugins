<?php
/*
Plugin Name: Display None Terminator
Description:  Delete all elements with "display: none" from pages.
Version: 1.5
Author: Lucas Moura
*/

/**
 * Options Page
 */

class DisplayNoneTerminator {

	private $display_none_terminator_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'display_none_terminator_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'display_none_terminator_page_init' ) );
	}

	public function display_none_terminator_add_plugin_page() {
		add_menu_page(
			'Display None Terminator', // page_title
			'Display None Terminator', // menu_title
			'manage_options', // capability
			'display-none-terminator', // menu_slug
			array( $this, 'display_none_terminator_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			80 // position
		);
	}

	public function display_none_terminator_create_admin_page() {




	$this->display_none_terminator_options = get_option( 'display_none_terminator_option_name' ); ?>

		<div class="wrap">
			<h2>Display None Terminator</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'display_none_terminator_option_group' );
					do_settings_sections( 'display-none-terminator-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function display_none_terminator_page_init() {
		register_setting(
			'display_none_terminator_option_group', // option_group
			'display_none_terminator_option_name', // option_name
			array( $this, 'display_none_terminator_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'display_none_terminator_setting_section', // id
			'Settings', // title
			array( $this, 'display_none_terminator_section_info' ), // callback
			'display-none-terminator-admin' // page
		);

		add_settings_field(
			'element_id_0', // id
			'Elements to remove by ID or class', // title
			array( $this, 'element_id_0_callback' ), // callback
			'display-none-terminator-admin', // page
			'display_none_terminator_setting_section' // section
		);

		add_settings_field(
			'element_id_exception_1', // id
			'Elements to NOT remove by ID or class', // title
			array( $this, 'element_id_exception_1_callback' ), // callback
			'display-none-terminator-admin', // page
			'display_none_terminator_setting_section' // section
		);



	}

	public function display_none_terminator_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['element_id_0'] ) ) {
			$sanitary_values['element_id_0'] = sanitize_text_field( $input['element_id_0'] );
		}

		if ( isset( $input['element_id_exception_1'] ) ) {
			$sanitary_values['element_id_exception_1'] = sanitize_text_field( $input['element_id_exception_1'] );
		}

		return $sanitary_values;
	}

	public function display_none_terminator_section_info() {
		
	}

	public function element_id_0_callback() {
		printf(
			 '<textarea style="width: 100%%; height: 150px; padding: 10px; overflow-y: scroll;" name="display_none_terminator_option_name[element_id_0]" id="element_id_0" >%s</textarea>',
        isset( $this->display_none_terminator_options['element_id_0'] ) ? esc_attr( $this->display_none_terminator_options['element_id_0']) : ''
		);
	}

	public function element_id_exception_1_callback() {
		printf(
			 '<textarea style="width: 100%%; height: 150px; padding: 10px; overflow-y: scroll;" name="display_none_terminator_option_name[element_id_exception_1]" id="element_id_exception_1" >%s</textarea>',
        isset( $this->display_none_terminator_options['element_id_exception_1'] ) ? esc_attr( $this->display_none_terminator_options['element_id_exception_1']) : ''
		);
	}

}
if ( is_admin() )
	$display_none_terminator = new DisplayNoneTerminator();

/* 
 * Retrieve this value with:
 * $display_none_terminator_options = get_option( 'display_none_terminator_option_name' ); // Array of All Options
 * $element_id_0 = $display_none_terminator_options['element_id_0']; // Element ID
 * $element_id_exception_1 = $display_none_terminator_options['element_id_exception_1']; // Element ID exception
 */


    // add the JS file


function enqueue_display_none_terminator_scripts() {
  $options = get_option( 'display_none_terminator_option_name' );
  wp_enqueue_script( 'display-none-terminator', plugin_dir_url( __FILE__ ) . 'display-none-terminator.js', array( 'jquery' ), '1.0', true );
  wp_localize_script( 'display-none-terminator', 'display_none_terminator_options', array(
      'element_id_0' => $options['element_id_0'],
      'element_id_exception_1' => $options['element_id_exception_1']
  ));
}
add_action( 'wp_enqueue_scripts', 'enqueue_display_none_terminator_scripts' );



function display_none_terminator_enqueue_styles(){
    if ( isset($_GET['page']) && $_GET['page'] == 'display-none-terminator' ) {
        wp_enqueue_style( 'options-page-style', plugin_dir_url( __FILE__ ) . 'options-style.css' );
    }
}
add_action('admin_enqueue_scripts', 'display_none_terminator_enqueue_styles');






?>