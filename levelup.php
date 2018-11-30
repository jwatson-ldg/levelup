<?php

require_once 'config.php';

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

// TODO: Build Calendar
// Step 1: Fill in the races
// Step 2: Fill in target mileage for each week
// Step 3: Fill in days not available to train
// Step 4: Fill in remaining rest days
// Step 5: Fill in available training sessions
// Step 6: ???
// Step 7: Profit!