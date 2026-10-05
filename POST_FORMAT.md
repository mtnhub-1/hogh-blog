# Post JSON format

```json
{
  "title": "Post title (H1)",
  "slug": "url-slug",
  "category": "Hiking Gear Guides | Hiking Tips | Hiking Trails Guide",
  "tags": ["tag one", "tag two"],
  "focus_keyword": "main keyword",
  "meta_title": "SEO title, max ~60 chars",
  "meta_description": "SEO description, 140-160 chars",
  "excerpt": "1-2 sentence summary",
  "content": "<p>Full post HTML (no H1)...</p>",
  "featured_image": "https://direct-image-url.jpg (optional)",
  "featured_image_alt": "alt text containing the focus keyword",
  "status": "publish | draft",
  "publish_at": "2026-10-05T07:00:00+03:00"
}
```

`status: "draft"` makes the post land as a draft for review. `publish_at` in the future schedules it.

## Refresh an existing post

Add `"update_slug": "<existing-post-slug>"` to rewrite that post in place instead of creating a new one.
The URL (slug), publish date and author stay the same. Title, content, excerpt, category, tags,
Rank Math fields and (optionally) the featured image are replaced. `slug`, `status` and `publish_at`
are ignored in refresh mode. Always also set `slug` to the same existing slug as a safety net: an older version of the sync snippet without refresh mode will then skip the file (slug already exists) instead of creating a duplicate. WordPress keeps the old version under Revisions.
Filename: posts/YYYY-MM-DD-3-refresh-<slug>.json
