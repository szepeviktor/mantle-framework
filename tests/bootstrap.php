<?php

namespace Mantle\Tests;

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/mantle.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require_once __DIR__ . '/../vendor/autoload.php';

try {
	spl_autoload_register(
		\Mantle\Framework\generate_wp_autoloader( __NAMESPACE__, __DIR__ )
	);
} catch ( \Exception $e ) {
	\wp_die( $e->getMessage() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

require $_tests_dir . '/includes/bootstrap.php';
