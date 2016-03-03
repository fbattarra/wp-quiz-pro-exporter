<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.battarra.it
 * @since      1.0.0
 *
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/admin
 * @author     Fabio Battarra <fabio.battarra@gmail.com>
 */
class Wp_Pro_Quiz_Export_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Pro_Quiz_Export_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Pro_Quiz_Export_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-pro-quiz-export-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Pro_Quiz_Export_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Pro_Quiz_Export_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-pro-quiz-export-admin.js', array('jquery'), $this->version, false);
    }

    /**
     *  Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    function wp_quiz_pro_export_menu()
    {
        add_submenu_page('wpProQuiz', 'CSV Export utility', 'Statistics &#8680; CSV', 'manage_options', $this->plugin_name, array($this, 'display_plugin_page'), '', 7);
    }

    function wp_quiz_pro_export_csv()
    {
        include_once('partials/wp-pro-quiz-export-admin-csv.php');
    }

    /**
     * Render the main page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_page()
    {
        include_once( 'partials/wp-pro-quiz-export-admin-display.php' );
    }
}
