describe( 'Restroom', () => {
	beforeAll( async () => {
		await page.goto( STORE_URL );
	} );

	it( 'should have "built with PooCommerce" footer', async () => {
		const footerText = await page.evaluate( () => document.querySelector( 'body' ).innerText );
		expect( footerText ).toMatch( 'Built with PooCommerce.' );
	} );
} );
