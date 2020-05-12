<?php
/**
 * Model_Register class file.
 *
 * @package Mantle
 */

namespace Mantle\Framework\Support\Providers;

use Mantle\Framework\Contracts\Database\Registrable as Registrable_Contract;
use Mantle\Framework\Service_Provider;

/**
 * Model Register Service Provider
 */
class Model_Register_Provider extends Service_Provider {
	/**
	 * Models to register for the application.
	 *
	 * @var string[]
	 */
	protected $models = [];

	/**
	 * Register the service provider.
	 */
	public function register() {
		$this->set_models_to_register( $this->app['config']->get( 'models.register' ) );
	}

	/**
	 * Bootstrap the service provider.
	 *
	 * @throws Provider_Exception Thrown on invalid model.
	 */
	public function boot() {
		if ( empty( $this->models ) ) {
			return;
		}

		foreach ( $this->models as $model ) {
			if ( ! in_array( Registrable_Contract::class, class_implements( $model ), true ) ) {
				throw new Provider_Exception( $model . ' does not implement ' . Registrable_Contract::class . ' interface' );
			}

			$model::register();
		}
	}

	/**
	 * Set the models to register.
	 *
	 * @param string[] $models Models to register.
	 */
	public function set_models_to_register( array $models ) {
		$this->models = $models;
	}
}
