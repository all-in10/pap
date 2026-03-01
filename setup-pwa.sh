#!/bin/bash
#
# PWA Setup Script for TeamCore
# Execute this script to complete PWA installation
#
# Usage: bash setup-pwa.sh
#

set -e

echo "========================================="
echo "TeamCore PWA - Complete Setup"
echo "========================================="
echo ""

# Check PHP version
echo "✓ Checking PHP..."
php --version

# Check Laravel
echo ""
echo "✓ Checking Laravel..."
php artisan --version

# Generate icons if not exist
echo ""
echo "📦 Generating PWA icons..."
if [ ! -f "public/pwa-icons/icon-192x192.png" ]; then
    php public/pwa-icons/generate-icons.php
else
    echo "  (icons already exist)"
fi

# Check Node/NPM
echo ""
echo "📦 Checking Node.js and npm..."
if ! command -v node &> /dev/null; then
    echo ""
    echo "⚠️  Node.js not found. Install with:"
    echo "  brew install node        (macOS)"
    echo "  choco install nodejs     (Windows)"
    echo "  apt install nodejs npm   (Linux)"
    echo ""
    read -p "Continue without build? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
else
    node --version
    npm --version
    
    # Install dependencies
    echo ""
    echo "📦 Installing npm dependencies..."
    npm install
    
    # Build assets
    echo ""
    echo "🔨 Building assets (Vite)..."
    npm run build
fi

# Run migrations
echo ""
echo "🗄️  Running migrations..."
php artisan migrate --force 2>/dev/null || php artisan migrate

# Validate PWA
echo ""
echo "✅ Validating PWA configuration..."
php artisan pwa:validate

echo ""
echo "========================================="
echo "✓ PWA Setup Complete!"
echo "========================================="
echo ""
echo "📚 Next steps:"
echo ""
echo "1. Start development server:"
echo "   php artisan serve"
echo ""
echo "2. Start queue worker (in another terminal):"
echo "   php artisan queue:work"
echo ""
echo "3. Open http://localhost:8000 in your browser"
echo ""
echo "4. Test PWA:"
echo "   - DevTools (F12) → Application tab"
echo "   - Check Service Workers"
echo "   - Offline test: Network tab → Offline checkbox"
echo ""
echo "5. Send test push notification:"
echo "   php artisan notify:test-push --user=1"
echo ""
echo "📖 Documentation:"
echo "   - Full guide: docs/PWA_IMPLEMENTATION_GUIDE.md"
echo "   - Quick ref:  docs/PWA_QUICK_REFERENCE.md"
echo ""
