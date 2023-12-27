<?php

return [
    'foo' => 'bar',

    // logger
    'logger' =>
        [
            'formatter' => new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n", 'Y-m-d H:i:s.u', true),
            'file_extension' => 'log',
//            'formatter' => new \Monolog\Formatter\JsonFormatter(),
//            'file_extension' => 'json',
        ],
];