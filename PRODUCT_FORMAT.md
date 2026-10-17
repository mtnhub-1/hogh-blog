# Product JSON format

One file per product in `products/`, named `YYYY-MM-DD-<slug>.json`.
The site's "HOGH Product Sync" WPCode snippet checks this folder hourly and creates each new file as a
DRAFT WooCommerce product. It never publishes and never sets price, stock, sizes, condition or photos:
the owner adds those and publishes manually.

```json
{
  "name": "Product name as shown in the shop",
  "slug": "permalink-matching-the-focus-keyword",
  "category": ["Existing WooCommerce category name"],
  "tags": ["tag one", "tag two"],
  "focus_keyword": "main keyword",
  "meta_title": "SEO title, under 60 chars, keyword first",
  "meta_description": "SEO description, 140-160 chars, includes keyword",
  "short_description": "<p>2-3 sentences with the keyword.</p>",
  "description": "<p>Full product description HTML in the HOGH product template...</p>",
  "image_alts": ["Main image alt", "Close-up alt", "In-use alt"]
}
```

- `category` is optional and must match an existing product category name exactly; unknown names are skipped (no new categories are created).
- `image_alts` are applied when the owner saves the draft after adding photos: main image gets the first, gallery images the next, and only empty alts are filled. They also show in a side box on the product edit screen.
- Put `<!--hogh-product-image-->` once in `description` (after the second paragraph). When the owner sets a main photo and saves, it is replaced with that photo so Rank Math's image test passes.
- If a product with the same slug already exists (any status, including trash), the file is skipped.
- Content rules are in `PRODUCT_TEMPLATE.md`.
