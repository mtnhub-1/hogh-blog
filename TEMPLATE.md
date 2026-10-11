# HOGH Blog Post Template — Hiking Outdoor Gear Hub

## SEO FIELDS (list separately, before the post)

Focus Keyword:
[Main search phrase, e.g. "what to pack for Mt Kenya"]

Post Title / H1 (what readers see on the page): written like a human editor would, specific to this post, and different in shape from recent titles. It should contain the keyword or a close natural variant.

SEO Title (under 60 characters, keyword at the start): states what the reader gets, in plain words. It can differ from the Post Title.

TITLE VARIETY RULES (both titles):
- Never reuse the "[Keyword]: [Number] [Adjective] Tips for [Outcome]" formula.
- Before writing, read the titles of the last 15 posts in topics-log.md / the posts folder. Today's titles must not share their structure or adjectives.
- Rotate between shapes such as:
  - Question: "Do You Really Need Rain Pants for Hiking in Kenya?"
  - How/what statement: "What to Wear Hiking in Kenya, From Ngong Hills to Mt Kenya"
  - Problem or scene: "Caught in a Downpour? How to Hike Safely in Kenya's Rains"
  - Comparison: "Shell, Fleece or Down? Hiking Jackets for Kenya Explained"
  - Plain guide: "Where to Buy Hiking Boots in Nairobi (and How to Get the Fit Right)"
  - Specific detail: "Elephant Hill: The Aberdares' Toughest Day Hike, Explained"
- Use a number in at most 1 in 4 titles, and only when the post really is a numbered list or checklist.
- Banned in titles: Essential, Ultimate, Smart, Proven, Amazing, Brilliant, Expert, Complete Guide, Everything You Need to Know, Top Tips, Unlock, Discover, Master.
- No title may start with the same first three words as any of the last 15 titles.

Permalink (short, keyword only):
[keyword-in-lowercase-hyphens]

Meta Description (under 160 characters, includes the keyword):
[What the post covers + benefit to the reader]. [Kenya context]. Read the full guide.

Category: one of Hiking Gear Guides, Hiking Tips or Hiking Trails Guide

Tags: 3-5 relevant tags

Excerpt: 1-2 sentences summarising the post, including the keyword

Image Alt Text:
1. Featured image: [Keyword] – [scene description]
2. In-content image: [Scene] on a hiking trail in Kenya (include the keyword)


## POST BODY STRUCTURE (HTML, in order)

1. Intro: 2 short paragraphs. Put the keyword in the first sentence, then state the reader's problem and what the post will give them.
2. In-content image, with keyword alt text.
3. Info Box 1: Quick Facts / Key Takeaways (blue).
4. H2 sections: 4-6 main sections, using H3s for sub-points. Put the keyword in at least one H2 and one H3.
5. Shop CTA (blue): place it right after the section that discusses gear.
6. Info Box 2: Pro Tips / Checklist (blue).
7. Hikes CTA (green): place it after the trail or experience content.
8. H2: Frequently Asked Questions, with 4-5 Q&As as H3s.
9. H2: Final Thoughts, a short conclusion with the keyword and a nudge to act.
10. WhatsApp CTA (orange box, green button), at the very end.


## RULES FOR A 93+ RANK MATH SCORE (MANDATORY)

Every post must score 93 or higher in Rank Math. tools/rankmath_score.py reproduces Rank Math's own scoring (same tests, same points, same word lists) and must report PASS before a post is saved. SETTINGS.md says whether the Content AI module is on; if it is on, run the checker with --content-ai-module-on.

