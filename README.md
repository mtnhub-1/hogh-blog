# hogh-blog

Automated blog posts for [hikingoutdoorhub.com](https://hikingoutdoorhub.com).

- `posts/` — one JSON file per post (`YYYY-MM-DD-N-slug.json`). The site's "HOGH Blog Sync" WPCode snippet checks this folder hourly and publishes any new file.
- `topics-log.md` — every topic already written, so posts never repeat.
- `POST_FORMAT.md` — the JSON fields the site understands.
- `products/` — one JSON file per product listing. The site's "HOGH Product Sync" WPCode snippet (`wpcode/hogh-product-sync.php`) checks hourly and creates each as a DRAFT WooCommerce product; the owner adds price, sizes, condition and photos, then publishes. Format: `PRODUCT_FORMAT.md`; content rules: `PRODUCT_TEMPLATE.md`.
