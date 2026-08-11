#!/usr/bin/env python3
"""Erzeugt die dekorativen SVG-Dateien des Themes.

Die Grafiken werden bewusst hier erzeugt und nicht von Hand geschrieben:
Baumkrone und Sternenfeld sollen organisch wirken, nicht geometrisch.
Aufruf:  python3 tools/make-svg.py
"""

import math
import os
import random

OUT = os.path.join(os.path.dirname(__file__), "..", "theme", "lindenzauber", "assets", "img")
OUT = os.path.normpath(OUT)
os.makedirs(OUT, exist_ok=True)


def write(name, body):
    path = os.path.join(OUT, name)
    with open(path, "w", encoding="utf-8") as fh:
        fh.write(body)
    print("%-22s %6d B" % (name, os.path.getsize(path)))


# ---------------------------------------------------------------- Sterne
def sterne(size, count, seed, rmin, rmax, opacity_min, opacity_max, sparkles=0):
    rnd = random.Random(seed)
    parts = []
    for _ in range(count):
        x = rnd.uniform(0, size)
        y = rnd.uniform(0, size)
        r = rnd.uniform(rmin, rmax)
        o = rnd.uniform(opacity_min, opacity_max)
        tint = rnd.choice(["#ffffff", "#fff8e2", "#e8f0ff", "#ffeec4"])
        parts.append(
            '<circle cx="%.1f" cy="%.1f" r="%.2f" fill="%s" opacity="%.2f"/>'
            % (x, y, r, tint, o)
        )

    # ein paar vierzackige Funkelsterne wie auf dem Plakat
    for _ in range(sparkles):
        x = rnd.uniform(0, size)
        y = rnd.uniform(0, size)
        s = rnd.uniform(3.2, 6.4)
        o = rnd.uniform(0.55, 0.95)
        d = (
            "M%.1f %.1fc%.1f %.1f %.1f %.1f %.1f %.1fc%.1f %.1f %.1f %.1f %.1f %.1f"
            "c%.1f %.1f %.1f %.1f %.1f %.1fc%.1f %.1f %.1f %.1f %.1f %.1fZ"
            % (
                x, y - s,
                0.18 * s, 0.55 * s, 0.45 * s, 0.82 * s, s, s,
                -0.55 * s, 0.18 * s, -0.82 * s, 0.45 * s, -s, s,
                -0.18 * s, -0.55 * s, -0.45 * s, -0.82 * s, -s, -s,
                0.55 * s, -0.18 * s, 0.82 * s, -0.45 * s, s, -s,
            )
        )
        parts.append('<path d="%s" fill="#fff6d8" opacity="%.2f"/>' % (d, o))

    return (
        '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" '
        'viewBox="0 0 %d %d">%s</svg>' % (size, size, size, size, "".join(parts))
    )


write("sterne-nah.svg", sterne(620, 130, 7, 0.5, 1.7, 0.30, 0.95, sparkles=7))


