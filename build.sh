#!/usr/bin/env bash
# Build the installable WordPress plugin zip into dist/.
set -euo pipefail

cd "$(dirname "$0")"
SLUG="suncoast-ele-widgets"
VERSION=$(grep -m1 "^ \* Version:" "plugin/$SLUG/$SLUG.php" | awk '{print $3}')
OUT="dist/$SLUG-$VERSION.zip"

mkdir -p dist
rm -f "$OUT" "dist/$SLUG.zip"

( cd plugin && zip -r -q -X "../$OUT" "$SLUG" \
    -x '*.DS_Store' -x '__MACOSX/*' -x '*/.git/*' )

cp "$OUT" "dist/$SLUG.zip"
echo "built $OUT ($(du -h "$OUT" | cut -f1))"
unzip -Z1 "$OUT" | sed "s/^/  /"
