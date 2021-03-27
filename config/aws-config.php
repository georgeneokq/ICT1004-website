<?php

return [
    'includes' => ['_aws'],
    'services' => [
        // All AWS clients extend from 'default_settings'. Here we are
        // overriding 'default_settings' with our default credentials and
        // providing a default region setting.
        'default_settings' => [
            'params' => [
                'credentials' => [
                    'key'    => getenv('AWS_ACCESS_KEY_ID'),
                    'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
                ],
                'region' => 'us-east-1'
            ]
        ]
    ]
];