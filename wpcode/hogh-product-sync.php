/**
 * HOGH Product Sync
 * Pulls product listings from the GitHub repo and creates them as DRAFT WooCommerce products
 * on hikingoutdoorhub.com, already formatted in the HOGH product template.
 *
 * WPCode: Add Snippet > PHP Snippet > paste everything below > Insert Method: Auto Insert,
 * Location: Run Everywhere > Activate. (Keep "HOGH Blog Sync" active too; they don't clash.)
 *
 * Each product is a JSON file in the repo's /products folder. The site checks hourly and,
 * for any file it hasn't seen, creates a draft product with: name, permalink, short description,
 * full HTML description, category, tags and the Rank Math focus keyword, SEO title and meta description.
 *
 * It NEVER publishes and NEVER sets price, stock, sizes or photos. You add those and publish yourself.
 *
 * Extras when you save a draft after adding photos:
 *  - Fills in empty image alt text from the listing's suggested alt texts (main image first, then gallery).
 *  - Replaces the image markers in the description with your photos (main photo, then the first two
 *    gallery photos), so Rank Math's image test gets full points.
 *
 * Run it on demand (admins only): Tools > Product Sync > Sync now, or visit /wp-admin/?hogh_product_sync=1
 */

// ---- Settings -------------------------------------------------------------
if ( ! defined( 'HOGH_PS_OWNER' ) )  define( 'HOGH_PS_OWNER', 'Mtnhub-1' );
if ( ! defined( 'HOGH_PS_REPO' ) )   define( 'HOGH_PS_REPO', 'hogh-blog' );
if ( ! defined( 'HOGH_PS_BRANCH' ) ) define( 'HOGH_PS_BRANCH', 'main' );
if ( ! defined( 'HOGH_PS_FOLDER' ) ) define( 'HOGH_PS_FOLDER', 'products' );
if ( ! defined( 'HOGH_PS_TOKEN' ) )  define( 'HOGH_PS_TOKEN', '' );  // leave empty for a public repo
if ( ! defined( 'HOGH_PS_MARKER' ) ) define( 'HOGH_PS_MARKER', '<!--hogh-product-image-->' );
// ---------------------------------------------------------------------------

add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'hogh_product_sync' ) ) {
		wp_schedule_event( time() + 120, 'hourly', 'hogh_product_sync' );
	}
} );
add_action( 'hogh_product_sync', 'hogh_product_sync_run' );

if ( ! function_exists( 'hogh_ps_get' ) ) {
	function hogh_ps_get( $url, $raw = false ) {
		$headers = array(
			'User-Agent' => 'HOGH-Product-Sync',
			'Accept'     => $raw ? 'application/vnd.github.raw+json' : 'application/vnd.github+json',
		);
		if ( HOGH_PS_TOKEN ) {
			$headers['Authorization'] = 'Bearer ' . HOGH_PS_TOKEN;
		}
		$r = wp_remote_get( $url, array( 'headers' => $headers, 'timeout' => 25 ) );
		if ( is_wp_error( $r ) ) {
			return new WP_Error( 'hogh_ps_http', $r->get_error_message() );
		}
		$code = (int) wp_remote_retrieve_response_code( $r );
		if ( 404 === $code && ! $raw ) {
			return '[]'; // products folder not created yet
		}
		if ( 200 !== $code ) {
			return new WP_Error( 'hogh_ps_http', 'GitHub returned HTTP ' . $code . ' for ' . $url );
		}
		return wp_remote_retrieve_body( $r );
	}
}

if ( ! function_exists( 'hogh_ps_slug_taken' ) ) {
	// True if any product (published, draft, private, pending or trashed) already uses this slug.
	function hogh_ps_slug_taken( $slug ) {
		global $wpdb;
		$found = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status <> 'auto-draft' AND ( post_name = %s OR post_name = %s ) LIMIT 1",
			$slug, $slug . '__trashed'
		) );
		return (bool) $found;
	}
}

if ( ! function_exists( 'hogh_product_sync_run' ) ) {
	function hogh_product_sync_run() {
		if ( ! class_exists( 'WC_Product_Simple' ) ) {
			return array( 'WooCommerce is not active.' );
		}
		if ( get_transient( 'hogh_ps_lock' ) ) {
			return array( 'Another product sync is already running.' );
		}
		set_transient( 'hogh_ps_lock', 1, 10 * MINUTE_IN_SECONDS );

		$log  = array();
		$done = get_option( 'hogh_ps_synced_files', array() );
		if ( ! is_array( $done ) ) {
			$done = array();
		}

		$list = hogh_ps_get( sprintf(
			'https://api.github.com/repos/%s/%s/contents/%s?ref=%s',
			rawurlencode( HOGH_PS_OWNER ), rawurlencode( HOGH_PS_REPO ), rawurlencode( HOGH_PS_FOLDER ), rawurlencode( HOGH_PS_BRANCH )
		) );
		if ( is_wp_error( $list ) ) {
			$log[] = $list->get_error_message();
			hogh_ps_finish( $log, $done );
			return $log;
		}
		$files = json_decode( $list, true );
		if ( ! is_array( $files ) ) {
			$files = array();
		}
		usort( $files, function ( $a, $b ) {
			return strcmp( $a['name'] ?? '', $b['name'] ?? '' );
		} );

		foreach ( $files as $f ) {
			$name = $f['name'] ?? '';
			if ( '.json' !== substr( $name, -5 ) || in_array( $name, $done, true ) ) {
				continue;
			}
			$raw = hogh_ps_get( $f['url'], true );
			if ( is_wp_error( $raw ) ) {
				$log[] = $name . ': ' . $raw->get_error_message();
				continue; // retry next hour
			}
			$log[]  = $name . ': ' . hogh_ps_create_product( json_decode( $raw, true ), $name );
			$done[] = $name;
		}

		if ( empty( $log ) ) {
			$log[] = 'Nothing new.';
		}
		hogh_ps_finish( $log, $done );
		return $log;
	}
}

