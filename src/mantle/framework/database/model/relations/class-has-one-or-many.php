<?php
/**
 * Has_One_Or_Many class file.
 *
 * @package Mantle
 */

namespace Mantle\Framework\Database\Model\Relations;

use Mantle\Framework\Database\Model\Model;
use Mantle\Framework\Database\Model\Post;
use Mantle\Framework\Database\Model\Term;
use Mantle\Framework\Database\Query\Builder;
use Mantle\Framework\Database\Query\Post_Query_Builder;
use Mantle\Framework\Database\Query\Term_Query_Builder;

/**
 * Has One or Many Relationship
 */
class Has_One_Or_Many extends Relation {
	/**
	 * Local key.
	 *
	 * @var string
	 */
	protected $local_key;

	/**
	 * Foreign key.
	 *
	 * @var string
	 */
	protected $foreign_key;

	/**
	 * Create a new has one or many relationship instance.
	 *
	 * @param Builder $query Query builder object.
	 * @param Model   $parent Parent model.
	 * @param string  $foreign_key Foreign key.
	 * @param string  $local_key Local key.
	 */
	public function __construct( Builder $query, Model $parent, string $foreign_key, ?string $local_key = null ) {
		$this->foreign_key = $foreign_key;
		$this->local_key   = $local_key;

		parent::__construct( $query, $parent );
	}

	/**
	 * Add constraints to the query.
	 */
	public function add_constraints() {
		return $this->query->whereMeta( $this->foreign_key, $this->parent->get( $this->local_key ) );
	}

	/**
	 * Attach a model to a parent model and save it.
	 *
	 * @param Model[]|Model $model Model instance to save.
	 * @return Model
	 */
	public function save( $model ): Model {
		if ( $this->is_post_term_relationship() ) {
			$this->parent->set_terms( $model );
		} elseif ( $this->is_term_post_relationship() ) {
			$models = is_array( $model ) ? $model : [ $model ];

			foreach ( $models as $model ) {
				$model->set_terms( $this->parent );
			}
		} else {
			$model->set_meta( $this->foreign_key, $this->parent->get( $this->local_key ) );
		}

		return $model;
	}

	/**
	 * Dissociate a model from a parent model.
	 *
	 * @param Model $model Model instance to save.
	 * @return Model
	 */
	public function remove( Model $model ): Model {
		if ( $this->is_post_term_relationship() ) {
			$this->parent->remove_terms( $model );
		} elseif ( $this->is_term_post_relationship() ) {
			$models = is_array( $model ) ? $model : [ $model ];

			foreach ( $models as $model ) {
				$model->remove_terms( $this->parent );
			}
		} else {
			$model->delete_meta( $this->foreign_key );
		}

		return $model;
	}

	/**
	 * Determine if this is a post -> term relationship.
	 *
	 * @return bool
	 */
	protected function is_post_term_relationship(): bool {
		return $this->parent instanceof Post && $this->query instanceof Term_Query_Builder;
	}

	/**
	 * Determine if this is a term -> post relationship.
	 *
	 * @return bool
	 */
	protected function is_term_post_relationship(): bool {
		return $this->parent instanceof Term && $this->query instanceof Post_Query_Builder;
	}
}