# ------------------------------------------------- weichgezeichnete Sterne
def sterne_weich(size=460, count=70, seed=404, unschaerfe=1.5):
    """Sternenfeld mit bereits eingebackener Unschärfe.

    Unterhalb des Kopfbereichs sollen die Sterne ruhig im Hintergrund
    liegen. Ein CSS-Weichzeichner über diese Fläche würde auf schwachen
    Geräten spürbar Leistung kosten; hier steckt die Unschärfe in der
    Grafik und wird genau einmal berechnet.

    Damit an den Kachelrändern keine Nähte entstehen, wird das Sternenfeld
    neunmal versetzt gezeichnet und anschließend auf die Kachel beschnitten.
    """
    rnd = random.Random(seed)
    punkte = []

    for _ in range(count):
        punkte.append(
            '<circle cx="%.1f" cy="%.1f" r="%.2f" fill="%s" opacity="%.2f"/>'
            % (
                rnd.uniform(0, size),
                rnd.uniform(0, size),
                rnd.uniform(1.1, 2.8),
                rnd.choice(["#ffffff", "#fff6dc", "#e6efff"]),
                rnd.uniform(0.35, 0.9),
            )
        )

    versatz = "".join(
        '<use href="#lz-s" x="%d" y="%d"/>' % (dx * size, dy * size)
        for dx in (-1, 0, 1)
        for dy in (-1, 0, 1)
    )

    return (
        '<svg xmlns="http://www.w3.org/2000/svg" width="%(s)d" height="%(s)d" '
        'viewBox="0 0 %(s)d %(s)d">'
        '<defs>'
        '<g id="lz-s">%(p)s</g>'
        '<filter id="lz-weich" x="-20%%" y="-20%%" width="140%%" height="140%%">'
        '<feGaussianBlur stdDeviation="%(b).1f"/></filter>'
        '<clipPath id="lz-kachel"><rect width="%(s)d" height="%(s)d"/></clipPath>'
        '</defs>'
        '<g clip-path="url(#lz-kachel)"><g filter="url(#lz-weich)">%(v)s</g></g>'
        '</svg>' % {"s": size, "p": "".join(punkte), "b": unschaerfe, "v": versatz}
    )


write("sterne-weich.svg", sterne_weich())


