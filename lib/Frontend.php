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
			$this->api = new Redmine\Client( $this->plugin );
		}

		if ( ! isset( $this->url ) ) {
			$this->url = new Redmine\UrlBuilder( $this->plugin );
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
			$this->plugin->get_name(),
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/redmine-embed.css',
			array(),
			$this->plugin->get_version(),
			'all' );

	}

	/**
	 * Register URL embed handler.
	 */
	public function register_embed_handlers () {
		$root_url = preg_quote( \trailingslashit( $this->plugin->get_option( 'root_url', false ) ) );

		if ( empty( $root_url ) ) {
			return;
		}

		\wp_embed_register_handler( 'redmine', '#^' . $root_url . 'issues/(?<id>\d+)#i', array( $this, 'embed_issue' ), true );
	}

	/**
	 * Outputs Redmine issue details.
	 *
	 * @param  array  $matches [description]
	 * @param  array  $attr    [description]
	 * @param  string $url     [description]
	 * @param  array  $rawattr [description]
	 */
	public function embed_issue( $matches, $attr, $url, $rawattr ) {
		$this->initialize();

		$issue_id = (int) $matches['id'];

		try {
			$data = $this->api->get_issue( $issue_id, array(), false );

		} catch ( \Exception $e ) {
			$is_unauthorized = $e->getCode() === 401 || $e->getCode() === 403;
			$error           = array();

			$error[] = sprintf(
				\__( 'Unable to display issue <a href="%s">#%d</a>: %s.', 'redmine-embed' ),
		        \esc_url( $this->url->get_public_url( 'issues', $issue_id ) ),
		        $issue_id,
		        \esc_html( $e->getMessage() )
		    );

			if ( \is_user_logged_in() && $is_unauthorized ) {
				$error[] = sprintf(
					\__( 'Please review <a href="%s" title="%s">your API key settings</a>.', 'redmine-embed' ),
					\esc_url( \get_edit_user_link() ),
					\esc_attr__( 'Edit your profile', 'redmine-embed' )
				);
			}

			return $this->template->render( 'issue-error', implode( ' ', $error ) );
		}

		$data = $this->render_issue_fields( $data );

		return $this->template->render( 'issue', $data );
	}

	/**
	 * Add rendered fields to the data object.
	 * @param  object $data Issue data.
	 * @return object       Issue data with added fields.
	 */
	private function render_issue_fields( $data ) {
		$data->issue->rendered = (object) array(
			'description' => $this->markup->textileRestricted( $data->issue->description ),
			'created_on'  => $this->get_formatted_date( strtotime( $data->issue->created_on ) ),
			'updated_on'  => $this->get_formatted_date( strtotime( $data->issue->updated_on ) ),
		);

		$data->issue->link                = $this->url->get_public_url( 'issues', $data->issue->id );
		$data->issue->spent_hours_link    = $this->url->get_public_url( 'issues', $data->issue->id, '/time_entries' );
		$data->issue->assigned_to->link   = $this->url->get_public_url( 'users', $data->issue->assigned_to->id );
		$data->issue->author->link        = $this->url->get_public_url( 'users', $data->issue->author->id );
		$data->issue->fixed_version->link = $this->url->get_public_url( 'versions', $data->issue->fixed_version->id );

		return $data;
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
