<?php

namespace App\Services;

use App\Order;
use App\Repositories\OffersRepository;
use App\Repositories\OrdersRepository;
use App\Repositories\UserHistoryRepository;
use App\Services\Gateway\Qiwi;
use App\Services\Gateway\VictoriaBank;
use App\User;

class CreateOrder
{
    protected $offer;
    /**
     * @var OrdersRepository
     */
    protected $orders;

    /**
     * @var OffersRepository
     */
    protected $offers;

    /**
     * @var UserHistoryRepository
     */
    private $records;

    public function __construct(OrdersRepository $orders, OffersRepository $offers, UserHistoryRepository $records)
    {
        $this->orders = $orders;
        $this->offers = $offers;
        $this->records = $records;
    }

    /**
     * @param User $user
     * @param array $data
     * @return mixed|static
     * @throws \Exception
     */
    public function create(User $user, array $data)
    {
        $data = $this->prepareOrderData($data);

        $this->validateHistoryOwner($user, $data);

        $order = $this->createOrder($data, function ($order) {
            switch ($order->gateway) {
                case Order::GATEWAY_QIWI:
                    return (new Qiwi($order))->prepare();

                case Order::GATEWAY_VB:
                    return (new VictoriaBank($order))->prepare();
                    break;
                case Order::GATEWAY_CASH:
                    return $order;
                    break;
            }

            throw new \Exception('Unknown gateway');
        });

        return $order;
    }

    public function update($order, $data)
    {
        $data = $this->prepareOrderData($data);

        return $this->orders->save($order, $data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareOrderData(array $data)
    {
        $this->offer = $this->offers->find($data['offer']);

        if (isset($data['workout']) && 'project' == $data['workout']) {
            $discount = 100;
            $amount = 0;
        } else {
            $discount = 0;
            if (isset($data['discount'])) {
                $discount = (int) $data['discount'];
            }
            $amount = $discount
                ? $this->getPriceWithDiscount($this->offer->price, $discount)
                : $this->offer->price;
        }

        $status = Order::STATUS_PENDING;
        if (isset($data['status'])) {
            $status = $data['status'];
        }

        return [
            'user_history_id' => $data['history'],
            'offer_id'        => $this->offer->id,
            'gateway'         => $data['gateway'],
            'period'          => $this->offer->period,
            'discount'        => $discount,
            'amount'          => $amount,
            'status'          => $status
        ];
    }

    protected function getPriceWithDiscount($price, $discount)
    {
        return (new Discount($price, $discount))->calculate();
    }

    /**
     * @param User $user
     * @param array $data
     * @throws \Exception
     */
    protected function validateHistoryOwner(User $user, array $data)
    {
        if (! $history = $this->records->findOne($data['user_history_id'], $user->id)) {
            throw new \Exception("History not found");
        }
    }

    protected function createOrder($data, \Closure $callback = null)
    {
        $order = $this->orders->create($data);

        if ($callback) {
            return call_user_func_array($callback, [$order]);
        }

        return $order;
    }
}





