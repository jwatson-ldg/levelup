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

    /**
     * @param $event
     * @return int
     */
    private function calculateEventAsPercentageOfWeeklyMileage($event)
    {
        // Currently, average distance is stored in miles. Going forward, this could change so keep that in mind!
        if ($event['distance_unit'] === 'kilometre') {
            $eventDistance = $event['distance'] / 1.609;
        } else {
            $eventDistance = $event['distance'];
        }

        return intval(($eventDistance / $this->avgWeeklyMileage) * 100);
    }

    private function calculateCombinedEventIntensityForTheWeek()
    {
        // TODO: Create decision engine classes, split this down into it (currently WAY too much code for one method)

        $previousWeeksEvent = $this->getPreviousWeeksEvent();
        $currentWeeksEvents = $this->getCurrentWeeksEvents();
        $nextWeeksEvent = $this->getNextWeeksEvent();

        // No races during current week, last day of previous week or first day of next week (typical training week)
        if ($currentWeeksEvents === false && $nextWeeksEvent === false && $previousWeeksEvent === false) {
            return 0;
        }

        $previousWeeksEventAsPercentageOfWeeklyMileage = $this->calculateEventAsPercentageOfWeeklyMileage($previousWeeksEvent);
        
        // No races during current week or first day of next week, but raced last day of previous week:
        if ($currentWeeksEvents === false && $nextWeeksEvent === false && $previousWeeksEvent !== false) {
            if ($previousWeeksEventAsPercentageOfWeeklyMileage > 15) {
                // Race distance was > 15% of weekly mileage
                return 3;
            } else if ($previousWeeksEventAsPercentageOfWeeklyMileage >= 10 && $previousWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                // Race distance was 10-15% of weekly mileage
                return 2;
            } else {
                // Race distance was < 10% of weekly mileage
                return 1;
            }
        }

        $nextWeeksEventAsPercentageOfWeeklyMileage = $this->calculateEventAsPercentageOfWeeklyMileage($nextWeeksEvent);

        //	No races during current week or last day of previous week, but racing first day of next week:
        if ($currentWeeksEvents === false && $previousWeeksEvent === false && $nextWeeksEvent !== false) {
            if ($nextWeeksEventAsPercentageOfWeeklyMileage > 15) {
                if ($nextWeeksEvent['target_event']) {
                    // Race distance is > 15% of weekly mileage and is a key race
                    return 4;
                } else {
                    // Race distance is > 15% of weekly mileage but not a key race
                    return 3;
                }
            } else if ($nextWeeksEventAsPercentageOfWeeklyMileage >= 10 && $nextWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                if ($nextWeeksEvent['target_event']) {
                    // Race distance is 10-15% of weekly mileage and is a key race
                    return 3;
                } else {
                    // Race distance is 10-15% of weekly mileage but not a key race
                    return 2;
                }
            } else {
                if ($nextWeeksEvent['target_event']) {
                    // Race distance is < 10% of weekly mileage and is a key race
                    return 2;
                } else {
                    // Race distance is < 10% of weekly mileage but not a key race
                    return 1;
                }
            }
        }

        // No races during current week, but raced last day of previous week and racing first day of next week:
        if ($currentWeeksEvents === false && $previousWeeksEvent !== false && $nextWeeksEvent !== false) {
            if ($previousWeeksEventAsPercentageOfWeeklyMileage > 15) {
                // Previous week's race distance was > 15% of weekly mileage and:
                if ($nextWeeksEventAsPercentageOfWeeklyMileage > 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is > 15% of weekly mileage and is a key race
                        return 7;
                    } else {
                        // next week's race distance is > 15% of weekly mileage but not a key race
                        return 6;
                    }
                } else if ($nextWeeksEventAsPercentageOfWeeklyMileage >= 10 && $nextWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is 10-15% of weekly mileage and is a key race
                        return 6;
                    } else {
                        // next week's race distance is 10-15% of weekly mileage but not a key race
                        return 5;
                    }
                } else {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is < 10% of weekly mileage and is a key race
                        return 5;
                    } else {
                        // next week's race distance is < 10% of weekly mileage but not a key race
                        return 4;
                    }
                }
            } else if ($previousWeeksEventAsPercentageOfWeeklyMileage >= 10 && $previousWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                // Previous week's race distance was 10-15% of weekly mileage and:
                if ($nextWeeksEventAsPercentageOfWeeklyMileage > 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is > 15% of weekly mileage and is a key race
                        return 6;
                    } else {
                        // next week's race distance is > 15% of weekly mileage but not a key race
                        return 5;
                    }
                } else if ($nextWeeksEventAsPercentageOfWeeklyMileage >= 10 && $nextWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is 10-15% of weekly mileage and is a key race
                        return 5;
                    } else {
                        // next week's race distance is 10-15% of weekly mileage but not a key race
                        return 4;
                    }
                } else {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is < 10% of weekly mileage and is a key race
                        return 4;
                    } else {
                        // next week's race distance is < 10% of weekly mileage but not a key race
                        return 3;
                    }
                }
            } else {
                // Previous week's race distance was < 10% of weekly mileage and:
                if ($nextWeeksEventAsPercentageOfWeeklyMileage > 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is > 15% of weekly mileage and is a key race
                        return 5;
                    } else {
                        // next week's race distance is > 15% of weekly mileage but not a key race
                        return 4;

                    }
                } else if ($nextWeeksEventAsPercentageOfWeeklyMileage >= 10 && $nextWeeksEventAsPercentageOfWeeklyMileage <= 15) {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is 10-15% of weekly mileage and is a key race
                        return 4;
                    } else {
                        // next week's race distance is 10-15% of weekly mileage but not a key race
                        return 3;
                    }
                } else {
                    if ($nextWeeksEvent['target_event']) {
                        // next week's race distance is < 10% of weekly mileage and is a key race
                        return 3;
                    } else {
                        // next week's race distance is < 10% of weekly mileage but not a key race
                        return 2;
                    }
                }
            }
        }

        $currentWeeksEventsAsPercentageOfWeeklyMileage = 0;
        $keyEventThisWeek = false;

        foreach ($currentWeeksEvents as $currentWeeksEvent) {
            $currentWeeksEventsAsPercentageOfWeeklyMileage += $this->calculateEventAsPercentageOfWeeklyMileage($currentWeeksEvent);
            if ($currentWeeksEvent['target_event']) {
                $keyEventThisWeek = true;
            }
        }

        // Racing once or more during current week, but not on last day of previous week or first day of next week:
        if ($currentWeeksEvents !== false && $previousWeeksEvent === false && $nextWeeksEvent === false) {
            if ($currentWeeksEventsAsPercentageOfWeeklyMileage > 15) {
                if ($keyEventThisWeek) {
                    // Total distance for all races during the week is > 15%% of weekly mileage. Contains a key race
                    return 5;
                } else {
                    // Total distance for all races during the week is > 15%% of weekly mileage. No key races
                    return 4;
                }
            } else if ($currentWeeksEventsAsPercentageOfWeeklyMileage >= 10 && $currentWeeksEventsAsPercentageOfWeeklyMileage <= 15) {
                if ($keyEventThisWeek) {
                    // Total distance for all races during the week is 10-15%% of weekly mileage. Contains a key race
                    return 4;
                } else {
                    // Total distance for all races during the week is 10-15%% of weekly mileage. No key races
                    return 3;
                }
            } else {
                if ($keyEventThisWeek) {
                    // Total distance for all races during the week is < 10% of weekly mileage. Contains a key race
                    return 3;
                } else {
                    // Total distance for all races during the week is < 10% of weekly mileage. No key races
                    return 2;
                }
            }
        }

        // Racing once or more during current week and on last day of previous week, but not on first day of next week:
        if ($currentWeeksEvents !== false && $previousWeeksEvent !== false && $nextWeeksEvent === false) {
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
        if ($currentWeeksEvents !== false && $nextWeeksEvent !== false && $previousWeeksEvent === false) {
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
        if ($currentWeeksEvents !== false && $nextWeeksEvent !== false && $previousWeeksEvent !== false) {
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