<?php
/*
Plugin name: Locate2u Delivery Booking
Plugin URI: 
Description: Booking Page with Locate2u Connections. 
Author: Locate2u Develeopers
Version: 1.0
*/


if( ! class_exists( 'WP_List_Table' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

add_action( 'plugins_loaded', function() {
    Locate2u_Plugin::get_instance();    
    Locate2u_Page_Template::get_instance();

    if ( ! defined( 'L2U_PLUGIN_FILE' ) ) {
        define( 'L2U_PLUGIN_FILE',  __FILE__  );
    }    
    require_once dirname( L2U_PLUGIN_FILE ) . '/public/locate2u-main.php';

} );


include( 'public/locate2u-initialize.php' );
register_activation_hook( __FILE__, 'l2u_plugin_install' );

register_activation_hook( __FILE__, function() {
	
} );




class Locate2u_Plugin 
{
    static $instance;

    public $list_obj;

    public function __construct()
    {
        add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    }


    public function plugin_menu()
    {
        add_menu_page(
            $page_title = 'Locate2u Booking',
            $menu_title = 'Locate2u Booking',
            $capability = 'manage_options',
            $menu_slug  = 'locate2u-plugin',
            $function   = 'locate2u_plugin',
            $icon_url   = plugins_url('/assets/images/plugin-icon.svg', __FILE__),
            $position   = 4
        );

		
    }

    public static function get_instance()
    {
        if( ! isset( self::$instance) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

	public static function check_slug( $post_name, $post_type ) 
    {
        global $wpdb;

        if( $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "' AND post_type = '" . $post_type . "'", 'ARRAY_A') ) {
            return true;
        } else {
            return false;
        }
    }
}




class Locate2u_Page_Template {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new Locate2u_Page_Template();
		}

		return self::$instance;

	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);


		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'view_project_template')
		);


		// Add your templates to this array.
      
        $stop_template_url = 'page-templates/stop-template.php';
		$shipment_template_url = 'page-templates/shipment-template.php';
		$thankyou_template_url = 'page-templates/thank-you.php';
		$this->templates = array(
			$stop_template_url => 'Stop Template',
			$shipment_template_url => 'Shipment Template',
			$thankyou_template_url => 'Thank You Page',
		);

	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		// Return the search template if we're searching (instead of the template for the first result)
		if ( is_search() ) {
			return $template;
		}

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		// Allows filtering of file path
		$filepath = apply_filters( 'page_templater_plugin_dir_path', plugin_dir_path( __FILE__ ) );

		$file =  $filepath . get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

}





