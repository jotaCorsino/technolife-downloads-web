#!/usr/bin/env python3
"""Build two separate, deterministic archives for manual cPanel upload."""

from __future__ import annotations

import argparse
import hashlib
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile, ZipInfo


ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / "dist" / "DEP-001"
PUBLIC = {
    "public/index.php": "links/index.php",
    "public/styles.css": "links/styles.css",
    "public/app.js": "links/app.js",
    "public/assets/logo-technolife.png": "links/assets/logo-technolife.png",
    "public/assets/favicon-technolife.png": "links/assets/favicon-technolife.png",
}
PRIVATE = {
    "src/DownloadScanner.php": "technolife-links-private/DownloadScanner.php",
    "deployment/config.php.example": "technolife-links-private/config.php.example",
}
LEVELS_MARKER = b"const PRIVATE_PARENT_LEVELS = 0;"


def archive(name: str, files: dict[str, str], private_levels: int) -> Path:
    target = OUTPUT / name
    with ZipFile(target, "w") as package:
        for source_name, archive_name in sorted(files.items()):
            source = ROOT / source_name
            if not source.is_file() or source.is_symlink():
                raise RuntimeError(f"Fonte ausente ou simbólica: {source_name}")
            data = source.read_bytes()
            if source_name == "public/index.php" and private_levels:
                if data.count(LEVELS_MARKER) != 1:
                    raise RuntimeError("Marcador de localização privada ausente no index.php")
                data = data.replace(
                    LEVELS_MARKER,
                    f"const PRIVATE_PARENT_LEVELS = {private_levels};".encode(),
                )
            entry = ZipInfo(archive_name, date_time=(2026, 10, 9, 0, 0, 0))
            entry.compress_type = ZIP_DEFLATED
            entry.external_attr = 0o100644 << 16
            package.writestr(entry, data)
    return target


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--private-levels",
        type=int,
        default=0,
        help="0 mantém o pacote fechado; use 2 ou mais somente após confirmar a estrutura no cPanel",
    )
    args = parser.parse_args()
    if args.private_levels != 0 and args.private_levels < 2:
        parser.error("--private-levels deve ser 0 ou pelo menos 2")

    OUTPUT.mkdir(parents=True, exist_ok=True)
    packages = [
        archive("DEP-001-public-links.zip", PUBLIC, args.private_levels),
        archive("DEP-001-private-reader.zip", PRIVATE, args.private_levels),
    ]
    checksums = "".join(
        f"{hashlib.sha256(path.read_bytes()).hexdigest()}  {path.name}\n"
        for path in packages
    )
    (OUTPUT / "SHA256SUMS").write_text(checksums, encoding="utf-8")
    (OUTPUT / "MANIFEST.txt").write_text(
        "DEP-001 — preparação local, sem dados reais ou publicação\n"
        "Destino público: conteúdo de links/ no document root de suporte.\n"
        "Destino privado: technolife-links-private/ fora de todas as raízes web.\n"
        "O arquivo config.php.example tem valores vazios; criar config.php somente no destino privado.\n"
        f"PRIVATE_PARENT_LEVELS={args.private_levels} "
        "(0 significa sem localização configurada e página em estado de erro).\n"
        "Leia docs/07-PREPARACAO-DEP-001.md antes de enviar qualquer arquivo.\n",
        encoding="utf-8",
    )
    print(OUTPUT)
    print(checksums, end="")


if __name__ == "__main__":
    main()
