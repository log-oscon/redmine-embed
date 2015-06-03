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
class API {

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
        $this->api_key  = $plugin->get_option( 'api_key' );
        $this->root_url = \trailingslashit( $plugin->get_option( 'root_url' ) );
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
     * Handle GET requests.
     * @param  string $resource Resource to fetch
     * @param  array  $options  Request options.
     * @param  array  $expires  Cache TTL, in seconds (defaults to 3600).
     * @return array            Response data.
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
        return \wp_remote_retrieve_body( \wp_remote_get( $url, $options ) );
    }

    /**
     * Extract resource path from a URL.  URL must begin with `$root_url`. 
     * @param  string $url    Redmine URL.
     * @param  string $format Expected response format, 'json' (the default) or 'xml'.
     * @return string         Resource matching the provided URL.
     */
    public function get_resource_url( $url, $format = 'json' ) {
        if ( substr_compare( $url, $this->root_url, 0, strlen( $this->root_url ) ) !== 0 ) {
            return;
        }

        return trim( $url, '/' ) . '.' . $format;
    }

}