if ( ! function_exists( 'hogh_ps_create_product' ) ) {
	function hogh_ps_create_product( $p, $file ) {
		if ( ! is_array( $p ) || empty( $p['name'] ) || empty( $p['description'] ) ) {
			return 'skipped (invalid JSON, or missing name or description).';
		}
		$slug = sanitize_title( ! empty( $p['slug'] ) ? $p['slug'] : $p['name'] );
		if ( hogh_ps_slug_taken( $slug ) ) {
			return 'skipped (a product with permalink "' . $slug . '" already exists).';
		}

		$product = new WC_Product_Simple();
		$product->set_name( wp_strip_all_tags( $p['name'] ) );
		$product->set_slug( $slug );
		$product->set_status( 'draft' ); // always a draft: the owner publishes manually
		$product->set_catalog_visibility( 'visible' );
		$product->set_description( wp_kses_post( $p['description'] ) );
		$product->set_short_description( wp_kses_post( $p['short_description'] ?? '' ) );

		$note = '';
		if ( ! empty( $p['category'] ) ) {
			$cat_ids = array();
			foreach ( (array) $p['category'] as $cat_name ) {
				$term = get_term_by( 'name', $cat_name, 'product_cat' );
				if ( $term ) {
					$cat_ids[] = (int) $term->term_id;
				} else {
					$note .= ' Category "' . $cat_name . '" not found, so it was left out.';
				}
			}
			if ( $cat_ids ) {
				$product->set_category_ids( $cat_ids );
			}
		}

		$id = $product->save();
		if ( ! $id ) {
			return 'failed to create the product.';
		}

		if ( ! empty( $p['tags'] ) ) {
			wp_set_object_terms( $id, array_map( 'sanitize_text_field', (array) $p['tags'] ), 'product_tag', false );
		}
		foreach ( array( 'focus_keyword' => 'rank_math_focus_keyword', 'meta_title' => 'rank_math_title', 'meta_description' => 'rank_math_description' ) as $k => $meta ) {
			if ( ! empty( $p[ $k ] ) ) {
				update_post_meta( $id, $meta, sanitize_text_field( $p[ $k ] ) );
			}
		}
		if ( ! empty( $p['image_alts'] ) ) {
			update_post_meta( $id, '_hogh_image_alts', array_values( array_map( 'sanitize_text_field', (array) $p['image_alts'] ) ) );
		}
		update_post_meta( $id, '_hogh_source_file', $file );

		return 'created draft product #' . $id . ' (' . $slug . ').' . $note;
	}
}

if ( ! function_exists( 'hogh_ps_finish' ) ) {
	function hogh_ps_finish( $log, $done ) {
		update_option( 'hogh_ps_synced_files', array_values( array_unique( $done ) ), false );
		update_option( 'hogh_ps_last_log', array( 'time' => current_time( 'mysql' ), 'log' => $log ), false );
		delete_transient( 'hogh_ps_lock' );
	}
}

