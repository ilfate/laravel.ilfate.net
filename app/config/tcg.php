<?php

return array(
    'game' => [
        'handDraw'   => 2,
        'spellsDraw' => 2,
        'template' => [
            3 => 'deploy',
            4 => 'battle',
            5 => 'finish'
        ],
    ],
    'cards' => array(
        [
            'card' => 0,
            'unit'  => 0,
            'spell' => 0
        ],
        [
            'card' => 1,
            'unit'  => 1,
            'spell' => 0
        ]
    ),
    'units' => [
        [
            'unit' => '\Tcg\Unit\Berserk',
            'name' => 'Berserk',
            'totalHealth' => 10,
            'attack' => [4,5],
            'text' => 'Bloodthirst',
            'keywords' => ['bloodthirst']
        ],
        [
            'unit' => '\Tcg\Unit\Berserk',
            'name'  => 'Super dude',
            'totalHealth' => 12,
            'attack' => [2,4],
            'attackRange' => 1,
            'armor' => 2,
            'text' => '2 Armor',
            'moveDistance' => 1, // not mandatory
        ],
    ],
    'spells' => [
        [
            'spell' => '\Tcg\Spell\Focus',
            'name'  => 'Focus',
            'type' => 'unit',
            'text' => 'Put focus on target unit'
        ],
    ],

);