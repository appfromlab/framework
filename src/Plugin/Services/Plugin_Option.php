<?php
namespace AFL\Framework\Plugin\Services;

use function delete_option;
use function get_option;
use function update_option;
use function add_option;

class Plugin_Option {

	protected $option_key_prefix;

	public function __construct( $option_key_prefix ) {

		if ( empty( $option_key_prefix ) ) {
			throw new \InvalidArgumentException( 'Option key prefix cannot be empty.' );
		}

		$this->option_key_prefix = $option_key_prefix;
	}

	public function get( $option_name, $default_value = false ) {

		return get_option( $this->option_key_prefix . $option_name, $default_value );
	}

	public function add( $option_name, $option_value, $autoload = false ) {

		return add_option( $this->option_key_prefix . $option_name, $option_value, '', $autoload );
	}

	public function update( $option_name, $option_value ) {

		return update_option( $this->option_key_prefix . $option_name, $option_value );
	}

	public function delete( $option_name ) {

		return delete_option( $this->option_key_prefix . $option_name );
	}
}
