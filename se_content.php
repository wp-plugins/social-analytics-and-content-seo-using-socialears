<?php
/*
Plugin Name: SocialEars
Plugin URI: http://www.socialears.com/socialears-wordpress-plugin/
Description: SocialEars Content Analyzer and Blog Title Generator
Version: 1.0.4
Author: SocialEars
Author URI: http://www.socialears.com
*/

function se_config_js() {
    echo '<script type="text/javascript">var SE_ASSET = "' . plugin_dir_url(__FILE__) . '"</script>';
}
// Add hook for admin <head></head>
add_action('admin_head', 'se_config_js');
wp_enqueue_script('content-analyze', plugin_dir_url(__FILE__) . 'js/functions.js');

/**
 * Install default settings
 */
function social_ears_activate()
{
    $defaults_array = array(
        "analyze_url" => "https://seg1.socialears.com/SE/SE_web/web/content_analyzer",
        "title_generator_url" => "https://seg1.socialears.com/SE/SE_web/web/BlogTitleGenerator",
    );
    add_option('se_option', $defaults_array);
}

register_activation_hook(__FILE__, 'social_ears_activate');

/**
 *  Deactivate plugin
 */
function social_ears_deactivate()
{
    delete_option('se_option');
}

register_deactivation_hook(__FILE__, 'social_ears_deactivate');

/**
 * Add analyze sidebar to the edit page
 */
function social_ears_sidebar()
{
    add_meta_box('social_ears', 'Content Analyze', 'social_ears_sidebar_html', 'post', 'side');
}

add_action('add_meta_boxes', 'social_ears_sidebar');

/**
 * Analyze sidebar HTML code
 * @param type $post
 */
function social_ears_sidebar_html($post)
{
    $se_options = get_option('se_option');

    echo '<input type="hidden" id="analyze_url" value="' . $se_options["analyze_url"] . '" />
		<input type="hidden" id="title_generator_url" value="' . $se_options["title_generator_url"] . '" />
		<style>#social_ears{display:none}</style>';
}

/**
 * Settings button on plugins panel
 */
function social_ears_links($links, $file)
{
    static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="options-general.php?page=social-ears">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'social_ears_links', 10, 2);


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
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Social Ears Settings',
            'Social Ears Analyze',
            'manage_options',
            'social-ears',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {

        if (isset($_POST['se_submit'])) {
            $updated_array = array(
                "analyze_url" => sanitize_text_field($_POST['se_option']['analyze_url']),
                "title_generator_url" => sanitize_text_field($_POST['se_option']['title_generator_url']),
            );

            update_option('se_option', $updated_array); ?>
            <div class="updated"><p><strong>Social Ears Settings were updated</strong></p></div>
            <?php
        }

        //Get current options
        $this->options = get_option('se_option');

        $html = <<<HTML
   <div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Social Ears Settings</h2>
		<form method="post">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">Analyze URL</th>
                        <td>
                            <input type="text" id="analyze_url" name="se_option[analyze_url]" value="{$this->options['analyze_url']}" size="70">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Title generator URL</th>
                        <td>
                            <input type="text" id="title_generator_url" name="se_option[title_generator_url]" value="{$this->options['title_generator_url']}" size="70">
                        </td>
                    </tr>
                </tbody>
            </table>
		    <p class="submit"><input type="submit" name="se_submit" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
    </div>
HTML;
        echo $html;
    }

}

if (is_admin()) {
    $my_settings_page = new social_ears();
}
