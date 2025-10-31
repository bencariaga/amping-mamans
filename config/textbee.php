<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Textbee
    |--------------------------------------------------------------------------
    |
    | It is an open-source SMS gateway. Turn any Android phone into an SMS gateway.
    |
    */

    'textbee' => [
        'base_url' => env('TEXTBEE_BASE_URL'),
        'api_key' => env('TEXTBEE_API_KEY'),
        'device_id' => env('TEXTBEE_DEVICE_ID'),
    ],

];
