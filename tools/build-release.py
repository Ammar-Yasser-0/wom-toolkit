#!/usr/bin/env python3
"""
Cross-platform release builder for Mirox Toolkit.

Creates dist/wom-toolkit.zip with POSIX forward-slash paths
regardless of the host operating system.

Usage:
    python tools/build-release.py
"""

import os
import sys
import zipfile
from pathlib import Path, PurePosixPath

REPO_ROOT = Path(__file__).resolve().parent.parent
DIST_DIR = REPO_ROOT / "dist"
ZIP_NAME = "wom-toolkit.zip"
ZIP_PATH = DIST_DIR / ZIP_NAME
PLUGIN_SLUG = "wom-toolkit"

REQUIRED_FILES = [
    "wom-toolkit/wom-toolkit.php",
    "wom-toolkit/uninstall.php",
    "wom-toolkit/readme.txt",
    "wom-toolkit/core/class-plugin.php",
    "wom-toolkit/core/class-admin.php",
    "wom-toolkit/core/class-modules.php",
    "wom-toolkit/core/class-settings.php",
    "wom-toolkit/core/class-updater.php",
    "wom-toolkit/core/class-module-manager.php",
    "wom-toolkit/core/class-base-module.php",
    "wom-toolkit/modules/custom-cursor/class-module.php",
    "wom-toolkit/modules/smooth-scrolling/class-module.php",
    "wom-toolkit/modules/login-branding/class-module.php",
    "wom-toolkit/modules/scrollbar-styles/class-module.php",
    "wom-toolkit/modules/smooth-scrolling/assets/vendor/lenis.min.js",
]

REQUIRED_DIRS = [
    "wom-toolkit/core/",
    "wom-toolkit/modules/",
    "wom-toolkit/assets/",
]


def load_distignore(repo_root: Path) -> set[str]:
    """Parse .distignore and return a set of normalized patterns."""
    distignore_path = repo_root / ".distignore"
    patterns = set()
    if distignore_path.exists():
        for raw_line in distignore_path.read_text(encoding="utf-8").splitlines():
            line = raw_line.strip()
            if not line or line.startswith("#"):
                continue
            patterns.add(line.lower())
    return patterns


def should_exclude(rel_path: str, patterns: set[str]) -> bool:
    """Check if a relative path matches any distignore pattern.

    Patterns starting with / match only at the top level (first path component).
    Patterns with / match from the start of the path.
    Patterns starting with * match file extensions.
    Other patterns match any filename or directory name in the path.
    """
    lower = rel_path.lower()
    parts = PurePosixPath(lower).parts
    name = parts[-1] if parts else ""

    for pattern in patterns:
        if pattern.startswith("/"):
            # Top-level only: match first path component
            top_level = "/" + parts[0] if parts else ""
            if pattern.endswith("/"):
                if top_level + "/" == pattern or top_level == pattern.rstrip("/"):
                    return True
            else:
                if top_level == pattern or lower == pattern.lstrip("/"):
                    return True
        elif pattern.startswith("*."):
            ext = pattern[1:]
            if name.endswith(ext):
                return True
        elif "/" in pattern:
            # Path prefix match
            p = pattern.rstrip("/")
            if lower.startswith(p) or lower == p:
                return True
        else:
            # Match any filename or directory component
            if name == pattern or pattern in parts:
                return True
    return False


def collect_files(repo_root: Path, patterns: set[str]) -> list[tuple[Path, str]]:
    """
    Walk the repo and return (absolute_path, zip_entry_name) tuples.
    zip_entry_name always uses POSIX forward slashes.
    """
    files = []
    for root, dirs, filenames in os.walk(repo_root):
        root_path = Path(root)
        rel_root = root_path.relative_to(repo_root)
        # Skip hidden directories and common non-distributable dirs
        # Only exclude top-level vendor/node_modules; keep nested ones (e.g. bundled libs)
        top_level_excludes = {"node_modules", "dist", "build", "tools", "__pycache__"}
        dirs[:] = [
            d for d in dirs
            if not d.startswith(".")
            and (
                d not in top_level_excludes
                or len(rel_root.parts) > 0
            )
        ]
        for fname in filenames:
            abs_path = root_path / fname
            rel_path = abs_path.relative_to(repo_root)
            rel_str = rel_path.as_posix()

            if should_exclude(rel_str, patterns):
                continue

            zip_entry = f"{PLUGIN_SLUG}/{rel_str}"
            files.append((abs_path, zip_entry))

    return files


