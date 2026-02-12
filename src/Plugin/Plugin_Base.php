<?php
namespace AFL\Framework\Plugin;

use AFL\Framework\Core\Application;
use AFL\Framework\Plugin\Services\Plugin_Option;
use AFL\Framework\Plugin\Services\Plugin_Transient;

class Plugin_Base extends Application {

	protected $option;
	protected $transient;

	public function boot( $file_path, $config_folder_path = '' ) {

		parent::boot( $file_path, $config_folder_path );

		$this->option    = new Plugin_Option( $this->config()->get( 'option_key_prefix' ) );
		$this->transient = new Plugin_Transient( $this->config()->get( 'option_key_prefix' ) );
	}

	/**
	 * Get Plugin Option service.
	 *
	 * @return Plugin_Option
	 */
	public function option() {
		return $this->option;
	}

	/**
	 * Get Plugin Transient service.
	 *
	 * @return Plugin_Transient
	 */
	public function transient() {
		return $this->transient;
	}

	public function get_installed_version() {
		return $this->option()->get( 'version' );
	}
}
