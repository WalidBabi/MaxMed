#!/bin/bash

echo "Starting optimization process..."

# Install dependencies
echo "Installing dependencies..."
npm install

# Optimize images (convert to WebP and resize)
echo "Optimizing images..."
npm run optimize-images

# Build optimized production assets
echo "Building optimized production assets..."
npm run build

echo "Optimization completed successfully!"
echo ""
echo "Here's what was optimized:"
echo "1. Images converted to WebP format and properly sized"
echo "2. JavaScript and CSS minified and compressed"
echo "3. Render-blocking resources eliminated"
echo "4. Gzip compression enabled through Nginx config"
echo ""
echo "Next steps:"
echo "1. Deploy the optimized assets to your server"
echo "2. Apply the Nginx configuration (nginx-config.conf)"
echo "3. Verify improvements with Lighthouse or PageSpeed Insights" 