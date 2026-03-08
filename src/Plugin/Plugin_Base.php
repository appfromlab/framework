<?php
namespace AFL\Framework\Plugin;

use AFL\Framework\Core\Application;
use AFL\Framework\Plugin\Services\Plugin_Option;
use AFL\Framework\Plugin\Services\Plugin_Transient;

/**
 * Plugin Base Class
 *
 * Extends the Application class to provide plugin-specific functionality
 * including option and transient management.
 *
 * @since 0.0.1
 */
class Plugin_Base extends Application {

	/**
	 * Boot the plugin application
	 *
	 * Initializes parent application and sets up plugin-specific services.
	 *
	 * @param string $file_path The main plugin file path.
	 * @param string $config_folder_path Optional path to configuration folder.
	 * @return void
	 */
	public function boot( $file_path, $config_folder_path = '' ) {

		parent::boot( $file_path, $config_folder_path );

		$this->singleton(
			Plugin_Option::class,
			function ( $app ) {
				return new Plugin_Option( $app->config()->get( 'option_key_prefix' ) );
			}
		);

		$this->singleton(
			Plugin_Transient::class,
			function ( $app ) {
				return new Plugin_Transient( $app->config()->get( 'option_key_prefix' ) );
			}
		);
	}

	/**
	 * Get the Plugin Option service
	 *
	 * @return Plugin_Option The option service instance.
	 */
	public function option() {
		return $this->get( Plugin_Option::class );
	}

	/**
	 * Get the Plugin Transient service
	 *
	 * @return Plugin_Transient The transient service instance.
	 */
	public function transient() {
		return $this->get( Plugin_Transient::class );
	}

	/**
	 * Get the currently installed version
	 *
	 * @return mixed The installed plugin version.
	 */
	public function get_installed_version() {
		return $this->option()->get( 'version' );
	}
}
