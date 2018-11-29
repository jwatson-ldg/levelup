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
        'distance_unit' => 'kilometres',
        'target_race' => true
    ],
    2 => [
        'date' => '25-11-2018',
        'type' => 'road',
        'distance' => 10,
        'distance_unit' => 'kilometres',
        'target_race' => false
    ],
    3 => [
        'date' => '01-12-2018',
        'type' => 'road',
        'distance' => 5,
        'distance_unit' => 'kilometres',
        'target_race' => false
    ]
];

// Training sessions available during this training block
$trainingSessions = [
    1 => [],
    2 => [],
    3 => [],
    4 => []
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