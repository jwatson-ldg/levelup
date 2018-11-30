<?php

// [DONE] Step 1: Build array of all dates for the block
// [DONE] Step 2: Fill in the events
// [WIP] Step 3: Fill in target mileage for each week
// Step 4: Fill in days not available to train
// Step 5: Fill in remaining rest days
// Step 6: Fill in available training sessions
// Step 7: ???
// Step 8: Profit!

require 'TrainingWeek.php';

class TrainingBlock
{
    private $trainingBlock = [];
    private $dates = [];
    private $startDate;
    private $numDays;
    private $events;
    private $avgMileage;

    public function __construct($startDate, $numDays, $events, $avgMileage)
    {
        $this->startDate = $startDate;
        $this->numDays = $numDays;
        $this->events = $events;
        $this->avgMileage = $avgMileage;

        $this->dates = $this->getDates();

        $this->buildTrainingBlock();
    }

    public function get()
    {
        return $this->trainingBlock;
    }

    private function buildTrainingBlock()
    {
        $this->addRacesToTrainingBlock();
        $this->addTargetWeeklyMileageToTrainingBlock();
    }

    private function addRacesToTrainingBlock()
    {
        foreach ($this->dates as $week => $days) {
            $eventDays = [];
            foreach ($days as $day) {
                foreach ($this->events as $event) {
                    if ($event['date'] === $day) {
                        $eventDays[$day][$event['time']] = [
                            'name' => $event['name'],
                            'type' => $event['type'],
                            'distance' => $event['distance'],
                            'distance_unit' => $event['distance_unit'],
                            'target_event' => $event['target_event']
                        ];
                    }
                    $this->trainingBlock[$week] = [
                        'mileage' => 0,
                        'days' => $eventDays
                    ];
                }
            }
        }
    }

    private function addTargetWeeklyMileageToTrainingBlock()
    {
        foreach ($this->dates as $weekDates) {
           $trainingWeek = new TrainingWeek($weekDates, $this->events, $this->avgMileage);
           die();

        }

//        $targetMileage = ...
//        foreach ($this->trainingBlock as $k => $week) {
//            $trainingBlock[$k]['mileage'] = $targetMileage;
//        }
    }

    private function getDates()
    {
        $dates = [];
        $day = $week = 0;

        $datePeriod = new DatePeriod(
            new DateTime($this->startDate),
            new DateInterval('P1D'),
            new DateTime($this->getEndDateOfTrainingBlock())
        );

        foreach ($datePeriod as $key => $value) {
            if ($day % 7 === 0) {
                $week++;
            }
            $dates[$week][] = $value->format('d-m-Y');
            $day++;
        }

        return $dates;
    }

    private function getEndDateOfTrainingBlock()
    {
        $endDate = strtotime($this->startDate);
        $endDate = strtotime("+ $this->numDays day", $endDate);

        return date('d-m-Y', $endDate);
    }
}