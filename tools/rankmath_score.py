#!/usr/bin/env python3
"""
Local replica of Rank Math's SEO score for one post JSON file.

Built from Rank Math's own analyzer source (seo-by-rank-math, assets/admin/src/analyzer):
same tests, same point values, same total formula: round(points / max_points * 100).
Power words and sentiment words are Rank Math's own English lists (tools/*.json).

Usage:
  python3 tools/rankmath_score.py posts/FILE.json [--content-ai-module-on] [--no-toc] [--no-thumbnail]

Assumptions (match this site): Easy Table of Contents is active (TOC test passes),
the Content AI module is OFF (its 5-point test is not counted), and the post has a
featured image (new posts set featured_image; refreshed posts keep their existing one).
Exit code 0 = score >= 93 (the required minimum), 1 = below 93.
"""
import html
import json
import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
POWER = set(json.load(open(os.path.join(HERE, 'rankmath_powerwords_en.json'))))
SENT = json.load(open(os.path.join(HERE, 'rankmath_sentiment_en.json')))
DOMAIN = 'https://hikingoutdoorhub.com/'
STOP = {'a', 'an', 'and', 'the', 'in', 'on', 'for', 'to', 'of', 'at', 'by', 'with', 'or', 'is'}
REQUIRED = 93


def text_only(h):
    h = re.sub(r'<!--.*?-->', ' ', h, flags=re.S)
    h = re.sub(r'<(script|style)[^>]*>.*?</\1>', ' ', h, flags=re.S | re.I)
    return html.unescape(re.sub(r'<[^>]+>', ' ', h))


def words(t):
    return [w for w in re.split(r'\s+', t) if re.sub(r'[^\w]', '', w)]


def sentiment(title):
    toks = re.sub(r"[.,/#!?$%^&*;:{}=_`\"~()]", ' ', title.lower()).split()
    score, prev = 0, ''
    for t in toks:
        v = SENT.get(t, 0)
        if prev in ('not', "don't", 'no', "isn't", "aren't", "won't", "can't", 'never'):
            v = -v
        score += v
        prev = t
    return score


def slugify(s):
    return re.sub(r'[^a-z0-9]+', '-', s.lower()).strip('-')


