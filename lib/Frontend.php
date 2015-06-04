<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/Frontend
 */

namespace logoscon\WP\RedmineEmbed;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/Frontend
 * @author     Your Name <email@example.com>
 */
class Frontend {

	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Redmine.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Redmine API client object.
	 */
	private $api;

	/**
	 * Template engine.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    \Handlebars
	 */
	private $template;

	/**
	 * Textile parser.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    \Netcarver\Textile
	 */
	private $textile;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * 
	 * @param Plugin $plugin This plugin's instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin  = $plugin;
	}

	/**
	 * Initialize dependencies.
	 */
	private function initialize() {
		if ( ! isset( $this->api ) ) {
			$this->api = new Redmine\API( $this->plugin );			
		}

		if ( ! isset( $this->url ) ) {
			$this->url = new Redmine\URL( $this->plugin );			
		}

		if ( ! isset( $this->markup ) ) {
			$this->markup = new \Netcarver\Textile\Parser();
		}

		if ( ! isset( $this->template ) ) {
			$this->template = new \Handlebars\Handlebars( array(
			    'loader'  => new \Handlebars\Loader\FilesystemLoader( dirname( __DIR__ ) . '/templates/' ),
			) );
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined in that particular
		 * class.
		 *
		 * The Loader will then create the relationship between the defined
		 * hooks and the functions defined in this class.
		 */

		\wp_enqueue_style(
			$this->plugin->get_plugin_name(),
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/redmine-embed.css',
			array(),
			$this->plugin->get_version(),
			'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined in that particular
		 * class.
		 *
		 * The Loader will then create the relationship between the defined
		 * hooks and the functions defined in this class.
		 */

		\wp_enqueue_script(
			$this->plugin->get_plugin_name(),
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/redmine-embed.js',
			array( 'jquery' ),
			$this->plugin->get_version(),
			false );

	}

	/**
	 * Register URL embed handler.
	 */
	public function register_embed_handler () {
		$root_url = preg_quote( \trailingslashit( $this->plugin->get_option( 'root_url', false ) ) );

		if ( empty( $root_url ) ) {
			return;
		}

		\wp_embed_register_handler( 'redmine', '#^' . $root_url . 'issues/(?<id>\d+)#i', array( $this, 'embed_issue' ), true );
	}

	/**
	 * Handle Redmine URLs in content.
	 * 
	 * @param  [type] $matches [description]
	 * @param  [type] $attr    [description]
	 * @param  [type] $url     [description]
	 * @param  [type] $rawattr [description]
	 * @return [type]          [description]
	 */
	public function embed_issue ( $matches, $attr, $url, $rawattr ) {
		$this->initialize();

		$issue_id = (int) $matches['id'];

		$response = $this->api->get_issue( $issue_id, array(), 3600 );

		if ( empty( $response ) ) {
			include $this->get_template( 'issue-error' );
			return; 
		}

		$response->options = (object) array(
			'base_url' => \trailingslashit( $this->plugin->get_option( 'root_url' ) ),
		);

		$response->issue->rendered = (object) array(
			'description' => $this->markup->textileRestricted( $response->issue->description ),
			'created_on'  => $this->get_formatted_date( strtotime( $response->issue->created_on ) ),
			'updated_on'  => $this->get_formatted_date( strtotime( $response->issue->updated_on ) ),
		);

		$response->issue->pending_ratio = (int) 100 - $response->issue->done_ratio;
		
$response->issue->done_ratio = 30;
$response->issue->pending_ratio = 70;

		echo $this->template->render( 'issue', $response );
	}

	/**
	 * Get template file path.
	 * 
	 * @param  string $template Template file (without the extension) to include.
	 * @return string           Absolute path to the requested template file.
	 */
	private function get_template( $template ) {
		return sprintf( '%s/templates/%s.php', dirname( __DIR__ ), $template );
	}

	/**
	 * Format date.
	 * @param  int    $timestamp Timestamp.
	 * @return string            Formatted date based on the timestamp.
	 */
	private function get_formatted_date( $timestamp = 0 ) {
		return strftime( '%c', $timestamp );
	}

}
