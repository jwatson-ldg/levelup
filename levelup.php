<?php

/*****************
 * START: Config *
 *****************/

// Enter start date in format DD-MM-YY
$startDate = '04-11-2018';

// Previous average mileage for a non-racing training week
$avgMileage = 60;

// Races during this training block
$races = [
    1 => [
        'date' => '04-11-2018',
        'type' => 'road',
        'distance' => 10,
        'distance_unit' => 'kilometre',
        'target_race' => true
    ],
    2 => [
        'date' => '25-11-2018',
        'type' => 'road',
        'distance' => 10,
        'distance_unit' => 'kilometre',
        'target_race' => false
    ],
    3 => [
        'date' => '01-12-2018',
        'type' => 'road',
        'distance' => 5,
        'distance_unit' => 'kilometre',
        'target_race' => false
    ]
];

// Training sessions available during this training block
$trainingSessions = [
    1 => [
        'date' => '06-11-18',
        'length_warm_up' => 3,
        'length_cool_down' => 2,
        'rep_groups' => [
            1 => [
                'rep_distance' => 600,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 12,
                'rep_recovery' => 90,
                'rep_recovery_unit' => 'second'
            ]
        ]
    ],
    2 => [
        'date' => '13-11-18',
        'length_warm_up' => 3,
        'length_cool_down' => 2,
        'rep_groups' => [
            1 => [
                'rep_distance' => 400,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 1,
                'rep_recovery' => 1,
                'rep_recovery_unit' => 'minute'
            ],
            2 => [
                'rep_distance' => 800,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 2,
                'rep_recovery' => 2,
                'rep_recovery_unit' => 'minute'
            ],
            3 => [
                'rep_distance' => 1,
                'rep_distance_unit' => 'kilometre',
                'rep_qty' => 3,
                'rep_recovery' => 2.5,
                'rep_recovery_unit' => 'minute'
            ],
            4 => [
                'rep_distance' => 800,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 2,
                'rep_recovery' => 2,
                'rep_recovery_unit' => 'minute'
            ],
            5 => [
                'rep_distance' => 400,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 1,
                'rep_recovery' => 0,
                'rep_recovery_unit' => 'second'
            ]
        ]
    ],
    3 => [
        'date' => '20-11-18',
        'length_warm_up' => 3,
        'length_cool_down' => 2,
        'rep_groups' => [
            1 => [
                'rep_distance' => 5,
                'rep_distance_unit' => 'minute',
                'rep_qty' => 4,
                'rep_recovery' => 2,
                'rep_recovery_unit' => 'minute'
            ]
        ]
    ],
    4 => [
        'date' => '27-11-18',
        'length_warm_up' => 3,
        'length_cool_down' => 2,
        'rep_groups' => [
            1 => [
                'rep_distance' => 800,
                'rep_distance_unit' => 'metre',
                'rep_qty' => 8,
                'rep_recovery' => 2,
                'rep_recovery_unit' => 'minute'
            ]
        ]
    ]
];

/***************
 * END: Config *
 ***************/

$introMsg = "\n\t********************************\n";
$introMsg .= "\tLevelup! Training plan generator\n";
$introMsg .= "\t********************************\n\n";

$endDate = strtotime($startDate);
$endDate = strtotime("+27 day", $endDate);
$endDate = date('d-m-Y', $endDate);
$startDateVerbose = date('l jS \of F Y', strtotime($startDate));
$endDateVerbose = date('l jS \of F Y', strtotime($endDate));
$dateMsg = "\tTraining for:\t$startDateVerbose \n\tTo:\t\t$endDateVerbose\n\n";

echo $introMsg;
echo $dateMsg;