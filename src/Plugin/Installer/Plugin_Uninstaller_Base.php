<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

/**
 * Plugin Uninstaller Base Class
 *
 * Abstract base class for handling plugin uninstallation logic.
 * Manages complete cleanup of plugin data including settings, transients, and database tables.
 *
 * @since 0.0.1
 */
abstract class Plugin_Uninstaller_Base {

	/**
	 * Plugin application instance
	 *
	 * @var Plugin_Base
	 */
	protected $app;

	/**
	 * Determine if plugin data should be deleted on uninstall.
	 *
	 * @return bool
	 */
	abstract public function should_delete_data();
	
	/**
	 * Delete scheduled WordPress cron hooks
	 *
	 * @return void
	 */
	abstract public function delete_scheduled_hooks();

	abstract public function delete_database_tables();

	abstract public function delete_settings();

	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	/**
	 * Boot the uninstaller
	 *
	 * @return void
	 */
	public function boot() {

		$this->uninstall();
	}

	/**
	 * Execute the plugin uninstallation process
	 *
	 * @return void
	 */
	public function uninstall() {

		if ( ! $this->should_delete_data() ) {
			return;
		}

		$this->delete_scheduled_hooks();
		$this->delete_database_tables();
		$this->delete_transients();
		$this->delete_settings();

		$this->app->option()->delete( 'version' );
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
