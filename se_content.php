<?php
/*
Plugin Name: SocialEars
Plugin URI: http://www.socialears.com/socialears-wordpress-plugin/
Description: SocialEars Content Analyzer and Blog Title Generator
Version: 1.0.1
Author: SocialEars
Author URI: http://www.socialears.com
*/

wp_enqueue_script('content-analyze','/wp-content/plugins/social_ears/js/functions.js');

/**
 * Install default settings 
 */
function social_ears_activate() {
	$defaults_array = array(
		"analyze_url" => "https://seg1.socialears.com/SE/SE_web/web/content_analyzer",
		"title_generator_url" => "https://seg1.socialears.com/SE/SE_web/web/BlogTitleGenerator",
	);
	add_option( 'se_option', $defaults_array );
}
register_activation_hook( __FILE__, 'social_ears_activate' );

/**
 *  Deactivate plugin
 */
function social_ears_deactivate() {
	delete_option('se_option');
}
register_deactivation_hook( __FILE__, 'social_ears_deactivate' );

/**
 * Add analyze sidebar to the edit page 
 */
function social_ears_sidebar() {
	add_meta_box( 'social_ears', 'Content Analyze', 'social_ears_sidebar_html', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'social_ears_sidebar' );

/**
 * Analyze sidebar HTML code
 * @param type $post 
 */
function social_ears_sidebar_html( $post ){
	$se_options = get_option( 'se_option' );

	echo '<input type="hidden" id="analyze_url" value="'. $se_options["analyze_url"] .'" />
		<input type="hidden" id="title_generator_url" value="'. $se_options["title_generator_url"] .'" />
		<style>#social_ears{display:none}</style>';
}

/**
 * Settings button on plugins panel
 */
function social_ears_links($links, $file) {
	static $this_plugin;
	if( !$this_plugin ){ $this_plugin = plugin_basename( __FILE__ ); }

	if( $file == $this_plugin ) {
		$settings_link = '<a href="options-general.php?page=social-ears">Settings</a>';
		array_unshift( $links, $settings_link );
	}	
	return $links;
}
add_filter( 'plugin_action_links', 'social_ears_links', 10, 2 );


/* Main class */
class social_ears
{
	/**
	* Holds the values to be used in the fields callbacks
	*/
	private $options;	

	/**
	*  Init
	*/
	public function __construct() {		
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	* Add options page
	*/
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Social Ears Settings', 
			'Social Ears Analyze', 
			'manage_options', 
			'social-ears', 
			array( $this, 'create_admin_page' )
		);
	}

	/**
	* Options page callback
	*/
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'se_option' );
	?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Social Ears Settings</h2>   
		<form method="post" action="options.php">
		<?php
			// This prints out all hidden setting fields
			settings_fields( 'se_option_group' );   
			do_settings_sections( 'social-ears' );
			submit_button(); 
		?>
		</form>
		</div>
	<?php
	}

	/**
	* Register and add settings
	*/
	public function page_init(){
		register_setting(
		'se_option_group', // Option group
		'se_option', // Option name
		array( $this, 'sanitize' ) // Sanitize
	);

	add_settings_section(
		'se_setting_section_id', // ID
		'', // Title
		'settings_content', // Callback
		'social-ears' // Page
	);
	add_settings_field(
		'analyze_url', 
		'Analyze URL', 
		array( $this, 'analyze_url_callback' ), 
			'social-ears', 
			'se_setting_section_id'
		);

    add_settings_field(
        'title_generator_url',
        'Title generator URL',
        array( $this, 'title_generator_url_callback' ),
        'social-ears',
        'se_setting_section_id'
    );

    //used in add_settings_section
    function settings_content(){

    }
    }

	/**
	* Sanitize each setting field as needed
	* @param array $input Contains all settings fields as array keys
	*/
	public function sanitize( $input ) {
		if( !empty( $input['analyze_url'] ) ){
			$input['analyze_url'] = esc_url( $input['analyze_url'] );
		}
        if( !empty( $input['title_generator_url'] ) ){
            $input['title_generator_url'] = esc_url( $input['title_generator_url'] );
        }
		return $input;
	}

	/** 
	* Get the settings option array and print one of its values
	*/
	public function analyze_url_callback() {
		printf( '<input type="text" id="analyze_url" name="se_option[analyze_url]" value="%s" size="70" />', esc_attr( $this->options['analyze_url'] ) );
	}

    public function title_generator_url_callback() {
        printf( '<input type="text" id="title_generator_url" name="se_option[title_generator_url]" value="%s" size="70" />', esc_attr( $this->options['title_generator_url'] ) );
    }
}

if( is_admin() ){
	$my_settings_page = new social_ears();
}