# ---------------------------------------------------------------- Lindenbaum
def linde():
    """Silhouette einer Linde: Stamm links unten, Krone nach rechts oben."""
    rnd = random.Random(1309)
    W, H = 900, 1180
    dark = "#05090f"
    mid = "#0a121d"
    leafdark = "#081410"
    leafmid = "#10231a"
    blobs = []
    twigs = []
    fireflies = []

    def branch(x, y, angle, length, width, depth):
        """Zeichnet einen Ast und ruft sich für Nebenäste selbst auf."""
        if depth == 0 or length < 14:
            # Blattbüschel am Astende
            for _ in range(rnd.randint(5, 9)):
                bx = x + rnd.uniform(-30, 30)
                by = y + rnd.uniform(-28, 28)
                r = rnd.uniform(13, 40)
                fill = leafmid if rnd.random() < 0.22 else leafdark
                blobs.append((bx, by, r, fill))
            if rnd.random() < 0.30:
                fireflies.append((x + rnd.uniform(-30, 30), y + rnd.uniform(-30, 30),
                                  rnd.uniform(1.1, 2.6), rnd.uniform(0.25, 0.85)))
            return

        x2 = x + math.cos(angle) * length
        y2 = y - math.sin(angle) * length
        cx = x + math.cos(angle + rnd.uniform(-0.32, 0.32)) * length * 0.55
        cy = y - math.sin(angle + rnd.uniform(-0.32, 0.32)) * length * 0.55
        twigs.append(
            '<path d="M%d %dQ%d %d %d %d" stroke="%s" stroke-width="%.1f" '
            'stroke-linecap="round" fill="none"/>' % (x, y, cx, cy, x2, y2, dark, width)
        )

        forks = 2 if depth > 2 else rnd.randint(2, 3)
        for i in range(forks):
            spread = rnd.uniform(0.24, 0.62) * (1 if i % 2 == 0 else -1)
            branch(
                x2, y2,
                angle + spread + rnd.uniform(-0.12, 0.12),
                length * rnd.uniform(0.58, 0.76),
                max(1.4, width * 0.62),
                depth - 1,
            )

    # Stamm
    trunk = (
        'M56 %d C68 %d 74 %d 88 %d C104 %d 126 %d 150 %d '
        'C176 %d 190 %d 196 %d L232 %d C226 %d 214 %d 200 %d '
        'C176 %d 150 %d 128 %d C104 %d 92 %d 92 %d Z'
        % (H, H - 120, H - 300, H - 460,
           H - 620, H - 760, H - 850,
           H - 900, H - 940, H - 980, H - 980,
           H - 930, H - 880, H - 840,
           H - 780, H - 700, H - 560,
           H - 400, H - 220, H)
    )

    # Wurzelanläufe
    roots = (
        'M40 %d C70 %d 96 %d 120 %d C150 %d 176 %d 210 %d L210 %d Z'
        % (H, H - 26, H - 54, H - 62, H - 70, H - 40, H - 14, H)
    )

    # Hauptäste
    random.Random(4)
    branch(190, H - 900, math.radians(74), 190, 17, 5)
    branch(196, H - 860, math.radians(38), 210, 15, 5)
    branch(186, H - 830, math.radians(14), 230, 13, 5)
    branch(176, H - 780, math.radians(-8), 190, 11, 4)
    branch(178, H - 930, math.radians(104), 150, 12, 4)

    # Krone zusätzlich verdichten
    for _ in range(420):
        a = rnd.uniform(-0.40, 1.40)
        d = rnd.uniform(50, 640)
        bx = 190 + math.cos(a) * d
        by = (H - 880) - math.sin(a) * d * rnd.uniform(0.35, 0.95)
        if by < 20 or bx > W - 20:
            continue
        r = rnd.uniform(14, 62) * (1.0 - min(d / 900.0, 0.55))
        fill = leafmid if rnd.random() < 0.18 else leafdark
        blobs.append((bx, by, r, fill))

    # Blätter als leicht gedrehte Ellipsen – wirkt lebendiger als Kreise
    blob_svg = "".join(
        '<ellipse cx="%d" cy="%d" rx="%d" ry="%d" fill="%s" transform="rotate(%d %d %d)"/>'
        % (bx, by, max(4, r * rnd.uniform(1.0, 1.35)), max(3, r * rnd.uniform(0.62, 0.92)),
           fill, rnd.uniform(-45, 45), bx, by)
        for (bx, by, r, fill) in blobs
    )

    # Vom Laternenlicht angewärmte Blätter direkt über der Laterne
    warm = "".join(
        '<ellipse cx="%d" cy="%d" rx="%d" ry="%d" fill="#1a2b18" opacity="%.2f" '
        'transform="rotate(%d %d %d)"/>'
        % (
            (wx := 190 + rnd.uniform(-40, 150)),
            (wy := (H - 720) - rnd.uniform(0, 200)),
            (rr := rnd.uniform(10, 28)) * 1.2, rr * 0.75,
            rnd.uniform(0.10, 0.28), rnd.uniform(-45, 45), wx, wy,
        )
        for _ in range(40)
    )
    blob_svg += warm
    fly_svg = "".join(
        '<circle cx="%d" cy="%d" r="%.1f" fill="#f6cf7a" opacity="%.2f"/>' % f
        for f in fireflies
    )

    # Laterne, die von einem Ast hängt
    lx, ly = 214, H - 690
    lantern = """
  <g id="lz-laterne">
    <path d="M%(lx)d %(ay)d L%(lx)d %(ly)d" stroke="%(dark)s" stroke-width="2.2" stroke-linecap="round"/>
    <circle cx="%(lx)d" cy="%(gy)d" r="86" fill="url(#lz-laternenlicht)"/>
    <path d="M%(x1)d %(y1)d h44 l7 13 h-58 z" fill="#1d1305"/>
    <path d="M%(x2)d %(y2)d h30 v46 h-30 z" fill="url(#lz-flamme)"/>
    <path d="M%(x1)d %(y1)d h44 v3 h-44 z M%(x1)d %(y3)d h44 v3 h-44 z" fill="#2a1d08"/>
    <path d="M%(x1)d %(y1)d l0 52 M%(x4)d %(y1)d l0 52" stroke="#2a1d08" stroke-width="3"/>
    <path d="M%(x5)d %(y4)d h20 l5 10 h-30 z" fill="#2a1d08"/>
  </g>""" % {
        "lx": lx, "ay": H - 790, "ly": ly - 14, "gy": ly + 24,
        "dark": dark,
        "x1": lx - 22, "y1": ly, "y3": ly + 49,
        "x2": lx - 15, "y2": ly + 4,
        "x4": lx + 22,
        "x5": lx - 10, "y4": ly - 10,
    }

    return """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %(W)d %(H)d" width="%(W)d" height="%(H)d" fill="none" aria-hidden="true" focusable="false">
  <defs>
    <radialGradient id="lz-laternenlicht" cx="50%%" cy="50%%" r="50%%">
      <stop offset="0%%" stop-color="#f9c86a" stop-opacity="0.55"/>
      <stop offset="45%%" stop-color="#f2a83c" stop-opacity="0.18"/>
      <stop offset="100%%" stop-color="#f2a83c" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="lz-flamme" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%%" stop-color="#fff3cf"/>
      <stop offset="55%%" stop-color="#f7c664"/>
      <stop offset="100%%" stop-color="#e08c2a"/>
    </linearGradient>
    <linearGradient id="lz-ausblenden" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%%" stop-color="#fff" stop-opacity="1"/>
      <stop offset="72%%" stop-color="#fff" stop-opacity="1"/>
      <stop offset="100%%" stop-color="#fff" stop-opacity="0"/>
    </linearGradient>
    <mask id="lz-baum-maske">
      <rect x="0" y="0" width="%(W)d" height="%(H)d" fill="url(#lz-ausblenden)"/>
    </mask>
    <filter id="lz-laub-weich" x="-8%%" y="-8%%" width="116%%" height="116%%">
      <feGaussianBlur stdDeviation="1.8"/>
    </filter>
  </defs>
  <g mask="url(#lz-baum-maske)">
    <g filter="url(#lz-laub-weich)">%(blobs)s</g>
    <g>%(twigs)s</g>
    <path d="%(trunk)s" fill="%(dark)s"/>
    <path d="%(roots)s" fill="%(mid)s"/>
    %(lantern)s
    <g>%(flies)s</g>
  </g>
</svg>
""" % {
        "W": W, "H": H,
        "blobs": blob_svg,
        "twigs": "".join(twigs),
        "trunk": trunk,
        "roots": roots,
        "dark": dark,
        "mid": mid,
        "lantern": lantern,
        "flies": fly_svg,
    }


