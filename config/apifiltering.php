<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default options
    |--------------------------------------------------------------------------
    |
    | You can configure here the default options for your filtered eloquent models
    |
    */
    'default' => [
        'where' => [],
        'order_by' => [],
        'limit' => 100,
        'offset' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Values casting
    |--------------------------------------------------------------------------
    |
    | As the filtering from url is generally string we can cast values on the
    | fly with the following array. You can, remove, edit or add values to be
    | casted.
    |
    | Some casts are required for working well with SQL. For example 'null'
    | should be parsed for making Where NULL/NOT NULL queries.
    |
    */

    'casting' => [
        [true, 1],
        [false, 0],
        ['true', 1],
        ['false', 0],
        ['null', null],
    ]
];
