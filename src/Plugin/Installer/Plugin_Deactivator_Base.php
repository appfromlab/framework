<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

abstract class Plugin_Deactivator_Base {

	protected $app;

	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	public function boot() {

		$this->deactivate();
	}

	public function deactivate() {

		$this->delete_transients();
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