write("linde.svg", linde())


# ---------------------------------------------------------------- Ornament
ORNAMENT = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 34" fill="none" aria-hidden="true" focusable="false">
  <path d="M8 17h96" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
  <path d="M216 17h96" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
  <path d="M104 17c14 0 18-9 28-9s14 9 28 9" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
  <path d="M160 17c14 0 18 9 28 9s14-9 28-9" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
  <circle cx="104" cy="17" r="1.8" fill="currentColor"/>
  <circle cx="216" cy="17" r="1.8" fill="currentColor"/>
  <path d="M160 1c1.9 9.7 4.4 12.2 14 16-9.6 1.9-12.1 4.4-14 16-1.9-9.7-4.4-12.2-14-16 9.6-1.9 12.1-4.4 14-16Z" fill="currentColor"/>
</svg>
"""
write("ornament.svg", ORNAMENT)


# ---------------------------------------------------------------- Icons
ICONS = {
    # Mond mit Sternen – Samstagabend
    "icon-mond.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <path d="M29.5 5A17.5 17.5 0 1 0 42 33.6 14.6 14.6 0 0 1 29.5 5Z" fill="currentColor"/>
  <path d="M38.4 6.6 40 10.7l4.1 1.6-4.1 1.6-1.6 4.1-1.6-4.1-4.1-1.6 4.1-1.6 1.6-4.1Z" fill="currentColor"/>
  <path d="M43.6 20.8l1 2.6 2.6 1-2.6 1-1 2.6-1-2.6-2.6-1 2.6-1 1-2.6Z" fill="currentColor"/>
</svg>
""",
    # Drei Figuren im Kreis – Familientag
    "icon-familie.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <circle cx="24" cy="24" r="21.6" stroke="currentColor" stroke-width="2.2"/>
  <circle cx="16.4" cy="17.4" r="3.1" fill="currentColor"/>
  <circle cx="31.6" cy="17.4" r="3.1" fill="currentColor"/>
  <circle cx="24" cy="24.6" r="2.5" fill="currentColor"/>
  <path d="M16.4 22.2c-3.1 0-5.2 2.1-5.2 5.2v8.2h10.4v-8.2c0-3.1-2.1-5.2-5.2-5.2Z" fill="currentColor"/>
  <path d="M31.6 22.2c-3.1 0-5.2 2.1-5.2 5.2v8.2h10.4v-8.2c0-3.1-2.1-5.2-5.2-5.2Z" fill="currentColor"/>
  <path d="M24 28.4c-2.3 0-3.8 1.6-3.8 3.8v3.6h7.6v-3.6c0-2.2-1.5-3.8-3.8-3.8Z" fill="currentColor"/>
