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

    private function calculateEventAsPercentageOfWeeklyMileage()
    {
        // ...
    }

    private function calculateCombinedEventIntensityForTheWeek()
    {
        $eventPreviousWeek = $this->getPreviousWeeksEvent();
        $eventsThisWeek = $this->getCurrentWeeksEvents();
        $eventNextWeek = $this->getNextWeeksEvent();

        //	No races during current week, last day of previous week or first day of next week (typical training week) [0]
        if ($eventsThisWeek === false && $eventNextWeek === false && $eventPreviousWeek === false) {
            return 0;
        }

        //	No races during current week or first day of next week, but raced last day of previous week:
        if ($eventsThisWeek === false && $eventNextWeek === false && $eventPreviousWeek !== false) {
            //		a. Race distance was < 10% of weekly mileage [1]
            //		b. Race distance was 10-15% of weekly mileage [2]
            //		c. Race distance was > 15% of weekly mileage [3]
        }

//	3. No races during current week or last day of previous week, but racing first day of next week:
//		a. Race distance is < 10% of weekly mileage but not a key race [1]
//		b. Race distance is < 10% of weekly mileage and is a key race [2]
//		c. Race distance  is 10-15% of weekly mileage but not a key race [2]
//		d. Race distance  is 10-15% of weekly mileage and is a key race [3]
//		e. Race distance  is > 15% of weekly mileage but not a key race [3]
//		f. Race distance  is > 15% of weekly mileage and is a key race [4]
//	4. No races during current week, but raced last day of previous week and racing first day of next week:
//		a. Previous week's race distance was < 10% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race [2]
//			b. next week's race distance is < 10% of weekly mileage and is a key race [3]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race [3]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race [4]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race [4]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race [5]
//		b. Previous week's race distance was 10-15% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race [3]
//			b. next week's race distance is < 10% of weekly mileage and is a key race [4]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race [4]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race [5]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race [5]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race [6]
//		c. Previous week's race distance was > 15% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race [4]
//			b. next week's race distance is < 10% of weekly mileage and is a key race [5]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race [5]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race [6]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race [6]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race [7]
//	5. Racing once or more during current week, but not on last day of previous week or first day of next week:
//		a. Total distance for all races during the week is < 10%  of weekly mileage. No key races [2]
//		b. Total distance for all races during the week is < 10% of weekly mileage. Contains a key race [3]
//		c. Total distance for all races during the week is 10-15%%  of weekly mileage. No key races [3]
//		d. Total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [4]
//		e. Total distance for all races during the week is > 15%%  of weekly mileage. No key races [4]
//		f. Total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [5]
//	6. Racing once or more during current week and on last day of previous week, but not on first day of next week:
//		a. Previous week's race distance was < 10% of weekly mileage and
//    a. total distance for all races during the week is < 10%  of weekly mileage. No key races [3]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [4]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [4]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [5]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [5]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [6]
//		b. Previous week's race distance was 10-15% of weekly mileage and
//			a. total distance for all races during the week is < 10%  of weekly mileage. No key races [4]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [5]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [5]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [6]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [6]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [7]
//		c. Previous week's race distance was > 15% of weekly mileage and
//    a. total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
//	7. Racing once or more during current week and on first day of next week, but not on last day of previous week:
//		a. Next week's race distance is < 10% of weekly mileage but not a key race and
//			a. total distance for all races during the week is < 10%  of weekly mileage. No key races [4]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [5]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [5]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [6]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [6]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [7]
//		b. Next week's race distance is < 10% of weekly mileage and is a key race and
//    a. total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
//		c. Next week's race distance  is 10-15% of weekly mileage but not a key race and
//			a. total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
//		d. Next week's race distance  is 10-15% of weekly mileage and is a key race and
//    a. total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
//		e. Next week's race distance  is > 15% of weekly mileage but not a key race and
//			a. total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
//		f. Next week's race distance  is > 15% of weekly mileage and is a key race and
//    a. total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//			b. total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//			c. total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//			d. total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//			e. total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//			f. total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//	8. The Calum James scenario. Racing once or more during current week and on first day of next week and on last day of previous week:
//		a. Previous week's race distance was < 10% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [5]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [6]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [6]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [7]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [7]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [8]
//			b. next week's race distance is < 10% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
//		b. Previous week's race distance was 10-15% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [6]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [7]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [7]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [8]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [8]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [9]
//			b. next week's race distance is < 10% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
//		c. Previous week's race distance was > 15% of weekly mileage and:
//			a. next week's race distance is < 10% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [7]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [8]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [8]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [9]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [9]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [10]
//			b. next week's race distance is < 10% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
//			c. next week's race distance  is 10-15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [8]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [9]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [9]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [10]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [10]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [11]
//			d. next week's race distance  is 10-15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
//			e. next week's race distance  is > 15% of weekly mileage but not a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [9]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [10]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [10]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [11]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [11]
//				6) total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [12]
//			f. next week's race distance  is > 15% of weekly mileage and is a key race and:
//				1) total distance for all races during the week is < 10%  of weekly mileage. No key races [10]
//				2) total distance for all races during the week is < 10% of weekly mileage. Contains a key race [11]
//				3) total distance for all races during the week is 10-15%%  of weekly mileage. No key races [11]
//				4) total distance for all races during the week is 10-15%%  of weekly mileage. Contains a key race [12]
//				5) total distance for all races during the week is > 15%%  of weekly mileage. No key races [12]
//total distance for all races during the week is > 15%%  of weekly mileage. Contains a key race [13]
    }

}