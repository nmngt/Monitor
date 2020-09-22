<?php
return [
    'georg-tusel.com' => [
        // Important checks
        'HttpHeaderStatusCode' => true,
        'CheckDnsA' => true,
        'CheckDnsMX' => true,
        'SslEndOfLife' => true,
        'RobotsMayIndex' => true,
        'RobotsMayFollow' => true,
        'RobotsTxtDoesNotExcludeAll' => true,
        
        'GoogleAnalyticsPropertyId' => [
            'configuration' => [
                'setValue' => 'UA-62557852-2',
            ],
        ],        
        'FindStringImpressum' => [
            'configuration' => [
                'setValue' => 'Impressum',
            ],
        ],
        'FindStringDatenschutz' => [
            'configuration' => [
                'setValue' => 'Datenschutz',
            ],
        ],

        // Nice to have checks
        'LinkHrefLang' => true,
        'LinkRelCanonical' => true,
        'TitleTagLength' => true,
        'AppleTouchIcon' => true,
        'ExactlyOneH1TagPresent' => true,
        'MetaDescriptionLength' => true,
        'MetaGeneratorNotPresent' => true,
        'OgPropertyPresent' => true,
        'HttpHeaderHasFarFutureExpiresHeader' => true,
        'HttpHeaderResourceIsGzipped' => true,
        'XuaCompatible' => true,


        // ToDo:        
        // 'checkGoogleAnalyticsSessionTimer' => true,
        // 'checkGoogleAnalyticsAnonymizeIp' => true,
        // 'checkUnwantedContent' => [
        //     '/debug/s'
        // ],
        // 'checkPagesExists' => [
        //     '/impressum/',
        //     '/datenschutz/',
        // ],
        // $default_checks,
    ],

];
