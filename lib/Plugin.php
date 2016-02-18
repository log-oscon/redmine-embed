<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PluginName
 * @subpackage PluginName/includes
 */

namespace logoscon\WP\RedmineEmbed;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PluginName
 * @subpackage PluginName/includes
 * @author     Your Name <email@example.com>
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PluginName_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The string used to uniquely identify this plugin.
	 */
	private $name = 'redmine-embed';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of the plugin.
	 */
	private $version = '1.0.0';

	/**
	 * Option key for the plugin.
	 *
	 * @var string
	 */
	private $option_key = 'redmine-embed';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();
		$plugin_i18n->set_domain( $this->get_name() );
		$plugin_i18n->load_plugin_textdomain();

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$settings      = new Admin\Settings( $this );
		$user_settings = new Admin\UserSettings( $this );

		$this->loader->add_action( 'admin_menu', $settings, 'menu' );
		$this->loader->add_action( 'admin_init', $settings, 'add' );

		$this->loader->add_action( 'show_user_profile', $user_settings, 'show_user_profile' );
		$this->loader->add_action( 'edit_user_profile', $user_settings, 'edit_user_profile' );
		$this->loader->add_action( 'personal_options_update', $user_settings, 'edit_user_profile_update' );
		$this->loader->add_action( 'edit_user_profile_update', $user_settings, 'edit_user_profile_update' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_frontend_hooks() {
		$frontend = new Frontend( $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $frontend, 'enqueue_styles' );
		$this->loader->add_action( 'init', $frontend, 'register_embed_handlers' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_frontend_hooks();
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PluginName_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * The name of the option key used to uniquely identify it in the database.
	 *
	 * @since     1.0.0
	 * @return    string    The option key name.
	 */
	public function get_option_key() {
		return $this->option_key;
	}

	/**
	 * Get a plugin option by name.
	 *
	 * @param  string $name    Option name.
	 * @param  mixed  $default Option default if not set.
	 * @return mixed           Option value.
	 */
	public function get_option( $name = null, $default = null ) {
		$options = \get_option( $this->get_option_key(), array() );

		if ( $name === null ) {
			return $options;
		}

		return isset( $options[ $name ] ) ? $options[ $name ] : $default;
	}

	/**
	 * Set a plugin option.
	 *
	 * @param string $name   Option name.
	 * @param mixed  $value  Option value.
	 */
	public function set_option( $name, $value ) {
		$options = \get_option( $this->get_option_key(), array() );
		$options[ $name ] = $value;
		$result = \update_option( $this->get_option_key(), $options, true );
	}

}