</svg>
""",
    # Ortsmarke
    "icon-ort.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <path d="M24 3c-8.3 0-15 6.7-15 15 0 10.6 13.2 25.6 13.8 26.2a1.6 1.6 0 0 0 2.4 0C25.8 43.6 39 28.6 39 18c0-8.3-6.7-15-15-15Z" fill="currentColor"/>
  <circle cx="24" cy="18" r="5.6" fill="#0e1830"/>
</svg>
""",
    # Lindenblatt – Signet des Festes
    "icon-blatt.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <path d="M24 45V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
  <path d="M24 21S9 20 5 9c11-4 19 3 19 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
  <path d="M24 21c0-9 8-16 19-12-4 11-19 12-19 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
  <path d="M24 33c-4-1-7-4-8-8m8 8c4-1 7-4 8-8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
</svg>
""",
    # Uhr – Erzählzeiten
    "icon-zeit.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <circle cx="24" cy="24" r="19" stroke="currentColor" stroke-width="2.4"/>
  <path d="M24 12v12.6l8 4.6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
""",
}

for name, body in ICONS.items():
    write(name, body)


# ---------------------------------------------------------------- Dorf
def dorf():
    """Sehr flache Dorf- und Kirchensilhouette für den unteren Hero-Rand."""
    rnd = random.Random(88)
    W, H = 1600, 190
    parts = []
    windows = []
    x = 40
    while x < W - 60:
        w = rnd.uniform(46, 104)
        h = rnd.uniform(38, 86)
        roof = rnd.uniform(16, 34)
        base = H
        parts.append(
            '<path d="M%.0f %.0f v%.0f l%.0f -%.0f l%.0f %.0f v%.0f Z" fill="#070d18"/>'
            % (x, base, -h, w / 2, roof, w / 2, roof, h)
        )
        if rnd.random() < 0.7:
            wx = x + w * rnd.uniform(0.3, 0.6)
            wy = base - h * rnd.uniform(0.35, 0.6)
            windows.append(
                '<rect x="%.0f" y="%.0f" width="5" height="6" fill="#f6cf7a" opacity="%.2f"/>'
                % (wx, wy, rnd.uniform(0.35, 0.8))
            )
        x += w + rnd.uniform(6, 26)

    # Kirche mit spitzem Turm, wie auf dem Plakat
    cx = W * 0.42
    parts.append(
        '<path d="M%.0f %d v-118 l22 -46 l22 46 v118 Z" fill="#060b14"/>' % (cx, H)
    )
    parts.append(
        '<path d="M%.0f %d v-74 l30 -30 l30 30 v74 Z" fill="#060b14"/>' % (cx + 44, H)
    )
    parts.append(
        '<path d="M%.0f %.0f v-16 M%.0f %.0f h12" stroke="#f6cf7a" stroke-width="2.4" '
        'stroke-linecap="round" opacity="0.5"/>' % (cx + 22, H - 164, cx + 16, H - 158)
    )
    windows.append(
        '<rect x="%.0f" y="%.0f" width="6" height="9" rx="3" fill="#f6cf7a" opacity="0.7"/>'
        % (cx + 19, H - 96)
    )

    return (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" '
        'preserveAspectRatio="xMidYMax slice" fill="none" aria-hidden="true" focusable="false">'
        '%s%s</svg>' % (W, H, W, H, "".join(parts), "".join(windows))
    )


