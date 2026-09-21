#!/usr/bin/env bash
# Build the installable WordPress plugin zip into dist/.
set -euo pipefail

cd "$(dirname "$0")"
SLUG="suncoast-ele-widgets"
VERSION=$(grep -m1 "^ \* Version:" "plugin/$SLUG/$SLUG.php" | awk '{print $3}')
OUT="dist/$SLUG-$VERSION.zip"

# Fail the build rather than ship a dead selector or an undeclared setting.
php -l "plugin/$SLUG/$SLUG.php" >/dev/null
find "plugin/$SLUG" -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v 'No syntax errors' && exit 1
command -v node >/dev/null && find "plugin/$SLUG/assets/js" -name '*.js' -print0 | xargs -0 -n1 node --check
python3 tools/audit.py

mkdir -p dist
rm -f "$OUT" "dist/$SLUG.zip"

( cd plugin && zip -r -q -X "../$OUT" "$SLUG" \
    -x '*.DS_Store' -x '__MACOSX/*' -x '*/.git/*' )

cp "$OUT" "dist/$SLUG.zip"
echo "built $OUT ($(du -h "$OUT" | cut -f1))"
unzip -Z1 "$OUT" | sed "s/^/  /"