Where the points are (Rank Math's real weights):
- Focus keyword in the SEO title (36 of ~100 points): the meta_title must contain the exact focus keyword.
- Content length (8 points): 2,500+ words gets full points; 2,000-2,499 gets 5; 1,500-1,999 gets 4. When the Content AI module is ON, 2,500+ words is required to reach 93. When it is OFF, 2,000+ is enough. Reach the length with genuinely useful sections (worked examples, comparisons, local specifics, common mistakes, care and maintenance, sizing), never padding.
- Images (6 points): 4 or more images in total, counting the featured image. So every post needs a featured image plus at least 3 <img> tags in the body, spread through the post, each with descriptive alt text (at least one containing the focus keyword).
- Keyword density (6 points): full points from 1.0% to 2.5%. Aim for 1.1-1.5% (the checker counts exact-phrase uses ÷ total words).
- Internal links (5), keyword in URL (5), dofollow external link (4 + 2), URL 75 characters or less including https://hikingoutdoorhub.com/ (4).
- 3 points each: keyword in the first 10% of the content, keyword in the content, keyword in an H2-H6 subheading, keyword in the first half of the SEO title, no paragraph over 120 words.
- 2 points each: keyword in the meta description, keyword in an image alt, Table of Contents (Easy Table of Contents plugin, auto-inserted).
- 1 point each, SEO title only: a number, a Rank Math power word (tools/rankmath_powerwords_en.json) and an emotional sentiment word (tools/rankmath_sentiment_en.json). Words that are both and read naturally: best, easy, safe, honest, free, comfortable, reliable, great, affordable, happy. Numbers can be natural: distances (13.5 km), altitudes (4,985 m), days, layers, months, list counts. Rotate the words; do not reuse the same power word in two consecutive posts' SEO titles.
- These title points apply to the SEO title (meta_title) only. The on-page Post Title (H1) stays fully human and varied.

Other rules:
- Bold text: none inside paragraphs. Bold labels in bullet lists are fine.
- Every word must earn its place.

## RULES FOR AEO (AI Overviews, ChatGPT, Perplexity and other answer engines)

Answer engines quote short, self-contained passages. Write so that any section can be lifted out and still make sense.

- Answer first: the first paragraph of the intro directly answers the question behind the keyword in 40-60 words, with the keyword in the first sentence. Explain the reader's problem and what the post covers in the second paragraph.
- Question headings: where natural, phrase H2s and H3s as the questions people actually search (What / How / When / Which / Is / Can), using wording seen in Google's "People also ask" and related searches during research.
- Answer under every heading: the first 1-2 sentences under each H2/H3 answer that heading directly (under 40 words) before going into detail.
- Self-contained passages: name the subject instead of starting with "it", "this" or "they" when a passage opens a section, so it still makes sense when quoted alone.
- Specific facts: give concrete, verified details such as elevations (m), distances (km), durations (hours), best months, difficulty, start points, counties and gear specs. Avoid vague claims like "fairly long" or "quite cold".
- Quick Facts box: 5-7 self-contained bullet facts, each with a label (e.g. <strong>Elevation:</strong> 3,700 m).
- Structured content: include at least one HTML table (comparison, kit list, route stages or month-by-month conditions) or numbered step list where it fits the topic. Use this table style:
  <table style="width:100%; border-collapse:collapse; margin:20px 0;"><thead><tr style="background-color:#0B789D; color:#ffffff;"><th style="padding:10px; text-align:left;">[Header]</th><th style="padding:10px; text-align:left;">[Header]</th></tr></thead><tbody><tr style="border-bottom:1px solid #dddddd;"><td style="padding:10px;">[Cell]</td><td style="padding:10px;">[Cell]</td></tr></tbody></table>
- FAQ answers: 40-80 words each, starting with a direct answer and repeating the subject (e.g. "Mt Longonot takes 4-5 hours..." rather than "It takes..."). Use real search questions.
- Final Thoughts: summarise the key answer in 2-3 sentences (worded freshly, see "RULES FOR SOUNDING HUMAN"), then the nudge to act.
- Trust signals: write as a Kenyan hiking gear and guided-hike business that knows these trails, but never invent personal stories, customer quotes, statistics or experiences. Cite authority sources for safety, park and weather facts.
- Avoid putting the year in titles or slugs so posts don't go stale; mention seasons and conditions instead.


## THE BOX TYPES (exact HTML)

### Info Box (blue), used twice. Change the heading each time:

<div style="background-color:#eaf6fa; border-left:5px solid #0B789D; padding:18px 20px; margin:20px 0; border-radius:6px;">
  <h3 style="margin-top:0; color:#0B789D;">[Quick Facts / Pro Tips / Packing Checklist]</h3>
  <ul style="margin:0; padding-left:20px; line-height:1.7;">
    <li>[Point 1]</li>
    <li>[Point 2]</li>
    <li>[Point 3]</li>
  </ul>
</div>

### Shop CTA (blue):

<div style="background-color:#eaf6fa; border:2px solid #0B789D; border-radius:10px; padding:22px; text-align:center; margin:25px 0;">
  <p style="margin:0 0 14px 0; font-size:16px; font-weight:bold; color:#1b1b1b;">Gear up for your next hike</p>
  <p style="margin:0 0 16px 0; font-size:14px; color:#333333; line-height:1.6;">Shop quality hiking and outdoor gear at great prices — from socks and gloves to hats, balaclavas and camping fuel. Everything you need for Kenya's trails, delivered fast.</p>
  <a href="https://hikingoutdoorhub.com/shop/" target="_blank" style="display:inline-block; background-color:#0B789D; color:#ffffff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:15px;">Shop Hiking Gear</a>
</div>

### Hikes CTA (green):

<div style="background-color:#eef7f0; border:2px solid #2E7D32; border-radius:10px; padding:22px; text-align:center; margin:25px 0;">
  <p style="margin:0 0 14px 0; font-size:16px; font-weight:bold; color:#1b1b1b;">Ready to hit the trail?</p>
  <p style="margin:0 0 16px 0; font-size:14px; color:#333333; line-height:1.6;">Join one of our upcoming guided hikes — from beginner-friendly day hikes to longer treks across Kenya's best routes.</p>
  <a href="https://hikingoutdoorhub.com/hike-with-us/" target="_blank" style="display:inline-block; background-color:#2E7D32; color:#ffffff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:15px;">View Our Hike Schedule</a>
</div>

### WhatsApp CTA (orange box, green button):

<div style="background-color:#fdf3e4; border:2px solid #EE9507; border-radius:10px; padding:22px; text-align:center; margin:25px 0;">
  <p style="margin:0 0 14px 0; font-size:16px; font-weight:bold; color:#1b1b1b;">[Question hook related to the post]</p>
  <p style="margin:0 0 16px 0; font-size:14px; color:#333333; line-height:1.6;">Our team can help you choose the right gear, answer your hiking questions and get your order dispatched quickly. Message us for a quick, straight answer.</p>
  <a href="https://wa.me/254793764786?text=Hi%2C%20I%20just%20read%20your%20blog%20post%20on%20[POST_TOPIC]" target="_blank" style="display:inline-block; background-color:#25D366; color:#ffffff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:15px;">Chat on WhatsApp</a>
</div>

### In-content image:

<img src="[IMAGE URL]" alt="[Keyword] [scene description]" style="width:100%; height:auto; border-radius:8px; margin:20px 0;" />

Note: the Shop CTA assumes the shop page is at /shop/ (WooCommerce default). Change it if yours is different.

## RULES FOR SOUNDING HUMAN (avoid an "AI-written" feel)

Readers and Google both notice formulas repeated across posts. Each post should read as if a knowledgeable Kenyan hiker and shop owner wrote it for this specific topic.

- Vary openings. The intro must not start the same way as any of the last 10 posts. Do not open with "The [keyword] is..." every time; lead with a concrete detail, a common mistake, a season, a place, or the reader's situation, and still answer the question within the first 40-60 words.
- Vary the Final Thoughts. Do not begin it with "The best [keyword] is/are...", "[Keyword] is simple...", "The simplest answer to...", or any opening used in the last 10 posts. Summarise in fresh words, then give one specific next step.
- Banned phrases anywhere in the body: "this guide", "in this guide", "whether you're a beginner or", "with the right gear", "takes the guesswork out", "look no further", "embark", "journey" (unless literally about travel), "delve", "dive into", "navigate" (unless about route-finding), "game-changer", "unforgettable", "breathtaking", "nestled", "hidden gem", "elevate", "seamless", "rest assured", "peace of mind", "plays a key role", "it's worth noting", "in conclusion", "ultimately", "adventure awaits".
- Use "essential" at most once per post, and "crucial" / "vital" not at all.
- Prefer specifics over adjectives: name the trail section, the month, the altitude, the fabric, the boot feature, the matatu stage, the temperature. Concrete details are what make writing feel human and trustworthy.
- Vary sentence length and rhythm. Mix short sentences with longer ones. Avoid lists of three adjectives.
- Not every section needs a bullet list. Use prose where a person would naturally explain something.
- FAQ questions must be phrased the way real people ask them (including slightly informal wording), not as tidy restatements of the H2s.
- Before saving, compare today's intros, H2s and Final Thoughts openings with the last 10 posts and rewrite anything that matches their pattern.
