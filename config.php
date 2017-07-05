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

    'ikajo' => [
        'defaultChannelId' => null,
        'defaultCurrency' => 'USD',
        'clientKey' => '',
        'clientPass' => '',
        'billingUrl' => ''
    ],

    'baseurl' => 'http://127.0.0.1:8000',
    'subDir' => '',

    'twig' => [
        'cache' => false
    ],

    'secret' => 'L71o/:E7J3:Fyp9hOn+q-/x0g(Q!-,n3|oyxb/CcFlb~UCU,<W?Ka3i*V;X~Fp9L',
    'timezone' => 'UTC',

    /**
     * User access token expires in 2 weeks
     */
    'authAccessTokenExpires' => 1209600
];