// When a synced product is saved: fill empty image alt text and drop the main photo into the description.
// Runs late (priority 99) so WooCommerce has already saved the gallery.
add_action( 'save_post', function ( $post_id, $post ) {
	static $busy = false;
	if ( $busy ) {
		return; // ignore the save triggered by our own update below
	}
	if ( 'product' !== $post->post_type || wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	if ( ! get_post_meta( $post_id, '_hogh_source_file', true ) ) {
		return; // only products created by this sync
	}
	$thumb   = (int) get_post_thumbnail_id( $post_id );
	$gallery = array_values( array_filter( array_map( 'intval', explode( ',', (string) get_post_meta( $post_id, '_product_image_gallery', true ) ) ) ) );
	$images  = array_values( array_filter( array_merge( array( $thumb ), $gallery ) ) );

	// 1. Alt text: main image gets alt 1, gallery images get alt 2, 3, ... Only empty alts are filled.
	$alts = get_post_meta( $post_id, '_hogh_image_alts', true );
	if ( is_array( $alts ) && $alts ) {
		foreach ( $images as $i => $img ) {
			if ( '' === trim( (string) get_post_meta( $img, '_wp_attachment_image_alt', true ) ) ) {
				update_post_meta( $img, '_wp_attachment_image_alt', $alts[ min( $i, count( $alts ) - 1 ) ] );
			}
		}
	}

	// 2. Swap the image markers in the description for your photos:
	//    <!--hogh-product-image--> = main photo, <!--hogh-product-image-2--> = 1st gallery photo,
	//    <!--hogh-product-image-3--> = 2nd gallery photo. A marker stays hidden until its photo exists.
	$map = array(
		HOGH_PS_MARKER                => $thumb,
		'<!--hogh-product-image-2-->' => $gallery[0] ?? 0,
		'<!--hogh-product-image-3-->' => $gallery[1] ?? 0,
	);
	$content = $post->post_content;
	foreach ( $map as $marker => $att ) {
		if ( ! $att || false === strpos( $content, $marker ) ) {
			continue;
		}
		$src = wp_get_attachment_image_url( $att, 'large' );
		if ( $src ) {
			$alt     = get_post_meta( $att, '_wp_attachment_image_alt', true );
			$img     = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" style="width:100%; height:auto; border-radius:8px; margin:20px 0;" />';
			$content = str_replace( $marker, $img, $content );
		}
	}
	if ( $content !== $post->post_content ) {
		$busy = true;
		wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );
		$busy = false;
	}
}, 99, 2 );

// Product edit screen: show the suggested alt texts so they're easy to copy.
add_action( 'add_meta_boxes_product', function ( $post ) {
	$alts = get_post_meta( $post->ID, '_hogh_image_alts', true );
	if ( ! is_array( $alts ) || ! $alts ) {
		return;
	}
	add_meta_box( 'hogh_ps_alts', 'HOGH: suggested image alt text', function () use ( $alts ) {
		echo '<p style="margin-top:0">Filled in automatically for empty alts when you save: main image first, then gallery.</p><ol style="margin-left:18px">';
		foreach ( $alts as $a ) {
			echo '<li>' . esc_html( $a ) . '</li>';
		}
		echo '</ol>';
	}, 'product', 'side', 'low' );
} );

// Tools > Product Sync page: "Sync now" button and the last result.
add_action( 'admin_menu', function () {
	add_management_page( 'Product Sync', 'Product Sync', 'manage_options', 'hogh-product-sync', function () {
		$ran = null;
		if ( isset( $_POST['hogh_ps_now'] ) && check_admin_referer( 'hogh_ps_now' ) ) {
			$ran = hogh_product_sync_run();
		}
		$last = get_option( 'hogh_ps_last_log' );
		$next = wp_next_scheduled( 'hogh_product_sync' );
		echo '<div class="wrap"><h1>Product Sync</h1>';
		echo '<p>Creates draft products from GitHub (' . esc_html( HOGH_PS_OWNER . '/' . HOGH_PS_REPO . '/' . HOGH_PS_FOLDER ) . '). It also runs automatically every hour. Drafts appear under Products &gt; Drafts.</p>';
		if ( $ran ) {
			echo '<div class="notice notice-success"><p><strong>Sync result:</strong><br>' . implode( '<br>', array_map( 'esc_html', (array) $ran ) ) . '</p></div>';
		}
		echo '<form method="post">';
		wp_nonce_field( 'hogh_ps_now' );
		echo '<p><button type="submit" name="hogh_ps_now" value="1" class="button button-primary">Sync now</button></p></form>';
		if ( $last && ! $ran ) {
			echo '<h2>Last sync</h2><p>' . esc_html( $last['time'] ) . '<br>' . implode( '<br>', array_map( 'esc_html', (array) $last['log'] ) ) . '</p>';
		}
		echo '<p>Next automatic check: ' . ( $next ? esc_html( wp_date( 'j M Y, H:i', $next ) ) : 'not scheduled yet' ) . '</p>';
		echo '<p>Product files imported so far: ' . count( (array) get_option( 'hogh_ps_synced_files', array() ) ) . '</p></div>';
	} );
} );

// Manual run for admins: /wp-admin/?hogh_product_sync=1
add_action( 'admin_init', function () {
	if ( isset( $_GET['hogh_product_sync'] ) && current_user_can( 'manage_options' ) ) {
		set_transient( 'hogh_ps_notice', hogh_product_sync_run(), 60 );
		wp_safe_redirect( admin_url( 'edit.php?post_type=product&post_status=draft' ) );
		exit;
	}
} );
add_action( 'admin_notices', function () {
	$log = get_transient( 'hogh_ps_notice' );
	if ( ! $log ) {
		return;
	}
	delete_transient( 'hogh_ps_notice' );
	echo '<div class="notice notice-info is-dismissible"><p><strong>Product Sync:</strong><br>' .
		implode( '<br>', array_map( 'esc_html', (array) $log ) ) . '</p></div>';
} );
