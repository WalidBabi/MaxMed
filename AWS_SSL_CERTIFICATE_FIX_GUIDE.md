# AWS SSL Certificate Fix Guide for MaxMed

## 🚨 Issue Summary

Your website `maxmedme.com` is currently only accessible via HTTP because the SSL certificate has expired. The certificate expired on **August 29, 2025**, and today is **August 30, 2025**.

### What Happened
- Your SSL certificate has expired
- Browsers are blocking HTTPS access due to the expired certificate
- Your infrastructure forces HTTPS redirects
- This creates a redirect loop, making the site inaccessible via HTTPS

### Current Status
- ✅ Website accessible via HTTP: `http://maxmedme.com`
- ❌ Website blocked via HTTPS: `https://maxmedme.com` (certificate expired)

## 🔧 AWS Solution Overview

Since you're using AWS, we'll use **AWS Certificate Manager (ACM)** to fix this issue:

1. **Request a new SSL certificate** from AWS Certificate Manager
2. **Validate the certificate** using DNS validation
3. **Update your AWS resources** (ALB, CloudFront, etc.) with the new certificate
4. **Set up monitoring** to prevent future issues

## 📋 AWS-Specific Files Created

### 1. `aws-ssl-certificate-fix.sh` (Linux/Mac)
- Comprehensive bash script for AWS SSL certificate management
- Handles ACM certificate requests and validation
- Updates ALB and CloudFront distributions
- Sets up CloudWatch monitoring

### 2. `aws-ssl-certificate-fix.ps1` (Windows)
- PowerShell version for Windows users
- Same functionality as the bash script
- Windows-specific error handling

## 🚀 Quick Fix Instructions

### Prerequisites

1. **AWS CLI installed and configured**
   ```bash
   # Install AWS CLI
   curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
   unzip awscliv2.zip
   sudo ./aws/install
   
   # Configure AWS credentials
   aws configure
   ```

2. **Required AWS permissions**
   - `acm:RequestCertificate`
   - `acm:DescribeCertificate`
   - `acm:DeleteCertificate`
   - `route53:ChangeResourceRecordSets`
   - `elasticloadbalancing:ModifyListener`
   - `cloudfront:UpdateDistribution`
   - `cloudwatch:PutMetricAlarm`

### Option 1: Automated Fix (Recommended)

#### For Linux/Mac:
```bash
# Make the script executable
chmod +x aws-ssl-certificate-fix.sh

# Run the fix script
./aws-ssl-certificate-fix.sh
```

#### For Windows:
```powershell
# Run PowerShell as Administrator
# Navigate to your project directory
.\aws-ssl-certificate-fix.ps1
```

### Option 2: Manual AWS Console Fix

#### Step 1: Request New Certificate
1. Go to **AWS Certificate Manager** console
2. Click **"Request a certificate"**
3. Enter domain names:
   - `maxmedme.com`
   - `www.maxmedme.com`
4. Choose **"DNS validation"**
5. Click **"Request"**

#### Step 2: Validate Certificate
1. Click on your new certificate
2. Copy the DNS validation records
3. Add them to your DNS provider (Route 53 or external)
4. Wait for validation (usually 5-30 minutes)

#### Step 3: Update AWS Resources

**For Application Load Balancer:**
1. Go to **EC2 > Load Balancers**
2. Select your ALB
3. Go to **Listeners** tab
4. Edit the HTTPS listener (port 443)
5. Select the new certificate
6. Save changes

**For CloudFront Distribution:**
1. Go to **CloudFront** console
2. Select your distribution
3. Go to **General** tab
4. Click **Edit**
5. Under **SSL Certificate**, select the new certificate
6. Save changes

## 🔍 AWS-Specific Verification Steps

After running the fix:

1. **Check certificate status in ACM console**
2. **Test HTTPS access**: Visit `https://maxmedme.com`
3. **Verify ALB/CloudFront configuration**
4. **Check CloudWatch alarms**

## 🛠️ AWS Troubleshooting

### Certificate Validation Fails
```bash
# Check certificate status
aws acm describe-certificate --certificate-arn YOUR_CERT_ARN --region us-east-1

# Check DNS records
aws route53 list-resource-record-sets --hosted-zone-id YOUR_HOSTED_ZONE_ID
```

