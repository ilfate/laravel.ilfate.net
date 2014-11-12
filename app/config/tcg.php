<?php

return array(
    'game' => [
        'handDraw'   => 2,
        'spellsDraw' => 2,
    ],
    'cards' => array(
        0 => ['card' => 0, 'unit' => 0, 'spell' => 0],
        1 => ['card' => 1, 'unit' => 1, 'spell' => 0],
        2 => ['card' => 2, 'unit' => 2, 'spell' => 0],
        3 => ['card' => 3, 'unit' => 3, 'spell' => 0],
        4 => ['card' => 4, 'unit' => 4, 'spell' => 0],
        5 => ['card' => 5, 'unit' => 5, 'spell' => 0],
        50 => ['card' => 50, 'unit' => 50, 'spell' => 0],
        51 => ['card' => 51, 'unit' => 51, 'spell' => 0],
        52 => ['card' => 52, 'unit' => 52, 'spell' => 0],
        53 => ['card' => 53, 'unit' => 53, 'spell' => 0],
        54 => ['card' => 54, 'unit' => 54, 'spell' => 0],
        55 => ['card' => 55, 'unit' => 55, 'spell' => 0],
        56 => ['card' => 56, 'unit' => 56, 'spell' => 0],
        57 => ['card' => 57, 'unit' => 57, 'spell' => 0],
    ),
    'units' => [
    // Dwarfs
        0 => [ 
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Guudlin',
            'totalHealth' => 8,
            'attack' => [1,4],
            'armor'  => 8,
            'text' => '',
        ],
        1 => [ 
            'unit' => '\Tcg\Unit\Dvallin',
            'name' => 'Dvallin',
            'totalHealth' => 12,
            'attack' => [2,4],
            'text' => 'On Attack gets + X armor where X is dealt damage.',
        ],
        2 => [ 
            'unit' => '\Tcg\Unit\TheMashine',
            'name' => 'The Mashine',
            'totalHealth' => 5,
            'attack' => [5,10],
            'armor'  => 20,
            'text' => 'Can attack only in front direction',
        ],
        3 => [ 
            'unit' => '\Tcg\Unit\GudTheSmith',
            'name' => 'Gud the Smith',
            'totalHealth' => 4,
            'attack' => [1,4],
            'text' => 'Can repair 3 armor to nearby frendly unit if there is no targets to attack',
        ],
        4 => [ 
            'unit' => '\Tcg\Unit\Common',
            'name' => 'The Defender',
            'totalHealth' => 4,
            'attack' => [1, 2],
            'armor'  => 16,
            'text' => 'Focus',
            'keywords' => ['focus']
        ],
        5 => [ 
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Swordsdwarf',
            'totalHealth' => 10,
            'attack' => [2, 6],
            'armor'  => 2,
            'text' => '',
        ],
        //bloodthirst
        50 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Furyless',
            'totalHealth' => 8,
            'attack' => [3, 7],
            'text' => '',
            'keywords' => ['bloodthirst']
        ],
        51 => [
            'unit' => '\Tcg\Unit\BlindValkiry',
            'name'  => 'Blind Valkiry',
            'totalHealth' => 16,
            'attack' => [5, 5],
            'text' => 'On attack: get 2 damage',
            'keywords' => ['bloodthirst', 'fast'],
            'moveDistance' => 2, // not mandatory
        ],
        52 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Flying Rage',
            'totalHealth' => 14,
            'attack' => [4, 4],
            'text' => '',
            'keywords' => ['bloodthirst'],
        ],
        53 => [
            'unit' => '\Tcg\Unit\Kruug',
            'name'  => 'Kruug',
            'totalHealth' => 12,
            'attack' => [3, 5],
            'text' => 'Heal 2 damage from itself on attack',
        ],
        54 => [
            'unit' => '\Tcg\Unit\AxeThrower',
            'name'  => 'Axe thrower',
            'totalHealth' => 6,
            'attack' => [2, 4],
            'text' => 'On deploy: Deal 4 damage to an enemy unit in front',
            'keywords' => ['bloodthirst'],
        ],
        55 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Runner',
            'totalHealth' => 10,
            'attack' => [1, 7],
            'text' => 'Fast',
            'keywords' => ['fast'],
            'moveDistance' => 2, // not mandatory
        ],
        56 => [
            'unit' => '\Tcg\Unit\BloodShaman',
            'name'  => 'Blood Shaman',
            'totalHealth' => 5,
            'attack' => [0, 1],
            'text' => 'Unit in front of Blood Shaman has: -3 damage from any source',
        ],
        57 => [
            'unit' => '\Tcg\Unit\Aarr',
            'name'  => 'Aarr',
            'totalHealth' => 11,
            'attack' => [3, 8],
            'text' => 'Can attack only diagonal',
        ],
        999 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Example dude',
            'totalHealth' => 12,
            'attack' => [2,4],
            'attackRange' => 1,
            'armor' => 2,
            'text' => 'Bloodthirst',
            'keywords' => ['bloodthirst', 'fast'],
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