<?php

namespace App\Nutrition\Load;

abstract class AbstractDriver
{
    /**
     * Min nutrition time
     *
     * @var int
     */
    protected $hourMin;

    /**
     * Max nutrition time
     *
     * @var int
     */
    protected $hourMax;

    /**
     * The total (max) number of food outlets
     *
     * @var
     */
    protected $eatingsNumber;

    /**
     * Nutrition rules based on time shift related by exercise time
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Resulted nutrition schedule
     *
     * @var array
     */
    protected $schedule = [];

    /**
     * Rules indexes
     *
     * @var array
     */
    protected $shifts;

    /**
     * Workout time (in minutes)
     *
     * @var integer
     */
    protected $time;

    /**
     * Index of current rule
     *
     * @var integer
     */
    protected $ruleIndex;

    /**
     * Handles relation btw timeShift and selected nutrient
     * Used for easy lookup then detecting mirroring timeShifts
     *
     * @var array
     */
    protected $shiftNutrient = [];

    /**
     * Index of Zero time rule
     *
     * @var int
     */
    protected $startIndex;

    /**
     * Index of shift where Left shifting stopped out
     *
     * @var int
     */
    protected $movingLeftStopped;

    /**
     * Index of shift where Right shifting stopped out
     *
     * @var int
     */
    protected $movingRightStopped;

    protected $iteration = 1;

    public function __construct()
    {
        $this->shifts = array_keys($this->rules);

        $this->startIndex = array_search(0, $this->shifts);
    }

    /**
     * Generate nutrition schedule
     *
     * @param $time - Exercise time
     * @return array
     */
    public function schedule($time)
    {
        $this->time = time_to_mins($time);

        while (true) {
            $shiftedLeft = $shiftedRight = 0;

            // if previous timeShift exists
            if (! $this->movingLeftStopped) {
                if (($this->ruleIndex = $this->canShiftLeft()) !== false && ! ($shiftedLeft = $this->fillSchedule())) {
                    $this->movingLeftStopped = $this->startIndex - $this->iteration;
                }
            }

            if ($this->scheduleFilled()) {
                break;
            }

            // if next timeShift exists
            if (! $this->movingRightStopped) {
                if (($this->ruleIndex = $this->canShiftRight()) !== false && ! ($shiftedRight = $this->fillSchedule())) {
                    $this->movingRightStopped = $this->startIndex + $this->iteration;
                }
            }

            if ($this->scheduleFilled()) {
                break;
            }

            if (! ($shiftedLeft || $shiftedRight)) {
                break;
            }

            $this->nextIteration();
        }

        // use non-greedy logic to add missing hours in places where potentially can be ignored main hours shift and added a smaller shift
        if (! $this->scheduleFilled()) {
            $this->orderScheduleByTime();
            $this->addMissingHours();
        }

        // add nutrition during exercise
        $this->schedule[$this->time] = $this->extractNutrient(0);

        $this->orderScheduleByTime();

        return $this->keysToTime();
    }

    /**
     * @return bool
     */
    private function canShiftLeft()
    {
        $nextIndex = $this->startIndex - $this->iteration;

        return $nextIndex >= 0 ? $nextIndex : false;
    }

    /**
     * @return bool
     */
    private function canShiftRight()
    {
        return ($nextIndex = $this->startIndex + $this->iteration) < count($this->shifts) ? $nextIndex : false;
    }

    /**
     * Get nutrition for specific hour
     *
     * @return int
     */
    protected function fillSchedule()
    {
        $shiftHours = $this->shift();

        $minutes = $this->getShiftedTime($shiftHours);

        $hour = $this->hour($minutes);

        // когда разрешаем есть в пределах последнего часа (18-19), следим, чтобы последний прием пищи не превышал 1 час
        // однако разница между временем тренировки и первым приемом пищи, так же не должна быть меньши чем X часов (временно 3)
        // ex.: время занятия: 9:30, приемы пищи: 7:30, 9:30, 12:30, 15:30, 17:30, 19:30 (19:30 на 1.5 часа позже чем макс позволенное 18:00)
        // поэтому понижаем до 19:00
        // данные ограничения работают для интервалов по ±2 часа и не пропустит смещение на ±3 часа
        // для смещения ±3 часа если понадобится, расписание доукомплектуется позже

        if ($this->eatableTime($hour) || (abs($hour - $this->hourMin) == 1 || abs($hour - $this->hourMax) == 1)) {
            if ($minutes > (($this->hourMax + 1) * 60) && ($minutes - $this->time > 180)) {
                $minutes -= 30;
            } else if ($minutes <= ($this->hourMin - 1) * 60) {
                $minutes += 30;
            }

            $this->schedule[$minutes] = $nutrient = $this->extractNutrient($shiftHours);
            $this->shiftNutrient[$shiftHours] = substr($nutrient, strpos($nutrient, ':') + 1);

            return true;
        }

        return false;
    }