### ALB Issues
```bash
# List load balancers
aws elbv2 describe-load-balancers

# Check listeners
aws elbv2 describe-listeners --load-balancer-arn YOUR_ALB_ARN

# Update listener certificate
aws elbv2 modify-listener \
    --listener-arn YOUR_LISTENER_ARN \
    --certificates CertificateArn=YOUR_NEW_CERT_ARN
```

### CloudFront Issues
```bash
# List distributions
aws cloudfront list-distributions

# Get distribution config
aws cloudfront get-distribution-config --id YOUR_DISTRIBUTION_ID

# Update distribution
aws cloudfront update-distribution \
    --id YOUR_DISTRIBUTION_ID \
    --distribution-config file://updated-config.json \
    --if-match YOUR_ETAG
```

## 🔄 AWS Automatic Renewal Setup

### CloudWatch Monitoring
The script automatically creates:
- CloudWatch alarm for certificate expiration
- SNS notifications (if topic exists)

### Manual Setup
```bash
# Create SNS topic for alerts
aws sns create-topic --name MaxMed-Alerts

# Create CloudWatch alarm
aws cloudwatch put-metric-alarm \
    --alarm-name "MaxMed-SSL-Certificate-Expiration" \
    --alarm-description "SSL Certificate expiration warning" \
    --metric-name "DaysToExpiry" \
    --namespace "AWS/CertificateManager" \
    --statistic "Minimum" \
    --period 86400 \
    --threshold 30 \
    --comparison-operator "LessThanThreshold" \
    --evaluation-periods 1 \
    --alarm-actions "arn:aws:sns:us-east-1:YOUR_ACCOUNT_ID:MaxMed-Alerts"
```

## 📊 AWS Monitoring

### Check Certificate Status
```bash
# List all certificates
aws acm list-certificates --region us-east-1

# Get certificate details
aws acm describe-certificate --certificate-arn YOUR_CERT_ARN --region us-east-1
```

### Monitor CloudWatch Metrics
```bash
# Get certificate metrics
aws cloudwatch get-metric-statistics \
    --namespace "AWS/CertificateManager" \
    --metric-name "DaysToExpiry" \
    --dimensions Name=CertificateArn,Value=YOUR_CERT_ARN \
    --start-time 2025-08-30T00:00:00Z \
    --end-time 2025-08-30T23:59:59Z \
    --period 86400 \
    --statistics Minimum
```

## 🚨 AWS Prevention Strategies

### 1. Automatic Certificate Renewal
- ACM certificates auto-renew if using AWS services
- Ensure your AWS resources are properly configured

### 2. CloudWatch Monitoring
- Set up alarms for certificate expiration
- Configure SNS notifications

### 3. Infrastructure as Code
- Use CloudFormation or Terraform for certificate management
- Version control your infrastructure

### 4. Regular Audits
- Monthly certificate status checks
- Quarterly infrastructure reviews

## 🔗 AWS-Specific Commands

```bash
# Check all certificates in your account
aws acm list-certificates --region us-east-1

# Get certificate details
aws acm describe-certificate --certificate-arn YOUR_CERT_ARN --region us-east-1

# List ALBs
aws elbv2 describe-load-balancers

# List CloudFront distributions
aws cloudwatch list-distributions

# Check Route 53 hosted zones
aws route53 list-hosted-zones

# Test DNS resolution
nslookup maxmedme.com
dig maxmedme.com
```

## 📞 AWS Support

If you encounter issues:

1. **Check AWS CloudTrail** for API call logs
2. **Review CloudWatch logs** for application errors
3. **Verify IAM permissions** for certificate management
4. **Contact AWS Support** if using paid support plan

## 🎯 AWS Best Practices

### Certificate Management
- Use ACM for all SSL certificates
- Enable automatic renewal
- Use DNS validation for better security
- Monitor certificate expiration

### Security
- Use HTTPS redirects
- Implement security headers
- Regular security audits
- Monitor for certificate issues

### Monitoring
- Set up CloudWatch alarms
- Configure SNS notifications
- Regular health checks
- Automated testing

---

**Last Updated**: August 30, 2025  
**Status**: SSL Certificate Expired - AWS Fix Required  
**Priority**: High - Affects website accessibility  
**AWS Region**: us-east-1 (update in scripts if different)