def build_zip(files: list[tuple[Path, str]], zip_path: Path) -> None:
    """Create the ZIP with explicit forward-slash entry names."""
    zip_path.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as zf:
        for abs_path, entry_name in files:
            zf.write(abs_path, entry_name)


def validate_zip(zip_path: Path) -> int:
    """
    Validate the ZIP archive. Returns the number of errors found.
    Checks:
      - No backslashes in any entry name
      - Required files present
      - Required directories present
      - No nested plugin directory (wom-toolkit/wom-toolkit/)
      - No OS junk files
    """
    errors = 0

    with zipfile.ZipFile(zip_path, "r") as zf:
        names = zf.namelist()

        # Check 1: No backslashes
        backslash_entries = [n for n in names if "\\" in n]
        if backslash_entries:
            print(f"FAIL: {len(backslash_entries)} entry/entries contain backslashes:")
            for e in backslash_entries:
                print(f"  {e}")
            errors += len(backslash_entries)
        else:
            print(f"OK: Zero backslash entries ({len(names)} total entries)")

        # Check 2: Main plugin file exists
        main_file = f"{PLUGIN_SLUG}/wom-toolkit.php"
        if main_file in names:
            print(f"OK: {main_file} exists")
        else:
            print(f"FAIL: {main_file} missing")
            errors += 1

        # Check 3: Required files
        for req in REQUIRED_FILES:
            if req in names:
                print(f"  OK: {req}")
            else:
                print(f"  FAIL: {req} missing")
                errors += 1

        # Check 4: Required directories (at least one file under each)
        for req_dir in REQUIRED_DIRS:
            has_files = any(n.startswith(req_dir) and not n.endswith("/") for n in names)
            if has_files:
                print(f"  OK: {req_dir} (has files)")
            else:
                print(f"  FAIL: {req_dir} missing or empty")
                errors += 1

        # Check 5: No nested plugin directory
        nested = [n for n in names if n.startswith(f"{PLUGIN_SLUG}/{PLUGIN_SLUG}/")]
        if nested:
            print(f"FAIL: Nested plugin directory detected:")
            for e in nested[:5]:
                print(f"  {e}")
            errors += 1
        else:
            print(f"OK: No nested {PLUGIN_SLUG}/{PLUGIN_SLUG}/ directory")

        # Check 6: No OS junk files
        junk_patterns = {".ds_store", "thumbs.db", "desktop.ini", "__macosx"}
        junk_found = [
            n for n in names
            if Path(n).name.lower() in junk_patterns or "__macosx" in n.lower()
        ]
        if junk_found:
            print(f"FAIL: OS junk files in archive:")
            for e in junk_found:
                print(f"  {e}")
            errors += 1
        else:
            print("OK: No OS junk files")

        # Print first 15 entries for inspection
        print(f"\nFirst 15 archive entries:")
        for n in names[:15]:
            print(f"  {n}")

    return errors


def main() -> int:
    print(f"Building {ZIP_NAME} from {REPO_ROOT}")
    print()

    patterns = load_distignore(REPO_ROOT)
    print(f"Loaded {len(patterns)} distignore patterns")

    files = collect_files(REPO_ROOT, patterns)
    print(f"Collected {len(files)} files for distribution")

    if not files:
        print("ERROR: No files collected. Aborting.")
        return 1

    # Remove old ZIP if exists
    if ZIP_PATH.exists():
        ZIP_PATH.unlink()

    build_zip(files, ZIP_PATH)
    zip_size = ZIP_PATH.stat().st_size
    print(f"\nCreated {ZIP_PATH} ({zip_size:,} bytes)")

    print(f"\nValidating ZIP...")
    errors = validate_zip(ZIP_PATH)

    if errors:
        print(f"\nFAILED: {errors} validation error(s) found")
        return 1

    print(f"\nPASSED: ZIP validation successful")
    return 0


if __name__ == "__main__":
    sys.exit(main())
