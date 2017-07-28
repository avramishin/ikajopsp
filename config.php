<?php
/**
 * Global config file
 *
 */

return [

    'db' => [
        'psp' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'psp'
        ]
    ],

    'baseurl' => 'http://127.0.0.1:8000',
    'subDir' => '',

    'twig' => [
        'cache' => false
    ],

    'secret' => '',
    'timezone' => 'UTC',
    'billingUrl' => 'https://secure.payinspect.com/post/',
    'defaultClientId' => 'b4markets',

    /**
     * User access token expires in 2 weeks
     */
    'authAccessTokenExpires' => 1209600
];