<?php
/**
 * Welcome Page Class
 *
 * @package     afa
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2015, Friedrich Schmidt
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
//require_once AFA_PLUGIN_DIR . 'class-tgm-plugin-automation.php';

/**
 * afa_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class afa_Welcome {
	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}
	/**
	 * Sends user to the Settings page on first activation of afa as well as each
	 * time afa is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0.1
	 * @return void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( false === get_transient( 'afa_activation_redirect' ) ){
			return;
		}

		// Delete the redirect transient
		delete_transient( 'afa_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ){
			return;
        }
		wp_safe_redirect( admin_url( 'options-general.php?page=all-adsense.php' ) );
		exit;
	}
}
$aw = new afa_Welcome();