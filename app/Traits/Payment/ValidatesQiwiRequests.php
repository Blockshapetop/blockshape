<?php

namespace App\Traits\Payment;

use App\Order;
use Illuminate\Http\Request;

trait ValidatesQiwiRequests
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    protected function validateCheck(Request $request)
    {
        if (!preg_match('~^\d{8}$~si', $request->get('account'))) {
            return $this->response($request, ['result' => 4]);
        }

        if (!$order = $this->findOrder($request)) {
            return $this->response($request, ['result' => 5]);
        }

        if (Order::STATUS_PENDING !== $order->status) {
            return $this->response($request, ['result' => 7]);
        }

        return $order;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    protected function validatePay(Request $request)
    {
        if (!preg_match('~^\d{8}$~si', $request->get('account'))) {
            return $this->response($request, ['result' => 4]);
        }

        if (!$order = $this->findOrder($request)) {
            return $this->response($request, ['result' => 5]);
        }

        if (Order::STATUS_PENDING !== $order->status) {
            return $this->response($request, ['result' => 7]);
        }

        if ($order->amount > $request->get('sum')) {
            return $this->response($request, ['result' => 241]);
        }

        if ($order->amount < $request->get('sum')) {
            return $this->response($request, ['result' => 242]);
        }

        return $order;
    }
}

