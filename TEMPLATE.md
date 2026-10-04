# HOGH Blog Post Template — Hiking Outdoor Gear Hub

## SEO FIELDS (list separately, before the post)

Focus Keyword:
[Main search phrase, e.g. "what to pack for Mt Kenya"]

SEO Title (under 60 characters, with the keyword at the start, a number, a power word and positive sentiment):
[Keyword]: [Number] [Power Word] Tips for [Outcome]

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


## RULES FOR A 93+ RANK MATH SCORE

- Length: at least 1,400 words. Aim for 1,600-2,000, because Rank Math gives more points for longer posts.
- Keyword density: 1-1.2%, which is about 15-20 uses in a 1,600-word post. Spread them naturally.
- Links: at least 2 internal links (one to a related product, one to another blog post).
- Links: at least 1 dofollow external link to an authority source, using rel="noopener" rather than nofollow.
- Paragraphs: under 120 words each, ideally 2-4 sentences.
- Bold text: none inside paragraphs. Bold labels in bullet lists are fine.
- Image: at least one inside the post body, with the keyword in its alt text.
- Table of Contents: Rank Math only detects this from a TOC plugin or block. Use a plugin such as Easy Table of Contents or LuckyWP Table of Contents, set to auto-insert on posts.


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