<?php
namespace AFL\Framework\Plugin\Services;

class Plugin_Transient {

	protected $transient_key_prefix;

	public function __construct( $transient_key_prefix ) {

		if ( empty( $transient_key_prefix ) ) {
			throw new \InvalidArgumentException( 'Transient key prefix cannot be empty.' );
		}

		$this->transient_key_prefix = $transient_key_prefix;
	}

	public function get( $transient_name, $default_value = false ) {

		$value = get_transient( $this->transient_key_prefix . $transient_name );

		return false === $value ? $default_value : $value;
	}

	public function set( $transient_name, $transient_value, $expiration = 0 ) {

		return set_transient( $this->transient_key_prefix . $transient_name, $transient_value, $expiration );
	}

	public function delete( $transient_name ) {

		return delete_transient( $this->transient_key_prefix . $transient_name );
	}
}
