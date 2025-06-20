# 📬 How to Check Your Emails in hMailServer

## Method 1: Using hMailServer Administrator

1. **Open hMail Administrator**
2. **Navigate to**: `Domains` → `localhost.com` → `Accounts` → `wbabi@localhost.com`
3. **Right-click on the account** → `Messages`
4. **Look for your test emails** in the message list

## Method 2: Using an Email Client (Recommended)

### Setup Outlook/Thunderbird to connect to hMailServer:

**Incoming Mail (IMAP/POP3) Settings:**
- **Server**: localhost
- **Port**: 143 (IMAP) or 110 (POP3)  
- **Username**: wbabi@localhost.com
- **Password**: 09888
- **Encryption**: None

**Outgoing Mail (SMTP) Settings:**
- **Server**: localhost
- **Port**: 25
- **Username**: wbabi@localhost.com
- **Password**: 09888
- **Encryption**: None

## Method 3: Check hMailServer Data Directory

1. **Find hMailServer data directory** (usually):
   - `C:\Program Files (x86)\hMailServer\Data\`
   - Or `C:\hMailServer\Data\`

2. **Navigate to**: `Domains\localhost.com\wbabi\`

3. **Look for .eml files** - these are your emails

## Method 4: Enable hMailServer's Built-in WebMail

1. In hMail Administrator:
   - `Settings` → `Protocols` → `HTTP`
   - Enable HTTP server
   - Set port (e.g., 8080)

2. Access webmail at: `http://localhost:8080`

## Quick Test

Run this to send a test email with a unique subject:

```bash
php artisan test:mail
```

Then check hMailServer Administrator for the new message. 