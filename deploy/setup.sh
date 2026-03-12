#!/bin/bash
# GatewayOS2 — Production Setup Script
# Run on your server after cloning the repo.

set -e

DEPLOY_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo ""
echo "  GatewayOS2 — Production Setup"
echo "  ─────────────────────────────"
echo "  Deploy dir: $DEPLOY_DIR"
echo ""

# 1. Create required directories
echo "Creating directories..."
mkdir -p "$DEPLOY_DIR/data/cache"
mkdir -p "$DEPLOY_DIR/data/messages"
mkdir -p "$DEPLOY_DIR/data/blog"

# 2. Set permissions — data writable by web server
echo "Setting permissions..."
chmod -R 755 "$DEPLOY_DIR"
chmod -R 775 "$DEPLOY_DIR/data"

# 3. Create env.json from template if missing
if [ ! -f "$DEPLOY_DIR/data/env.json" ]; then
    echo "Creating env.json from template..."
    cp "$DEPLOY_DIR/data/env.example.json" "$DEPLOY_DIR/data/env.json"
    echo ""
    echo "  ⚠  Edit data/env.json with your production values:"
    echo "     - SITE_URL (your domain)"
    echo "     - TURNSTILE keys (from dash.cloudflare.com)"
    echo "     - UMAMI_ID (from cloud.umami.is)"
    echo ""
fi

# 4. Initialize data files if missing
[ ! -f "$DEPLOY_DIR/data/users.json" ] && echo "[]" > "$DEPLOY_DIR/data/users.json"
[ ! -f "$DEPLOY_DIR/data/tokens.json" ] && echo "[]" > "$DEPLOY_DIR/data/tokens.json"
[ ! -f "$DEPLOY_DIR/data/analytics.json" ] && echo '{"total_views":0,"daily":{}}' > "$DEPLOY_DIR/data/analytics.json"
[ ! -f "$DEPLOY_DIR/data/reset_codes.json" ] && echo "[]" > "$DEPLOY_DIR/data/reset_codes.json"
[ ! -f "$DEPLOY_DIR/data/blog/posts.json" ] && echo "[]" > "$DEPLOY_DIR/data/blog/posts.json"

# 5. Build assets
echo "Building assets..."
if command -v ruby &> /dev/null; then
    ruby "$DEPLOY_DIR/tools/build.rb"
else
    echo "  ⚠  Ruby not found — skipping build. CSS/JS must be pre-built."
fi

# 6. Create admin user
echo ""
read -p "Create admin user? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if command -v ruby &> /dev/null; then
        ruby "$DEPLOY_DIR/tools/seed.rb"
    elif command -v php &> /dev/null; then
        echo "  Use: php -r \"... manual admin creation ...\""
    fi
fi

echo ""
echo "  ✓ Setup complete!"
echo ""
echo "  Next steps:"
echo "  1. Edit data/env.json with your domain and API keys"
echo "  2. Point your web server document root to: $DEPLOY_DIR/public"
echo "  3. Set up SSL with: certbot --nginx -d yourdomain.com"
echo "  4. Configure email: ruby tools/setup_email.rb"
echo ""
