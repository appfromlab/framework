<?php
namespace AFL\Framework\Plugin\Services;

/**
 * Plugin Transient Service
 *
 * Provides a wrapper around WordPress transients with automatic key prefixing
 * for better organization and namespace isolation of plugin transients.
 *
 * @since 0.0.1
 */
class Plugin_Transient {

	/**
	 * Transient key prefix for namespace isolation
	 *
	 * @var string
	 */
	protected $transient_key_prefix;

	/**
	 * Initialize the plugin transient service
	 *
	 * @param string $transient_key_prefix The prefix to prepend to all transient names
	 * @throws InvalidArgumentException If prefix is empty
	 */
	public function __construct( $transient_key_prefix ) {

		if ( empty( $transient_key_prefix ) ) {
			throw new \InvalidArgumentException( 'Transient key prefix cannot be empty.' );
		}

		$this->transient_key_prefix = $transient_key_prefix;
	}

	/**
	 * Get a plugin transient value
	 *
	 * @param string $transient_name The transient name (without prefix)
	 * @param mixed  $default_value Value to return if transient doesn't exist
	 * @return mixed The transient value or default value
	 */
	public function get( $transient_name, $default_value = false ) {

		$value = \get_transient( $this->transient_key_prefix . $transient_name );

		return false === $value ? $default_value : $value;
	}

	/**
	 * Set a plugin transient value
	 *
	 * @param string $transient_name The transient name (without prefix)
	 * @param mixed  $transient_value The transient value
	 * @param int    $expiration Optional. Expiration time in seconds (0 = no expiration)
	 * @return bool Whether the transient was successfully set
	 */
	public function set( $transient_name, $transient_value, $expiration = 0 ) {

		return \set_transient( $this->transient_key_prefix . $transient_name, $transient_value, $expiration );
	}

	/**
	 * Delete a plugin transient
	 *
	 * @param string $transient_name The transient name (without prefix)
	 * @return bool Whether the transient was successfully deleted
	 */
	public function delete( $transient_name ) {

		return \delete_transient( $this->transient_key_prefix . $transient_name );
	}
}
