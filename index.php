<?php

/**********
 * Config *
 **********/

// Enter start date in format DD/MM/YY
$startDate = '04-11-2018';

$introMsg .= "\n\t********************************\n";
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