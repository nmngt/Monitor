<?php
return [
    'TitleTagLength' => [
        'class' => 'App\Checks\TitleTagLength',
    ],
    'AppleTouchIcon' => [
        'class' => 'App\Checks\AppleTouchIcon',
    ],
    'GoogleAnalyticsPropertyId' => [
        'class' => 'App\Checks\GoogleAnalyticsPropertyId',
        'calls' => [
            'setValue' => 'SKIP_MATCHING_PROPERTY_IDS',
        ],
    ],
    'CheckDnsA' => [
            'class' => 'App\Checks\CheckDnsRecord',
        'calls' =>  [
            'setName' => "Check DNS A record exists.",
            'setValue' => "A",
        ],
    ],
    'CheckDnsMX' => [
        'class' => 'App\Checks\CheckDnsRecord',
        'calls' =>  [
            'setName' => "Check DNS MX record exists.",
            'setValue' => "MX",
        ],
    ],
    'RobotsMayIndex' => [
        'class' => 'App\Checks\RobotsMayIndex',
    ],
    'RobotsMayFollow' => [
        'class' => 'App\Checks\RobotsMayFollow',
    ],
    'SslEndOfLife' => [
        'class' => 'App\Checks\SslEndOfLife',
    ],
    'FindStringOnWebsite' => [
        'class' => 'App\Checks\FindStringOnWebsite',
    ],
    'FindStringImpressum' => [
        'class' => 'App\Checks\FindStringOnWebsite',
        'calls' => [
            'setName' => 'Find string Impressum on website',
            'setValue' => 'Impressum',
        ],
    ],
    'FindStringDatenschutz' => [
        'class' => 'App\Checks\FindStringOnWebsite',
        'calls' => [
            'setName' => 'Find string Datenschutz on website',
            'setValue' => 'Datenschutz',
        ],
    ],
    'ExactlyOneH1TagPresent' => [
        'class' => 'App\Checks\H1TagPresent',
        'calls' =>  [
            'setName' => "Exactly 1 H1-tag present",
            'allowMultipleTags' => false,
        ],
    ],
    'HtmlTagNotPresent' => [
        'class' => 'App\Checks\HtmlTagNotPresent',
    ],
    'HttpHeaderStatusCode' => [
        'class' => 'App\Checks\HttpHeaderStatusCode',
    ],
    'HttpHeaderHasFarFutureExpiresHeader' => [
        'class' => 'App\Checks\HttpHeaderHasFarFutureExpiresHeader',
    ],
    'HttpHeaderResourceIsGzipped' => [
        'class' => 'App\Checks\HttpHeaderResourceIsGzipped',
    ],
    'LinkRelCanonical' => [
        'class' => 'App\Checks\LinkRelCanonical',
    ],
    'LinkHrefLang' => [
        'class' => 'App\Checks\LinkHrefLang',
    ],
    'MetaDescriptionLength' => [
        'class' => 'App\Checks\MetaDescriptionLength',
    ],
    'MetaGeneratorNotPresent' => [
        'class' => 'App\Checks\MetaGeneratorNotPresent',
    ],
    'OgPropertyPresent' => [
        'class' => 'App\Checks\OgPropertyPresent',
    ],
    'RobotsTxtDoesNotExcludeAll' => [
        'class' => 'App\Checks\RobotsTxtDoesNotExcludeAll',
    ],
    'XuaCompatible' => [
        'class' => 'App\Checks\XuaCompatible',
    ],
];
