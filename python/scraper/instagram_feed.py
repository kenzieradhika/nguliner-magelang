#!/usr/bin/env python3
"""
NGuliner Magelang — Instagram Feed Scraper (best-effort)

Mengambil post publik dari profil Instagram @ngulinermagelang dan
menyimpannya sebagai JSON siap diimpor:

    php artisan feed:import data/instagram.json

CATATAN PENTING:
- Instagram sering memblokir akses publik & mengubah struktur halaman,
  jadi skrip ini best-effort dan hanya menjamin berjalan bila endpoint
  publik masih tersedia.
- Untuk produksi, gunakan Meta Graph API resmi (token + izin
  instagram_basic) — struktur sama, tinggal ganti fungsi fetch_profile.

Pemakaian:
    python instagram_feed.py --username ngulinermagelang --max 12 --output data/instagram.json
"""

from __future__ import annotations

import argparse
import json
import re
import sys
from datetime import datetime, timezone
from pathlib import Path

if sys.stdout and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

import httpx

DEFAULT_HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/126.0 Safari/537.36"
    ),
    "Accept-Language": "id-ID,id;q=0.9,en;q=0.8",
}


def fetch_profile(username: str, max_items: int) -> list[dict]:
    """Coba beberapa jalur publik; kembalikan list post."""
    with httpx.Client(headers=DEFAULT_HEADERS, follow_redirects=True, timeout=30) as client:
        # Jalur 1: endpoint JSON lama (kadang masih jalan untuk profil publik)
        url = f"https://www.instagram.com/{username}/?__a=1&__d=dis"
        try:
            resp = client.get(url)
            if resp.status_code == 200:
                data = resp.json()
                user = (
                    data.get("data", {}).get("user")
                    or data.get("graphql", {}).get("user")
                )
                edges = (
                    user.get("edge_owner_to_timeline_media", {}).get("edges", [])
                    if user else []
                )
                posts = [edge["node"] for edge in edges[:max_items]]
                if posts:
                    return [node_to_post(p) for p in posts]
        except (httpx.HTTPError, ValueError):
            pass

        # Jalur 2: parse halaman profil untuk data JSON tertanam
        resp = client.get(f"https://www.instagram.com/{username}/")
        if resp.status_code != 200:
            return []

        page = resp.text
        shared = re.search(r"window\._sharedData\s*=\s*({.*?});\s*</script>", page, re.DOTALL)
        if shared:
            try:
                user = json.loads(shared.group(1))["entry_data"]["ProfilePage"][0]["graphql"]["user"]
                edges = user.get("edge_owner_to_timeline_media", {}).get("edges", [])
                return [node_to_post(e["node"]) for e in edges[:max_items]]
            except (json.JSONDecodeError, KeyError, IndexError):
                pass

        # Jalur 3: meta description sebagai fallback minimal
        desc = re.search(r'<meta property="og:description" content="([^"]+)"', page)
        if desc:
            return [{
                "ig_id": f"{username}-latest",
                "image_url": None,
                "permalink": f"https://www.instagram.com/{username}/",
                "caption": desc.group(1),
                "posted_at": datetime.now(timezone.utc).isoformat(),
            }]

    return []


def node_to_post(node: dict) -> dict:
    ts = node.get("taken_at_timestamp")
    return {
        "ig_id": str(node.get("id") or node.get("shortcode") or ""),
        "image_url": (
            node.get("display_url")
            or (node.get("thumbnail_resources") or [{}])[-1].get("src")
        ),
        "permalink": f"https://www.instagram.com/p/{node.get('shortcode', '')}/",
        "caption": (node.get("edge_media_to_caption", {}).get("edges") or [{}])[0]
        .get("node", {}).get("text", ""),
        "posted_at": datetime.fromtimestamp(ts, tz=timezone.utc).isoformat() if ts else None,
    }


def main() -> None:
    parser = argparse.ArgumentParser(description="NGuliner Instagram feed scraper")
    parser.add_argument("--username", default="ngulinermagelang")
    parser.add_argument("--max", type=int, default=12)
    parser.add_argument("--output", default="data/instagram.json")
    args = parser.parse_args()

    posts = fetch_profile(args.username, args.max)

    if not posts:
        print("[!] Tidak bisa mengambil feed (Instagram memblokir akses publik).")
        print("    Solusi: gunakan Meta Graph API resmi, atau import JSON dari admin panel.")
        sys.exit(1)

    out = Path(args.output)
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_text(json.dumps(posts, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"[OK] {len(posts)} post tersimpan ke {out}")
    print("    Impor ke Laravel: php artisan feed:import " + str(out))


if __name__ == "__main__":
    main()
