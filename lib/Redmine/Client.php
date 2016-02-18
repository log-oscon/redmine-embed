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

namespace logoscon\WP\RedmineEmbed\Redmine;

use logoscon\WP\RedmineEmbed\Plugin;

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
class Client {

    /**
     * The plugin's instance.
     *
     * @since  1.0.0
     * @access private
     * @var    Plugin $plugin This plugin's instance.
     */
    private $plugin;

    /**
     * Root URL for Redmine.
     * @access private
     * @var string
     */
    private $root_url = '';

    /**
     * REST API key for Redmine.
     * @var string
     */
    private $api_key = '';

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     *
     * @param Plugin $plugin This plugin's instance.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin   = $plugin;
        $this->api_key  = $this->get_api_key();
        $this->root_url = \trailingslashit( $plugin->get_option( 'root_url' ) );
        $this->url      = new UrlBuilder( $plugin );
    }

    /**
     * Fetch an issue.
     * @param  string $id      Issue ID.
     * @param  array  $options Request options.
     * @param  array  $expires Cache TTL, in seconds (defaults to 3600).
     * @return mixed           Response data.
     */
    public function get_issue( $id, $options = array(), $expires = 3600  ) {
        $url = $this->url->get_json_resource_url( 'issues', $id );

        return json_decode( $this->get( $url, $options, $expires ) );
    }

    /**
     * Handle GET requests.
     * @param  string $resource Resource to fetch
     * @param  array  $options  Request options.
     * @param  array  $expires  Cache TTL, in seconds (defaults to 3600).
     * @return mixed            Response data.
     */
    public function get( $url, $options = array(), $expires = 3600 ) {
        $options = $this->add_credentials( $options );

        if ( $expires === false ) {
            // Bypass cache
            return $this->get_body( $url, $options );
        }

        $cache_key = sha1( $url . $this->api_key );

        return tlc_transient( $cache_key )
            ->updates_with( array( $this, 'get_body' ), array( $url, $options ) )
            ->expires_in( $expires )
            ->get();
    }

    /**
     * Fetch the response body for a GET request.
     * @param  string $resource Resource to fetch
     * @param  array  $options  Request options.
     * @return string           Request body.
     */
    public function get_body( $url, $options ) {
        $response = \wp_remote_get( $url, $options );
        $code     = \wp_remote_retrieve_response_code( $response );
        $message  = \wp_remote_retrieve_response_message( $response );

        if ( substr( $code, 0, 1 ) !== '2' ) {
            // A non-2xx class status code means there was an error.
            throw new \Exception( $message, (int) $code );
        }

        return \wp_remote_retrieve_body( $response );
    }

    /**
     * Add credentials to a set of request options.
     * @param array $options Request options.
     */
    private function add_credentials( $options = array() ) {
        if ( ! isset( $options['headers'] ) ) {
            $options['headers'] = array();
        }

        $options['headers']['X-Redmine-API-Key'] = $this->api_key;

        return $options;
    }

    /**
     * Get the configured Redmine API key.
     *
     * Looks for the user key first and falls back to the globally configured one.
     *
     * @return string Configured Redmine API key.
     */
    private function get_api_key() {
        $user_id = \get_current_user_id();
        $api_key = \get_user_meta( $user_id, 'redmine_embed_api_key', true );

        if ( empty( $api_key ) ) {
            $api_key = $this->plugin->get_option( 'api_key' );
        }

        return \sanitize_key( $api_key );
    }

}
