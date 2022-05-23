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

