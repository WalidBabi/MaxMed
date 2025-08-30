#!/bin/bash

# AWS SSL Certificate Fix Script for MaxMed
# This script will fix the expired SSL certificate issue on AWS

set -e

echo "🔒 AWS SSL Certificate Fix Script for MaxMed"
echo "============================================="

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

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    print_error "AWS CLI is not installed. Please install it first:"
    echo "  https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html"
    exit 1
fi

# Check if AWS credentials are configured
if ! aws sts get-caller-identity &> /dev/null; then
    print_error "AWS credentials are not configured. Please run:"
    echo "  aws configure"
    exit 1
fi

print_status "Starting AWS SSL certificate fix process..."

# Variables
DOMAIN="maxmedme.com"
WWW_DOMAIN="www.maxmedme.com"
REGION="us-east-1"  # Change this to your AWS region

# Step 1: Check current certificate status
print_status "Checking current certificate status..."
CURRENT_CERT=$(aws acm list-certificates --region $REGION --query "CertificateSummaryList[?DomainName=='$DOMAIN'].CertificateArn" --output text)

if [ -n "$CURRENT_CERT" ]; then
    print_status "Found existing certificate: $CURRENT_CERT"
    
    # Check certificate expiration
    EXPIRATION=$(aws acm describe-certificate --certificate-arn $CURRENT_CERT --region $REGION --query "Certificate.NotAfter" --output text)
    print_status "Certificate expires: $EXPIRATION"
    
    # Check if certificate is expired
    EXPIRATION_EPOCH=$(date -d "$EXPIRATION" +%s)
    CURRENT_EPOCH=$(date +%s)
    
    if [ $CURRENT_EPOCH -gt $EXPIRATION_EPOCH ]; then
        print_warning "Certificate has expired. Creating new certificate..."
        EXPIRED_CERT=$CURRENT_CERT
    else
        print_success "Certificate is still valid"
        exit 0
    fi
else
    print_status "No existing certificate found for $DOMAIN"
fi

# Step 2: Request new certificate
print_status "Requesting new SSL certificate from AWS Certificate Manager..."
NEW_CERT_ARN=$(aws acm request-certificate \
    --domain-name $DOMAIN \
    --subject-alternative-names $WWW_DOMAIN \
    --validation-method DNS \
    --region $REGION \
    --query "CertificateArn" \
    --output text)

if [ $? -eq 0 ]; then
    print_success "New certificate requested: $NEW_CERT_ARN"
else
    print_error "Failed to request new certificate"
    exit 1
fi

# Step 3: Get DNS validation records
print_status "Getting DNS validation records..."
sleep 10  # Wait for certificate to be created

VALIDATION_RECORDS=$(aws acm describe-certificate \
    --certificate-arn $NEW_CERT_ARN \
    --region $REGION \
    --query "Certificate.DomainValidationOptions[].ResourceRecord" \
    --output json)

if [ -z "$VALIDATION_RECORDS" ] || [ "$VALIDATION_RECORDS" = "[]" ]; then
    print_error "No validation records found. Certificate may not be ready yet."
    exit 1
fi

print_status "DNS validation records:"
echo "$VALIDATION_RECORDS" | jq -r '.[] | "Name: \(.Name), Value: \(.Value), Type: \(.Type)"'

# Step 4: Check if using Route 53 for DNS
print_status "Checking if Route 53 is used for DNS..."
HOSTED_ZONE=$(aws route53 list-hosted-zones --query "HostedZones[?Name=='$DOMAIN.'].Id" --output text)

if [ -n "$HOSTED_ZONE" ]; then
    print_status "Found Route 53 hosted zone: $HOSTED_ZONE"
    
    # Extract hosted zone ID (remove /hostedzone/ prefix)
    HOSTED_ZONE_ID=$(echo $HOSTED_ZONE | sed 's|/hostedzone/||')
    
    # Create validation records in Route 53
    print_status "Creating DNS validation records in Route 53..."
    
    echo "$VALIDATION_RECORDS" | jq -r '.[] | {
        "Changes": [{
            "Action": "UPSERT",
            "ResourceRecordSet": {
                "Name": .Name,
                "Type": .Type,
                "TTL": 300,
                "ResourceRecords": [{"Value": .Value}]
            }
        }]
    }' > /tmp/validation-changes.json
    
    aws route53 change-resource-record-sets \
        --hosted-zone-id $HOSTED_ZONE_ID \
        --change-batch file:///tmp/validation-changes.json
    
    if [ $? -eq 0 ]; then
        print_success "DNS validation records created in Route 53"
    else
        print_error "Failed to create DNS validation records"
        exit 1
    fi
    
    rm -f /tmp/validation-changes.json
else
    print_warning "Route 53 hosted zone not found for $DOMAIN"
    print_status "Please manually add the following DNS records to your DNS provider:"
    echo "$VALIDATION_RECORDS" | jq -r '.[] | "Name: \(.Name), Value: \(.Value), Type: \(.Type)"'
fi

