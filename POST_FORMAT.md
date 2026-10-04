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