write("dorf.svg", dorf())


# ---------------------------------------------------------------- Laub an der Banderole
def laub(spiegeln=False):
    """Blattbüschel für die Enden der Banderole »Eintritt frei«."""
    W, H = 104, 96
    # Blatt: Mandelform, Spitze nach rechts
    leaf = "M0 0C16-16 42-13 56 0 42 13 16 16 0 0Z"
    # (Drehung, x, y, Skalierung, Füllung)
    anordnung = [
        (-34, 6, 54, 1.00, "#1b3a20"),
        (-8, 2, 44, 0.86, "#24512a"),
        (18, 10, 36, 0.92, "#16301b"),
        (46, 22, 26, 0.74, "#24512a"),
        (-62, 20, 66, 0.70, "#16301b"),
    ]
    parts = []
    for rot, x, y, s, fill in anordnung:
        parts.append(
            '<g transform="translate(%d %d) rotate(%d) scale(%.2f)">'
            '<path d="%s" fill="%s"/>'
            '<path d="M0 0h56" stroke="#0d1f12" stroke-width="1.6" opacity="0.55"/>'
            "</g>" % (x, y, rot, s, leaf, fill)
        )
    # zwei goldene Beeren, wie die Zierpunkte auf dem Plakat
    parts.append('<circle cx="30" cy="22" r="4.2" fill="#e0b04a"/>')
    parts.append('<circle cx="41" cy="30" r="3.2" fill="#c8912f"/>')

    inhalt = "".join(parts)
    if spiegeln:
        inhalt = '<g transform="translate(%d 0) scale(-1 1)">%s</g>' % (W, inhalt)

    return (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" '
        'fill="none" aria-hidden="true" focusable="false">%s</svg>' % (W, H, W, H, inhalt)
    )


write("laub-links.svg", laub(spiegeln=False))
write("laub-rechts.svg", laub(spiegeln=True))


# ---------------------------------------------------------------- Funkelstern
# Als Aufzählungszeichen: bei 1 rem Größe deutlich besser lesbar als ein Blatt.
FUNKEL = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
  <path d="M24 1c3 14.6 6.4 18.4 22 23-15.6 3-19 6.4-22 23-3-14.6-6.4-18.4-22-23 15.6-3 19-6.4 22-23Z" fill="currentColor"/>
