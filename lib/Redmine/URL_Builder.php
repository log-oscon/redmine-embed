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
 * @author     Your Name <engenharia@log.pt>
 */
class URL_Builder {

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
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * 
     * @param Plugin $plugin This plugin's instance.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin   = $plugin;
        $this->root_url = \trailingslashit( $plugin->get_option( 'root_url', '' ) );
    }

    /**
     * Get a public resource link on Redmine.
     * @param  string  $resource Resource type.
     * @param  integer $id       Resource ID.
     * @param  string  $suffix   Suffix to append to the URL.
     * @return string            Public (frontend) resource link.
     */
    public function get_public_url( $resource = '', $id = 0, $suffix = '' ) {
        $path = $id ? sprintf( '%s/%d', $resource, $id ) : $resource;
        return $this->root_url . $path . $suffix;
    }

    /**
     * Get a REST API resource link on Redmine.
     * @param  string  $resource Resource type.
     * @param  integer $id       Resource ID.
     * @param  string  $suffix   Suffix to append to the URL.
     * @return string            REST API resource link.
     */
    public function get_json_resource_url( $resource = '', $id = 0, $suffix = '' ) {
        return $this->get_public_url( $resource, $id, $suffix ) . '.json';
    }

}