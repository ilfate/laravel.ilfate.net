<?php

return array(
    'game' => [
        'test' => [
            'handDraw'           => 5,
            'spellsDraw'         => 2,
            'actionUrl'          => '/tcg/test/action',
            'mapType'            => 'fixed',
            'minimumCardsInGame' => 3,
        ],
        'debug' => [
            'handDraw'           => 2,
            'spellsDraw'         => 2,
            'actionUrl'          => '/tcg/test/action',
            'mapType'            => 'fixed',
            'minimumCardsInGame' => 1,
        ],
    ],
    'cards' => array(
        1 => ['card' => 1, 'unit' => 1, 'spell' => 1, 'image' => 'pl_m.png'],  //Guudlin
        2 => ['card' => 2, 'unit' => 2, 'spell' => 1, 'image' => 'pl_m.png'],  // Dvallin
        3 => ['card' => 3, 'unit' => 3, 'spell' => 1, 'image' => 'pl_m.png'],
        4 => ['card' => 4, 'unit' => 4, 'spell' => 1, 'image' => 'dwa_1.png'],  // Gud the Smith
        5 => ['card' => 5, 'unit' => 5, 'spell' => 1, 'image' => 'dwa_2.png'],   // The Defender
        6 => ['card' => 6, 'unit' => 6, 'spell' => 1, 'image' => 'dwa_3.png'],   // Swordsdwarf
        7 => ['card' => 7, 'unit' => 7, 'spell' => 1, 'image' => 'dwa_1_1.png', 'isKing' => true],
        8 => ['card' => 8, 'unit' => 8, 'spell' => 1, 'image' => 'pl_m.png'],
        50 => ['card' => 50, 'unit' => 50, 'spell' => 1, 'image' => 'vik_2.png'], // furyless
        51 => ['card' => 51, 'unit' => 51, 'spell' => 1, 'image' => 'vik_4.png'],  // Blind Valkiry
        52 => ['card' => 52, 'unit' => 52, 'spell' => 1, 'image' => 'vik_3_1.png'],   //Flying Rage
        53 => ['card' => 53, 'unit' => 53, 'spell' => 1, 'image' => 'vik_1.png'], //Kruug
        54 => ['card' => 54, 'unit' => 54, 'spell' => 1, 'image' => 'vik_2_1.png'],    // Axe thrower
        55 => ['card' => 55, 'unit' => 55, 'spell' => 1, 'image' => 'vik_1_1.png'],     // Runner
        56 => ['card' => 56, 'unit' => 56, 'spell' => 1, 'image' => 'pl_m.png'],
        57 => ['card' => 57, 'unit' => 57, 'spell' => 1, 'image' => 'vik_5.png'],    // Aarr
        59 => ['card' => 59, 'unit' => 59, 'spell' => 1, 'image' => 'vik_3.png', 'isKing' => true],  //Viking Leader
    ),
    'units' => [
        // Dwarfs
        1 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Guudlin',
            'totalHealth' => 8,
            'attack' => [1,4],
            'armor'  => 8,
            'moveType' => 2,
            'moveSteps' => 2,
            'text' => '',
        ],
        2 => [
            'unit' => '\Tcg\Unit\Dvallin',
            'name' => 'Dvallin',
            'totalHealth' => 12,
            'attack' => [2,4],
            'text' => 'On Attack gets + X armor where X is dealt damage.',
        ],
        3 => [
            'unit' => '\Tcg\Unit\TheMashine',
            'name' => 'The Mashine',
            'totalHealth' => 5,
            'attack' => [7,10],
            'armor'  => 20,
            'text' => 'Can attack only in front direction',
        ],
        4 => [
            'unit' => '\Tcg\Unit\GudTheSmith',
            'name' => 'Gud the Smith',
            'totalHealth' => 4,
            'attack' => [1,4],
            'text' => 'Can repair 3 armor to nearby frendly unit if there is no targets to attack',
        ],
        5 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'The Defender',
            'totalHealth' => 4,
            'attack' => [1, 2],
            'armor'  => 16,
            'text' => 'Focus',
            'keywords' => ['focus']
        ],
        6 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Swordsdwarf',
            'totalHealth' => 10,
            'attack' => [2, 6],
            'armor'  => 2,
            'text' => '',
        ],
        7 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Dwarf King',
            'totalHealth' => 15,
            'attack' => [2, 3],
            'armor'  => 30,
            'text' => 'King',
        ],
        8 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Rockthrower',
            'totalHealth' => 5,
            'attack' => [1, 4],
            'text' => 'Range 2',
            'attackRange' => 2,
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
            'keywords' => ['bloodthirst'],
            'moveSteps' => 2, // not mandatory
        ],
        52 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Flying Rage',
            'totalHealth' => 14,
            'attack' => [4, 4],
            'moveSteps' => 2,
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
            'moveSteps' => 3, // not mandatory
        ],
        56 => [
            'unit' => '\Tcg\Unit\BloodShaman',
            'name'  => 'Blood Shaman',
            'totalHealth' => 5,
            'attack' => [0, 1],
            'moveSteps' => 2,
            'text' => 'Unit in front of Blood Shaman has: -3 damage from any source',
        ],
        57 => [
            'unit' => '\Tcg\Unit\Aarr',
            'name'  => 'Aarr',
            'totalHealth' => 11,
            'attack' => [3, 8],
            'moveSteps' => 2,
            'text' => 'Can attack only diagonal',
        ],
//        58 => [
//            'unit' => '\Tcg\Unit\Harpoon',
//            'name'  => 'Harpoon',
//            'totalHealth' => 8,
//            'attack' => [2, 5],
//            'text' => 'On deploy: pull enemy unit closer (range 2)',
//        ],
        59 => [
            'unit' => '\Tcg\Unit\Common',
            'name'  => 'Viking Leader',
            'totalHealth' => 25,
            'attack' => [3, 5],
            'moveSteps' => 2,
            'text' => 'King',
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
            'moveSteps' => 1, // not mandatory
            'moveType'     => 1
        ],
    ],
    'spells' => [
        1 => [
            'spell' => '\Tcg\Spell\Focus',
            'name'  => 'Focus',
            'type' => 'unit',
            'text' => 'Put focus on target unit'
        ],
    ],
    'fieldObjects' => [
        1 => [
            'class' => '\Tcg\FieldObject\Common',
            'image' => 'f_castle.jpg',
            'passable' => false,
        ],
    ],
    'fieldMap' => [
        1 => [
            ['x' => 2, 'y' => 2, 'id' => 1],
            ['x' => 1, 'y' => 3, 'id' => 1],
            ['x' => 0, 'y' => 3, 'id' => 1],
            ['x' => 5, 'y' => 5, 'id' => 1],
            ['x' => 6, 'y' => 4, 'id' => 1],
            ['x' => 7, 'y' => 4, 'id' => 1],
        ]
    ]


);