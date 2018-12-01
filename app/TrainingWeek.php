<?php

class TrainingWeek
{
    private $weekDates = [];
    private $dateDayBeforeCurrentTrainingWeek;
    private $dateDayAfterCurrentTrainingWeek;
    private $events = [];
    private $avgWeeklyMileage = 0;
    private $trainingWeekIntensity = 0;

    public function __construct($weekDates, $events, $avgWeeklyMileage)
    {
        $this->weekDates = $weekDates;
        $this->dateDayBeforeCurrentTrainingWeek = date('d-m-Y', strtotime("-1 day", strtotime($weekDates[0])));
        $this->dateDayAfterCurrentTrainingWeek = date('d-m-Y', strtotime("+1 day", strtotime($weekDates[6])));
        $this->events = $events;
        $this->avgWeeklyMileage = $avgWeeklyMileage;

        $this->calculateCombinedEventIntensityForTheWeek();
    }

    private function getPreviousWeeksEvent()
    {
        foreach ($this->events as $event) {
            if ($event['date'] === $this->dateDayBeforeCurrentTrainingWeek) {
                return $event;
            }
        }

        return false;
    }

    private function getCurrentWeeksEvents()
    {
        $currentWeeksEvents = false;

        foreach ($this->weekDates as $day) {
            foreach ($this->events as $event) {
                if ($day == $event['date']) {
                    $currentWeeksEvents[] = $event;
                }
            }
        }

        return $currentWeeksEvents;
    }

    private function getNextWeeksEvent()
    {
        foreach ($this->events as $event) {
            if ($event['date'] === $this->dateDayAfterCurrentTrainingWeek) {
                return $event;
            }
        }

        return false;
    }

    private function calculateEventAsPercentageOfWeeklyMileage($event)
    {
        // Currently, average distance is stored in miles. Going forward, this could change so keep that in mind!
        if ($event['distanceUnit'] === 'kilometre') {
            $eventDistance = $event['distance'] / 1.609;
        } else {
            $eventDistance = $event['distance'];
        }

        return number_format(($eventDistance / $this->avgWeeklyMileage) * 100, 0);
    }