</svg>
"""
write("icon-funkel.svg", FUNKEL)


# ---------------------------------------------------------------- Platzhalter Plakat
def platzhalter_plakat():
    """Ruhiger Rahmen im Plakat-Look, solange das echte Plakat fehlt.

    WordPress entfernt Bild-Blöcke ohne Bildadresse restlos aus der Seite.
    Ein mitgeliefertes Platzhalterbild sorgt deshalb dafür, dass der
    Abschnitt auch vor dem Einsetzen des Plakats vollständig aussieht.
    """
    rnd = random.Random(555)
    W, H = 620, 877  # DIN-A4-Verhältnis
    sterne = "".join(
        '<circle cx="%d" cy="%d" r="%.1f" fill="#fff6d8" opacity="%.2f"/>'
        % (rnd.uniform(24, W - 24), rnd.uniform(24, H - 24),
           rnd.uniform(0.7, 1.9), rnd.uniform(0.2, 0.7))
        for _ in range(60)
    )

    return """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %(W)d %(H)d" width="%(W)d" height="%(H)d" fill="none">
  <defs>
    <linearGradient id="p-himmel" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%%" stop-color="#070d1c"/>
      <stop offset="55%%" stop-color="#13203c"/>
      <stop offset="100%%" stop-color="#0a1224"/>
    </linearGradient>
  </defs>
  <rect width="%(W)d" height="%(H)d" fill="url(#p-himmel)"/>
  <g>%(sterne)s</g>
  <rect x="18" y="18" width="%(iw)d" height="%(ih)d" rx="2" stroke="#c89a34" stroke-width="1.4"
        stroke-dasharray="9 7" opacity="0.55"/>
  <g transform="translate(%(cx)d %(cy)d) scale(2.4)" opacity="0.6">
    <path d="M24 45V21" stroke="#f0c868" stroke-width="1.8" stroke-linecap="round"/>
    <path d="M24 21S9 20 5 9c11-4 19 3 19 12Z" stroke="#f0c868" stroke-width="1.8" stroke-linejoin="round"/>
    <path d="M24 21c0-9 8-16 19-12-4 11-19 12-19 12Z" stroke="#f0c868" stroke-width="1.8" stroke-linejoin="round"/>
    <path d="M24 33c-4-1-7-4-8-8m8 8c4-1 7-4 8-8" stroke="#f0c868" stroke-width="1.3" stroke-linecap="round"/>
  </g>
  <path d="M%(o1)d %(oy)d h96 M%(o2)d %(oy)d h96" stroke="#c89a34" stroke-width="1.1" stroke-linecap="round" opacity="0.6"/>
  <path d="M%(mx)d %(oy2)d c1.9 9.7 4.4 12.2 14 16-9.6 1.9-12.1 4.4-14 16-1.9-9.7-4.4-12.2-14-16 9.6-1.9 12.1-4.4 14-16Z"
        fill="#c89a34" opacity="0.6"/>
</svg>
""" % {
        "W": W, "H": H,
        "iw": W - 36, "ih": H - 36,
        "sterne": sterne,
        "cx": W // 2 - 58, "cy": H // 2 - 130,
        "o1": W // 2 - 150, "o2": W // 2 + 54, "oy": H // 2 + 70,
        "mx": W // 2, "oy2": H // 2 + 54,
    }


write("platzhalter-plakat.svg", platzhalter_plakat())


def platzhalter_bild(W=800, H=600):
    """Querformat-Platzhalter für die Fotogalerie."""
    rnd = random.Random(909)
    sterne = "".join(
        '<circle cx="%d" cy="%d" r="%.1f" fill="#fff6d8" opacity="%.2f"/>'
        % (rnd.uniform(16, W - 16), rnd.uniform(16, H - 16),
           rnd.uniform(0.7, 1.8), rnd.uniform(0.2, 0.65))
        for _ in range(45)
    )

    return """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %(W)d %(H)d" width="%(W)d" height="%(H)d" fill="none">
  <defs>
    <linearGradient id="b-himmel" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%%" stop-color="#0a1122"/>
      <stop offset="100%%" stop-color="#16233e"/>
    </linearGradient>
  </defs>
  <rect width="%(W)d" height="%(H)d" fill="url(#b-himmel)"/>
  <g>%(sterne)s</g>
  <g transform="translate(%(cx)d %(cy)d) scale(2.2)" opacity="0.45">
    <path d="M24 45V21" stroke="#f0c868" stroke-width="1.8" stroke-linecap="round"/>
    <path d="M24 21S9 20 5 9c11-4 19 3 19 12Z" stroke="#f0c868" stroke-width="1.8" stroke-linejoin="round"/>
    <path d="M24 21c0-9 8-16 19-12-4 11-19 12-19 12Z" stroke="#f0c868" stroke-width="1.8" stroke-linejoin="round"/>
    <path d="M24 33c-4-1-7-4-8-8m8 8c4-1 7-4 8-8" stroke="#f0c868" stroke-width="1.3" stroke-linecap="round"/>
  </g>
</svg>
""" % {"W": W, "H": H, "sterne": sterne, "cx": W // 2 - 53, "cy": H // 2 - 53}


write("platzhalter-bild.svg", platzhalter_bild())
print("fertig.")
