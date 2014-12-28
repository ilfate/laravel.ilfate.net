<?php

return array(
    'game' => [
        'battle' => [
            'handDraw'           => 5,
            'spellsDraw'         => 4,
            'actionUrl'          => '/tcg/battle/action',
            'mapType'            => 'fixed',
            'minimumCardsInGame' => 3,
        ],
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
    'defaultKingsIds' => [7, 59],
    'kingsIds' => [7, 59],
    'fractions' => [
        1, // Dwarfs 
        2, // Vikings
    ],
    'cards' => array(
        1 => ['card' => 1, 'unit' => 1, 'spell' => 1, 'fraction' => 1, 'image' => 15],  //Guudlin
        2 => ['card' => 2, 'unit' => 2, 'spell' => 1, 'fraction' => 1, 'image' => 16],  // Dvallin
        3 => ['card' => 3, 'unit' => 3, 'spell' => 5, 'fraction' => 1, 'image' => 'pl_m.png'],  // The Mashine
        4 => ['card' => 4, 'unit' => 4, 'spell' => 6, 'fraction' => 1, 'image' => 2],  // Gud the Smith
        5 => ['card' => 5, 'unit' => 5, 'spell' => 5, 'fraction' => 1, 'image' => 17],   // The Defender
        6 => ['card' => 6, 'unit' => 6, 'spell' => 1, 'fraction' => 1, 'image' => 4],   // Swordsdwarf
        7 => ['card' => 7, 'unit' => 7, 'spell' => 1, 'fraction' => 1, 'image' => 1, 'isKing' => true],
        8 => ['card' => 8, 'unit' => 8, 'spell' => 7, 'fraction' => 1, 'image' => 'pl_m.png'],  // Rockthrower
        9 => ['card' => 9, 'unit' => 9, 'spell' => 7, 'fraction' => 1, 'image' => 14],  // Deep miner
        50 => ['card' => 50, 'unit' => 50, 'spell' => 1, 'fraction' => 2, 'image' => 7], // furyless
        51 => ['card' => 51, 'unit' => 51, 'spell' => 1, 'fraction' => 2, 'image' => 11],  // Blind Valkiry
        52 => ['card' => 52, 'unit' => 52, 'spell' => 1, 'fraction' => 2, 'image' => 10],   //Flying Rage
        53 => ['card' => 53, 'unit' => 53, 'spell' => 1, 'fraction' => 2, 'image' => 5], //Kruug
        54 => ['card' => 54, 'unit' => 54, 'spell' => 2, 'fraction' => 2, 'image' => 13],    // Axe thrower     // axe
        55 => ['card' => 55, 'unit' => 55, 'spell' => 2, 'fraction' => 2, 'image' => 6],     // Runner
        56 => ['card' => 56, 'unit' => 56, 'spell' => 4, 'fraction' => 2, 'image' => 8],      // Blood Shaman
        57 => ['card' => 57, 'unit' => 57, 'spell' => 3, 'fraction' => 2, 'image' => 12],    // Aarr
        59 => ['card' => 59, 'unit' => 59, 'spell' => 1, 'fraction' => 2, 'image' => 9, 'isKing' => true],  //Viking Leader
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
            'totalHealth' => 1,
            'attack' => [5,10],
            'armor'  => 4,
            'attackRange' => 3,
            'attackType' => 2,
            'text' => 'Can attack only in front direction. Cant attack after move',
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
            'armor'  => 20,
            'text' => 'King',
        ],
        8 => [
            'unit' => '\Tcg\Unit\Common',
            'name' => 'Rockthrower',
            'totalHealth' => 5,
            'attack' => [2, 4],
            'text' => 'Range 2',
            'attackRange' => 2,
        ],
        9 => [
            'unit' => '\Tcg\Unit\DeepMiner',
            'name' => 'Deep miner',
            'totalHealth' => 8,
            'attack' => [2, 3],
            'armor'  => 2,
            'text' => 'On death: Draw a card.',
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
            'attack' => [1, 1],
            'attackRange' => 1,
            'text' => 'Can throw an axe for 6 damage. Can pick up an axe.',
            'keywords' => ['bloodthirst'],
        ],
        55 => [
            'unit' => '\Tcg\Unit\UnitCanThrowAxe',
            'name'  => 'Runner',
            'totalHealth' => 10,
            'attack' => [1, 4],
            'text' => 'Fast. Can pick up an axe',
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
            'moveType' => 2,
            'text' => 'Can move only diagonal. On death: leave an Axe on the field.',
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
            'text' => 'Put focus on target unit',
        ],
        2 => [
            'spell' => '\Tcg\Spell\Axe',
            'name'  => 'Axe',
            'type' => 'unit',
            'text' => 'Throw an axe for 6 damage',
        ],
        3 => [
            'spell' => '\Tcg\Spell\Heal',
            'name'  => 'Heal beam',
            'type' => 'unit',
            'text' => 'Heal 3 damage from target unit',
            'data' => ['value' => 3],
        ],
        4 => [
            'spell' => '\Tcg\Spell\Heal',
            'name'  => 'Heal stream',
            'type' => 'unit',
            'text' => 'Heal 6 damage from target unit',
            'data' => ['value' => 6],
        ],
        5 => [
            'spell' => '\Tcg\Spell\Armor',
            'name'  => 'Restore armor',
            'type' => 'unit',
            'text' => 'Restore 6 armor to target unit',
            'data' => ['value' => 6],
        ],
        6 => [
            'spell' => '\Tcg\Spell\Armor',
            'name'  => 'Recreate armor',
            'type' => 'unit',
            'text' => 'Restore 10 armor to target unit',
            'data' => ['value' => 10],
        ],
        7 => [
            'spell' => '\Tcg\Spell\Armor',
            'name'  => 'Add armor',
            'type' => 'unit',
            'text' => 'Add 4 armor to target unit',
            'data' => ['value' => 4, 'mode' => 'add'],
        ],
    ],
    'fieldObjects' => [
        1 => [
            'class' => '\Tcg\FieldObject\Common',
            'image' => 'f_castle.jpg',
            'passable' => false,
        ],
        2 => [
            'class' => '\Tcg\FieldObject\Common',
            'image' => 'axe.png',
            'passable' => true,
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