    private function calculateCombinedEventIntensityForTheWeek()
    {
        $eventPreviousWeek = $this->getPreviousWeeksEvent();
        $eventsThisWeek = $this->getCurrentWeeksEvents();
        $eventNextWeek = $this->getNextWeeksEvent();

        // No races during current week, last day of previous week or first day of next week (typical training week)
        if ($eventsThisWeek === false && $eventNextWeek === false && $eventPreviousWeek === false) {
            return 0;
        }

        // No races during current week or first day of next week, but raced last day of previous week:
        if ($eventsThisWeek === false && $eventNextWeek === false && $eventPreviousWeek !== false) {
            $eventPercentageOfWeeklyMileage = $this->calculateEventAsPercentageOfWeeklyMileage($eventPreviousWeek);

            if ($eventPercentageOfWeeklyMileage > 15) {
                // Race distance was > 15% of weekly mileage
                return 3;
            } else if ($eventPercentageOfWeeklyMileage >= 10 && $eventPercentageOfWeeklyMileage <= 15) {
                // Race distance was 10-15% of weekly mileage
                return 2;
            } else {
                // Race distance was < 10% of weekly mileage
                return 1;
            }
        }

        //	No races during current week or last day of previous week, but racing first day of next week:
        if ($eventsThisWeek === false && $eventPreviousWeek === false && $eventNextWeek !== false) {
            // Race distance is < 10% of weekly mileage but not a key race [1]
            // Race distance is < 10% of weekly mileage and is a key race [2]
            // Race distance  is 10-15% of weekly mileage but not a key race [2]
            // Race distance  is 10-15% of weekly mileage and is a key race [3]
            // Race distance  is > 15% of weekly mileage but not a key race [3]
            // Race distance  is > 15% of weekly mileage and is a key race [4]
        }

        // No races during current week, but raced last day of previous week and racing first day of next week:
        if ($eventsThisWeek === false && $eventPreviousWeek !== false && $eventNextWeek !== false) {
            // Previous week's race distance was < 10% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race [2]
                // next week's race distance is < 10% of weekly mileage and is a key race [3]
                // next week's race distance  is 10-15% of weekly mileage but not a key race [3]
                // next week's race distance  is 10-15% of weekly mileage and is a key race [4]
                // next week's race distance  is > 15% of weekly mileage but not a key race [4]
                // next week's race distance  is > 15% of weekly mileage and is a key race [5]
            // Previous week's race distance was 10-15% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race [3]
                // next week's race distance is < 10% of weekly mileage and is a key race [4]
                // next week's race distance  is 10-15% of weekly mileage but not a key race [4]
                // next week's race distance  is 10-15% of weekly mileage and is a key race [5]
                // next week's race distance  is > 15% of weekly mileage but not a key race [5]
                // next week's race distance  is > 15% of weekly mileage and is a key race [6]
            // Previous week's race distance was > 15% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race [4]
                // next week's race distance is < 10% of weekly mileage and is a key race [5]
                // next week's race distance  is 10-15% of weekly mileage but not a key race [5]
                // next week's race distance  is 10-15% of weekly mileage and is a key race [6]
                // next week's race distance  is > 15% of weekly mileage but not a key race [6]
                // next week's race distance  is > 15% of weekly mileage and is a key race [7]
        }

        // Racing once or more during current week, but not on last day of previous week or first day of next week:
        if ($eventsThisWeek !== false && $eventPreviousWeek === false && $eventNextWeek === false) {
            // Total distance for all races during the week is < 10%  of weekly mileage. No key races [2]
            // Total distance for all races during the week is < 10% of weekly mileage. Contains a key race [3]
            // Total distance for all races during the week is 10-15%%  of weekly mileage. No key races [3]
            // Total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [4]
            // Total distance for all races during the week is > 15%%  of weekly mileage. No key races [4]
            // Total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [5]
        }


        // Racing once or more during current week and on last day of previous week, but not on first day of next week:
        if ($eventsThisWeek !== false && $eventPreviousWeek !== false && $eventNextWeek === false) {
            // Previous week's race distance was < 10% of weekly mileage and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [3]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [4]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [4]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [5]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [5]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [6]
            // Previous week's race distance was 10-15% of weekly mileage and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [4]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [5]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [5]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [6]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [6]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [7]
            // Previous week's race distance was > 15% of weekly mileage and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
        }

        // Racing once or more during current week and on first day of next week, but not on last day of previous week:
        if ($eventsThisWeek !== false && $eventNextWeek !== false && $eventPreviousWeek === false) {
            // Next week's race distance is < 10% of weekly mileage but not a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [4]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [5]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [5]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [6]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [6]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [7]
            // Next week's race distance is < 10% of weekly mileage and is a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
            // Next week's race distance  is 10-15% of weekly mileage but not a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
            // Next week's race distance  is 10-15% of weekly mileage and is a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
            // Next week's race distance  is > 15% of weekly mileage but not a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
            // Next week's race distance  is > 15% of weekly mileage and is a key race and
                // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
        }

        // The Calum James scenario. Racing once or more during current week and on first day of next week and on last day of previous week:
        if ($eventsThisWeek !== false && $eventNextWeek !== false && $eventPreviousWeek !== false) {
            // Previous week's race distance was < 10% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
                // next week's race distance is < 10% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
                // next week's race distance  is 10-15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
                // next week's race distance  is 10-15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
                // next week's race distance  is > 15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
                // next week's race distance  is > 15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
            // Previous week's race distance was 10-15% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
                // next week's race distance is < 10% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
                // next week's race distance  is 10-15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
                // next week's race distance  is 10-15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
                // next week's race distance  is > 15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
                // next week's race distance  is > 15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
            // Previous week's race distance was > 15% of weekly mileage and:
                // next week's race distance is < 10% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
                // next week's race distance is < 10% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
                // next week's race distance  is 10-15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
                // next week's race distance  is 10-15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
                // next week's race distance  is > 15% of weekly mileage but not a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
                // next week's race distance  is > 15% of weekly mileage and is a key race and:
                    // total distance for all races during the week is < 10%  of weekly mileage. No key races [10]
                    // total distance for all races during the week is < 10% of weekly mileage. Contains a key race [11]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. No key races [11]
                    // total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [12]
                    // total distance for all races during the week is > 15%%  of weekly mileage. No key races [12]
                    // total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [13]
        }
    }

}