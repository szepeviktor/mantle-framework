<?php
/**
 * Comment_Meta class file.
 *
 * @package Mantle
 */

namespace Mantle\Framework\Database\Model\Meta;

/**
 * Comment Model Meta
 */
class Comment_Meta {
	/**
	 * Get the meta type for the object.
	 *
	 * @return string
	 */
	public function get_meta_type(): string {
		return 'comment';
	}
}
