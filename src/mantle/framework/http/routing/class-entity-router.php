<?php
/**
 * Entity_Router class file.
 *
 * @package Mantle
 */

namespace Mantle\Framework\Http\Routing;

use InvalidArgumentException;
use Mantle\Framework\Contracts\Http\Routing\Url_Routable;
use Mantle\Framework\Database\Model\Model;
use Mantle\Framework\Database\Model\Post;
use Mantle\Framework\Database\Model\Term;
use RuntimeException;
use WP_Post_Type;

use function Mantle\Framework\Helpers\add_action;

/**
 * Provide routing to a WordPress data object: post or term.
 */
class Entity_Router {

	/**
	 * Router instance.
	 *
	 * @var Router
	 */
	protected $router;

	protected $entities = [];

	/**
	 * Constructor.
	 *
	 * @param Router $router Router instance.
	 */
	public function __construct( Router $router ) {
		$this->router = $router;

		// $router->get_container()['events']->action( 'template_redirect', fn() => $this->handle_template_redirect() );
	}

	/**
	 * Handle the template redirect event.
	 */
	// protected function handle_template_redirect() {
	// 	// global $wp_query;
	// 	// dd($wp_query);
	// 	if ( empty( $this->entities ) || is_404() ) {
	// 		return;
	// 	}

	// 	foreach ( $this->entities as $entity => $controller ) {
	// 		$object = static::get_entity_object( $entity );

	// 		if (
	// 			$object instanceof \WP_Post_Type
	// 			&& ( \is_singular( $object->name ) || \is_post_type_archive( $object->name ) )
	// 		) {
	// 			$this->dispatch_controller( $controller );
	// 		} elseif ( $object instanceof \WP_Taxonomy ) {
	// 			dd('tax');
	// 		}
	// 	}
	// }

	// protected function dispatch_controller( $controller ) {
	// 	$method = is_singular() || is_tax() ? 'show' :
	// 	dd($controller);
	// }

	/**
	 * Retrieve the controller method to invoke.
	 *
	 * @return string
	 */
	protected function get_controller_method(): string {
		if ( is_singular() || is_tax() ) {
			return 'show';
		}

		return 'index';
	}

	/**
	 * Add a entity to the router.
	 *
	 * @param string $entity
	 * @param string $controller
	 * @return void
	 */
	public function add( string $entity, string $controller ) {
		if ( ! is_subclass_of( $entity, Model::class ) ) {
			throw new InvalidArgumentException( "Unknown entity type: [{$entity}]" );
		}

		if ( ! in_array( Url_Routable::class, class_implements( $entity ) ) ) {
			throw new InvalidArgumentException( "Unroutable entity: [{$entity}]" );
		}

		// $this->entities[ $entity ] = $controller;
		// return;

		// $index    = $this->get_index_endpoint( $entity );
		$this->router->get( $this->get_singular_endpoint( $entity ), [ $controller, 'view' ] );

	}

	protected function get_index_endpoint( string $entity ): ?string {
		return null;
	}

	protected function get_singular_endpoint( string $entity ): string {
		$route = $entity::get_route();

		if ( ! $route ) {
			throw new InvalidArgumentException( "Unable to get singular endpoint for entity: [{$entity}]" );
		}

		return $route;
	}

	/**
	 * Retrieve the underling entity object: post type.
	 *
	 * @param string $entity Entity class name.
	 * @return \WP_Post_Type|\WP_Taxonomy
	 */
	protected static function get_entity_object( string $entity ) {
		if ( is_subclass_of( $entity, Post::class ) ) {
			$object = \get_post_type_object( $entity::get_object_name() );
		} elseif ( is_subclass_of( $entity, Term::class ) ) {
			$object = \get_taxonomy( $entity::get_object_name() );
		}

		if ( empty( $object ) ) {
			throw new InvalidArgumentException( "Unknown entity object: [{$entity}]" );
		}

		return $object;
	}
}
