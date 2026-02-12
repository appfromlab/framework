<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
class Config_Manager {

	protected $list = array();
	private $primary_folder_path;

	public function __construct() {
	}

	public function boot( $config_folder_path ) {

		$this->set_primary_folder_path( $config_folder_path );

		$file_list = array(
			'app'       => $this->get_primary_folder_path() . 'app.php',
			'providers' => $this->get_primary_folder_path() . 'providers.php',
		);

		foreach ( $file_list as $file_key => $file_path ) {
			if ( file_exists( $file_path ) ) {
				$this->load_from_file( $file_key, $file_path );
			}
		}
	}

	public function set_primary_folder_path( $folder_path ) {

		$folder_path = rtrim( $folder_path, '/' );
		$folder_path = rtrim( $folder_path, '\\' );

		$this->primary_folder_path = $folder_path . DIRECTORY_SEPARATOR;
	}

	public function get_primary_folder_path() {
		return $this->primary_folder_path;
	}

	public function load_from_file( $key, $file_path ) {

		$config = new Config();
		$config->load_from_file( $file_path );

		$this->list[ $key ] = $config;
	}

	public function get( $key ) {

		if ( isset( $this->list[ $key ] ) ) {
			return $this->list[ $key ];
		} else {
			return null;
		}
	}

	public function set( $key, $value ) {

		if ( isset( $this->list[ $key ] ) ) {
			$this->list[ $key ] = $value;
		}
	}
}