# Step 5: Wait for certificate validation
print_status "Waiting for certificate validation..."
while true; do
    STATUS=$(aws acm describe-certificate \
        --certificate-arn $NEW_CERT_ARN \
        --region $REGION \
        --query "Certificate.Status" \
        --output text)
    
    print_status "Certificate status: $STATUS"
    
    if [ "$STATUS" = "ISSUED" ]; then
        print_success "Certificate has been issued successfully!"
        break
    elif [ "$STATUS" = "FAILED" ]; then
        print_error "Certificate validation failed"
        exit 1
    elif [ "$STATUS" = "PENDING_VALIDATION" ]; then
        print_status "Certificate is pending validation. Waiting 30 seconds..."
        sleep 30
    else
        print_status "Unknown status: $STATUS. Waiting 30 seconds..."
        sleep 30
    fi
done

# Step 6: Update Load Balancer or CloudFront distribution
print_status "Checking for Load Balancers..."
ALB_LIST=$(aws elbv2 describe-load-balancers --query "LoadBalancers[?DNSName=='$DOMAIN' || DNSName=='$WWW_DOMAIN'].LoadBalancerArn" --output text)

if [ -n "$ALB_LIST" ]; then
    print_status "Found Application Load Balancer(s): $ALB_LIST"
    
    for ALB_ARN in $ALB_LIST; do
        print_status "Updating ALB listener with new certificate..."
        
        # Get listener ARN
        LISTENER_ARN=$(aws elbv2 describe-listeners \
            --load-balancer-arn $ALB_ARN \
            --query "Listeners[?Port==443].ListenerArn" \
            --output text)
        
        if [ -n "$LISTENER_ARN" ]; then
            aws elbv2 modify-listener \
                --listener-arn $LISTENER_ARN \
                --certificates CertificateArn=$NEW_CERT_ARN
            
            if [ $? -eq 0 ]; then
                print_success "Updated ALB listener with new certificate"
            else
                print_error "Failed to update ALB listener"
            fi
        fi
    done
fi

# Check for CloudFront distributions
print_status "Checking for CloudFront distributions..."
CF_DISTRIBUTIONS=$(aws cloudfront list-distributions \
    --query "DistributionList.Items[?Aliases.Items[?contains(@, '$DOMAIN')]].Id" \
    --output text)

if [ -n "$CF_DISTRIBUTIONS" ]; then
    print_status "Found CloudFront distribution(s): $CF_DISTRIBUTIONS"
    
    for DIST_ID in $CF_DISTRIBUTIONS; do
        print_status "Updating CloudFront distribution with new certificate..."
        
        # Get current distribution config
        aws cloudfront get-distribution-config --id $DIST_ID > /tmp/cf-config.json
        
        # Update certificate ARN in config
        jq --arg cert "$NEW_CERT_ARN" '.DistributionConfig.ViewerCertificate.ACMCertificateArn = $cert' /tmp/cf-config.json > /tmp/cf-config-updated.json
        
        # Update distribution
        ETAG=$(jq -r '.ETag' /tmp/cf-config.json)
        aws cloudfront update-distribution \
            --id $DIST_ID \
            --distribution-config file:///tmp/cf-config-updated.json \
            --if-match $ETAG
        
        if [ $? -eq 0 ]; then
            print_success "Updated CloudFront distribution with new certificate"
        else
            print_error "Failed to update CloudFront distribution"
        fi
        
        rm -f /tmp/cf-config.json /tmp/cf-config-updated.json
    done
fi

# Step 7: Clean up expired certificate (optional)
if [ -n "$EXPIRED_CERT" ]; then
    print_status "Cleaning up expired certificate..."
    aws acm delete-certificate --certificate-arn $EXPIRED_CERT --region $REGION
    
    if [ $? -eq 0 ]; then
        print_success "Expired certificate deleted"
    else
        print_warning "Failed to delete expired certificate (may be in use)"
    fi
fi

# Step 8: Set up automatic renewal monitoring
print_status "Setting up certificate monitoring..."

# Create CloudWatch alarm for certificate expiration
ALARM_NAME="MaxMed-SSL-Certificate-Expiration"
aws cloudwatch put-metric-alarm \
    --alarm-name $ALARM_NAME \
    --alarm-description "SSL Certificate expiration warning for $DOMAIN" \
    --metric-name "DaysToExpiry" \
    --namespace "AWS/CertificateManager" \
    --statistic "Minimum" \
    --period 86400 \
    --threshold 30 \
    --comparison-operator "LessThanThreshold" \
    --evaluation-periods 1 \
    --alarm-actions "arn:aws:sns:us-east-1:$(aws sts get-caller-identity --query Account --output text):MaxMed-Alerts" \
    --region $REGION

if [ $? -eq 0 ]; then
    print_success "CloudWatch alarm created for certificate monitoring"
else
    print_warning "Failed to create CloudWatch alarm (SNS topic may not exist)"
fi

print_success "🎉 AWS SSL certificate fix completed successfully!"
echo ""
print_status "Next steps:"
echo "1. Test your website: https://$DOMAIN"
echo "2. Verify HTTPS redirects are working"
echo "3. Monitor certificate status in AWS Console"
echo "4. Set up SNS notifications for certificate alerts"
echo ""
print_status "Certificate ARN: $NEW_CERT_ARN"
print_status "Region: $REGION"
