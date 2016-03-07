<?php

return array(

    'defaultController' => 'HomeController',

    // Just put null value if you has enable .htaccess file
    'indexFile' => INDEX_FILE . '/',

    'module' => array(
        'path' => APP,
        'domainMapping' => array(),
    ),

    'vendor' => array(
        'path' => GEAR.'vendors/'
    ),

    'alias' => [
        'controller' => [
            'class' => 'AliasController',
            'method' => 'index'
        ],
     ],
);
