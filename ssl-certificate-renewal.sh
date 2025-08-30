#!/bin/bash

# SSL Certificate Renewal Script for MaxMed
# This script will renew the expired Let's Encrypt certificate

set -e

echo "🔒 SSL Certificate Renewal Script for MaxMed"
echo "=============================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "❌ This script must be run as root (use sudo)"
    exit 1
fi

# Check if certbot is installed
if ! command -v certbot &> /dev/null; then
    echo "📦 Installing certbot..."
    apt update
    apt install -y certbot
fi

# Stop nginx temporarily to free up port 80
echo "🛑 Stopping nginx temporarily..."
systemctl stop nginx || docker-compose stop nginx

# Wait a moment for port to be freed
sleep 3

# Renew the certificate
echo "🔄 Renewing SSL certificate for maxmedme.com..."
certbot renew --force-renewal --cert-name maxmedme.com

# Check if renewal was successful
if [ $? -eq 0 ]; then
    echo "✅ Certificate renewed successfully!"
    
    # Check certificate expiration
    echo "📅 Certificate expiration date:"
    openssl x509 -in /etc/letsencrypt/live/maxmedme.com/cert.pem -noout -dates
    
    # Test nginx configuration
    echo "🔍 Testing nginx configuration..."
    nginx -t
    
    if [ $? -eq 0 ]; then
        echo "✅ Nginx configuration is valid"
        
        # Restart nginx
        echo "🔄 Restarting nginx..."
        systemctl start nginx || docker-compose up -d nginx
        
        echo "✅ SSL certificate renewal completed successfully!"
        echo "🌐 Your website should now be accessible via HTTPS"
    else
        echo "❌ Nginx configuration test failed"
        exit 1
    fi
else
    echo "❌ Certificate renewal failed"
    echo "🔄 Attempting to obtain a new certificate..."
    
    # Try to obtain a new certificate
    certbot certonly --standalone -d maxmedme.com -d www.maxmedme.com --agree-tos --email admin@maxmedme.com --force-renewal
    
    if [ $? -eq 0 ]; then
        echo "✅ New certificate obtained successfully!"
        systemctl start nginx || docker-compose up -d nginx
    else
        echo "❌ Failed to obtain new certificate"
        exit 1
    fi
fi

echo ""
echo "🎉 SSL certificate renewal process completed!"
echo "🔗 Test your site: https://maxmedme.com"
