<?php
namespace AFL\Framework\Plugin\Services;

/**
 * Plugin Option Service
 *
 * Provides a wrapper around WordPress options with automatic key prefixing
 * for better organization and namespace isolation of plugin options.
 *
 * @since 0.0.1
 */
class Plugin_Option {

	/**
	 * Option key prefix for namespace isolation
	 *
	 * @var string
	 */
	protected $option_key_prefix;

	/**
	 * Initialize the plugin option service
	 *
	 * @param string $option_key_prefix The prefix to prepend to all option names.
	 * @throws \InvalidArgumentException If prefix is empty.
	 */
	public function __construct( $option_key_prefix ) {

		if ( empty( $option_key_prefix ) ) {
			throw new \InvalidArgumentException( 'Option key prefix cannot be empty.' );
		}

		$this->option_key_prefix = $option_key_prefix;
	}

	/**
	 * Get a plugin option value
	 *
	 * @param string $option_name The option name (without prefix).
	 * @param mixed  $default_value Value to return if option doesn't exist.
	 * @return mixed The option value or default value.
	 */
	public function get( $option_name, $default_value = false ) {

		return get_option( $this->option_key_prefix . $option_name, $default_value );
	}

	/**
	 * Add a new plugin option
	 *
	 * @param string $option_name The option name (without prefix).
	 * @param mixed  $option_value The option value.
	 * @param bool   $autoload Whether to autoload the option.
	 * @return bool  Whether the option was successfully added.
	 */
	public function add( $option_name, $option_value, $autoload = false ) {

		return add_option( $this->option_key_prefix . $option_name, $option_value, '', $autoload );
	}

	/**
	 * Update a plugin option
	 *
	 * @param string $option_name The option name (without prefix).
	 * @param mixed  $option_value The new option value.
	 * @return bool  Whether the option was successfully updated.
	 */
	public function update( $option_name, $option_value ) {

		return update_option( $this->option_key_prefix . $option_name, $option_value );
	}

	/**
	 * Delete a plugin option
	 *
	 * @param string $option_name The option name (without prefix).
	 * @return bool  Whether the option was successfully deleted.
	 */
	public function delete( $option_name ) {

		return delete_option( $this->option_key_prefix . $option_name );
	}
}
