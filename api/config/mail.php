<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

return [
    'apiSecurityKey' => env('API_AUTH_BYPASS_TOKEN'),
    'mailers'        => [
        'sendGrid' => [
            'baseUri'   => env('SENDGRID_API_BASE_URI', 'https://api.sendgrid.com/v3/'),
            'apiKey'    => env('SENDGRID_API_KEY',''),
            'apiSecret' => env('SENDGRID_API_SECRET_KEY', ''),
            'fromName'  => env('SENDGRID_API_FROM_NAME', 'Admin'),
            'fromEmail' => env('SENDGRID_API_FROM_EMAIL', ''),
            'priority'  => env('SENDGRID_API_PRIORITY', 2)
        ],
        'mailJet'  => [
            'baseUri'   => env('MAILJET_API_BASE_URI', 'https://api.mailjet.com/v3.1/'),
            'apiKey'    => env('MAILJET_API_KEY', ''),
            'apiSecret' => env('MAILJET_API_SECRET_KEY', ''),
            'fromName'  => env('MAILJET_API_FROM_NAME', 'Admin'),
            'fromEmail' => env('MAILJET_API_FROM_EMAIL', ''),
            'priority'  => env('MAILJET_API_PRIORITY', 1)
        ]
    ],
    'cache'          => [
        'content' => '15'
    ],
    'env' => env('APP_ENV', 'dev')
];
