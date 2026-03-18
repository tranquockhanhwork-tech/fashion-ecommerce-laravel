<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /* ===================== VIETTEL POST ===================== */
    'viettelpost' => [
        'base_url'             => env('VTP_BASE_URL', 'https://partner.viettelpost.vn/v2'),
        'username'             => env('VTP_USERNAME', ''),
        'password'             => env('VTP_PASSWORD', ''),
        // Thông tin kho gửi hàng (shop)
        'sender_address'       => env('VTP_SENDER_ADDRESS', ''),
        'sender_province'      => env('VTP_SENDER_PROVINCE', 0),  // ID tỉnh
        'sender_district'      => env('VTP_SENDER_DISTRICT', 0),  // ID huyện
        'sender_ward'          => env('VTP_SENDER_WARD', 0),      // ID xã
        'sender_phone'         => env('VTP_SENDER_PHONE', ''),
        'sender_email'         => env('VTP_SENDER_EMAIL', ''),
        'sender_province_name' => env('VTP_SENDER_PROVINCE_NAME', 'TP. Hồ Chí Minh'),
    ],

    'vietqr' => [
        'base_url'     => env('VIETQR_BASE_URL', 'https://api.vietqr.io'),
        'client_id'    => env('VIETQR_CLIENT_ID', ''),
        'api_key'      => env('VIETQR_API_KEY', ''),
        'bank_name'    => env('VIETQR_BANK_NAME', ''),
        'acq_id'       => env('VIETQR_ACQ_ID', ''),
        'account_no'   => env('VIETQR_ACCOUNT_NO', ''),
        'account_name' => env('VIETQR_ACCOUNT_NAME', ''),
        'template'     => env('VIETQR_TEMPLATE', 'compact2'),
        'format'       => env('VIETQR_FORMAT', 'jpg'),
    ],

];
