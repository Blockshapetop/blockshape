<?php

namespace App\Http\Controllers\Payment;

use App\Events\VbPayment;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Order;
use App\Repositories\OrdersRepository;
use App\Services\CurrencyConverter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Terranet\VictoriaBank\Gateway\Transactions\Authorization;
use Terranet\VictoriaBank\Gateway\Transactions\Completion;
use Terranet\VictoriaBank\Gateway\Transactions\Reversal;
use Terranet\VictoriaBank\VbRepository;
use Terranet\VictoriaBank\VbServiceException;
use VbService;

class VbController extends Controller
{
    /**
     * @var OrdersRepository
     */
    private $orders;

    /**
     * VbController constructor.
     * @param OrdersRepository $orders
     */
    public function __construct(OrdersRepository $orders)
    {
        $this->orders = $orders;
    }

    /**
     * Send payment request to victoria bank
     *
     * @param Request $request
     * @return mixed
     */
    public function process(Request $request)
    {
        // dd($request->all());
        $order = $this->orders->find($request->get('order_id'));

        $user = $this->fetchUserFromOrder($order);

        try {
            $amount = (new CurrencyConverter($order->amount))->convert();

            return VbService::pay($order->id, $amount, $user->email, trans('user.payment.vb.description'));
        } catch (VbServiceException $e) {
            return redirect()->withError($e);
        }
    }

    /**
     * @param $order
     * @return mixed
     */
    protected function fetchUserFromOrder($order)
    {
        return $order->userHistory->user;
    }

    /**
     * All VB callbacks will come here
     *
     * @param Request $request
     * @return string
     */
    public function response(Request $request)
    {
        if (config('vb.debug')) {
            app('files')->append(storage_path('logs/vb.log'), debugMessage($request->all()));
        }

        $bankResponse = app('vb-response');

        $this->validateResponse($bankResponse);

        $order = $this->fetchOrder($bankResponse);

        $this->orders->save($order, [
            'details' => $request->all()
        ]);

        (new VbRepository)->create([
            'order_id'     => $order->id,
            'client_email' => 'test@example.com',
            'status'       => $bankResponse->getStatus(),
            'params'       => $request->all()
        ]);

        switch ((int) $request->get('TRTYPE')) {
            case Authorization::TR_TYPE:
                if ($bankResponse->isSuccessStatus()) {
                    //VbService::confirm($request->all());
                    event(new VbPayment($order));
                }
                break;

            case Completion::TR_TYPE:
                $status = $this->normalizeStatus($bankResponse);

                $this->orders->save($order, [
                    'status'  => in_array($status, ['pending', 'paid', 'declined', 'fault']) ? $status : 'fault',
                    'paid_at' => ('paid' == $status ? Carbon::createFromFormat('YmdHis', $request->get('TIMESTAMP')) : null),
                ]);
                break;

            case Reversal::TR_TYPE;
                if ($bankResponse->isSuccessStatus()) {
                    $this->orders->save($order, [
                        'status' => "refund"
                    ]);
                }
                break;
        }

        return 'ok';
    }

    /**
     * @param $bankResponse
     */
    protected function validateResponse($bankResponse)
    {
        if (! $bankResponse->isValid()) {
            abort(500, 'Bad request');
        }
    }

    /**
     * @param $bankResponse
     * @return mixed
     */
    protected function fetchOrder($bankResponse)
    {
        if (! $order = $this->orders->find((int) $bankResponse->getOrder())) {
            abort(404, 'Order not found');
        }

        return $order;
    }

    /**
     * @param $bankResponse
     * @return string
     */
    protected function normalizeStatus($bankResponse)
    {
        $status = strtolower($bankResponse->getStatus());

        if ('success' == $status) {
            return 'paid';
        }

        return $status;
    }

    /**
     * Page where user will be redirected after success payment
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success(Request $request)
    {
        $bankResponse = app('vb-response');

        $this->validateResponse($bankResponse);

        if ($bankResponse->isSuccessStatus()) {
            return view('payment.vb.success', [
                'data' => $request->all()
            ]);
        }

        return view('payment.vb.failure');
    }
}




