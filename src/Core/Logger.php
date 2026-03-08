<?php
namespace AFL\Framework\Core;

/**
 * Logger Class
 *
 * Provides simple logging functionality using WordPress debug logging.
 * Logs are written to wp-content/debug.log when WP_DEBUG_LOG is enabled.
 *
 * @since 0.0.1
 */
class Logger {

	/**
	 * Flag to track whether logging is enabled
	 *
	 * @var bool
	 */
	private $enable_log = false;

	public function __construct( bool $enable_log = false ) {
		$this->enable( $enable_log );
	}

	/**
	 * Enable or disable logging
	 *
	 * @param bool $status Whether to enable logging.
	 * @return void
	 */
	public function enable( bool $status ) {

		$this->enable_log = $status;
	}

	/**
	 * Write a message to the debug log
	 *
	 * @param mixed $message The message to log (will be exported as string).
	 * @return void
	 */
	public function write( $message ) {
		if ( true === $this->enable_log ) {
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			\error_log( \var_export( $message, true ) );
			// phpcs:enable
		}
	}
}
