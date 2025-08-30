#!/bin/bash

# SSL Certificate Fix Script for MaxMed
# This script will fix the expired SSL certificate issue

set -e

echo "🔒 SSL Certificate Fix Script for MaxMed"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "This script must be run as root (use sudo)"
    exit 1
fi

print_status "Starting SSL certificate fix process..."

# Step 1: Backup current configuration
print_status "Backing up current nginx configuration..."
cp docker/nginx/conf.d/app.conf docker/nginx/conf.d/app.conf.backup.$(date +%Y%m%d_%H%M%S)

# Step 2: Apply temporary HTTP-only configuration
print_status "Applying temporary HTTP-only configuration..."
cp docker/nginx/conf.d/app-temp.conf docker/nginx/conf.d/app.conf

# Step 3: Restart nginx with temporary configuration
print_status "Restarting nginx with temporary configuration..."
if command -v docker-compose &> /dev/null; then
    docker-compose restart nginx
else
    systemctl restart nginx
fi

print_success "Website is now accessible via HTTP while we fix the SSL certificate"

# Step 4: Check if certbot is installed
if ! command -v certbot &> /dev/null; then
    print_status "Installing certbot..."
    apt update
    apt install -y certbot
fi

# Step 5: Stop nginx temporarily to free up port 80 for certificate renewal
print_status "Stopping nginx temporarily for certificate renewal..."
if command -v docker-compose &> /dev/null; then
    docker-compose stop nginx
else
    systemctl stop nginx
fi

sleep 5

# Step 6: Renew the certificate
print_status "Renewing SSL certificate for maxmedme.com..."
certbot renew --force-renewal --cert-name maxmedme.com

# Check if renewal was successful
if [ $? -eq 0 ]; then
    print_success "Certificate renewed successfully!"
    
    # Check certificate expiration
    print_status "Certificate expiration date:"
    openssl x509 -in /etc/letsencrypt/live/maxmedme.com/cert.pem -noout -dates
    
    # Step 7: Restore original configuration
    print_status "Restoring original HTTPS configuration..."
    cp docker/nginx/conf.d/app.conf.backup.* docker/nginx/conf.d/app.conf
    
    # Test nginx configuration
    print_status "Testing nginx configuration..."
    if command -v docker-compose &> /dev/null; then
        docker-compose exec nginx nginx -t
    else
        nginx -t
    fi
    
    if [ $? -eq 0 ]; then
        print_success "Nginx configuration is valid"
        
        # Restart nginx with HTTPS configuration
        print_status "Restarting nginx with HTTPS configuration..."
        if command -v docker-compose &> /dev/null; then
            docker-compose up -d nginx
        else
            systemctl start nginx
        fi
        
        print_success "SSL certificate fix completed successfully!"
        print_success "Your website is now accessible via HTTPS"
        
        # Clean up backup files older than 7 days
        find docker/nginx/conf.d/ -name "app.conf.backup.*" -mtime +7 -delete
        
    else
        print_error "Nginx configuration test failed"
        print_status "Restoring backup configuration..."
        cp docker/nginx/conf.d/app.conf.backup.* docker/nginx/conf.d/app.conf
        exit 1
    fi
else
    print_error "Certificate renewal failed"
    print_status "Attempting to obtain a new certificate..."
    
    # Try to obtain a new certificate
    certbot certonly --standalone -d maxmedme.com -d www.maxmedme.com --agree-tos --email admin@maxmedme.com --force-renewal
    
    if [ $? -eq 0 ]; then
        print_success "New certificate obtained successfully!"
        
        # Restore original configuration
        cp docker/nginx/conf.d/app.conf.backup.* docker/nginx/conf.d/app.conf
        
        if command -v docker-compose &> /dev/null; then
            docker-compose up -d nginx
        else
            systemctl start nginx
        fi
        
        print_success "SSL certificate fix completed successfully!"
    else
        print_error "Failed to obtain new certificate"
        print_warning "Website will remain HTTP-only until certificate is manually renewed"
        
        # Keep the temporary configuration
        if command -v docker-compose &> /dev/null; then
            docker-compose up -d nginx
        else
            systemctl start nginx
        fi
        exit 1
    fi
fi

# Step 8: Set up automatic renewal
print_status "Setting up automatic certificate renewal..."
cat > /etc/cron.d/ssl-renewal << EOF
# SSL Certificate Auto-renewal for MaxMed
0 12 * * * root /usr/bin/certbot renew --quiet --deploy-hook "systemctl reload nginx || docker-compose restart nginx"
EOF

print_success "Automatic renewal cron job created"

echo ""
print_success "🎉 SSL certificate fix process completed!"
echo ""
print_status "Next steps:"
echo "1. Test your website: https://maxmedme.com"
echo "2. Check that HTTPS redirects are working"
echo "3. Monitor certificate expiration: certbot certificates"
echo ""
print_status "If you encounter any issues, check the logs:"
echo "- Nginx logs: docker-compose logs nginx"
echo "- Certbot logs: journalctl -u certbot"
