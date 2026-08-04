#!/usr/bin/env python3
"""
NGuliner Magelang — Web Scraper Data Kuliner

Mengumpulkan data kuliner dari web publik lalu menormalkannya menjadi
JSON yang siap diimpor ke Laravel:

    php artisan places:import data/kuliner.json

Contoh pemakaian:
    python scraper.py scrape --query "bakso magelang" --source pergi-kuliner --max 20 --output data/kuliner.json
    python scraper.py scrape --query "es dawet magelang" --max 10
    python scraper.py normalize --file hasil-raw.json --output data/kuliner.json

Catatan: struktur HTML situs sumber bisa berubah; selector disimpan per-source
di dict SOURCES agar mudah disesuaikan.
"""

from __future__ import annotations

import argparse
import json
import re
import sys
import time
from dataclasses import dataclass, field
from pathlib import Path
from urllib.parse import quote_plus

if sys.stdout and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

import httpx
from selectolax.parser import HTMLParser

DEFAULT_HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
        "(KHTML, like Gecko) Chrome/126.0 Safari/537.36 NGulinerBot/1.0"
    ),
    "Accept-Language": "id-ID,id;q=0.9,en;q=0.8",
}

# Selector tiap sumber. Key = nama sumber.
SOURCES = {
    "pergi-kuliner": {
        "search_url": "https://www.pergikuliner.com/cari?q={query}",
        "card": "div[data-reactid*='list'] a, .card-item, .content-item",
        "name": "h2, h3, .title, [itemprop='name']",
        "address": "[itemprop='address'], .address, .location",
        "price": ".price, .harga",
        "rating": ".rating, .star",
        "link": "a",
    },
    "generic": {
        "search_url": "https://www.google.com/search?q={query}&num={max}",
        "card": "div[data-sncf], .yuRUbf",
        "name": "h3",
        "address": "",
        "price": "",
        "rating": "",
        "link": "a",
    },
}


@dataclass
class Place:
    name: str = ""
    category: str = ""
    tagline: str = ""
    description: str = ""
    address: str = ""
    latitude: float | None = None
    longitude: float | None = None
    whatsapp: str = ""
    open_days: str = ""
    open_time: str = ""
    close_time: str = ""
    price_range: str = ""
    tips: str = ""
    since_year: int | None = None
    is_legendary: bool = False
    is_featured: bool = False
    image: str = ""
    source: str = ""
    source_url: str = ""

    def to_dict(self) -> dict:
        d = {
            "name": self.name,
            "category": self.category,
            "tagline": self.tagline or None,
            "description": self.description or None,
            "address": self.address or None,
            "latitude": self.latitude,
            "longitude": self.longitude,
            "whatsapp": self.whatsapp or None,
            "open_days": self.open_days or None,
            "open_time": self.open_time or None,
            "close_time": self.close_time or None,
            "price_range": self.price_range or None,
            "tips": self.tips or None,
            "since_year": self.since_year,
            "is_legendary": self.is_legendary,
            "is_featured": self.is_featured,
            "image": self.image or "/img/places/street.svg",
            "source": self.source,
            "source_url": self.source_url,
        }
        return {k: v for k, v in d.items() if v is not None and v != ""}


def clean(text: str) -> str:
    text = re.sub(r"<[^>]+>", " ", text or "")
    text = text.replace("&amp;", "&").replace("&quot;", '"').replace("&#39;", "'")
    text = re.sub(r"\s+", " ", text).strip()
    return text


def guess_category(name: str) -> str:
    name_l = name.lower()
    if "bakso" in name_l:
        return "Bakso"
    if "dawet" in name_l or "cendol" in name_l:
        return "Es Dawet"
    if "martabak" in name_l:
        return "Martabak"
    if "nasi goreng" in name_l or "magelangan" in name_l:
        return "Nasi Goreng Magelangan"
    if "kopi" in name_l or "wedang" in name_l or "ronde" in name_l or "teh" in name_l:
        return "Kopi & Wedang"
    if "sate" in name_l or "mie" in name_l or "soto" in name_l or "getuk" in name_l or "gorengan" in name_l:
        return "Street Food"
    return "Street Food"


def extract_year(text: str) -> int | None:
    m = re.search(r"sejak\s+(?:tahun\s+)?(19|20)\d{2}", text, re.IGNORECASE)
    return int(m.group(1)) if m else None


def extract_time(text: str) -> tuple[str, str]:
    m = re.search(r"(\d{1,2}(?:\.\d{2})?)\s*[-–]\s*(\d{1,2}(?:\.\d{2})?)", text)
    if m:
        open_t = m.group(1).replace(".", ":")
        close_t = m.group(2).replace(".", ":")
        if len(open_t) == 4 and ":" not in open_t:
            open_t = f"{open_t[:2]}:{open_t[2:]}"
        if len(close_t) == 4 and ":" not in close_t:
            close_t = f"{close_t[:2]}:{close_t[2:]}"
        return open_t, close_t
    return "", ""


