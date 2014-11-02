<?php

return array(
    'game' => [
        'handDraw'   => 2,
        'spellsDraw' => 2,
        'template' => [
            3 => 'deploy',
            4 => 'battle'
        ],
    ],
    'cards' => array(
        [
            'card' => 0,
            'name'  => 'Berserk',
            'unit'  => 0,
            'spell' => 0
        ],
        [
            'card' => 1,
            'name'  => 'Super dude',
            'unit'  => 1,
            'spell' => 0
        ]
    ),
    'units' => [
        [
            'unit' => '\Tcg\Unit\Berserk',
            'totalHealth' => 10,
            'text' => 'Bloodthirst'
        ],
        [
            'unit' => '\Tcg\Unit\Berserk',
            'totalHealth' => 12,
            'text' => 'SUPA',
            'moveDistance' => 1, // not mandatory
        ],
    ],
    'spells' => [
        [
            'spell' => 0,
            'type' => 'focus',
            'text' => 'Put focus on target unit'
        ],
    ],

);