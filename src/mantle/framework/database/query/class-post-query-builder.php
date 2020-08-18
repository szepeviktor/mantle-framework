<?php
/**
 * Post_Query_Builder class file.
 *
 * @package Mantle
 * @phpcs:disable Squiz.Commenting.FunctionComment
 */

namespace Mantle\Framework\Database\Query;

use Mantle\Framework\Database\Model\Term;
use Mantle\Framework\Helpers;
use Mantle\Framework\Support\Collection;

use function Mantle\Framework\Helpers\collect;

/**
 * Post Query Builder
 */
class Post_Query_Builder extends Builder {
	use Queries_Relationships;

	/**
	 * Query Variable Aliases
	 *
	 * @var array
	 */
	protected $query_aliases = [
		'id'          => 'p',
		'post_author' => 'author',
		'post_name'   => 'name',
	];

	/**
	 * Query Where In Aliases
	 *
	 * @var array
	 */
	protected $query_where_in_aliases = [
		'author'      => 'author__in',
		'id'          => 'post__in',
		'post_name'   => 'post_name__in',
		'post_parent' => 'post_parent__in',
		'tag'         => 'tag__in',
		'tag_slug'    => 'tag_slug__in',
	];

	/**
	 * Query Where Not In Aliases
	 *
	 * @var array
	 */
	protected $query_where_not_in_aliases = [
		'author'      => 'author__not_in',
		'id'          => 'post__not_in',
		'post_name'   => 'post_name__not_in',
		'post_parent' => 'post_parent__not_in',
		'tag'         => 'tag__not_in',
		'tag_slug'    => 'tag_slug__not_in',
	];

	/**
	 * Tax Query.
	 *
	 * @var array
	 */
	protected $tax_query = [];

	/**
	 * Get the query arguments.
	 *
	 * @return array
	 */
	protected function get_query_args(): array {
		$this->apply_scopes();

		if ( is_array( $this->model ) ) {
			$post_type = [];

			foreach ( $this->model as $model ) {
				$post_type[] = $model::get_object_name();
			}
		} else {
			$post_type = $this->model::get_object_name();
		}

		return array_merge(
			[
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'meta_query'          => $this->meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'tax_query'           => $this->tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'order'               => $this->order,
				'orderby'             => $this->order_by,
				'post_type'           => $post_type,
				'posts_per_page'      => $this->limit,
				'suppress_filters'    => false,
			],
			$this->wheres,
		);
	}

	/**
	 * Execute the query.
	 *
	 * @return Collection
	 */
	public function get(): Collection {
		$post_ids = \get_posts( $this->get_query_args() );
		if ( empty( $post_ids ) ) {
			return collect();
		}

		/**
		 * Translate the post IDs to model objects.
		 *
		 * @todo Use a more abstract way to get the correct model for the post.
		 */
		if ( is_array( $this->model ) ) {
			$model_object_types = static::get_model_object_names();
			return collect( $post_ids )
				->map(
					function ( $post_id ) use ( $model_object_types ) {
						$post_type = \get_post_type( $post_id );

						if ( empty( $post_type ) ) {
							return null;
						}

						return $model_object_types[ $post_type ]::find( $post_id );
					}
				)
				->filter();
		} else {
			return collect( $post_ids )
				->map( [ $this->model, 'find' ] )
				->filter();
		}
	}

	/**
	 * Include a taxonomy query.
	 *
	 * @param array|string $term Term ID/array of IDs.
	 * @param string       $taxonomy Taxonomy name.
	 * @param string       $operator Operator to use, defaults to 'IN'.
	 *
	 * @throws Query_Exception Unknown term to query against.
	 */
	public function whereTerm( $term, $taxonomy = null, string $operator = 'IN' ) {
		if ( $term instanceof Term ) {
			$taxonomy = $term->taxonomy();
			$term     = $term->id();
		}

		if ( $term instanceof \WP_Term ) {
			$taxonomy = $term->taxonomy;
			$term     = $term->term_id;
		}

		// Get the taxonomy if it wasn't passed.
		if ( empty( $taxonomy ) && ! is_array( $term ) ) {
			$object = Helpers\get_term_object( $term );

			if ( empty( $object ) ) {
				throw new Query_Exception( 'Unknown term: ' . $term );
			}

			$taxonomy = $object->taxonomy;
			$term     = $object->term_id;
		}

		$this->tax_query[] = [
			'field'            => 'term_id',
			'include_children' => true,
			'operator'         => $operator,
			'taxonomy'         => $taxonomy,
			'terms'            => $term,
		];

		return $this;
	}

	/**
	 * Include a taxonomy query with the relation set to 'AND'.
	 *
	 * @param array|string $term Term ID/array of IDs.
	 * @param string       $taxonomy Taxonomy name.
	 * @param string       $operator Operator to use, defaults to 'IN'.
	 */
	public function andWhereTerm( ...$args ) {
		$this->tax_query['relation'] = 'AND';
		return $this->whereTerm( ...$args );
	}

	/**
	 * Include a taxonomy query with the relation set to 'OR'.
	 *
	 * @param array|string $term Term ID/array of IDs.
	 * @param string       $taxonomy Taxonomy name.
	 * @param string       $operator Operator to use, defaults to 'IN'.
	 */
	public function orWhereTerm( ...$args ) {
		$this->tax_query['relation'] = 'OR';
		return $this->whereTerm( ...$args );
	}
}
