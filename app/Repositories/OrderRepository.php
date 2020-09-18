<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    private $orders;
    private $orderItemRepository;

    /**
     * OrderRepository constructor.
     *
     * @param Order $model
     */
    public function __construct(Order $model, OrderItemRepositoryInterface $orderItemRepository)
    {
        parent::__construct($model);
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Fetches all orders from all order systems.
     *
     * @return Collection
     */
    public function fetchAll(): Collection
    {
        $orderSystemObj = new \App\Models\OrderSystem;
        $orderSystems = $orderSystemObj->all();

        $orders = collect([]);

        foreach ($orderSystems as $orderSystem) {
            // fetch orders for each order system
            $response = Http::get($orderSystem->get_endpoint_url);
            $newOrders = json_decode($response->body());

            // we add the order system to uniquely identify an order
            // by its order id given by the external order system
            // and its order system id
            foreach ($newOrders as &$newOrder) {
                $newOrder->order_system_id = $orderSystem->id;
            }

            // merge to return all orders
            $orders = $orders->merge($newOrders);
        }

        return $orders;
    }

    /**
     * Saves given orders to DB.
     *
     * @param Collection $orders
     * @return Collection
     */
    public function saveCollection(Collection $orders): Collection
    {
        $newOrders = collect([]);

        foreach ($orders as $order) {

            // we only want to process new orders
            if ($order->status !== 'new') {
                continue;
            }

            $newOrder = $this->updateOrCreate(
                [
                    'order_system_id' => $order->order_system_id,
                    'external_id' => $order->id,
                ],
                [
                    'shipping_date' => new \Carbon\Carbon($order->shippingDate),
                    'retailer_carrier_code' => $order->retailerCarrierCode,
                    'retailer_carrier_service_code' => $order->retailerCarrierServiceCode,
                    'billing_city' => $order->billingCity,
                    'billing_address' => $order->billingAddress,
                    'billing_country' => $order->billingCountry,
                    'billing_state' => $order->billingState,
                    'shipping_city' => $order->shippingCity,
                    'shipping_address' => $order->shippingAddress,
                    'shipping_country' => $order->shippingCountry,
                    'shipping_state' => $order->shippingState,
                    'status' => $order->status,
                ]
            );

            $this->orderItemRepository->saveCollectionAndSetValidity(collect($order->orderItems), $newOrder->id);

            $newOrders->push($newOrder);
        }

        return $newOrders;
    }

    /**
     * Determines and sets validity of all given orders.
     *
     * @param Collection $orders
     * @return Collection
     */
    public function setValidity(Collection $orders): Collection
    {
        foreach ($orders as $order) {
            $order->setValidity();
        }
        return $orders;
    }

    /**
     * Fetches all orders from all order systems and saves them to the DB.
     *
     * @return Collection
     */
    public function fetchAllAndSave(): Collection
    {
        $this->orders = $this->fetchAll();
        $this->orders = $this->saveCollection($this->orders);
        return $this->orders;
    }

    /**
     * Verifies and processes orders.
     *
     * @return Collection
     */
    public function verifyAndProcessAll(): Collection
    {
        $this->orders = $this->setValidity($this->orders);
        return $this->orders;
    }

    /**
     * Submits the processing results back to the order systems.
     *
     * @param Collection $orders
     * @return Collection
     */
    public function submitProcessingResults(Collection $orders): Collection
    {
        foreach ($orders as $order) {
            if ($order->submission_result !== 'OK') {
                // adjust url with order it
                $url = str_replace(':orderId', $order->id, $order->orderSystem->put_endpoint_url);
                // call endpoint
                $response = Http::put($url, [
                    'status' => $order->status,
                ]);
                // save response result
                $order->submission_result = $response->successful() ? 'OK' : 'FAILED';
                $order->save();
            }
        }
        return $this->orders;
    }
}
