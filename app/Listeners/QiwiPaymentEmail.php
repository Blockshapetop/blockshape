<?php

namespace App\Listeners;

use App\Events\QiwiPayment;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Terranet\Administrator\Model\Settings;

class QiwiPaymentEmail
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  QiwiPayment $event
     * @return void
     */
    public function handle(QiwiPayment $event)
    {
        $order = $event->order;
        $user = $order->userHistory->user;

        $this->mailer->send('emails.qiwi.payment_ok', [
            'txn_id' => $order->details['txn_id'],
            'name'   => $user->name,
            'period' => $order->period
        ], function (Message $message) use ($user) {
            $message->from(Settings::getOption('from::email'), Settings::getOption('from::name'));
            $message->to($user->email);
            $message->subject(trans('user.emails.qiwi_payment.subject'));
        });
    }
}


ists('id')->toArray();
        $excludes = $this->record->excludes->lists('id')->toArray();

        $params = new Params();
        $params->setForTarget($target = $this->record->target->slug);


        if ($forUser == 'allDays') {
            $days = $this->allDays;
        }elseif($forUser === 'oneDay'){
            $days = $this->oneDay;
        }else{
            $days = $this->weekDays;
        }

        // dd($days);
        foreach ($days as $dayNum => $day) {
            $disabled = [];
            $type = $this->schedule[$day]['type'];

            $nutrition = [];
            $workoutTime = null;
            if (in_array($type, ['activity', 'rest'])) {
                if ('activity' == $type) {
                    $workoutTime = $this->schedule[$day]['time'];
                    $dailySchedule = $this->loadScheduler()->schedule($workoutTime);
                } else {
                    $dailySchedule = $this->restScheduler()->schedule();
                }

                $eatingNum = 0;
                foreach ($dailySchedule as $hour => $nutrient) {
                    if ('water' == $nutrient) {
                        continue;
                    }

                    list($eating, $nutrient) = explode(':', $nutrient);

                    $isSnack = ('snack' == $eating);
                    if ('main' == $eating) {
                        $eatingNum++;
                    }

                    $params->setNutrient($nutrient)
                        ->setSnack($isSnack)
                        ->setEatingNum($eatingNum)
                        ->setDiseases($diseases)
                        ->setAllergies($allergies)
                        ->setFoodExcludes($excludes)
                        ->setDisabled($disabled);

                    $placement = null;
                    if ('activity' == $type && ! $isSnack) {
                        $hourInMins = (new TimeToMins($hour))->convert();
                        $workoutTimeInMins = (new TimeToMins($workoutTime))->convert();

                        $placement = $hourInMins == $workoutTime ? null : ($hourInMins > $workoutTimeInMins ? 'after' : 'before');
                    }
                    $params->setPlacement($placement);

                    $recipe = $this->recipeFinder->find($params);

                    if (! $recipe) {
                        $recipe = new Recipe([
                            'nutrient'  => new Nutrient(['name' => $nutrient]),
                            'name'      => 'Not found (' . $params->debug() . ')',
                            'quantity'  => 0,
                            'season'    => null,
                            'snack'     => $isSnack,
                            'eating'    => $eatingNum,
                            'placement' => $placement
                        ]);
                    }

                    if ($recipe && $recipe->id) {
                        $disabled[] = $recipe->id;
                    }

                    $nutrition[$hour] = [
                        'nutrient'  => $recipe->nutrient->name,
                        'name'      => /*$eating . ' | ' . */$recipe->name,
                        'quantity'  => $recipe->getQuantity($target),
                        'season'    => $recipe->season,
                        'snack'     => $recipe->snack,
                        'eating'    => $recipe->eating,
                        'placement' => $recipe->placement
                    ];
                }
            } else {
                $nutrition = glossary('definition.detoxification', 'body');
            }

            $schedule[$dayNum] = [
                'day' => $day,
                'type' => $type,
                'workout' => $workoutTime,
                'nutrition' => $nutrition
            ];
        }

        return $schedule;
    }

    /**
     * @return mixed
     */
    private function loadScheduler()
    {
        return app('LoadScheduler')->driver($this->record->target->slug);
    }

    /**
     * @return mixed
     */
    private function restScheduler()
    {
        return app('RestScheduler')->driver($this->record->target->slug);
    }

    /**
     * @return array
     */
    public function getWeekDays()
    {
        return $this->weekDays;
    }

    /**
     * @param $record
     * @return $this
     */
    public function setRecord($record)
    {
        $this->record = $record;

        return $this;
    }
}