def scrape_source(client: httpx.Client, query: str, source: str, max_items: int) -> list[Place]:
    cfg = SOURCES.get(source)
    if not cfg:
        print(f"[!] Sumber '{source}' tidak dikenal. Pilihan: {', '.join(SOURCES)}")
        sys.exit(1)

    url = cfg["search_url"].format(query=quote_plus(query), max=max_items)
    print(f"[*] Mengambil {url}")

    resp = client.get(url, follow_redirects=True, timeout=30)
    resp.raise_for_status()
    parser = HTMLParser(resp.text)

    nodes = parser.css(cfg["card"])
    print(f"[*] Ditemukan {len(nodes)} kartu")

    places: list[Place] = []
    for node in nodes[:max_items]:
        name = clean(node.css_first(cfg["name"]).text() if node.css_first(cfg["name"]) else "")
        if not name or len(name) < 3:
            continue

        link = ""
        link_node = node.css_first(cfg["link"]) if cfg["link"] else None
        if link_node:
            href = link_node.attributes.get("href", "")
            link = href if href.startswith("http") else f"https://www.pergikuliner.com{href}" if source == "pergi-kuliner" else href

        address = clean(node.css_first(cfg["address"]).text()) if cfg["address"] and node.css_first(cfg["address"]) else ""
        price = clean(node.css_first(cfg["price"]).text()) if cfg["price"] and node.css_first(cfg["price"]) else ""
        card_text = clean(node.text())

        p = Place(
            name=name,
            category=guess_category(name),
            address=address,
            price_range=price or ("Rp8.000" if "Rp" in card_text else ""),
            since_year=extract_year(card_text),
            open_time=extract_time(card_text)[0],
            close_time=extract_time(card_text)[1],
            source=source,
            source_url=link,
        )

        if "legend" in card_text.lower():
            p.is_legendary = True

        places.append(p)
        time.sleep(0.4)

    return places


def normalize(raw: list | dict) -> list[Place]:
    """Normalisasi data mentah (apa pun bentuknya) menjadi list Place."""
    if isinstance(raw, dict):
        raw = raw.get("places") or raw.get("data") or [raw]

    places: list[Place] = []
    for item in raw:
        if not isinstance(item, dict):
            continue
        name = clean(str(item.get("name", "")))
        if not name:
            continue
        card_text = clean(" ".join(str(v) for v in item.values() if isinstance(v, (str, int))))

        p = Place(
            name=name,
            category=str(item.get("category") or guess_category(name)),
            tagline=clean(str(item.get("tagline", ""))),
            description=clean(str(item.get("description", ""))),
            address=clean(str(item.get("address", ""))),
            whatsapp=clean(str(item.get("whatsapp", ""))),
            price_range=clean(str(item.get("price_range", ""))),
            tips=clean(str(item.get("tips", ""))),
            is_legendary=bool(item.get("is_legendary", False)),
            is_featured=bool(item.get("is_featured", False)),
            image=clean(str(item.get("image", ""))),
            source=str(item.get("source", "manual")),
            source_url=str(item.get("source_url", "")),
        )

        lat = item.get("latitude")
        lng = item.get("longitude")
        try:
            p.latitude = float(lat) if lat not in (None, "") else None
            p.longitude = float(lng) if lng not in (None, "") else None
        except (TypeError, ValueError):
            pass

        year = item.get("since_year")
        p.since_year = int(year) if year else extract_year(card_text)

        ot, ct = extract_time(card_text)
        p.open_time = clean(str(item.get("open_time", ""))) or ot
        p.close_time = clean(str(item.get("close_time", ""))) or ct
        p.open_days = clean(str(item.get("open_days", ""))) or "Mon,Tue,Wed,Thu,Fri,Sat,Sun"

        places.append(p)

    return places


def main() -> None:
    parser = argparse.ArgumentParser(description="NGuliner web scraper data kuliner")
    sub = parser.add_subparsers(dest="command", required=True)

    p_scrape = sub.add_parser("scrape", help="Scrape dari web publik")
    p_scrape.add_argument("--query", required=True, help="Kata kunci, mis. 'bakso magelang'")
    p_scrape.add_argument("--source", default="pergi-kuliner", choices=list(SOURCES))
    p_scrape.add_argument("--max", type=int, default=20)
    p_scrape.add_argument("--output", default="data/kuliner.json")

    p_norm = sub.add_parser("normalize", help="Normalisasi file JSON mentah")
    p_norm.add_argument("--file", required=True, help="File JSON mentah")
    p_norm.add_argument("--output", default="data/kuliner.json")

    args = parser.parse_args()

    if args.command == "scrape":
        with httpx.Client(headers=DEFAULT_HEADERS) as client:
            places = scrape_source(client, args.query, args.source, args.max)
    else:
        with open(args.file, encoding="utf-8") as f:
            places = normalize(json.load(f))

    if not places:
        print("[!] Tidak ada data ditemukan.")
        sys.exit(1)

    out = Path(args.output)
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_text(json.dumps([p.to_dict() for p in places], ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"[OK] {len(places)} tempat tersimpan ke {out}")
    print("    Impor ke Laravel: php artisan places:import " + str(out))


if __name__ == "__main__":
    main()
