<?php

namespace NGT\Monitor\Contracts;

class Constants
{
    // app
    const VERSION = '1.0.3';
    const PROPAGANDA = 'NGT\\Monitor';

    // status codes
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    // error level
    const INFO = "info";
    const DEBUG = "debug";
    const ERROR = "error";
    const FATAL = "fatal";
    const SUCCESS = "success";
    const WARNING = "warning";

    // default checks (used by check:single)
    const DEFAULT_CHECKS = [
        'HttpHeaderStatusCode' => true,
        'CheckDnsA' => true,
        'CheckDnsMX' => true,
        'SslEndOfLife' => true,
        'RobotsMayIndex' => true,
        'RobotsMayFollow' => true,
        'GoogleAnalyticsPropertyId' => true,
        'ExactlyOneH1TagPresent' => true,
        'MetaDescriptionLength' => true,
        'MetaGeneratorNotPresent' => true,
        'OgPropertyPresent' => true,
        'TitleTagLength' => true,
        'AppleTouchIcon' => true,
        'LinkHrefLang' => true,
        'LinkRelCanonical' => true,
        // 'HttpHeaderHasFarFutureExpiresHeader' => true,
        // 'HttpHeaderResourceIsGzipped' => true,
        // 'XuaCompatible' => true,
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
    ];

    // ssl checks (used by check:ssl)
    const SSL_CHECKS = [
        'SslEndOfLife' => true,
    ];
}
