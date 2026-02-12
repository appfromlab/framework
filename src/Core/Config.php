<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
class Config {

	private $config = array();

	public function __construct() {
	}

	public function load_from_file( $file_path ) {

		if ( file_exists( $file_path ) ) {
			$this->config = include $file_path;
		}
	}

	public function get( $key ) {

		if ( isset( $this->config[ $key ] ) ) {
			return $this->config[ $key ];
		} else {
			return null;
		}
	}

	public function get_all() {
		return $this->config;
	}

	public function set( $key, $value ) {

		$this->config[ $key ] = $value;
	}
}
