<?php
/**
 * Post_Meta class file.
 *
 * @package Mantle
 */

namespace Mantle\Framework\Database\Model\Meta;

/**
 * Post Model Meta
 */
class Post_Meta {
	/**
	 * Get the meta type for the object.
	 *
	 * @return string
	 */
	public function get_meta_type(): string {
		return 'post';
	}
}
