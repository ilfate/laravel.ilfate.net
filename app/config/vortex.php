<?php

return array(
    'map' => [
        'keysNumber' => 3,
        'actions' => [
            1 => 'arrow',
            2 => 'view',
            3 => 'keys',
            4 => 'bomb',
            5 => 'knight',
            6 => 'portal',
            7 => 'vortex',
            8 => 'treasure'
        ],
        'actionChanses' => [
            1,1,1, 2,2,2, 3,3, 4, 5,5, 6, 7,
            1,1,1,1,1,1,1,1,1,1,1,1,1,1,1
        ],
        'defaultAccessDirections' => '0+1+2+3',
        'actionsTypes' => [
            1 => [
                'types' => [
                1 => '0', 
                2 => '1', 
                3 => '2', 
                4 => '3', 
                5 => '0.5', 
                6 => '1.5', 
                7 => '2.5', 
                8 => '3.5', 
                9 => '0+2', 
                10 => '1+3', 
                11 => '0.5+2.5', 
                12 => '1.5+3.5', 
                13 => '0+1+2+3', 
                14 => '0.5+1.5+2.5+3.5'
                ],
                'chances' => [1,1,1,2,2,2,3,3,3,4,4,4,5,5,5,6,6,6,7,7,7,8,8,8,9,9,10,10,11,11,12,12,13,13,14,
                    5,5,5,6,6,6,7,7,7,8,8,8,5,5,5,6,6,6,7,7,7,8,8,8,5,5,5,6,6,6,7,7,7,8,8,8,5,5,5,6,6,6,7,7,7,8,8,8,5,5,5,6,6,6,7,7,7,8,8,8
                ]
            ],
            2 => [
                'types' => [
                    1 => '0',
                    2 => '1',
                    3 => '2',
                    4 => '3',
                    5 => '0.5',
                    6 => '1.5',
                    7 => '2.5',
                    8 => '3.5',
                    9 => '0+1+2+3+0.5+1.5+2.5+3.5'
                ],
                'chances' => [1,2,3,4,5,6,7,8,9],
                'range' => 3,
            ],
            3 => [
                'types' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => -1,
                    6 => -2,
                    7 => -3,
                ],
                'chances' => [1,1,2,2,3,4,5,6,7]
            ],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
        ]
    ]
);