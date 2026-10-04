# hogh-blog

Automated blog posts for [hikingoutdoorhub.com](https://hikingoutdoorhub.com).

- `posts/` — one JSON file per post (`YYYY-MM-DD-N-slug.json`). The site's "HOGH Blog Sync" WPCode snippet checks this folder hourly and publishes any new file.
- `topics-log.md` — every topic already written, so posts never repeat.
- `POST_FORMAT.md` — the JSON fields the site understands.
