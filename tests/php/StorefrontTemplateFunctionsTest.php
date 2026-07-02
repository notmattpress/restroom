<?php
/**
 * Tests for Restroom template functions.
 *
 * @package restroom
 */

class RestroomTemplateFunctionsTest extends WP_UnitTestCase {

	/**
	 * Footer credit should preserve the PooCommerce credit covered by the browser smoke test.
	 */
	public function test_restroom_credit_displays_poocommerce_credit() {
		ob_start();
		restroom_credit();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'Built with PooCommerce', wp_strip_all_tags( $output ) );
	}
}
