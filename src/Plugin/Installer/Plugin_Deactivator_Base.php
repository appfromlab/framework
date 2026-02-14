<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

/**
 * Plugin Deactivator Base Class
 *
 * Abstract base class for handling plugin deactivation logic.
 * Manages cleanup of transients when the plugin is deactivated.
 *
 * @since 0.0.1
 */
abstract class Plugin_Deactivator_Base {

	/**
	 * Plugin application instance
	 *
	 * @var Plugin_Base
	 */
	protected $app;

	/**
	 * Initialize the plugin deactivator
	 *
	 * @param Plugin_Base $app The plugin application instance.
	 */
	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	/**
	 * Boot the deactivator
	 *
	 * @return void
	 */
	public function boot() {

		$this->deactivate();
	}

	/**
	 * Execute the plugin deactivation process
	 *
	 * @return void
	 */
	public function deactivate() {

		$this->delete_transients();
	}

	/**
	 * Delete all plugin transients from the database
	 *
	 * @global wpdb $wpdb
	 * @return void
	 */
	public function delete_transients() {
		global $wpdb;

		$option_key = $this->app->config()->get( 'option_key' );

		if ( empty( $option_key ) ) {
			return;
		}

		$transient_name_like         = $wpdb->esc_like( '_transient_' . $option_key . '_' ) . '%';
		$transient_timeout_name_like = $wpdb->esc_like( '_transient_timeout_' . $option_key . '_' ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s",
				$transient_name_like,
				$transient_timeout_name_like
			)
		);
	}
}
