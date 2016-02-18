<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://log.pt
 * @since      1.0.0
 *
 * @package    RedmineEmbed
 * @subpackage RedmineEmbed/Admin
 */

namespace logoscon\WP\RedmineEmbed\Admin;

use logoscon\WP\RedmineEmbed\Plugin;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    RedmineEmbed
 * @subpackage RedmineEmbed/Admin
 * @author     log.OSCON, Lda. <engenharia@log.pt>
 */
class UserSettings {

	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin This plugin's instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Display the user profile form field.
	 *
	 * @param  \WP_User $user User instance.
	 */
	public function show_user_profile( \WP_User $user ) {
		$url = $this->plugin->get_option( 'root_url', '' );

		if ( empty( $url ) ) {
			return;
		}

		if ( ! \current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		$api_key = \get_user_option( 'redmine_embed_api_key', $user->ID );

		?>
		<h3><?php \_e( 'Redmine', 'redmine-embed' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="redmine_embed_api_key"><?php
					\_e( 'API Key', 'redmine-embed' );
				?></label></th>
				<td>
					<input type="text" name="redmine_embed_api_key"
						id="redmine_embed_api_key" class="regular-text"
						value="<?php echo \esc_attr( $api_key ); ?>">
					<br>
					<span class="description"><?php
						printf(
							\__( 'Enter your API key for the Redmine install at %s. It may be found on the right-hand pane of your account page.', 'redmine-embed' ),
							\esc_url( $url )
						);
					?></span>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Update saved fields on the edited user profile.
	 * @param int $user_id Edited user ID.
	 */
	public function edit_user_profile_update( $user_id ) {
		if ( ! \current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$api_key = \sanitize_key( $_POST['redmine_embed_api_key'] );

		\update_user_option( $user_id, 'redmine_embed_api_key', $api_key );
	}

}
