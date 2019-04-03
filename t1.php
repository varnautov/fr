<?php

require __DIR__.'/classes/T1.php';

use t1\T1;

$example = [
    'name' => 'Software',
    'properties' => [
        'version' => '',
        'size' => 195,
        'param' => 0
    ],
    'author' => [
        [
            'name' => '',
            'email' => ''
        ],
        [
            'name' => 'Ivan',
            'email' => 'mail@example.com'
        ]
    ]
];


$worker = new T1($example);
$worker->run();
print_r($worker->getResult());
