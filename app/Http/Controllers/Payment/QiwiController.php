<?php

namespace App\Http\Controllers\Payment;

use App\Events\QiwiPayment;
use App\Http\Controllers\Controller;
use App\Order;
use App\Repositories\OrdersRepository;
use App\Traits\Payment\SendsQiwiResponse;
use App\Traits\Payment\ValidatesQiwiRequests;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QiwiController extends Controller
{
    use ValidatesQiwiRequests, SendsQiwiResponse;

    /**
     * @var OrdersRepository
     */
    private $orders;

    /**
     * QiwiController constructor.
     * @param OrdersRepository $orders
     */
    public function __construct(OrdersRepository $orders)
    {
        $this->orders = $orders;
    }

    public function process(Request $request)
    {
        /**
         * accepted `check` or `pay`
         */
        $command = $request->get('command');

        if (! method_exists($this, $command)) {
            throw new \Exception("Unknown command: {$command}");
        }

        return call_user_func_array([$this, $command], [$request]);
    }

    /**
     * @param Request $request
     * @return \SimpleXMLElement|string
     */
    protected function check(Request $request)
    {
        $response = $this->validateCheck($request);

        if ($response instanceof Order) {
            return $this->response($request, [
                "fields" => [
                    "amount" => $response->amount
                ]
            ]);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    protected function pay(Request $request)
    {
        $response = $this->validatePay($request);

        if ($response instanceof Order) {
            $order = $this->orders->save($response, [
                'status'  => Order::STATUS_PAID,
                'paid_at' => Carbon::createFromFormat('YmdHis', $request->get('txn_date')),
                'details' => $request->all()
            ]);

            event(new QiwiPayment($order));

            return $this->response($request, [
                'prv_txn' => $order->id,
                'sum'     => $this->toAmount($order->amount)
            ]);
        }

        return $response;
    }
}