    /**
     * Time shift by index
     *
     * @param null $byIndex
     * @return mixed
     */
    protected function shift($byIndex = null)
    {
        if (! $byIndex)
            $byIndex = $this->ruleIndex;

        return $this->shifts[$byIndex];
    }

    /**
     * @param $hoursShift
     * @return mixed
     */
    protected function getShiftedTime($hoursShift)
    {
        $minutes = $this->time + $hoursShift * 60;

        if ($this->positiveShift($hoursShift)) {
            $minutes += 60;
        }

        return $minutes;
    }

    /**
     * Check if selected time is later then exercise time
     *
     * @param $hoursShift
     * @return bool
     */
    protected function positiveShift($hoursShift)
    {
        return $hoursShift > 0;
    }

    /**
     * @param $minutes
     * @return array
     */
    protected function hour($minutes)
    {
        return floor($minutes / 60);
    }

    /**
     * Allow to eat at this time or not
     *
     * @param $hour
     * @return bool
     */
    protected function eatableTime($hour)
    {
        return ((int) $hour >= $this->hourMin && (int) $hour <= $this->hourMax);
    }

    /**
     * @param       $shiftHours
     * @return mixed
     */
    protected function extractNutrient($shiftHours)
    {
        if (is_string($this->rules[$shiftHours])) {
            return $this->rules[$shiftHours];
        } else {
            $options = $this->rules[$shiftHours];
            $type = $options['type'];
            $nutrients = $options['nutrients'];

            // регулируем равнозаполнение всеми компонентами
            if (count($this->shiftNutrient) && count($nutrients) > 1) {
                $nutrientCounts = array_count_values($this->shiftNutrient);
                arsort($nutrientCounts);
                $exclude = array_keys($nutrientCounts)[0];

                $nutrients = array_filter($nutrients, function ($nutrient) use ($exclude) {
                    return $nutrient != $exclude;
                });

                // if remain only vegetables-carbohydrates, restore excluded component
                if (in_array('vegetables-carbohydrates', $nutrients) && 1 == count($nutrients)) {
                    $nutrients[] = $exclude;
                }
            }

            // validate eating number and nutrients depending of main target
            if (method_exists($this, 'fixNutrients') && $response = $this->fixNutrients($shiftHours, $type)) {
                list($type, $nutrients) = $response;
            }

            shuffle($nutrients);
            $nutrient = $type . ":" . $nutrients[0];
        }

        return $nutrient;
    }

    /**
     * @return bool
     */
    private function scheduleFilled()
    {
        return count($this->schedule) == ($this->eatingsNumber);
    }

    /**
     * @return mixed
     */
    protected function nextIteration()
    {
        $this->iteration++;
    }

    /**
     * Order schedule by time
     *
     */
    protected function orderScheduleByTime()
    {
        ksort($this->schedule);
    }

    /**
     * Пытаемся "дополнить" дневной рацион недостающими компонентами
     */
    private function addMissingHours()
    {
        $scheduleHours = array_keys($this->schedule);

        // выясняем в какую сторону есть возможность добавить недостающий прием пищи
        $shiftLeft = $this->startIndex - $this->movingLeftStopped;
        $shiftRight = $this->movingRightStopped - $this->startIndex;

        if (! $this->scheduleFilled() && $shiftLeft < $shiftRight && ($firstEatingTime = array_shift($scheduleHours))) {
            $expectedShift = abs($this->shift($this->movingLeftStopped + 1) - $this->shift($this->movingLeftStopped));
            $allowShift = ceil(max($expectedShift - 2, 1) / 2);
            if ($allowShift <= 1) {
                $missingTime = $this->hourMin * 60 - $allowShift * 60;
                $this->schedule[$missingTime] = $this->extractNutrient($this->shift($this->movingLeftStopped));
            }
        }

        if (! $this->scheduleFilled() && $shiftRight < $shiftLeft && ($lastEatingTime = array_pop($scheduleHours))) {
            $expectedShift = abs($this->shift($this->movingRightStopped + 1) - $this->shift($this->movingRightStopped));
            $allowShift = ceil(max($expectedShift - 2, 1) / 2);
            if ($allowShift <= 1) {
                $missingTime = $this->hourMax * 60 + $allowShift * 60;
                $this->schedule[$missingTime] = $this->extractNutrient($this->shift($this->movingRightStopped));
            }
        }
    }

    private function keysToTime()
    {
        $vals = array_values($this->schedule);
        $keys = array_keys($this->schedule);
        $keys = array_map(function ($key) {
            return mins_to_time($key);
        }, $keys);

        return array_combine($keys, $vals);
    }

    /**
     * @param $mins
     * @param $hours
     * @return array
     */
    protected function zeroPaddify($hours, $mins)
    {
        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $mins = str_pad($mins, 2, '0', STR_PAD_LEFT);

        return [$hours, $mins];
    }
}
