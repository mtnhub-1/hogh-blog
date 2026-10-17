# HOGH Product Listing Template

Used for every product file in `products/` (see `PRODUCT_FORMAT.md` for the JSON fields).
The owner supplies only the product name. Price, sizes, condition, stock and photos are added by the
owner in WordPress, so never invent them in the copy: no prices, size ranges, fabric percentages,
UPF ratings or waterproof claims unless the product name or verified manufacturer specs establish them.
Branded products (e.g. Coleman, Cadac, Salomon): look up the real specs first and use only verified facts.
Stock is mostly thrifted (pre-owned) quality gear, with some new items; don't call an item new or used in the copy.

## SEO fields
- focus_keyword: the plain, searchable product phrase.
- meta_title: under 60 chars; keyword at the start + a number + a power word + positive sentiment.
  Vary the pattern between products (not always "7 Reasons to...").
- slug: lowercase-hyphens, matching the focus keyword.
- meta_description: 140-160 chars, includes keyword, Kenya context, ends with a call to action.
- short_description: 2-3 sentences, includes the keyword, more than a one-line value prop. No WhatsApp mention.
- image_alts: 3 (main/hero in context, close-up of a feature, worn/used on a trail), each containing the keyword.

## Description structure (HTML, in order)
1. Opening paragraph: problem or scenario tied to Kenya's trails or weather. Keyword in the first sentence.
2. Second paragraph: how it works / what makes it good.
3. `<!--hogh-product-image-->` marker on its own line.
4. H2 "Why Choose [keyword]?" + 2 paragraphs, with an internal link to a related product on the site.
5. Key Features info box (blue).
6. H2 "Benefits" + one-line intro paragraph + bullet list (bold labels allowed in bullets only), then `<!--hogh-product-image-2-->`.
7. H2 "Perfect For" + bullet list.
8. H2 "Where to Use [keyword] in Kenya" + 1-2 paragraphs naming real Kenyan trails/places.
9. Closing context paragraph with ONE dofollow external authority link (`target="_blank" rel="noopener"`, never nofollow).
9b. `<!--hogh-product-image-3-->` on its own line.
10. Hikes CTA box (green) linking to https://hikingoutdoorhub.com/hike-with-us/
11. H2 "Frequently Asked Questions" + 4 Q&As as H3; keyword in at least one H3.
12. WhatsApp CTA box (orange, green button) with a product-specific pre-filled message.

## Rank Math rules (target 93+)
- 900+ words in the description (minimum 620).
- Keyword density 1-1.5%; keyword in at least two H2s and one H3.
- No bold or strong tags inside paragraphs.
- Paragraphs under 120 words.
- At least one internal link and one dofollow external link.
- Check before committing: copy the file to a scratch JSON with `content` = `description` (each image marker swapped for a dummy `<img alt="[keyword]">`, since the owner's photos replace them), then run `python3 tools/rankmath_score.py <scratch.json>`. It must report 93+.

## Boxes (exact HTML)

Key Features (blue):
```html
<div style="background-color:#eaf6fa; border-left:5px solid #0B789D; padding:18px 20px; margin:20px 0; border-radius:6px;">
  <h3 style="margin-top:0; color:#0B789D;">Key Features</h3>
  <ul style="margin:0; padding-left:20px; line-height:1.7;">
    <li>[Feature]</li>
  </ul>
</div>
```

Hikes CTA (green):
```html
<div style="background-color:#eef7f0; border:2px solid #2E7D32; border-radius:10px; padding:22px; text-align:center; margin:25px 0;">
  <p style="margin:0 0 14px 0; font-size:16px; font-weight:bold; color:#1b1b1b;">Ready to put your new gear to the test?</p>
  <p style="margin:0 0 16px 0; font-size:14px; color:#333333; line-height:1.6;">Check out our upcoming guided hikes and find your next trail adventure — from beginner-friendly day hikes to longer treks across Kenya's best routes.</p>
  <a href="https://hikingoutdoorhub.com/hike-with-us/" target="_blank" style="display:inline-block; background-color:#2E7D32; color:#ffffff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:15px;">View Our Hike Schedule</a>
</div>
```

WhatsApp CTA (orange box, green button):
```html
<div style="background-color:#fdf3e4; border:2px solid #EE9507; border-radius:10px; padding:22px; text-align:center; margin:25px 0;">
  <p style="margin:0 0 14px 0; font-size:16px; font-weight:bold; color:#1b1b1b;">[Question hook]</p>
  <p style="margin:0 0 16px 0; font-size:14px; color:#333333; line-height:1.6;">Our team can help you [confirm sizing / check compatibility], check current stock, and get your order dispatched quickly. Message us for a quick, straight answer before you buy.</p>
  <a href="https://wa.me/254793764786?text=Hi%2C%20I%27m%20interested%20in%20the%20[URL-ENCODED%20PRODUCT%20NAME]" target="_blank" style="display:inline-block; background-color:#25D366; color:#ffffff; padding:12px 28px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:15px;">Chat on WhatsApp</a>
</div>
```
