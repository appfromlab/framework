<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
class Logger {

	private $enable_log = false;

	public function boot() {

		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			$this->enable( true );
		}
	}

	public function enable( bool $status ) {

		$this->enable_log = $status;
	}

	public function write( $message ) {
		if ( true === $this->enable_log ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			\error_log( \var_export( $message, true ) );
			// phpcs:enable
		}
	}
}
