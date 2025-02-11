<?php
/**
 * HTML_String class file
 *
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
 *
 * @package Mantle
 */

namespace Mantle\Testing;

use Mantle\Testing\Concerns\Element_Assertions;
use PHPUnit\Framework\Assert;

/**
 * HTML String
 *
 * Perform assertions against a HTML string.
 */
class Assertable_HTML_String {
	use Element_Assertions;

	/**
	 * Constructor.
	 *
	 * @param string $content The HTML content to test.
	 */
	public function __construct( protected readonly string $content ) {}

	/**
	 * Retrieve the content.
	 */
	protected function get_content(): string {
		return $this->content;
	}

	/**
	 * Assert that the content contains the expected string.
	 *
	 * @param string   $needle The $needle to assert against.
	 * @param int|null $count The number of times the $needle should appear in the content.
	 */
	public function assertContains( string $needle, ?int $count = null ): static {
		Assert::assertStringContainsString( $needle, $this->content, 'The content does not contain the expected string: ' . $needle );

		if ( null !== $count ) {
			Assert::assertEquals(
				$count,
				substr_count( $this->content, $needle ),
				sprintf(
					'The content does not contain the the expected string (%s) %d %s.',
					$needle,
					$count,
					1 === $count ? 'time' : 'times',
				),
			);
		}

		return $this;
	}

	/**
	 * Assert that the content does not contain the expected string.
	 *
	 * @param string $needle The $needle to assert against.
	 */
	public function assertNotContains( string $needle ): static {
		Assert::assertStringNotContainsString( $needle, $this->content, 'The content contains the unexpected string.' );

		return $this;
	}
}
