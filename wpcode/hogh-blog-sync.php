/**
 * HOGH Blog Sync
 * Pulls new blog posts from a GitHub repo and publishes them on hikingoutdoorhub.com.
 *
 * WPCode: Add Snippet > PHP Snippet > paste everything below > Insert Method: Auto Insert,
 * Location: Run Everywhere > Activate.
 *
 * Each post is a JSON file in the repo's /posts folder. The site checks hourly,
 * imports any file it hasn't seen, sets the category, tags, Rank Math SEO fields
 * and featured image, then publishes (or schedules it if "publish_at" is in the future).
 *
 * Run it on demand (admins only): Tools > Blog Sync > Sync now, or visit /wp-admin/?hogh_sync=1
 */

// ---- Settings -------------------------------------------------------------
if ( ! defined( 'HOGH_GH_OWNER' ) )  define( 'HOGH_GH_OWNER', 'Mtnhub-1' );
if ( ! defined( 'HOGH_GH_REPO' ) )   define( 'HOGH_GH_REPO', 'hogh-blog' );
if ( ! defined( 'HOGH_GH_BRANCH' ) ) define( 'HOGH_GH_BRANCH', 'main' );
if ( ! defined( 'HOGH_GH_TOKEN' ) )  define( 'HOGH_GH_TOKEN', '' );  // leave empty for a public repo
if ( ! defined( 'HOGH_AUTHOR_ID' ) ) define( 'HOGH_AUTHOR_ID', 1 );  // WordPress user ID shown as author
// ---------------------------------------------------------------------------

add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'hogh_blog_sync' ) ) {
		wp_schedule_event( time() + 60, 'hourly', 'hogh_blog_sync' );
	}
} );
add_action( 'hogh_blog_sync', 'hogh_blog_sync_run' );

if ( ! function_exists( 'hogh_gh_get' ) ) {
	function hogh_gh_get( $url, $raw = false ) {
		$headers = array(
			'User-Agent' => 'HOGH-Blog-Sync',
			'Accept'     => $raw ? 'application/vnd.github.raw+json' : 'application/vnd.github+json',
		);
		if ( HOGH_GH_TOKEN ) {
			$headers['Authorization'] = 'Bearer ' . HOGH_GH_TOKEN;
		}
		$r = wp_remote_get( $url, array( 'headers' => $headers, 'timeout' => 25 ) );
		if ( is_wp_error( $r ) ) {
			return new WP_Error( 'hogh_http', $r->get_error_message() );
		}
		$code = wp_remote_retrieve_response_code( $r );
		if ( 200 !== (int) $code ) {
			return new WP_Error( 'hogh_http', 'GitHub returned HTTP ' . $code . ' for ' . $url );
		}
		return wp_remote_retrieve_body( $r );
	}
}

