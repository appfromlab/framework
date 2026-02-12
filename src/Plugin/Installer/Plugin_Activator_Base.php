<?php
namespace AFL\Framework\Plugin\Installer;

use AFL\Framework\Plugin\Plugin_Base;

abstract class Plugin_Activator_Base {

	protected $app;

	public function __construct( Plugin_Base $app ) {
		$this->app = $app;
	}

	public function boot() {

		$this->install();
	}

	public function is_installing() {

		return 'yes' === $this->app->transient()->get( 'installing' ) ? true : false;
	}

	public function start_installing() {

		$this->app->transient()->set( 'installing', 'yes', 600 ); // 10 minutes
	}

	public function finish_installing() {

		// finally save version.
		if ( false === $this->app->option()->get( 'version' ) ) {
			$this->app->option()->add( 'version', $this->app->get_version(), true );
		} else {
			$this->app->option()->update( 'version', $this->app->get_version() );
		}

		$this->app->transient()->delete( 'installing' );
	}

	public function should_update() {

		return $this->app->get_installed_version() !== $this->app->get_version() ? true : false;
	}

	public function should_upgrade() {

		if ( version_compare( $this->app->get_installed_version(), $this->app->get_version(), '<' ) ) {
			return true;
		}

		return false;
	}

	public function maybe_install() {

		if ( $this->should_update() ) {
			$this->install();
		}
	}

	public function install() {

		if ( ! is_blog_installed() || $this->is_installing() ) {
			return false;
		}

		$this->start_installing();

		// run install sequence.
		$this->create_default_settings();
		$this->create_database_tables();
		$this->create_user_roles();

		$this->finish_installing();
	}

	public function create_default_settings() {
	}

	public function create_database_tables() {
	}

	public function create_user_roles() {
	}
}
