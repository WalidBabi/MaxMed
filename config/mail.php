<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => 30,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        // Campaign mailer using Mailtrap bulk stream
        'campaign' => [
            'transport' => 'smtp',
            'host' => env('MAIL_CAMPAIGN_HOST', 'smtp.mailtrap.io'),
            'port' => env('MAIL_CAMPAIGN_PORT', 2525),
            'username' => env('MAIL_CAMPAIGN_USERNAME'),
            'password' => env('MAIL_CAMPAIGN_PASSWORD'),
            'encryption' => env('MAIL_CAMPAIGN_ENCRYPTION', 'tls'),
            'timeout' => 60, // Longer timeout for bulk sending
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        // Development mailer - saves emails to log file instead of sending
        'file' => [
            'transport' => 'log',
            'channel' => 'single',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign "From" Address
    |--------------------------------------------------------------------------
    |
    | This is the address used for campaign emails. It can be different from
    | the transactional email address to help with deliverability and tracking.
    |
    */

    'campaign_from' => [
        'address' => env('MAIL_CAMPAIGN_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        'name' => env('MAIL_CAMPAIGN_FROM_NAME', env('MAIL_FROM_NAME', 'Example')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Deliverability Settings
    |--------------------------------------------------------------------------
    |
    | These settings help improve email deliverability and reduce the chance
    | of emails being marked as spam or going to promotions tab.
    |
    */

    'deliverability' => [
        // Maximum emails per hour to avoid rate limiting
        'max_emails_per_hour' => env('MAIL_MAX_PER_HOUR', 1000),
        
        // Delay between emails in seconds
        'delay_between_emails' => env('MAIL_DELAY_BETWEEN', 1),
        
        // Business communication settings
        'business_communication' => [
            'enabled' => env('MAIL_BUSINESS_COMMUNICATION', true),
            'industry' => env('MAIL_INDUSTRY_TYPE', 'Healthcare-Supplies'),
            'communication_type' => env('MAIL_COMMUNICATION_TYPE', 'Business-Important'),
        ],
        
        // Spam prevention settings
        'spam_prevention' => [
            'avoid_promotional_words' => env('MAIL_AVOID_PROMOTIONAL', true),
            'max_subject_length' => env('MAIL_MAX_SUBJECT_LENGTH', 60),
            'require_business_header' => env('MAIL_REQUIRE_BUSINESS_HEADER', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Email Address
    |--------------------------------------------------------------------------
    |
    | This option specifies the email address where admin notifications
    | (such as user registrations and logins) will be sent. If not set,
    | the system will try to find an admin user in the database.
    |
    */

    'admin_email' => env('MAIL_ADMIN_EMAIL'),

];
