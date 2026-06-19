#!/usr/bin/env python3
"""Generate the extension's PNG icons from the brand padlock mark.

No SVG rasterizer is needed: each icon is drawn natively with Pillow at 4x and
downsampled (LANCZOS) for clean antialiasing. Mirrors extension/icon.svg — the
floating-padlock glyph in cream on brand accent green (#3a4f3a).

Run inside a venv with Pillow:  python make-icons.py
"""
from PIL import Image, ImageDraw

GREEN = (58, 79, 58, 255)    # --accent  #3a4f3a
CREAM = (245, 242, 236, 255) # --bg      #f5f2ec
SS = 4                       # supersample factor

def draw(size: int) -> Image.Image:
    n = size * SS
    img = Image.new("RGBA", (n, n), (0, 0, 0, 0))
    d = ImageDraw.Draw(img)
    u = n / 128.0  # design grid is 128 units

    # Rounded-square background.
    d.rounded_rectangle([0, 0, n - 1, n - 1], radius=28 * u, fill=GREEN)

    sw = max(2, round(8 * u))   # body/shackle stroke
    # Lock body (stroked rounded rect).
    d.rounded_rectangle([38 * u, 58 * u, 90 * u, 102 * u], radius=9 * u,
                        outline=CREAM, width=sw)
    # Shackle: an arc bridging two uprights.
    d.arc([51 * u, 34 * u, 77 * u, 60 * u], start=180, end=360, fill=CREAM, width=sw)
    d.line([51 * u, 47 * u, 51 * u, 58 * u], fill=CREAM, width=sw)
    d.line([77 * u, 47 * u, 77 * u, 58 * u], fill=CREAM, width=sw)
    # Keyhole.
    d.ellipse([59 * u, 71 * u, 69 * u, 81 * u], fill=CREAM)
    d.line([64 * u, 79 * u, 64 * u, 88 * u], fill=CREAM, width=max(2, round(6 * u)))

    return img.resize((size, size), Image.LANCZOS)

for s in (16, 48, 128):
    draw(s).save(f"icons/icon{s}.png")
    print(f"wrote icons/icon{s}.png")
