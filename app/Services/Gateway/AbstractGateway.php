<?php

namespace App\Services\Gateway;

use App\Order;
use App\Repositories\OrdersRepository;

class AbstractGateway
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var OrdersRepository
     */
    protected $orders;

    /**
     * Qiwi constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->orders = app(OrdersRepository::class);
    }
}
