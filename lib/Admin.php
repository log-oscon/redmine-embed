<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    PluginName
 * @subpackage PluginName/admin
 */

namespace logoscon\WP\RedmineEmbed;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    PluginName
 * @subpackage PluginName/admin
 * @author     Your Name <email@example.com>
 */
class Admin {

	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Options slug.
	 * @var string
	 */
	private $options_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * 
	 * @param Plugin $plugin This plugin's instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin      = $plugin;
		$this->options_key = $plugin->get_name();
	}

	/**
	 * Register the menu entry for the plugin's settings page.
	 *
	 * @since 1.1.0
	 */
	public function admin_menu() {
		\add_options_page(
			__( 'Redmine Embed', 'redmine-embed' ),
			__( 'Redmine Embed', 'redmine-embed' ),
			'manage_options',
			$this->plugin->get_name(),
			array( $this, 'page_settings' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @uses add_settings_field()
	 * @uses add_settings_section()
	 * @uses register_setting()
	 *
	 * @since   1.0.0
	 */
	public function add_settings() {
		\register_setting(
			$this->plugin->get_name(),
			$this->options_key,
			array( $this, 'validate_settings' )
		);

		// Default plugin settings:
		\add_settings_section( 'default', '', false, $this->plugin->get_name() );

		\add_settings_field(
			'root_url',
			__( 'Redmine URL', 'redmine-embed' ),
			array( $this, 'field_root_url' ),
			$this->plugin->get_name()
		);

		\add_settings_field(
			'api_key',
			__( 'API Key', 'redmine-embed' ),
			array( $this, 'field_api_key' ),
			$this->plugin->get_name()
		);
	}

	/**
	 * Validates and updates CAS server plugin settings.
	 *
	 * @param  array $input Unvalidated input arguments when settings are updated.
	 *
	 * @return array        Validated plugin settings to be saved in the database.
	 *
	 * @since 1.1.0
	 */
	public function validate_settings( $input ) {
		$options = $this->plugin->get_option();

		$options['root_url'] = \esc_url_raw( $input['root_url'] );
		$options['api_key']  = $input['api_key'];

		return $options;
	}

	/**
	 * Displays the CAS server settings page in the dashboard.
	 *
	 * @uses \_e()
	 * @uses \do_settings_sections()
	 * @uses \settings_fields()
	 * @uses \submit_button()
	 *
	 * @since 1.1.0
	 */
	public function page_settings() {
		?>
		<div class="wrap">
			<h2><?php \_e( 'Redmine Embed', 'redmine-embed' ); ?></h2>

			<p><?php \_e( 'Configure how Redmine is integrated on this site.', 'redmine-embed' ); ?></p>

			<form action="options.php" method="POST">
				<?php \do_settings_sections( $this->plugin->get_name() ); ?>
				<?php \settings_fields( $this->plugin->get_name() ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Display the configuration field for the Redmine root URL.
	 *
	 * @uses \esc_url()
	 *
	 * @since 1.0.0
	 */
	public function field_root_url() {
		$root_url = \esc_url( $this->plugin->get_option( 'root_url' ) );

		?>
		<input name="<?php echo $this->options_key; ?>[root_url]" type="text" value="<?php echo $root_url; ?>"
			 id="root_url" class="regular-text" aria-describedby="root_url-description">
		<p id="root_url-description" class="root_url description">
			<?php _e( 'Enter the address for your Redmine install.', 'redmine-embed' ); ?>
		</p>
		<?php
	}

	/**
	 * Display the configuration field for the Redmine API key.
	 *
	 * @uses \esc_url()
	 *
	 * @since 1.0.0
	 */
	public function field_api_key() {
		$api_key = \sanitize_text_field( $this->plugin->get_option( 'api_key' ) );

		?>
		<input name="<?php echo $this->options_key; ?>[api_key]" type="text" value="<?php echo $api_key; ?>"
			 id="api_key" class="regular-text" aria-describedby="api_key-description">
		<p id="api_key-description" class="api_key description">
			<?php _e( 'Enter your Redmine API key. It may be found on the right-hand pane of your account page.', 'redmine-embed' ); ?>
		</p>
		<?php
	}

}
