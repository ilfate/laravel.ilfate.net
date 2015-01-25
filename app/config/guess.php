<?php

return array(
    'game' => [
        'questions' => [],
        'difficulty' => [
        	0 => 1,
        	5 => 2,
        	10 => 3,
        	20 => 4,
        	30 => 5,
        	40 => 6,
        	50 => 7,
        	75 => 8,
        	100 => 9,
        ],
        'levels' => [
        	1 => [15, 1,   1, [1]],
        	2 => [12, 1.1, 1, [1, 1, 1, 2]],
        	3 => [12, 1.2, 1, [1, 1, 2, ]],//3
        	4 => [10, 1.3, 1, [1, 1, 2, 2, ]],//3, 4
        	5 => [10, 1.4, 2, [1, 1, 2, 2, ]],//3, 3, 4, 4, 5
        	6 => [10, 1.5, 2, [1, 2, ]],//3, 4, 5
        	7 => [8,  2,   2, [1, 1, 2, 2, ]],//3, 3, 4, 4, 5, 5, 6
        	8 => [6,  3,   2, [1, 2, ]],//3, 4, 5, 6
        	9 => [5,  10,  2, [1, 2]],//3, 3, 4, 4, 5, 6
        ],
        'types' => [
        	1 => 'normal',
        	2 => 'reverse',
        	3 => 'groups',
        	4 => 'reverse_group',
        	5 => 'channels',
        	6 => 'actors'
        ],
    ]
);