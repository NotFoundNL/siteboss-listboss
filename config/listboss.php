<?php

// This file is published by the siteboss-listboss package

return [

    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | Configure the endpoint to use for the ListBoss API.
    |
    */

    'endpoint' => env('LISTBOSS_ENDPOINT', 'https://listboss.nl/v2/'),

    /*
    |--------------------------------------------------------------------------
    | API key
    |--------------------------------------------------------------------------
    |
    | This is the API key used to authenticate with ListBoss.
    |
    */

    'api_key' => env('LISTBOSS_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | SSL verification
    |--------------------------------------------------------------------------
    |
    | This can be used to test against a local server with a self-signed
    | certificate. It is recommended to leave this set to true.
    |
    */

    'ssl_verify' => env('LISTBOSS_SSL_VERIFY', true),

    /*
    |--------------------------------------------------------------------------
    | Enable the backend
    |--------------------------------------------------------------------------
    |
    | This can be used to disable the backend routes. This is useful if you
    | want to use the API only.
    |
    */

    'backend' => env('LISTBOSS_BACKEND', true),

];
