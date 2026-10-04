/**
 * HOGH Local Business Schema
 * Tells Google the shop's name, address, phone and type, on the homepage
 * and the Nairobi shop page.
 *
 * WPCode: Add Snippet > PHP Snippet > paste everything below > Insert Method: Auto Insert,
 * Location: Run Everywhere > Activate.
 */
add_action( 'wp_head', function () {
	if ( ! is_front_page() && ! is_page( 'hiking-gear-shop-nairobi' ) ) {
		return;
	}
	$schema = array(
		'@context'          => 'https://schema.org',
		'@type'             => array( 'SportingGoodsStore', 'Store' ),
		'@id'               => 'https://hikingoutdoorhub.com/#localbusiness',
		'name'              => 'Hiking Outdoor Gear Hub',
		'description'       => 'Hiking and outdoor gear shop in Nairobi selling quality second-hand and new hiking gear, and organising guided hikes across Kenya.',
		'url'               => 'https://hikingoutdoorhub.com/',
		'telephone'         => '+254793764786',
		'priceRange'        => 'KSh',
		'currenciesAccepted' => 'KES',
		'paymentAccepted'   => 'M-Pesa, Cash',
		'address'           => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Mountain Mall, 3rd Floor, Shop D39, Thika Road',
			'addressLocality' => 'Nairobi',
			'addressRegion'   => 'Nairobi County',
			'addressCountry'  => 'KE',
		),
		'hasMap'            => 'https://www.google.com/maps?q=Mountain+Mall,+Thika+Road,+Nairobi',
		'areaServed'        => array( 'Nairobi', 'Kenya' ),
	);
	$logo = get_site_icon_url( 512 );
	if ( $logo ) {
		$schema['logo']  = $logo;
		$schema['image'] = $logo;
	}
	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
} );
