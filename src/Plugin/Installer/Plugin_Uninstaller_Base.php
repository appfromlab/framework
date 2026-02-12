<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

abstract class Plugin_Uninstaller_Base {

	protected $app;

	/**
	 * Determine if plugin data should be deleted on uninstall.
	 *
	 * @return boolean
	 */
	abstract public function should_delete_data();

	abstract public function delete_scheduled_hooks();

	abstract public function delete_database_tables();

	abstract public function delete_settings();

	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	public function boot() {

		$this->uninstall();
	}

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