if ( ! function_exists( 'hogh_blog_sync_run' ) ) {
	function hogh_blog_sync_run() {
		if ( get_transient( 'hogh_sync_lock' ) ) {
			return array( 'Another sync is already running.' );
		}
		set_transient( 'hogh_sync_lock', 1, 10 * MINUTE_IN_SECONDS );

		$log  = array();
		$done = get_option( 'hogh_synced_files', array() );
		if ( ! is_array( $done ) ) {
			$done = array();
		}

		$list_url = sprintf(
			'https://api.github.com/repos/%s/%s/contents/posts?ref=%s',
			rawurlencode( HOGH_GH_OWNER ), rawurlencode( HOGH_GH_REPO ), rawurlencode( HOGH_GH_BRANCH )
		);
		$list = hogh_gh_get( $list_url );
		if ( is_wp_error( $list ) ) {
			$log[] = $list->get_error_message();
			hogh_sync_finish( $log, $done );
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

			$raw = hogh_gh_get( $f['url'], true );
			if ( is_wp_error( $raw ) ) {
				$log[] = $name . ': ' . $raw->get_error_message();
				continue; // retry next hour
			}
			$p = json_decode( $raw, true );
			if ( empty( $p['title'] ) || empty( $p['content'] ) ) {
				$log[]  = $name . ': skipped (missing title or content).';
				$done[] = $name;
				continue;
			}

			$slug = sanitize_title( ! empty( $p['slug'] ) ? $p['slug'] : $p['title'] );
			if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
				$log[]  = $name . ': skipped (a post with slug "' . $slug . '" already exists).';
				$done[] = $name;
				continue;
			}

			$cat_ids = array();
			if ( ! empty( $p['category'] ) ) {
				$term = get_term_by( 'name', $p['category'], 'category' );
				if ( $term ) {
					$cat_ids[] = (int) $term->term_id;
				}
			}

			$status = ( isset( $p['status'] ) && 'draft' === $p['status'] ) ? 'draft' : 'publish';
			$args   = array(
				'post_type'     => 'post',
				'post_title'    => wp_strip_all_tags( $p['title'] ),
				'post_name'     => $slug,
				'post_content'  => wp_kses_post( $p['content'] ),
				'post_excerpt'  => sanitize_text_field( $p['excerpt'] ?? '' ),
				'post_status'   => $status,
				'post_author'   => (int) HOGH_AUTHOR_ID,
				'post_category' => $cat_ids,
				'tags_input'    => array_map( 'sanitize_text_field', (array) ( $p['tags'] ?? array() ) ),
			);

			// Schedule for later if publish_at is in the future.
			if ( 'publish' === $status && ! empty( $p['publish_at'] ) ) {
				$ts = strtotime( $p['publish_at'] );
				if ( $ts && $ts > time() + 60 ) {
					$args['post_status']   = 'future';
					$args['post_date']     = wp_date( 'Y-m-d H:i:s', $ts );
					$args['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $ts );
				}
			}

			$id = wp_insert_post( $args, true );
			if ( is_wp_error( $id ) ) {
				$log[] = $name . ': ' . $id->get_error_message();
				continue;
			}

			// Rank Math SEO fields.
			if ( ! empty( $p['focus_keyword'] ) ) {
				update_post_meta( $id, 'rank_math_focus_keyword', sanitize_text_field( $p['focus_keyword'] ) );
			}
			if ( ! empty( $p['meta_title'] ) ) {
				update_post_meta( $id, 'rank_math_title', sanitize_text_field( $p['meta_title'] ) );
			}
			if ( ! empty( $p['meta_description'] ) ) {
				update_post_meta( $id, 'rank_math_description', sanitize_text_field( $p['meta_description'] ) );
			}
			update_post_meta( $id, '_hogh_source_file', $name );

			// Featured image (optional).
			if ( ! empty( $p['featured_image'] ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
				$alt = sanitize_text_field( $p['featured_image_alt'] ?? $p['title'] );
				// Download manually so image URLs without a file extension (e.g. Unsplash) still work.
				$tmp = download_url( esc_url_raw( $p['featured_image'] ), 30 );
				if ( is_wp_error( $tmp ) ) {
					$img_id = $tmp;
				} else {
					$exts = array( 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif' );
					$mime = wp_get_image_mime( $tmp );
					if ( ! isset( $exts[ $mime ] ) ) {
						@unlink( $tmp );
						$img_id = new WP_Error( 'hogh_img', 'not a supported image type' );
					} else {
						$file   = array( 'name' => $slug . '.' . $exts[ $mime ], 'tmp_name' => $tmp );
						$img_id = media_handle_sideload( $file, $id, $alt );
						if ( is_wp_error( $img_id ) ) {
							@unlink( $tmp );
						}
					}
				}
				if ( ! is_wp_error( $img_id ) ) {
					set_post_thumbnail( $id, $img_id );
					update_post_meta( $img_id, '_wp_attachment_image_alt', $alt );
				} else {
					$log[] = $name . ': post created, image failed (' . $img_id->get_error_message() . ').';
				}
			}

			$done[] = $name;
			$log[]  = $name . ': imported as post #' . $id . ' (' . get_post_status( $id ) . ').';
		}

		if ( empty( $log ) ) {
			$log[] = 'Nothing new.';
		}
		hogh_sync_finish( $log, $done );
		return $log;
	}
}

if ( ! function_exists( 'hogh_sync_finish' ) ) {
	function hogh_sync_finish( $log, $done ) {
		update_option( 'hogh_synced_files', array_values( array_unique( $done ) ), false );
		update_option( 'hogh_sync_last_log', array( 'time' => current_time( 'mysql' ), 'log' => $log ), false );
		delete_transient( 'hogh_sync_lock' );
	}
}

// Tools > Blog Sync page: "Sync now" button and the last sync result.
add_action( 'admin_menu', function () {
	add_management_page( 'Blog Sync', 'Blog Sync', 'manage_options', 'hogh-blog-sync', function () {
		$ran = null;
		if ( isset( $_POST['hogh_sync_now'] ) && check_admin_referer( 'hogh_sync_now' ) ) {
			$ran = hogh_blog_sync_run();
		}
		$last = get_option( 'hogh_sync_last_log' );
		$next = wp_next_scheduled( 'hogh_blog_sync' );
		echo '<div class="wrap"><h1>Blog Sync</h1>';
		echo '<p>Imports new posts from GitHub (' . esc_html( HOGH_GH_OWNER . '/' . HOGH_GH_REPO ) . '). It also runs automatically every hour.</p>';
		if ( $ran ) {
			echo '<div class="notice notice-success"><p><strong>Sync result:</strong><br>' . implode( '<br>', array_map( 'esc_html', (array) $ran ) ) . '</p></div>';
		}
		echo '<form method="post">';
		wp_nonce_field( 'hogh_sync_now' );
		echo '<p><button type="submit" name="hogh_sync_now" value="1" class="button button-primary">Sync now</button></p></form>';
		if ( $last && ! $ran ) {
			echo '<h2>Last sync</h2><p>' . esc_html( $last['time'] ) . '<br>' . implode( '<br>', array_map( 'esc_html', (array) $last['log'] ) ) . '</p>';
		}
		echo '<p>Next automatic check: ' . ( $next ? esc_html( wp_date( 'j M Y, H:i', $next ) ) : 'not scheduled yet' ) . '</p>';
		echo '<p>Posts imported so far: ' . count( (array) get_option( 'hogh_synced_files', array() ) ) . '</p></div>';
	} );
} );

// Manual run + status for admins: /wp-admin/?hogh_sync=1
add_action( 'admin_init', function () {
	if ( isset( $_GET['hogh_sync'] ) && current_user_can( 'manage_options' ) ) {
		$log = hogh_blog_sync_run();
		set_transient( 'hogh_sync_notice', $log, 60 );
		wp_safe_redirect( admin_url( 'edit.php' ) );
		exit;
	}
} );
add_action( 'admin_notices', function () {
	$log = get_transient( 'hogh_sync_notice' );
	if ( ! $log ) {
		return;
	}
	delete_transient( 'hogh_sync_notice' );
	echo '<div class="notice notice-info is-dismissible"><p><strong>Blog Sync:</strong><br>' .
		implode( '<br>', array_map( 'esc_html', (array) $log ) ) . '</p></div>';
} );
