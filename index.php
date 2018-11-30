<?php

require_once 'config.php';
require_once 'app/TrainingBlock.php';

$trainingBlock = new TrainingBlock($startDate, $numDays, $events, $avgMileage);
//$trainingBlock->get();