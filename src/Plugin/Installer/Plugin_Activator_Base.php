<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

/**
 * Plugin Activator Base Class
 *
 * Abstract base class for handling plugin activation logic.
 * Manages plugin installation, version tracking, and default setup.
 *
 * @since 0.0.1
 */
abstract class Plugin_Activator_Base {

	/**
	 * Plugin application instance
	 *
	 * @var Plugin_Base
	 */
	protected $app;

	/**
	 * Initialize the plugin activator
	 *
	 * @param Plugin_Base $app The plugin application instance.
	 */
	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	/**
	 * Boot the activator
	 *
	 * @return void
	 */
	public function boot() {

		$this->install();
	}

	/**
	 * Check if plugin is currently installing
	 *
	 * @return bool
	 */
	public function is_installing() {

		return 'yes' === $this->app->transient()->get( 'installing' ) ? true : false;
	}

	/**
	 * Mark installation as in progress
	 *
	 * @return void
	 */
	public function start_installing() {

		$this->app->transient()->set( 'installing', 'yes', 600 ); // 10 minutes
	}

	/**
	 * Complete the installation and save version
	 *
	 * @return void
	 */
	public function finish_installing() {

		// finally save version.
		if ( false === $this->app->option()->get( 'version' ) ) {
			$this->app->option()->add( 'version', $this->app->get_version(), true );
		} else {
			$this->app->option()->update( 'version', $this->app->get_version() );
		}

		$this->app->transient()->delete( 'installing' );
	}

	/**
	 * Check if plugin needs updating
	 *
	 * @return bool
	 */
	public function should_update() {

		return $this->app->get_installed_version() !== $this->app->get_version() ? true : false;
	}

	/**
	 * Check if plugin needs upgrading (version is higher)
	 *
	 * @return bool
	 */
	public function should_upgrade() {

		if ( version_compare( $this->app->get_installed_version(), $this->app->get_version(), '<' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Conditionally install if plugin needs updating
	 *
	 * @return void
	 */
	public function maybe_install() {

		if ( $this->should_update() ) {
			$this->install();
		}
	}

	/**
	 * Execute the plugin installation process
	 *
	 * @return bool
	 */
	public function install() {

		if ( ! \is_blog_installed() || $this->is_installing() ) {
			return false;
		}

		$this->start_installing();

		// run install sequence.
		$this->create_default_settings();
		$this->create_database_tables();
		$this->create_user_roles();

		$this->finish_installing();
	}

	/**
	 * Create default plugin settings
	 *
	 * Override this method to implement custom default settings.
	 *
	 * @return void
	 */
	public function create_default_settings() {
	}

	/**
	 * Create plugin database tables
	 *
	 * Override this method to create custom database tables.
	 *
	 * @return void
	 */
	public function create_database_tables() {
	}

	/**
	 * Create plugin user roles
	 *
	 * Override this method to create custom user roles.
	 *
	 * @return void
	 */
	public function create_user_roles() {
	}
}