def score(p, content_ai=False, toc=True, thumbnail=True):
    content = p.get('content', '')
    kw = p['focus_keyword'].strip().lower()
    seo_title = (p.get('meta_title') or p.get('title') or '').strip()
    tl = seo_title.lower()
    slug = p.get('update_slug') or p.get('slug') or ''
    desc = (p.get('meta_description') or '').lower()
    txt = text_only(content)
    wl = words(txt.lower())
    wc = len(wl)
    R = []  # (test, points, max, note)

    def add(name, ok_pts, mx, note=''):
        R.append((name, ok_pts, mx, note))

    add('Focus keyword in SEO title', 36 if kw in tl else 0, 36)
    add('Focus keyword in meta description', 2 if kw in desc else 0, 2)
    kslug = slugify(kw)
    kslug_ns = '-'.join(w for w in kslug.split('-') if w not in STOP)
    add('Focus keyword in URL', 5 if (kslug in slug or kslug_ns in slug) else 0, 5, slug)
    first = wl[:int(wc * 0.1)] if wc > 400 else wl
    first_txt = ' '.join(re.sub(r'[^\w\s\'-]', '', w) for w in first)
    kw_words = ' '.join(re.sub(r'[^\w\s\'-]', '', w) for w in kw.split())
    add('Focus keyword at beginning of content', 3 if kw_words in first_txt else 0, 3)
    add('Focus keyword in content', 3 if kw in txt.lower() else 0, 3)
    # Safety margin: count 100 fewer words than we see, so small differences in how
    # WordPress counts words can never push a post into a lower length band.
    wc_safe = wc - 100
    for lo, pts in ((2500, 8), (2000, 5), (1500, 4), (1000, 3), (600, 2)):
        if wc_safe >= lo:
            break
    else:
        pts = 0
    add('Content length', pts, 8, f'{wc} words (needs 2600+ here for full points, 2100+ for 5/8)')
    sub = re.search(r'<h[2-6][^>]*>[^<]*' + re.escape(kw) + r'.*?</h[2-6]>', content.lower(), flags=re.S)
    add('Focus keyword in subheading', 3 if sub else 0, 3)
    alts = re.findall(r'<img[^>]*\salt=(["\'])(.*?)\1', content.lower())
    kw_alt = re.escape(' '.join(dict.fromkeys(kw.split()))).replace(r'\ ', '.*')
    thumb_alt = (p.get('featured_image_alt') or '').lower()
    alt_ok = kw in thumb_alt or any(re.search(kw_alt, a[1]) for a in alts)
    add('Focus keyword in image alt', 2 if alt_ok else 0, 2)
    cnt = len(re.findall(re.escape(kw), txt.lower()))
    dens = round(cnt / wc * 100, 2) if wc else 0
    if dens < 0.5 or dens > 2.5:
        dpts = 0
    elif dens < 0.75:
        dpts = 2
    elif dens < 1.0:
        dpts = 3
    else:
        dpts = 6
    add('Keyword density', dpts, 6, f'{dens}% ({cnt} uses; 1.0-2.5% for full points)')
    url_len = len(DOMAIN + slug + '/')
    add('URL length', 4 if url_len <= 75 else 0, 4, f'{url_len} chars')
    links = re.findall(r'<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>', content, flags=re.I)
    tags = re.findall(r'<a\s[^>]*>', content, flags=re.I)
    ext = [t for t in tags if re.search(r'href=["\']https?://', t) and 'hikingoutdoorhub.com' not in t and 'wa.me' not in t]
    ext_any = [t for t in tags if re.search(r'href=["\']https?://', t) and 'hikingoutdoorhub.com' not in t]
    add('Linking to external sources', 4 if ext_any else 0, 4)
    add('External dofollow link', 2 if any('nofollow' not in t for t in ext) else 0, 2)
    internal = [l for l in links if 'hikingoutdoorhub.com' in l or l.startswith('/')]
    add('Linking to internal resources', 5 if internal else 0, 5)
    add('Focus keyword at beginning of SEO title', 3 if 0 <= tl.find(kw) < len(tl) // 2 else 0, 3)
    s = sentiment(seo_title)
    add('Sentiment in title', 1 if s != 0 else 0, 1, f'sentiment {s}')
    pw = [w for w in POWER if (' ' not in w and w in tl.split()) or (' ' in w and w in tl)]
    add('Power word in title', 1 if pw else 0, 1, ', '.join(pw[:3]))
    add('Number in title', 1 if re.search(r'\d', seo_title) else 0, 1)
    add('Table of contents', 2 if toc else 0, 2)
    paras = [len(words(text_only(x))) for x in re.findall(r'<p[^>]*>(.*?)</p>', content, flags=re.S | re.I)]
    longest = max(paras) if paras else 0
    add('Short paragraphs', 3 if longest <= 120 else 0, 3, f'longest {longest} words')
    imgs = len(re.findall(r'<img(?:[^>]+)?>', content, flags=re.I)) + (1 if thumbnail else 0)
    ipts = {0: 0, 1: 1, 2: 2, 3: 4}.get(imgs, 6)
    add('Images / media', ipts, 6, f'{imgs} images incl. featured (4+ for full points)')
    if content_ai:
        add('Content AI', 0, 5, 'not used')
    got = sum(r[1] for r in R)
    mx = sum(r[2] for r in R)
    return round(got / mx * 100), R


def main():
    args = [a for a in sys.argv[1:] if not a.startswith('-')]
    flags = set(a for a in sys.argv[1:] if a.startswith('-'))
    worst = 100
    for f in args:
        p = json.load(open(f))
        if not p.get('content'):
            print(f'{f}: title-only update, not scored')
            continue
        sc, R = score(p, '--content-ai-module-on' in flags, '--no-toc' not in flags, '--no-thumbnail' not in flags)
        worst = min(worst, sc)
        print(f'{f}: {sc}/100 {"PASS" if sc >= REQUIRED else "FAIL (needs " + str(REQUIRED) + "+)"}')
        for name, got, mx, note in R:
            if got < mx or '-v' in flags:
                print(f'   - {name}: {got}/{mx} {note}')
    sys.exit(0 if worst >= REQUIRED else 1)


if __name__ == '__main__':
    main()
